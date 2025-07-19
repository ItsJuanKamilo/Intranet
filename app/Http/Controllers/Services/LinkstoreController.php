<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Adempiere\M_Product;
use App\Models\Adempiere\M_ProductPrice;
use App\Models\Adempiere\M_Storage;
use App\Models\Adempiere\Views\MaestroProductos;
use App\Models\Warehouse\WarehouseLinkstore;
use App\Models\Admin\Report;
use App\Models\Catalog\Product;
use App\Models\Server\Configuration;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LinkstoreController extends Controller
{

    const LINKSTORE_PRODS = "https://v1.lk.cl/clientes/listados/fabricantes.php?e=0684098bbb37a5579baa53ecbf71d17a";
    const LINKSTORE_BRANDS = "https://v1.lk.cl/clientes/listados/manufacturers.php";
    const LINKSTORE_CAT = "https://v1.lk.cl/clientes/listados/categorias.php";
    const LINKSTORE_SUBCAT = "https://v1.lk.cl/clientes/listados/subcategorias.php";


    public function test()
    {
        return response()->json(['status' => 'ok', 'msg' => 'test'], 200);
    }

    public static function products($id = null, $brand = null, $category = null)
    {

        try {
            $dataproducts = json_decode(
                Http::withBasicAuth('gautien@artilec.net', 'ga3043123')->get(self::LINKSTORE_PRODS)->body()
            );
            //only load ubiquity = 403
            /*
              Id marca ( Nombres en opción Marcas)
              id ProductoWeb
              Código producto
              Descripción
              Cantidad
              Cantidad en tránsito
              Fecha llegada transito
              precio
              Precio Distribuidor
              Id categoría (nombre en opción Categorías)
              Id subcategoría (Nombre en opción Subcategorías)
              Peso
             */

            echo "Productos: " . count($dataproducts) . "\n";

            $products = array();
            $brands = LinkstoreController::brands();
            $categories = LinkstoreController::categories();
            $subcategories = LinkstoreController::subcategories();

            //ajuste de precio venta : precio * FACTOR_LK redondeado a 0
            $confg = Configuration::where('name', 'bodega_sincronizar_linkstore_factorlinkstore')->first();
            $factor_lk = (float)$confg->value;
            $products_api = Product::all();

            foreach ($dataproducts as $p) {

                $codigo_lk = $p[1];
                switch ($p[1]) {
                    case '10445' :
                        $p[1] = '31043';
                        break;
                    case '5331' :
                        $p[1] = '60968';
                        break;
                    case '6854' :
                        $p[1] = '60110';
                        break;
                    case '6059' :
                        $p[1] = '60227';
                        break;
                    case '10768' :
                        $p[1] = '60116';
                        break;
                    case '5194' :
                        $p[1] = '62010';
                        break;
                    case '4039' :
                        $p[1] = '60108';
                        break;
                    case '6598' :
                        $p[1] = '60234';
                        break;
                    case '6597' :
                        $p[1] = '60243';
                        break;
                    case '4043' :
                        $p[1] = '60221';
                        break;
                    case '4041' :
                        $p[1] = '60220';
                        break;
                    case '4040' :
                        $p[1] = '60230';
                        break;
                    case '3214' :
                        $p[1] = '60064';
                        break;
                    case '3118' :
                        $p[1] = '60091';
                        break;
                    case '5332' :
                        $p[1] = '60105';
                        break;
                    case '5323' :
                        $p[1] = '60219';
                        break;
                    case '11008' :
                        $p[1] = '31096';
                        break;
                    case '11594' :
                        $p[1] = '31044';
                        break;
                    case '11595' :
                        $p[1] = '31112';
                        break;
                    case '7841' :
                        $p[1] = '60296';
                        break;
                    case '3187' :
                        $p[1] = '60290';
                        break;
                    case '6591' :
                        $p[1] = '60291';
                        break;
                    case '6592' :
                        $p[1] = '60299';
                        break;
                    case '7066' :
                        $p[1] = '60298';
                        break;
                    case '6593' :
                        $p[1] = '60286';
                        break;
                    case '6594' :
                        $p[1] = '60289';
                        break;
                    case '9199' :
                        $p[1] = '60288';
                        break;
                    case '6600' :
                        $p[1] = '60029';
                        break;
                    case '9752' :
                        $p[1] = '60248';
                        break;
                    case '6894' :
                        $p[1] = '60107';
                        break;
                    case '4203' :
                        $p[1] = '60067';
                        break;
                    case '4201' :
                        $p[1] = '60965';
                        break;
                    case '5539' :
                        $p[1] = '42047';
                        break;
                    case '9749' :
                        $p[1] = '60242';
                        break;
                    case '6501' :
                        $p[1] = '60113';
                        break;
                    case '4048' :
                        $p[1] = '60070';
                        break;
                    case '4038' :
                        $p[1] = '60071';
                        break;
                    case '6502' :
                        $p[1] = '60229';
                        break;
                    case '5659' :
                        $p[1] = '60238';
                        break;
                    case '6728' :
                        $p[1] = '60217';
                        break;
                    case '9755' :
                        $p[1] = '60245';
                        break;
                    case '6061' :
                        $p[1] = '70118';
                        break;
                    case '6062' :
                        $p[1] = '70117';
                        break;
                    case '3765' :
                        $p[1] = '60082';
                        break;
                    case '10430' :
                        $p[1] = '60155';
                        break;
                    case '4586' :
                        $p[1] = '60115';
                        break;
                    case '6499' :
                        $p[1] = '60233';
                        break;
                    case '4031' :
                        $p[1] = '60088';
                        break;
                    case '6054' :
                        $p[1] = '70126';
                        break;
                    case '4307' :
                        $p[1] = '60102';
                        break;
                    case '4030' :
                        $p[1] = '60080';
                        break;
                    case '9746' :
                        $p[1] = '60244';
                        break;
                    case '5326' :
                        $p[1] = '19088';
                        break;
                    case '4727' :
                        $p[1] = '60247';
                        break;
                    case '9602' :
                        $p[1] = '80061';
                        break;
                    case '7392' :
                        $p[1] = '60236';
                        break;
                    case '7393' :
                        $p[1] = '60240';
                        break;
                    case '7394' :
                        $p[1] = '60241';
                        break;
                    case '9626' :
                        $p[1] = '60117';
                        break;
                    case '7658' :
                        $p[1] = '61995';
                        break;
                    case '4613' :
                        $p[1] = '60106';
                        break;
                    case '7787' :
                        $p[1] = '31111';
                        break;
                    case '10623' :
                        $p[1] = '62007';
                        break;
                    case '10414' :
                        $p[1] = '70076';
                        break;
                    case '11164' :
                        $p[1] = '80062';
                        break;
                    case '9344' :
                        $p[1] = '61997';
                        break;
                    default:
                        $p[1] = 'UB' . $p[1];
                        break;
                }

                //limitar nombre a 55 caracteres y modelo a 17


                $price = (int)round($p[8] / $factor_lk, -1);
                $brand_lk = Str::upper($brands[$p[0]]['name']);


                if ($categories && array_key_exists($p[9], $categories)) {
                    $cat = $categories[$p[9]]['name'];
                } else {
                    $cat = 'SIN CATEGORIA';
                }

                if ($subcategories && array_key_exists($p[10], $subcategories)) {
                    $subcat = $subcategories[$p[10]]['name'];
                } else {
                    $subcat = 'SIN SUBCATEGORIA';
                }


                if ($id && $p[1] != $id)
                    continue;
                else if ($brand && Str::upper($brand) != $brand_lk)
                    continue;
                else if ($category && Str::lower($cat) != Str::lower($category))
                    continue;

                $sku = $p[1];
                $model = $p[2];
                //algunos modelos acaban en . y se debe quitar
                if (Str::endsWith($model, '.')) {
                    $model = substr($model, 0, -1);
                }

                $product = $products_api->where('model', $model)->first();
                //$product = null;

                $products[$p[1]]['id_marca'] = $p[0];
                $products[$p[1]]['marca'] = Str::upper($brands[$p[0]]['name']);
                $products[$p[1]]['id_producto'] = $p[1];
                $products[$p[1]]['lk_id'] = $codigo_lk;
                $products[$p[1]]['codigo'] = $p[2];
                $products[$p[1]]['descripcion'] = $p[3];
                $products[$p[1]]['cantidad'] = $p[4];
                $products[$p[1]]['transito'] = $p[5];
                $products[$p[1]]['fecha_transito'] = $p[6];
                $products[$p[1]]['precio_lk'] = $p[7];
                $products[$p[1]]['precio_lk_distribuidor'] = $p[8];
                $products[$p[1]]['precio_artilec_factor'] = $price;
                $products[$p[1]]['precio_artilec_l2'] = $product->l2 ?? 0;
                $products[$p[1]]['ctopp_artilec'] = $product->cost ?? 0;
                $products[$p[1]]['id_categoria'] = $p[9];
                $products[$p[1]]['id_subcategoria'] = $p[10];
                $products[$p[1]]['categoria'] = $cat;
                $products[$p[1]]['subcategoria'] = $subcat;
                $products[$p[1]]['peso'] = $p[11];
                $products[$p[1]]['imagenes'] = json_encode($p[12]);
                $products[$p[1]]['descripcionweb'] = $p[13];
                $products[$p[1]]['url_fabricante'] = $p[14];
                $products[$p[1]]['url_datasheet'] = $p[15];
                $products[$p[1]]['d16'] = $p[16];
                $products[$p[1]]['relacionados'] = $p[17];

            }
            echo "Productos filtrados: " . count($products) . "\n";

            return $products;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    public static function brands($brand = null)
    {
        try {

            $brands = array();
            $databrands = json_decode(Http::withBasicAuth('gautien@artilec.net', 'ga3043123')->get(self::LINKSTORE_BRANDS)->body());
            /*
             *   array:2 [
             *      0 => "480"
             *      1 => "000PORDEFECTO"
             *   ]
             *   0 -> codigo
             *   1 -> nombre
             */
            foreach ($databrands as $b) {
                if ($brand && strtolower($b[1]) === strtolower($brand)) {
                    $brands = array();
                    $brands[$b[0]]['id'] = $b[0];
                    $brands[$b[0]]['name'] = $b[1];
                    break;
                } else {
                    $brands[$b[0]]['id'] = $b[0];
                    $brands[$b[0]]['name'] = $b[1];
                }
            }

            return $brands;
        } catch (\Exception $exc) {
            Log::error("Error brands: " . $exc->getMessage());
            throw $exc;
        }
    }

    public static function categories($cat = null)
    {
        try {

            $cats = array();
            $datacat = json_decode(Http::withBasicAuth('gautien@artilec.net', 'ga3043123')->get(self::LINKSTORE_CAT)->body());
            /**
             * "1": {
             *   "id": "1",
             *   "name": "Inalámbrico Exterior"
             * },
             */
            foreach ($datacat as $b) {
                if ($cat && strtolower($b[1]) == strtolower($cat)) {
                    $cats = array();
                    $cats[$b[0]]['id'] = $b[0];
                    $cats[$b[0]]['name'] = $b[1];
                    break;
                } else {
                    $cats[$b[0]]['id'] = $b[0];
                    $cats[$b[0]]['name'] = $b[1];
                }
            }
            return $cats;
        } catch (\Exception $exc) {
            return null;
        }
    }

    public static function subcategories($subcat = null)
    {
        try {

            $subcats = array();
            $datasubcat = json_decode(Http::withBasicAuth('gautien@artilec.net', 'ga3043123')->get(self::LINKSTORE_SUBCAT)->body());


            foreach ($datasubcat as $b) {
                if ($subcat && strtolower($b[1]) == strtolower($subcat)) {
                    $subcats = array();
                    $subcats[$b[0]]['id'] = $b[0];
                    $subcats[$b[0]]['name'] = $b[1];
                    break;
                } else {
                    $subcats[$b[0]]['id'] = $b[0];
                    $subcats[$b[0]]['name'] = $b[1];
                }
            }
            return $subcats;
        } catch (\Exception $exc) {
            return null;
        }
    }

    public static function save_warehouse_inventory_day($products)
    {
        $day_inventory = Carbon::now();

        try {

            DB::beginTransaction();
            WarehouseLinkstore::where('date_inventory', $day_inventory->format('Y-m-d'))->delete();
            $last_day_inventory = WarehouseLinkstore::select(DB::raw('MAX(date_inventory) as fecha'))->first()->fecha;

            foreach ($products as $p) {

                $p_lastinventory = WarehouseLinkstore::where('date_inventory', $last_day_inventory)->where('id_producto', $p['id_producto'])->first();

                $wh = new WarehouseLinkstore();
                $wh->date_inventory = $day_inventory->format('Y-m-d');

                $wh->id_producto = $p['id_producto'];
                $wh->lk_id = $p['lk_id'];
                $wh->brand = $p['marca'];
                $wh->id_brand = $p['id_marca'];
                $wh->code_lk = $p['codigo'];
                $wh->description_lk = $p['descripcion'];
                $wh->stock = $p['cantidad'];
                if (is_numeric($p['transito']))
                    $wh->transit = $p['transito'];
                else
                    $wh->transit = 0;

                if ($p_lastinventory) {
                    $wh->sale = ($p_lastinventory)->stock - ($p['cantidad']);
                }

                if ($p['fecha_transito'] != 0)
                    $wh->transit_date = $p['fecha_transito'];
                $wh->price_lk_public = $p['precio_lk'];
                $wh->price_lk_vendor = $p['precio_lk_distribuidor'];
                $wh->price_artilec_factor = $p['precio_artilec_factor'];
                $wh->price_artilec_l2 = $p['precio_artilec_l2'];
                $wh->ctopp_artilec = $p['ctopp_artilec'];
                $wh->id_category = $p['id_categoria'] ?? 0;
                $wh->id_subcategory = $p['id_subcategoria'] ?? 0;
                $wh->category = $p['categoria'] ?? 'SIN CATEGORIA';
                $wh->subcategory = $p['subcategoria'] ?? 'SIN SUBCATEGORIA';
                $wh->weight = $p['peso'];
                $wh->replacement = $p['d16'];
                $wh->relateds = $p['relacionados'];
                $wh->save();
            }

            DB::commit();

        } catch (\Exception $exc) {
            DB::rollBack();
            throw $exc;
        }
    }


    public static function send_email_stock_prices()
    {

        $day_inventory = Date::now();
        $last_day_inventory = WarehouseLinkstore::select(DB::raw('MAX(date_inventory) as fecha'))->first()->fecha;
        $lastinventory = WarehouseLinkstore::where('date_inventory', $last_day_inventory)->get();


        $report = new Report(Report::XLSX, "Reporte Stock/Precios Ubiquiti contra Linkstore Mensual ", 'ubiquiti-stockprecios-semanal/');

        $report->data = [
            "headers" => array("Codigo", "ProductoWeb", "Modelo", "PrecioL2", "PrecioLKVenta", "PrecioLKCompra", "FisicoArtilec", "FisicoLK"),
            "data" => array()
        ];


        $prods_adempiere = MaestroProductos::get(['codproid', 'total_fisico', 'l2'])->keyBy('codproid')->toArray();


        foreach ($lastinventory as $li) {

            $stock_adempiere = 0;

            if (array_key_exists($li->id_producto, $prods_adempiere)) {
                $stock_adempiere = $prods_adempiere[$li->id_producto]['total_fisico'];

                $report->data['data'][$li->id_producto] = [
                    $li->id_producto,
                    $li->description_lk,
                    $li->code_lk,
                    $li->price_artilec_l2,
                    $li->price_lk_public,
                    $li->price_lk_vendor,
                    $stock_adempiere,
                    $li->stock
                ];
            }
        }

        $report->addline(count($lastinventory) . " productos.");
        $report->endlog();

        $report->sendNotification("mtapay@artilec.net");
        $report->sendNotification("cmella@artilec.com");

    }

}
