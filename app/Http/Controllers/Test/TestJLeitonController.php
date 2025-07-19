<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Web\Event;
use Carbon\Carbon;

class TestJLeitonController extends Controller
{
    public function index()
    {
        // Obtener el primer evento disponible
        $event = Event::find(624); // Obtiene el evento con el ID 624 // O puedes usar alguna lógica para seleccionar un evento específico

        // Verificar si el evento existe
        if (!$event) {
            return response()->json(['message' => 'No hay eventos disponibles.']);
        }

        // Obtener los asistentes del evento
        $attendees = $event->attendances;

        // Obtener los ruts de empresa (tax_vat) de los asistentes (sin el dígito verificador)
        $attendeeRuts = $attendees->pluck('tax_vat')
            ->map(function ($taxVat) {
                // Quitar el guion y el dígito verificador de tax_vat
                return substr($taxVat, 0, strlen($taxVat) - 2); // Cortamos el DV
            })
            ->toArray();

        // Obtener los vendedores de la base de datos de ventas
        $vendedores = \DB::connection('pgsql')
            ->table('partners')
            // Convertir 'vat' a VARCHAR para la comparación
            ->whereIn(\DB::raw('CAST(vat AS TEXT)'), $attendeeRuts)
            ->join('users', \DB::raw('CAST(partners.customer_salesperson_tax AS TEXT)'), '=', \DB::raw('CAST(users.rut AS TEXT)'))  // Convertir 'customer_salesperson_tax' y 'rut' a VARCHAR
            ->select('partners.vat', 'users.name', 'users.surname_1', 'partners.customer_salesperson_tax', 'partners.vat_dv')
            ->get();

        // Juntar los vendedores con los asistentes según el rut completo
        foreach ($attendees as $attendee) {
            // Obtener el rut completo concatenando vat y vat_dv
            $fullVat = substr($attendee->tax_vat, 0, strlen($attendee->tax_vat) - 2);  // Eliminar el DV

            // Encontrar el vendedor correspondiente por rut completo
            $vendedor = $vendedores->firstWhere('vat', $fullVat);

            // Añadir el nombre y apellido del vendedor al asistente
            $attendee->vendor_name = $vendedor ? $vendedor->name . ' ' . $vendedor->surname_1 : 'No asignado';
        }

        // Retornar los asistentes con los vendedores asignados como respuesta JSON
        return response()->json($attendees);











//$events = Event::withCount(['attendances', 'confirmedAttendances'])->latest();
        //dd($events->count());
       // return DataTables::of($events)
         //   ->addColumn('action', function ($event) {
         //       $editUrl = route('marketing.events.edit', $event->id);
         //       $deleteUrl = route('marketing.events.destroy', $event->id);
          //      return '<a href="'.$editUrl.'" class="btn btn-sm btn-primary" title="Editar">
          //                  <i class="fas fa-edit"></i>
          //              </a>
           //             <form action="'.$deleteUrl.'" method="POST" class="d-inline" onsubmit="return confirm(\'¿Seguro que deseas eliminar este evento?\')">
           //                 '.csrf_field().method_field('DELETE').'
           //                 <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
           //                     <i class="fas fa-trash-alt"></i>
           //                 </button>
           //             </form>';
           // })
          //  ->rawColumns(['action'])
          //  ->make(true);
//
        //$result = Storage::put('api/avatars/test.txt', 'Hola, mundo');
        //return response()->json([
        //    'result' => $result,
        //    'url' => Storage::url('api/avatars/test.txt')
        //]);


        // Consulta para obtener los nombres de las tablas del esquema "public"
        //$tables = DB::connection('pgsql')
        //    ->select("SELECT table_name
        //              FROM information_schema.tables
        //              WHERE table_schema = 'public'
        //              AND table_type = 'BASE TABLE'");
//
       // return response()->json($tables);

        //$content = "Este es un archivo de prueba";
        //$filePath = 'test.txt';
        //$result = Storage::put($filePath, $content);
        //$url = Storage::url($filePath);
        //dd($result,$url);

       // if ($result) {
       //     return response()->json([
       //         'message' => 'Archivo subido exitosamente',
       //         'file'    => $filePath,
       //     ]);
       // } else {
       //     return response()->json([
       //         'message' => 'Error al subir el archivo'
       //     ], 500);
       // }



      //  try {
      //      // Obtiene el cliente AWS S3 usando el método getClient()
      //      $clients = Storage::disk('s3')->getClient();
//
      //      // Listamos los buckets (requiere el permiso s3:ListAllMyBuckets)
      //      $buckets = $clients->listBuckets();
//
      //      dd($buckets);
      //  } catch (\Exception $e) {
      //      dd('Error de conexión: ' . $e->getMessage());
      //  }


        $url = 'https://laravel.com/api/9.x/Illuminate/Notifications/Messages/MailMessage.html';
        $markdown = new MailMessage();
        $markdown->subject('Restablecimiento de Contraseña');
        $markdown->markdown('mail.auth.reset-password', ['url' => $url]);

        $renderedMarkdown = Markdown::parse($markdown->render());

        return $renderedMarkdown;

    }
}
