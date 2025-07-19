<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Catalog\ProductImage;
use Carbon\Carbon;



class ProductController extends Controller
{
    public function index()
    {
        // Retorna la vista para el listado de productos
        return view('products.index');
    }

    public function edit($sku)
    {
        // Encuentra el producto por SKU y carga la relación de la imagen
        $product = Product::with('brand', 'image')  // Cargamos la relación 'image'
        ->findOrFail($sku);

        // Si no hay imagen, asignamos una variable nula o una URL predeterminada
        $imageUrl = $product->image ? $product->image->url : null;

        // Retorna la vista con el producto y su URL de imagen
        return view('products.edit', compact('product', 'imageUrl'));
    }

    public function update(Request $request, $sku)
    {
        // 1) Validación
        $messages = [
            'images.*.image' => 'Cada archivo debe ser una imagen.',
            'images.*.mimes' => 'Las imágenes deben ser de tipo: jpeg, png, jpg, gif, svg.',
            'images.*.max'   => 'Cada imagen no debe exceder los 4 MB.',
        ];
        $validated = $request->validate([
            'description' => 'nullable|string',
            'image_id'    => 'nullable|array',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ], $messages);

        $product = Product::findOrFail($sku);

        DB::beginTransaction();
        try {
            // 2) Actualizar descripción
            if ($request->filled('description')) {
                $product->description = $validated['description'];
                $product->save();
            }

            // 3) Preparar datos
            $slots       = $request->input('image_id', []);    // [ slot => id|null, ... ]
            $filesBySlot = $request->file('images', []);       // [ slot => UploadedFile, ... ]
            $existing    = DB::table('catalog_products_images')
                ->where('sku', $sku)
                ->get();

            // 4) Eliminar registros ya no usados
            $keepIds = array_filter($slots, fn($v) => !is_null($v) && $v !== '');
            foreach ($existing as $img) {
                if (!in_array($img->id, $keepIds)) {
                    Storage::disk('s3')->delete($img->path);
                    DB::table('catalog_products_images')
                        ->where('id', $img->id)
                        ->delete();
                }
            }

            // 5) Subidas nuevas (slot A)
            foreach ($slots as $order => $id) {
                if (! empty($filesBySlot[$order])) {
                    $file   = $filesBySlot[$order];
                    $record = $id ? $existing->firstWhere('id', $id) : null;

                    // borrar anterior si existe
                    if ($record) {
                        Storage::disk('s3')->delete($record->path);
                    }

                    // generar nombre: slot 0 => "SKU.ext", otros => "SKU-order.ext"
                    $ext      = $file->getClientOriginalExtension();
                    $filename = $order === 0
                        ? "{$product->sku}.{$ext}"
                        : "{$product->sku}-{$order}.{$ext}";

                    $folder = 'catalogo/productos/imagenes';
                    $path   = $file->storeAs($folder, $filename, 's3');
                    Storage::disk('s3')->setVisibility($path, 'public');

                    // URL con nocache
                    $url    = Storage::disk('s3')->url($path) . '?nocache=' . time();

                    $data = [
                        'sku'           => $product->sku,
                        'order'         => $order,
                        'path'          => $path,
                        'url'           => $url,
                        'filename'      => $filename,
                        'is_active'     => true,
                        'updated_at'    => Carbon::now(),
                        'title'         => $product->name,
                        'seo_title'     => $product->name,
                        'og_title'      => $product->name,
                        'og_image_url'  => $url,
                    ];

                    if ($record) {
                        DB::table('catalog_products_images')
                            ->where('id', $record->id)
                            ->update($data);
                    } else {
                        $data['created_at'] = Carbon::now();
                        DB::table('catalog_products_images')
                            ->insert($data);
                    }
                }
            }

            // 6) Renombrados con temp (slot B)
            // refrescar existing tras posibles inserciones/updates
            $existing = DB::table('catalog_products_images')
                ->where('sku', $sku)
                ->get();

            // preparar operaciones de rename
            $ops = [];
            foreach ($slots as $order => $id) {
                $record = $id ? $existing->firstWhere('id', $id) : null;
                if ($record && $record->order != $order) {
                    $oldPath = $record->path;
                    $ext     = pathinfo($oldPath, PATHINFO_EXTENSION);
                    $newName = $order === 0
                        ? "{$product->sku}.{$ext}"
                        : "{$product->sku}-{$order}.{$ext}";
                    $newPath = dirname($oldPath) . "/{$newName}";
                    $ops[]   = compact('record','oldPath','newPath','order');
                }
            }

            // primera pasada: copy old → temp, delete old
            foreach ($ops as &$op) {
                $op['tempPath'] = $op['newPath'] . '.tmp';
                Storage::disk('s3')->copy($op['oldPath'],  $op['tempPath']);
                Storage::disk('s3')->delete($op['oldPath']);
                Storage::disk('s3')->setVisibility($op['tempPath'], 'public');
            }
            unset($op);

            // segunda pasada: copy temp → final, delete temp, actualizar BD
            foreach ($ops as $op) {
                Storage::disk('s3')->copy($op['tempPath'], $op['newPath']);
                Storage::disk('s3')->delete($op['tempPath']);
                Storage::disk('s3')->setVisibility($op['newPath'], 'public');

                // URL con nocache
                $newUrl = Storage::disk('s3')->url($op['newPath']) . '?nocache=' . time();

                DB::table('catalog_products_images')
                    ->where('id', $op['record']->id)
                    ->update([
                        'order'         => $op['order'],
                        'path'          => $op['newPath'],
                        'url'           => $newUrl,
                        'filename'      => basename($op['newPath']),
                        'og_image_url'  => $newUrl,
                        'updated_at'    => Carbon::now(),
                    ]);
            }

            DB::commit();
            return back()->with('success', 'Producto e imágenes actualizados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error actualizando el producto: ".$e->getMessage());
            return back()->withErrors('Ocurrió un error al actualizar el producto.');
        }
    }







    public function datatable(Request $request)
    {
        try {
            $query = Product::leftJoin('catalog_products_prices', 'catalog_products.sku', '=', 'catalog_products_prices.product')
                ->leftJoin('catalog_products_stock as stock_huechuraba', function ($join) {
                    $join->on('catalog_products.sku', '=', 'stock_huechuraba.product')
                        ->where('stock_huechuraba.warehouse', 'HUECHURABA');
                })
                ->leftJoin('catalog_products_stock as stock_exequiel', function ($join) {
                    $join->on('catalog_products.sku', '=', 'stock_exequiel.product')
                        ->where('stock_exequiel.warehouse', 'EXEQUIEL');
                })
                ->leftJoin('catalog_products_stock as stock_ml', function ($join) {
                    $join->on('catalog_products.sku', '=', 'stock_ml.product')
                        ->where('stock_ml.warehouse', 'MERCADO LIBRE');
                })
                ->with('brand', 'image')
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->where('brand', '!=', '0')
                ->select(
                    'catalog_products.sku',
                    'catalog_products.internal_id',
                    'catalog_products.name',
                    'catalog_products.model',
                    'catalog_products.brand',
                    'catalog_products.index',
                    'catalog_products.category',
                    'catalog_products.subcategory',
                    'catalog_products_prices.price as price_net_iva',
                    // Concatenamos 'name' y 'sku' para la búsqueda combinada
                    DB::raw("CONCAT(catalog_products.name, ' ', catalog_products.sku) as product_info"),
                    DB::raw('COALESCE(stock_huechuraba.qty_physical, 0) as stock_huechuraba_qty'),
                    DB::raw('COALESCE(stock_exequiel.qty_physical, 0) as stock_exequiel_qty'),
                    DB::raw('COALESCE(stock_ml.qty_physical, 0) as stock_ml_qty')
                );


            // Agregar filtros de búsqueda
            $search = $request->input('search.value');
            if (!empty($search)) {
                // Buscar solo en el nombre y SKU de la tabla catalog_products
                $query->where(function($query) use ($search) {
                    $query->whereRaw('catalog_products.name ILIKE ?', ['%' . $search . '%'])
                        ->orWhereRaw('catalog_products.sku ILIKE ?', ['%' . $search . '%'])
                        ->orWhereRaw('catalog_products.brand ILIKE ?', ["%{$search}%"]);

                });
            }

            return DataTables::of($query)
                ->addColumn('product_info', function ($product) {

                    $cat    = $product->category    ?: 'sin-categoria';
                    $subcat = $product->subcategory ?: 'sin-subcategoria';
                    $brandName  = $product->brand ?? 'N/A';
                    $model      = $product->model;
                    $index      = $product->index;
                    $sku        = $product->sku;
                    $internalId = $product->internal_id;
                    $name       = htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8');

                    // arma el slug
                    $urlSlug = Str::slug("{$cat}-{$subcat}-{$brandName}-{$name}");

                    // construye la URL completa
                    $url = "https://artilec.com/{$urlSlug}";

                    // devuelves tu HTML con el enlace
                    $safeName = e($product->name);

                    return <<<HTML
                    <div class="px-2">
                      <!-- 1) Título a toda la anchura -->
                      <a href="{$url}" target="_blank" class="text-navy" style="font-size:15px;">
                        <strong>{$safeName}</strong>
                      </a>

                      <div class="row gx-1 gy-1" style="font-size:14px;">
                        <div class="col-6">
                          <div><strong>Modelo:</strong> {$model}</div>
                          <div><strong>Marca:</strong> {$brandName}</div>
                          <div><strong>ID:</strong> {$internalId}</div>
                        </div>
                        <div class="col-6">
                          <div><strong>Índice:</strong> {$index}</div>
                          <div><strong>PDF: </strong><a href="https://artilec.com" target="_blank" class="text-danger"><i class="fas fa-file-alt fa-lg"></i></a></div>
                          <strong><div>Código: <span class="text-primary">{$sku}</div></strong>
                        </div>
                      </div>
                    </div>
                    HTML;
                })

                ->addColumn('product_image', function ($product) {
                    if ($product->image && $product->image->url) {
                        $url = $product->image->url;
                        $name = e($product->name);
                        return <<<HTML
                        <div style="overflow: visible; display: inline-block;">
                          <img
                            src="{$url}"
                            alt="Imagen de {$name}"
                            style="
                              max-width: 80px;
                              height: auto;
                              transition: transform 0.2s ease, z-index 0s 0.1ms;
                              position: relative;
                              z-index: 1;
                              cursor: pointer;
                              border: 1px solid #001f3f;
                              border-radius: 4px;
                            "
                            onmouseover="this.style.transform='scale(3)'; this.style.zIndex=9999; this.style.transition='transform 0.2ms ease';"
                            onmouseout="this.style.transform='scale(1)'; this.style.zIndex=1; this.style.transition='transform 0.2ms ease';"
                          >
                        </div>
                        HTML;
                    }
                    return 'Sin imagen';
                })


                ->addColumn('price_net_iva', function ($product) {
                    if ($product->price && $product->price->price) {
                        $priceNet = $product->price->price;
                        $formattedPriceNet = number_format($priceNet, 0, ',', '.');  // Sin decimales y con separadores de miles

                        return "
                        <div style='text-align: center;'>
                            <span class='badge bg-navy' style='font-size: 16px; font-weight: bold; padding: 8px 16px; display: inline-block; margin-bottom: 5px;'>\${$formattedPriceNet} + IVA</span><br>
                        </div>";

                    }
                    return 'Sin precio';
                })
                ->addColumn('pendiente', function ($product) {
                    return 'Pendiente';
                })
                ->addColumn('stock_hue', function ($product) {
                    if ($product->stockHue) {
                        $stockTotal = $product->stockHue->qty_physical;
                        $stockReserved = $product->stockHue->qty_reserved;
                    } else {
                        // Si no tiene stock, asignar 0
                        $stockTotal = 0;
                        $stockReserved = 0;
                    }

                    $stockTotalFormatted = number_format($stockTotal, 0, ',', '.');
                    $stockReservedFormatted = number_format($stockReserved, 0, ',', '.');
                    $stockColor = $stockTotal == 0 ? 'bg-danger' : 'bg-success'; // Rojo si el stock es 0, verde si es mayor a 0
                    $fizicoColor = $stockTotal == 0 ? 'bg-danger' : 'bg-white'; // Rojo si el físico es 0, amarillo si tiene stock

                    return "
                    <div style='text-align: center;'>
                        <!-- Recuadro de Stock Total grande y en el centro -->
                        <span class='badge {$stockColor}' style='font-size: 20px; font-weight: bold; padding: 8px 13px; display: inline-block; margin-bottom: 5px;'>{$stockTotalFormatted}</span><br>

                        <!-- Recuadro de Stock Físico (solo rojo si es 0, amarillo si hay stock) -->
                        <span class='badge {$fizicoColor}' style='font-size: 13px; font-weight: bold; margin-bottom: 2px;'>Físico: {$stockTotalFormatted}</span><br>

                        <!-- Recuadro de Stock Reservado (si está 0, será rojo, de lo contrario será amarillo) -->
                        <span class='badge bg-white' style='font-size: 13px; font-weight: bold;'>Reservado: {$stockReservedFormatted}</span>
                    </div>";
                })
                ->addColumn('stock_exe', function ($product) {
                    // Verificar si el producto tiene stock asociado para Exequiel
                    if ($product->stockExe) {
                        $stockTotal = $product->stockExe->qty_physical;
                        $stockReserved = $product->stockExe->qty_reserved;
                    } else {
                        $stockTotal = 0;
                        $stockReserved = 0;
                    }

                    // Formatear los números con separadores de miles
                    $stockTotalFormatted = number_format($stockTotal, 0, ',', '.');
                    $stockReservedFormatted = number_format($stockReserved, 0, ',', '.');

                    // Determinamos el color del recuadro dependiendo del valor de stock
                    $stockColor = $stockTotal == 0 ? 'bg-danger' : 'bg-success';
                    $fizicoColor = $stockTotal == 0 ? 'bg-danger' : 'bg-white';

                    // Formateamos el stock de manera visual con los colores básicos de los recuadros
                    return "
                    <div style='text-align: center;'>
                        <span class='badge {$stockColor}' style='font-size: 20px; font-weight: bold; padding: 8px 13px; display: inline-block; margin-bottom: 5px;'>{$stockTotalFormatted}</span><br>
                        <span class='badge {$fizicoColor} text-white' style='font-size: 13px; font-weight: bold; margin-bottom: 2px;'>Físico: {$stockTotalFormatted}</span><br>
                        <span class='badge bg-white' style='font-size: 13px; font-weight: bold;'>Reservado: {$stockReservedFormatted}</span>
                    </div>";
                })

                ->addColumn('stockmercadolibre', function ($product) {
                    // Verificar si el producto tiene stock asociado para Mercado Libre
                    if ($product->stockMercadoLibre) {
                        // Obtener las cantidades de stock físico y reservado
                        $stockTotal = $product->stockMercadoLibre->qty_physical;
                        $stockReserved = $product->stockMercadoLibre->qty_reserved;
                    } else {
                        // Si no tiene stock, asignar 0
                        $stockTotal = 0;
                        $stockReserved = 0;
                    }

                    // Formatear los números con separadores de miles
                    $stockTotalFormatted = number_format($stockTotal, 0, ',', '.');
                    $stockReservedFormatted = number_format($stockReserved, 0, ',', '.');

                    // Determinamos el color del recuadro dependiendo del valor de stock
                    $stockColor = $stockTotal == 0 ? 'bg-danger' : 'bg-success';
                    $fizicoColor = $stockTotal == 0 ? 'bg-danger' : 'bg-white';

                    // Formateamos el stock de manera visual con los colores básicos de los recuadros
                    return "
                    <div style='text-align: center;'>
                        <span class='badge {$stockColor}' style='font-size: 20px; font-weight: bold; padding: 8px 13px; display: inline-block; margin-bottom: 5px;'>{$stockTotalFormatted}</span><br>
                        <span class='badge {$fizicoColor}' style='font-size: 13px; font-weight: bold; margin-bottom: 2px;'>Físico: {$stockTotalFormatted}</span><br>
                        <span class='badge bg-white' style='font-size: 13px; font-weight: bold;'>Reservado: {$stockReservedFormatted}</span>
                    </div>";
                })

                ->addColumn('action', function ($product) {
                    $buttons = '';

                    // Ruta para el botón de editar
                    $editUrl = route('products.edit', $product->sku);
                    $buttons .= '<a href="' . $editUrl . '" class="btn btn-sm btn-primary" title="Editar" data-use-iframe="true" data-iframe-tab="Editar Producto">
                    <i class="fas fa-edit"></i>
                 </a>';

                    return $buttons;
                })

                ->orderColumn('price_net_iva', function ($q, $dir) {
                    $q->orderByRaw("catalog_products_prices.price {$dir} NULLS LAST");
                })

                ->rawColumns(['product_info', 'product_image','price_net_iva','stock_hue','stock_exe','stockmercadolibre','action'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('Error en datatable de productos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            // Retornar un error JSON para que DataTables pueda manejarlo sin romper
            return response()->json([
                'error' => 'Error interno del servidor. Por favor, revisa el log para más detalles.',
            ], 500);
        }
    }







}
