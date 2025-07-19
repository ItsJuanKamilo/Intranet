<?php

namespace App\Http\Controllers\Services;

use App\Mail\MercadolibreControlPDF;
use App\Models\Adempiere\C_Order;
use App\Models\Adempiere\WS_Sale;
use App\Models\ArtilecNotification;
use App\Models\MercadolibreOrders;
use App\Models\MercadolibreStore;
use App\Notifications\DiscordNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PDF;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;

class MercadolibreController extends Controller
{

    public static function syncOrdersMercadolibreToBD()
    {
        $stores = MercadolibreStore::whereNotNull('access_token')->where('active', true)->get();

        foreach ($stores as $store) {
            $store->tokenUpdate();

            $sales = $store->getOrders();
            $ordersml = $sales->get('results');
            $paging = $sales->get('paging');
            $store->orders_total = $paging['total'];
            $store->save();

            $store->processOrders($ordersml);

            if (!$store->legacydata) {
                $offset = 0;
                $offset += $paging['limit'];

                while ($offset < $store->orders_total) {
                    $sales = $store->getOrders($offset);
                    $ordersml = $sales->get('results');
                    $paging = $sales->get('paging');

                    $store->processOrders($ordersml);
                    $offset += $paging['limit'];
                }

                $store->legacydata = true;
                $store->save();
            }


        }
    }

    public static function searchOrder($store_id, $order_id)
    {
        $store = MercadolibreStore::find($store_id);
        $store->tokenUpdate();
        return $store->getOrder($order_id);
    }


    public static function checkProcesses()
    {
        $stores = MercadolibreStore::whereNotNull('access_token')->where('active', true)->get();

        foreach ($stores as $store) {

            $store->tokenUpdate();
            $processes = $store->processes;
            if ($processes) {
                if ($processes->email_internal) $store->sendInternalEmails();
                if ($processes->termalprint) $store->printTermalOrders();
                //if ($processes->messages_initial) $store->sendMessages();
                if ($processes->adempiere) $store->syncAdempiereMercadolibreOrders();
            }

        }

    }

    public static function checkProcessesDaily()
    {
        $stores = MercadolibreStore::whereNotNull('access_token')->where('active', true)->get();

        foreach ($stores as $store) {

            $processes = $store->processes;
            if ($processes) {
                if ($processes->report_publications_paused) $store->reportPausedPublications();
            }

        }

    }


    public static function checkOrdersToPrint()
    {
        $stores = MercadolibreStore::whereNotNull('access_token')->where('active', true)->get();

        foreach ($stores as $store) {
            $processes = $store->processes;
            if ($processes->pdf_control) {
                $store->printControlPDF();
            }
        }

    }

    public function getStores(Request $request)
    {
        $stores = MercadolibreStore::where('active', true)->get();
        return response()->json($stores);
    }


    public function getOrders(Request $request)
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $orders = MercadolibreOrders::where('store_id', $request->store_id)
            ->whereDate('created_at', '>=', $thirtyDaysAgo)  // Filtra las órdenes de los últimos 30 días
            ->orderBy('created_at', 'desc')
            ->get();

        return view('intranet.bodega.mercadolibre.orders', compact('orders'));
    }


    public function printOrderLabel(Request $request)
    {
        $order = MercadolibreOrders::find($request->id);
        $print = $order->printTermalLabel(true);

        if ($print) {
            return response()->json(['message' => 'Etiqueta impresa']);
        } else {
            // Devolver un error 500
            return response()->json(['message' => 'Error al imprimir la etiqueta'], 500);
        }
    }

    public function syncOrderLabel(Request $request)
    {
        $order = MercadolibreOrders::find($request->id);
        $sync = $order->loadFromMercadolibreTermalLabel();

        if ($sync) {
            return response()->json(['message' => 'Etiqueta sincronizada. Actualizando..']);
        } else {
            // Devolver un error 500
            return response()->json(['message' => 'Error al sincronizar la etiqueta'], 500);
        }
    }


    public function sendOrderEmail(Request $request)
    {
        $order = MercadolibreOrders::find($request->id);
        $order->sendInternalEmail(true);
        return response()->json(['message' => 'Email enviado']);
    }


}
