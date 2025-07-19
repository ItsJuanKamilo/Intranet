<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Catalog\ProductFilesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Server\AdempiereController;
use App\Http\Controllers\Services\LinkstoreController;
use App\Http\Controllers\Services\OdooApiClient;
use App\Jobs\Informatica\Sync\SyncInvoices;
use App\Jobs\Informatica\Sync\SyncLinkstore;
use App\Jobs\Informatica\Sync\SyncPartners;
use App\Jobs\ProcessPendingEmail;
use App\Mail\Api\TestMailApi;
use App\Mail\Marketing\TestMailMarketing;
use App\Mail\Sales\TestMailSales;
use App\Mail\TestMail;
use App\Models\Adempiere\C_Bpartner_Location;
use App\Models\Adempiere\C_Invoice;
use App\Models\Adempiere\C_Order;
use App\Models\Adempiere\M_Cost;
use App\Models\Adempiere\M_Product;
use App\Models\Adempiere\M_Product_Category;
use App\Models\Adempiere\M_Product_Classification;
use App\Models\Adempiere\M_Product_Group;
use App\Models\Adempiere\M_Rma;
use App\Models\Admin\Report;
use App\Models\Catalog\Product;
use App\Models\Catalog\ProductBrand;
use App\Models\Catalog\ProductCategory;
use App\Models\Documents\Invoice;
use App\Models\Partner;
use App\Models\PartnerAddress;
use App\Models\Server\PendingEmail;
use App\Models\Server\ScheduleJob;
use App\Models\User;
use App\Models\Web\EventAttended;
use App\Services\MailgunService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class TestMtapayController extends Controller
{
    public function index()
    {
        // Set unlimited execution time and memory limit
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        Log::info('⏳ Inicio de la ejecución del método Test MTapay.');

        $startTime = microtime(true);

        $this->testCode();

        $endTime = microtime(true);
        $executionTime = number_format($endTime - $startTime, 6);
        $memoryUsage = round(memory_get_usage() / 1024 / 1024, 2);
        $peakMemoryUsage = round(memory_get_peak_usage() / 1024 / 1024, 2);

        Log::info("✅ Ejecución terminada en {$executionTime} segundos.");
        Log::info("📌 Memoria utilizada: {$memoryUsage} MB (máximo {$peakMemoryUsage} MB)");


    }

    private function testCode()
    {

        try {

            $odoo = new OdooApiClient();
            //$odoo->deleteAllPartners();
            $odoo->uploadAdempiereProducts();



            dd("aloha");


            $new_product = [
                'name' => 'TEST_002_NAME',
                'default_code' => 'TEST_002',
                'list_price' => 1000.00,
                'standard_price' => 500.00,
                'product_brand_id' => 331, // ID de la marca
                'categ_id' => 1258, // ID de la categoría
                'allow_out_of_stock_order' => false,
                "volume" => 0.66,
                "volume_uom_name" => 'm³',
                "weight" => 66.6,
                "weight_uom_name" => 'kg',
                'description' => 'Descripción del producto de prueba',
                'type' => 'consu',
                "is_published" => true,
                "can_publish" => true,
                "invoice_policy" => 'delivery',
                "image_1920" => base64_encode(file_get_contents("https://www.artilec.com/images/catalog/products/42291.jpg")),
                "image_1024" => base64_encode(file_get_contents("https://www.artilec.com/images/catalog/products/42291.jpg")),
                "image_512" => base64_encode(file_get_contents("https://www.artilec.com/images/catalog/products/42291.jpg")),
                "image_256" => base64_encode(file_get_contents("https://www.artilec.com/images/catalog/products/42291.jpg")),
                "image_128" => base64_encode(file_get_contents("https://www.artilec.com/images/catalog/products/42291.jpg")),
            ];

            $product = $odoo->createProduct($new_product);
            $product_id = $product['id'];
            $p = $odoo->getProduct($product_id);
            dd($p);


        } catch (\Exception $e) {
           throw $e;
        }

        //$adempiere->syncBrands();
        //$adempiere->syncCategories();
        //$adempiere->syncProducts();
        //$adempiere->syncPrices();
        //$adempiere->syncStock();
        //$adempiere->syncInvoices();
        //$adempiere->syncPartners();


    }
}
