<?php

// app/Http/Controllers/PartnerController.php

namespace App\Http\Controllers\Partners;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class PartnerController extends Controller
{
    public function index()
    {
        return view('partners.index');
    }

    public function datatable(Request $request)
    {
        $sub = \DB::table('partner_addresses')
            ->select('partner_id', \DB::raw('MIN(id) as first_billing_id'))
            ->where('is_billing', true)
            ->groupBy('partner_id');

        $query = Partner::where('is_customer', true)
            ->leftJoinSub($sub, 'pa_first', function ($join) {
                $join->on('partners.id', '=', 'pa_first.partner_id');
            })
            ->leftJoin('partner_addresses as pa', 'pa.id', '=', 'pa_first.first_billing_id')
            ->leftJoin('users', function($join) {
                $join->on(\DB::raw('users.rut::text'), '=', 'partners.customer_salesperson_tax');
            })
            ->select(
                'partners.id',
                'partners.dv',
                'partners.name as cliente',
                'partners.customer_salesperson_tax',
                'partners.email',
                'partners.phone',
                'partners.credit_used',
                'partners.credit_limit_min',
                'partners.credit_limit_max',
                'partners.is_customer',
                'partners.is_vendor',
                'partners.is_subdistributor',
                'partners.is_bdm',
                'partners.payment_email_1 as email_cobranzas',
                'pa.region as region',
                'users.email as vendor_email',
                'users.phone as vendor_phone',
                'users.annex as vendor_annex',
                'users.local as vendor_local',
                \DB::raw("CONCAT(users.name, ' ', users.surname_1) as vendor_name")
            );

        return \Yajra\DataTables\Facades\DataTables::of($query)
            ->filter(function($query) use ($request) {
                if ($search = $request->get('search')['value']) {
                    $query->where(function($q) use ($search) {
                        $q->where('partners.id', 'like', "%{$search}%")
                            ->orWhere('partners.name', 'like', "%{$search}%")
                            ->orWhere('partners.email', 'like', "%{$search}%")
                            ->orWhere('partners.phone', 'like', "%{$search}%")
                            ->orWhere('partners.payment_email_1', 'like', "%{$search}%")
                            ->orWhere('pa.region', 'like', "%{$search}%")
                            ->orWhere('partners.customer_salesperson_tax', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(users.name, ' ', users.surname_1) ILIKE ?", ["%{$search}%"]);
                    });
                }
            })
            ->addColumn('cliente_info', function ($partner) {
                $rutConDv = $partner->id;
                if (isset($partner->dv)) {
                    $rutConDv .= '-' . $partner->dv;
                }
                $comuna = $partner->region ? $partner->region : '<span class="text-danger">N/A</span>';
                return "<strong>🪪:</strong> <strong>{$rutConDv}</strong><br><strong>👨🏻‍💼️:</strong> {$partner->cliente}<br><strong>📍:</strong> {$comuna}";
            })
            ->addColumn('contacto', function ($partner) {
                $emailPrincipal = $partner->email ? $partner->email : '<span class="text-danger">N/A</span>';
                $emailPago = $partner->email_cobranzas ? $partner->email_cobranzas : '<span class="text-danger">N/A</span>';
                $telefono = $partner->phone
                    ? '📞: ' . $partner->phone
                    : '📞: <span class="text-danger">N/A</span>';
                return "<strong>Correo Principal:</strong> {$emailPrincipal}<br><strong>Correo Pago:</strong> {$emailPago}<br><strong>{$telefono}</strong>";
            })
            ->addColumn('creditos', function ($partner) {
                $used = $partner->credit_used ?? 0;
                $max = $partner->credit_limit_max ?? 0;
                if ($max == 0) {
                    return '<span class="text-danger">El Cliente no tiene crédito.</span>';
                }
                $available = $max - $used;
                $usedFormatted = number_format($used, 0, ',', '.');
                $availableFormatted = number_format($available, 0, ',', '.');
                $maxFormatted = number_format($max, 0, ',', '.');
                $percentage = ($max > 0) ? round(($used / $max) * 100) : 0;
                return "<strong>Usado:</strong> \${$usedFormatted} ({$percentage}% usado)
            <br><span class=\"text-danger\"><strong>Disponible:</strong> \${$availableFormatted}</span>
            <br><strong>Máx:</strong> \${$maxFormatted}";
            })
            ->addColumn('tipo', function ($partner) {
                $tipo = [];

                // Condiciones para 'Cliente'
                $tipo[] = $partner->is_customer
                    ? '<i class="fas fa-check text-success"></i> <strong>Cliente</strong>'
                    : '<i class="fas fa-times text-danger"></i> Cliente';

                // Condiciones para 'Proveedor'
                $tipo[] = $partner->is_vendor
                    ? '<i class="fas fa-check text-success"></i> <strong>Proveedor</strong>'
                    : '<i class="fas fa-times text-danger"></i> Proveedor';

                // Condiciones para 'Subdistribuidor'
                $tipo[] = $partner->is_subdistributor
                    ? '<i class="fas fa-check text-success"></i> <strong>Subdistribuidor</strong>'
                    : '<i class="fas fa-times text-danger"></i> Subdistribuidor';

                // Condiciones para 'BDM'
                $tipo[] = $partner->is_bdm
                    ? '<i class="fas fa-check text-success"></i> <strong>BDM</strong>'
                    : '<i class="fas fa-times text-danger"></i> BDM';

                return implode('<br>', $tipo);
            })

            ->addColumn('vendedor', function ($partner) {
                if ($partner->customer_salesperson_tax === 'ecommerc') {
                    return 'Comercial';
                }
                $nombre = $partner->vendor_name ? trim($partner->vendor_name) : '<span class="text-danger">N/A</span>';
                $correo = $partner->vendor_email ? $partner->vendor_email : '<span class="text-danger">N/A</span>';
                $tel = $partner->vendor_phone ? $partner->vendor_phone : '<span class="text-danger">N/A</span>';
                $anexo = $partner->vendor_annex ? $partner->vendor_annex : '<span class="text-danger">N/A</span>';
                $local = $partner->vendor_local ? $partner->vendor_local : '<span class="text-danger">N/A</span>';
                return "<strong>Nombre:</strong> {$nombre}<br><strong>Correo:</strong> {$correo}<br><strong>Tel:</strong> {$tel} | <strong>Anexo:</strong> {$anexo}<br><strong>Local:</strong> {$local}";
            })
            ->addColumn('action', function ($partner) {
                $showUrl = route('partners.show', $partner->id);
                $clientName = addslashes($partner->cliente);
                return '<a href="' . $showUrl . '"
            data-use-iframe="true"
            data-iframe-tab="Cliente: ' . $clientName . '"
            class="nav-link">
            <i class="fa fa-eye"></i> Ver
        </a>';
            })
            ->rawColumns([
                'cliente_info',
                'contacto',
                'creditos',
                'tipo',
                'vendedor',
                'action'
            ])
            ->make(true);
    }

    public function show($id)
    {
        $partner = Partner::where('partners.id', $id)
            ->leftJoin('partner_addresses as pa', 'pa.partner_id', '=', 'partners.id')
            ->leftJoin('users', function ($join) {
                $join->on(\DB::raw("users.rut::text"), '=', 'partners.customer_salesperson_tax');
            })
            ->select(
                'partners.id',
                'partners.dv',
                'partners.name',
                'partners.type',
                'partners.vat_description',
                'partners.is_customer',
                'partners.is_vendor',
                'partners.is_subdistributor',
                'partners.is_bdm',
                'partners.customer_pricing_list_name',
                'pa.region',
                'pa.city',
                'pa.province',
                'pa.address',
                \DB::raw("CONCAT(users.name, ' ', users.surname_1) as seller"),
                'users.email',
                'partners.customer_payment_method_name',
                'partners.customer_payment_term_name',
                'partners.credit_limit_max',
                'partners.credit_used',
                'partners.credit_status',
                'partners.payment_email_1',
                'partners.payment_email_2',
                'partners.email',
                'users.email as email_seller',
                'partners.phone',
                'users.phone as phone_seller',
                'users.local as local_seller',
            )
            ->firstOrFail();

        if (\Schema::hasTable('invoices')) {
            $invoices = \DB::table('invoices')->where('partner_id', $id)->get();
        } else {
            $invoices = collect();
        }

        return view('partners.show', compact('partner', 'invoices'));
    }





}
