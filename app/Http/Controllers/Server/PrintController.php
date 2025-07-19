<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\Notifications\ArtilecLog;
use App\Models\Web\Event;
use App\Models\Web\EventAttended;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\Printer;
use Html2Text\Html2Text;

class PrintController extends Controller
{
    public const VENTAS="DCPL2540DW";
    public const BODEGA_MESON="MesonClientes";
    public const BODEGA_REGION="BodegaDespachos";
    public const BODEGA_SANTIAGO="ImpresoraBodegaSantiago";
    public static function test($printer_name)
    {
        // Genera el archivo PDF a partir de la vista
        $pdf = app()->make(PDF::class);

        $evento= Event::find(369);
        $inscritos = EventAttended::where('evento_id',369)->get();

        $html = View::make('pdf.marketing.evento',compact('evento','inscritos'))->render();
        $pdf->loadHTML($html);
        $filename= 'app/eventos/'.$evento->id.'.pdf';
        $pdf->save(storage_path($filename));

        return $pdf->stream();
    }

    public static function printPDF($printer_name,$filename){
        $command = "lp -d $printer_name -o media=letter -o fit-to-page $filename";
//ArtilecLog::sendMessage($command);
        $res = exec($command);
        //ArtilecLog::sendMessage($res);
        //si res contiene request id is, es porquye la impresion fue buena y devuelve true
        return strpos($res, 'request id is') !== false;
    }

}

