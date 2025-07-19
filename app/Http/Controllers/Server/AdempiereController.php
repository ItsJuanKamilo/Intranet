<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\Adempiere\C_BPartner;
use App\Models\Adempiere\C_Bpartner_Location;
use App\Models\Adempiere\C_Currency;
use App\Models\Adempiere\C_Invoice;
use App\Models\Adempiere\C_Order;
use App\Models\Adempiere\M_Cost;
use App\Models\Adempiere\M_PriceList;
use App\Models\Adempiere\M_PriceListVersion;
use App\Models\Adempiere\M_Product;
use App\Models\Adempiere\M_Product_Category;
use App\Models\Adempiere\M_Product_Classification;
use App\Models\Adempiere\M_Product_Group;
use App\Models\Adempiere\M_ProductPrice;
use App\Models\Adempiere\M_Storage;
use App\Models\Adempiere\M_Warehouse;
use App\Models\Catalog\Product;
use App\Models\Catalog\ProductBrand;
use App\Models\Catalog\ProductCategory;
use App\Models\Catalog\ProductPrice;
use App\Models\Catalog\ProductPricelist;
use App\Models\Catalog\ProductStock;
use App\Models\Catalog\ProductWarehouse;
use App\Models\Documents\Invoice;
use App\Models\Partner;
use App\Models\PartnerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class AdempiereController extends Controller
{
    public const SYNC_HOURS = 1;
    public const SYNC_DAYS = 3;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function syncBrands()
    {

        $date_sync = now()->subHours(self::SYNC_HOURS);

        $m_product_groups = M_Product_Group::where('updated', '>=', $date_sync)->get()->keyBy('m_product_group_id')->toArray();
        //$m_product_groups = M_Product_Group::get()->keyBy('m_product_group_id')->toArray();


        foreach ($m_product_groups as $mpg) {

            $id = Str::upper(Str::trim($mpg['name']));

            $brand = ProductBrand::updateOrCreate(
                ['id' => $id],
                [
                    'is_active' => $mpg['isactive'] == 'Y' ? 1 : 0,
                    'internal_id' => $mpg['m_product_group_id'],
                    'code' => Str::upper(Str::trim($mpg['value'])),
                    'slug' => Str::slug(Str::upper(Str::trim($mpg['name'])), '-'),
                    'margin_planned' => $mpg['plannedmargin'],
                    'warranty_months' => $mpg['artilec_garantia_meses'],
                    'created_at' => $mpg['created'],
                    'updated_at' => $mpg['updated'],
                ]
            );
        }

    }

    public function syncCategories()
    {

        $date_sync = now()->subHours(self::SYNC_HOURS);

        $m_product_category = M_Product_Category::where('ad_client_id', 2000006)
            ->where('updated', '>=', $date_sync)
            ->orderBy('m_product_category_id', 'asc')
            ->orderBy('m_product_category_parent_id', 'asc')
            ->get()
            ->keyBy('m_product_category_id')
            ->toArray();


        foreach ($m_product_category as $mpc) {
            $id = $mpc['m_product_category_id'];
            $category = ProductCategory::updateOrCreate(
                ['id' => $id],
                [
                    'is_active' => $mpc['isactive'] == 'Y' ? 1 : 0,
                    'internal_id' => $mpc['m_product_category_id'],
                    'name' => Str::upper(Str::trim($mpc['name'])),
                    'code' => Str::upper(Str::trim($mpc['value'])),
                    'slug' => Str::slug(Str::upper(Str::trim($mpc['name'] . ' ' . $mpc['m_product_category_id'])), '-'),
                    'margin_planned' => $mpc['plannedmargin'],
                    'parent_id' => $mpc['m_product_category_parent_id'],
                    'created_at' => $mpc['created'],
                    'updated_at' => $mpc['updated'],
                ]
            );
        }
    }

    public function syncPrices()
    {
        $date_sync = now()->subHours(self::SYNC_HOURS);

        $m_pricelist = M_PriceListVersion::where('ad_client_id', 2000006)
            ->orderBy('m_pricelist_version_id', 'asc')
            ->where('m_pricelist_version_id', 2000004)
            ->where('updated', '>=', $date_sync)
            ->get()
            ->keyBy('m_pricelist_version_id')
            ->toArray();


        foreach ($m_pricelist as $mpl) {

            $mplf = M_PriceList::where('m_pricelist_id', $mpl['m_pricelist_id'])->first();
            $currency = C_Currency::where('c_currency_id', $mplf['c_currency_id'])->first();

            $id = Str::upper(Str::trim($mpl['name']));
            $price_list = ProductPricelist::updateOrCreate(
                ['id' => $id],
                [
                    'is_active' => $mpl['isactive'] == 'Y' ? 1 : 0,
                    'description' => Str::upper(Str::trim($mpl['description'])),
                    'currency' => $currency['iso_code'] ?? null,
                    'created_at' => $mpl['created'],
                    'updated_at' => $mpl['updated'],
                ]
            );
        }

        //actualizar solo lista de precios L2
        $m_productprices = M_ProductPrice::where('ad_client_id', 2000006)
            ->where('m_pricelist_version_id', 2000004)
            ->where('updated', '>=', $date_sync)
            ->get()
            ->toArray();

        Log::info("Total Product Prices to Sync: " . count($m_productprices));

        $products = Product::all();

        foreach ($m_productprices as $m_productprice) {
            try {

                $product = $products->where('internal_id', $m_productprice['m_product_id'])->first();
                if ($product) {
                    $price = ProductPrice::where('product', $product->sku)
                        ->where('pricelist', 'LISTA 2')
                        ->first();

                    if ($price && round($m_productprice['pricelist'], 2) <> $price->price) {

                        if ($price->price == round($m_productprice['pricelist'], 2)) {
                            continue;
                        }

                        $price->update([
                            'is_active' => $m_productprice['isactive'] == 'Y' ? 1 : 0,
                            'price' => round($m_productprice['pricelist'], 2),
                            'price_limit' => round($m_productprice['pricelimit'], 2),
                            'created_at' => $m_productprice['created'],
                            'updated_at' => $m_productprice['updated'],
                        ]);
                    } else if ($product && !$price) {
                        $price = new ProductPrice();
                        $price->product = $product->sku;
                        $price->pricelist = 'LISTA 2';
                        $price->is_active = $m_productprice['isactive'] == 'Y' ? 1 : 0;
                        $price->price = round($m_productprice['pricelist'], 2);
                        $price->price_limit = round($m_productprice['pricelimit'], 2);
                        $price->created_at = $m_productprice['created'];
                        $price->updated_at = $m_productprice['updated'];
                        $price->save();
                    }
                }

            } catch (\Exception $e) {
                Log::error("Error al sincronizar el precio del producto {$m_productprice['m_product_id']}: " . $e->getMessage());
                continue;
            }
        }
    }

    public function syncStock()
    {

        $date_sync = now()->subHours(self::SYNC_HOURS);

        $adempiere_warehouses = M_Warehouse::where('ad_client_id', 2000006)
            ->where('isactive', 'Y')
            ->where('updated', '>=', $date_sync)
            ->get();
        foreach ($adempiere_warehouses as $warehouse) {
            $warehouse_id = Str::upper(Str::trim($warehouse->value));
            $product_warehouse = ProductWarehouse::updateOrCreate(
                ['id' => $warehouse_id],
                [
                    'is_active' => $warehouse->isactive == 'Y' ? 1 : 0,
                    'internal_id' => $warehouse->m_warehouse_id,
                    'created_at' => $warehouse->created,
                    'updated_at' => $warehouse->updated,
                ]
            );
        }

        //actualizar stock de 24 horas
        $date = now()->subHours(self::SYNC_HOURS);

        $adempiere_stocks = M_Storage::join('m_locator', 'm_storage.m_locator_id', '=', 'm_locator.m_locator_id')
            ->join('m_product', 'm_storage.m_product_id', '=', 'm_product.m_product_id')
            ->join('m_warehouse', 'm_locator.m_warehouse_id', '=', 'm_warehouse.m_warehouse_id')
            ->select(
                'm_product.value',
                'm_warehouse.value as warehouse',
                DB::raw('SUM(m_storage.qtyonhand) as total_qtyonhand'),
                DB::raw('MAX(m_storage.updated) as updated')
            )
            ->groupBy('m_product.value', 'm_warehouse.value')
            ->havingRaw('SUM(m_storage.qtyonhand) > 0')
            ->havingRaw('MAX(m_storage.updated) >= ?', [$date])
            ->get();

        Log::info('Total Stocks to Sync: ' . count($adempiere_stocks));

        foreach ($adempiere_stocks as $stock) {

            try {
                $catalog_stock = ProductStock::updateOrCreate(
                    ['product' => $stock->value, 'warehouse' => Str::upper(Str::trim($stock->warehouse))],
                    [
                        'qty_physical' => round($stock->total_qtyonhand, 0),
                        'created_at' => now(),
                        'updated_at' => $stock->updated,
                    ]
                );
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    public function syncProducts()
    {

        //actualizar stock de 24 horas
        $date = now()->subHours(self::SYNC_HOURS);

        $adempiere_products = M_Product::where('ad_client_id', 2000006)
            ->where('updated', '>=', $date)
            ->get();
        $m_product_classifications = M_Product_Classification::all()->keyBy('m_product_classification_id')->toArray();
        $costs = M_Cost::all()->keyBy('m_product_id')->toArray();

        if (count($adempiere_products) == 0) {
            Log::info('No hay productos para sincronizar.');
            return;
        } else {
            Log::info('Total Products to Sync: ' . count($adempiere_products));
        }


        foreach ($adempiere_products as $ademproduct) {

            $certifications = collect([
                $ademproduct->ul == 'Y' ? 'UL' : null,
                $ademproduct->ce == 'Y' ? 'CE' : null,
                $ademproduct->etl == 'Y' ? 'ETL' : null,
                $ademproduct->rohs == 'Y' ? 'ROHS' : null,
            ])->filter();

            $certifications = $certifications->isEmpty() ? null : $certifications->implode(', ');
            $m_product_classification = $m_product_classifications[$ademproduct->m_product_classification_id]['value'] ?? null;


            $brand = ProductBrand::where('internal_id', $ademproduct->m_product_group_id)->first();
            $category = ProductCategory::where('internal_id', $ademproduct->m_product_category_id)->first();
            $subcategory = ProductCategory::where('internal_id', $ademproduct->m_product_sub_category_id)->first();
            $subsubcategory = ProductCategory::where('internal_id', $ademproduct->m_product_subsub_category_id)->first();


            $id = Str::upper(Str::trim($ademproduct->value));

            $product = Product::updateOrCreate(
                ['sku' => $id],
                [
                    'internal_id' => $ademproduct->m_product_id,
                    'is_active' => $ademproduct->isactive === 'Y' ? 1 : 0,
                    'name' => Str::upper(Str::trim($ademproduct->name)),
                    'model' => Str::upper(Str::trim($ademproduct->upc)),
                    'type' => $ademproduct->producttype === 'I' ? 'articulo' : 'servicio',
                    'margin_planned' => $ademproduct->plannedmargin,
                    'obsolescence' => $ademproduct->lowlevel,
                    'index' => $m_product_classification,
                    'cost' => isset($costs[$ademproduct->m_product_id]) ? ROUND($costs[$ademproduct->m_product_id]['currentcostprice'],0) : 0,

                    'category' => $category ? Str::upper(Str::trim($category->name)) : null,
                    'subcategory' => $subcategory ? Str::upper(Str::trim($subcategory->name)) : null,
                    'subsubcategory' => $subsubcategory ? Str::upper(Str::trim($subsubcategory->name)) : null,
                    'brand' => $brand->id ?? null,

                    'width' => $ademproduct->shelfwidth,
                    'height' => $ademproduct->shelfheight,
                    'depth' => $ademproduct->shelfdepth,
                    'weight' => $ademproduct->weight,
                    'volume' => $ademproduct->volume,

                    'certifications' => $certifications,

                    'is_sale' => $ademproduct->issold === 'Y' ? 1 : 0,
                    'is_purchased' => $ademproduct->ispurchased === 'Y' ? 1 : 0,
                    'is_web' => 1,
                    'is_kit' => Str::startsWith($ademproduct->value, '.') ? 1 : 0,
                    'is_discontinued' => $ademproduct->discontinued === 'Y' ? 1 : 0,

                    'created_at' => $ademproduct->created,
                    'updated_at' => $ademproduct->updated,
                ]
            );


            // Add token if it does not exist
            if (empty($product->token)) {
                $product->token = Str::random(60);
                $product->save();
            }
        }
    }

    public function syncPartners()
    {

        $date = now()->subDays(self::SYNC_DAYS);

        $partners = C_BPartner::where('ad_client_id', 2000006)
            ->where('updated', '>=', $date)
            ->get();

        if(count($partners) == 0){
            Log::info('No hay partners para sincronizar.');
            return;
        }else{
            Log::info('Total Partners to Sync: ' . count($partners));
        }

        foreach ($partners as $partner_adempiere) {

            $partner = Partner::where('id', $partner_adempiere->value)->firstOrNew();
            //$partner = Partner::where('id', $partner_adempiere->value)->first();
            if ($partner) {
                $partner->syncWithAdempierePartner($partner_adempiere);

                $c_bpartner_location = null;
                $invoice = C_Invoice::where('c_bpartner_id', $partner->internal_id)
                    ->where('docstatus', 'CO')
                    ->orderBy('updated', 'desc')
                    ->first();
                if ($invoice) {
                    $c_bpartner_location = C_Bpartner_Location::find($invoice->c_bpartner_location_id);
                } else {
                    $order = C_Order::where('c_bpartner_id', $partner->internal_id)
                        ->where('docstatus', 'CO')
                        ->orderBy('updated', 'desc')
                        ->first();
                    if ($order) {
                        $c_bpartner_location = C_Bpartner_Location::find($order->c_bpartner_location_id);
                    } else {
                        $c_bpartner_location = C_Bpartner_Location::where('c_bpartner_id', $partner->internal_id)
                            ->where('isbillto', 'Y')
                            ->orderBy('updated', 'desc')
                            ->first();
                    }
                }

                if ($c_bpartner_location && $c_bpartner_location->c_city_id && $c_bpartner_location->c_location_id) {
                    PartnerAddress::where('partner_id', $partner->id)->where('is_billing', true)->delete();
                    $address = PartnerAddress::where('id', $c_bpartner_location->c_bpartner_location_id)->firstOrNew();
                    $address->syncWithAdempiereLocation($c_bpartner_location);
                }
            }
        }
    }


    public function syncInvoices()
    {

        $date = now()->subDays(self::SYNC_DAYS);

        $invoices = C_Invoice::where('ad_client_id', 2000006)
            //->whereBetween('updated', ['2025-05-01', '2025-05-31'])
            //->where('c_invoice_id', 2235366)
            ->where('updated', '>=', $date)
            ->where('totallines', '>', 0)
            ->orderBy('c_invoice_id', 'asc')
            ->get();

        //dd($invoices);

        Log::info('Total Invoices to Sync: ' . count($invoices));

        foreach ($invoices as $invoice) {
            // if (!Invoice::where('id', $invoice->c_invoice_id)->exists())
            $invoice->syncToIntranet();
        }

        Log::info('Total Invoices Sync: ' . count($invoices));




    }

}
