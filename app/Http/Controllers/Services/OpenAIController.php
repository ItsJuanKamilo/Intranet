<?php

namespace App\Http\Controllers\Services;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Adempiere\Views\MaestroProductosWeb;
use App\Models\Producto;
use App\Models\ProductoOpenAI;
use App\Models\Web\ProductoWebDescripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OpenAIController extends Controller
{

    public static function question($search)
    {

        $data = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])
            ->post("https://api.openai.com/v1/chat/completions", [
                "model" => "gpt-3.5-turbo-16k",
                'messages' => [
                    [
                        "role" => "user",
                        "content" => $search
                    ]
                ]
            ]);

        return $data['choices'][0]['message']['content'];
    }


    public static function cargarDatosWebProductosDesdeOPENAI()
    {

        $productos = MaestroProductosWeb::all();

        foreach ($productos as $producto) {
            try {

                $aux = ProductoOpenAI::where('sku', $producto->codproid)->first();

                if ((!$aux || $aux->descripcion == '')) {
                    $search = "Estamos lanzando productos en nuestra tienda online Artilec en el link  https://www.artilec.com/ de productos de seguridad y necesitamos crear fichas de productos detalladas y convincentes para cada uno.
    Necesito que me entreges una ficha del producto optimizada para SEO en formato json : titulo , slug  (incluye el sku al final), metadescription , url_detalles_tecnicos ( link para buscar los detalles tecnicos del producto en google que contenga el numero de serie , marca en el url y Artilec ) , keywords , descripcion para una tienda online( una descripción que atraiga a los clientes y que incluya todos los detalles relevantes del producto. Asegúrate de incluir las especificaciones tecnicas en una lista y cualquier otra información importante que los clientes necesiten saber.Tambien queremos destacar información sobre la marca. Recuerda que queremos que sea fácil de leer y que incluya palabras clave relevantes para mejorar la visibilidad del producto en motores de búsqueda y redes sociales ) , productos_alternativos (  Tambien si me puedes agregar una lista de productos alternativos si conoces con una url de busqueda en google a los alternativos con el numero de serie , marca y artilec ).
     Utiliza esta descripcion para generar la nueva ficha producto $producto->nombreproducto, numero de serie $producto->modelo , marca $producto->marca , sku $producto->codproid";
                    if (!$aux) $aux = new ProductoOpenAI();
                    $aux->sku = $producto->codproid;
                    $aux->producto = $producto->nombreproducto;
                    $aux->modelo = $producto->modelo;
                    $res = OpenAIController::question($search);
                    $res = json_decode($res);
                    if (isset($res->titulo)) $aux->titulo = $res->titulo;
                    if (isset($res->slug)) $aux->slug = $res->slug;
                    if (isset($res->metadescription)) $aux->metadescription = $res->metadescription;
                    if (isset($res->url_detalles_tecnicos)) $aux->url_detalles_tecnicos = $res->url_detalles_tecnicos;
                    if (isset($res->keywords)) $aux->keywords = $res->keywords;
                    if (isset($res->descripcion)) $aux->descripcion = $res->descripcion;
                    if (isset($res->productos_alternativos)) $aux->productos_alternativos = json_encode($res->productos_alternativos);
                    $aux->save();
                }

            } catch (\Exception $e) {
               continue;
            }
        }

    }


    public static function actualizarDatosWebDesdeInformacionOpenAI()
    {


        $openai = ProductoOpenAI::whereNotNull('descripcion')->where('esta_en_web', false)->get();

        foreach ($openai as $open) {
            $desc = null;
            if (Str::length($open->sku) < 10) {

                $desc = ProductoWebDescripcion::find($open->sku);

                $aux = $open->descripcion;
                if ($desc && $desc->description && Str::length($desc->description) > 149) $aux = $desc->description;


                ProductoWebDescripcion::updateOrCreate(
                    ['codigo' => $open->sku],
                    [
                        'codigo' => $open->sku,
                        'descripcion' => $aux,
                        'productos_alternativos' => $open->productos_alternativos,
                        'url_detalles_tecnicos' => $open->url_detalles_tecnicos
                    ]
                );


                $open->esta_en_web = true;
                $open->save();
            }
        }


    }

}
