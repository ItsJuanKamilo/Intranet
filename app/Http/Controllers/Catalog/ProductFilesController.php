<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Product;
use App\Models\Catalog\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductFilesController extends Controller
{
    public function checkS3ImagesToProduct()
    {
        $files = Storage::files('catalogo/productos/imagenes');
        $files = collect($files)->map(function ($file) {
            return [
                'name' => basename($file),
                'path' => $file,
            ];
        });

        $iteration = 1;
        foreach ($files as $file) {
            $nameWithoutExt = pathinfo($file['name'], PATHINFO_FILENAME);

            if (preg_match('/^([A-Za-z0-9]+)(?:-([0-9]+))?$/', $nameWithoutExt, $matches)) {
                $sku = $matches[1];
                $imageNumber = isset($matches[2]) ? (int)$matches[2] : 0;
            } else {
                $sku = $nameWithoutExt;
                $imageNumber = 1;
            }

            $product = Product::where('sku', $sku)->first();
            if (!$product) {
                continue;
            }

            $filename = $file['name'];
            $pathOriginal = $file['path'];
            $urlOriginal = Storage::url($file['path']);

            $fileTimestamp = Storage::lastModified($file['path']);
            $fileDate = \Carbon\Carbon::createFromTimestamp($fileTimestamp);

            ProductImage::updateOrCreate(
                [
                    'filename' => $filename,
                ],
                [
                    'sku' => $sku,
                    'order' => $imageNumber,
                    'is_active' => true,
                    'path' => $pathOriginal,
                    'url' => $urlOriginal,
                    'path_large' => null,
                    'url_large' => null,
                    'path_medium' => null,
                    'url_medium' => null,
                    'path_thumb' => null,
                    'url_thumb' => null,
                    'title' => $product->name ?? $file['name'],
                    'tags' => $product->tags ?? null,
                    'alt_text' => $product->short_description ?? null,
                    'seo_title' => $product->seo_title ?? $product->name ?? null,
                    'seo_description' => $product->seo_description ?? $product->short_description ?? null,
                    'og_title' => $product->og_title ?? $product->name ?? null,
                    'og_description' => $product->og_description ?? $product->short_description ?? null,
                    'og_image_url' => $urlOriginal,
                    'twitter_title' => $product->twitter_title ?? $product->name ?? null,
                    'twitter_description' => $product->twitter_description ?? $product->short_description ?? null,
                    'twitter_image_url' => $urlOriginal,
                    'schema_image_url' => $urlOriginal,
                    'created_at' => $fileDate,
                    'updated_at' => $fileDate,
                ]
            );
            $iteration++;
        }

        echo "Total images processed: " . $iteration . "\n";
    }



}
