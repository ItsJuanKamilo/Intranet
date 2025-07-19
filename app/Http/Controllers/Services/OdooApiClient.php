<?php


namespace App\Http\Controllers\Services;

use App\Models\Catalog\Product;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OdooApiClient
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = env('ODOO_URL', 'https://miodoo.com'); // Cambia esto a tu URL de Odoo
        $this->username = env('ODOO_USER');
        $this->password = env('ODOO_PASSWORD');
    }

    private function getAccessToken()
    {
        if (Cache::has('odoo_token')) {
            $this->token = Cache::get('odoo_token')['access_token'];
        }

        $this->login();
    }

    protected function login()
    {
        $response = Http::withHeaders([
            'Content-Type' => 'text/html',
        ])->get("{$this->baseUrl}/api/auth/get_tokens", [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Login en Odoo falló: ' . $response->body());
        }

        $token = $response->json();
        $this->token = $token['access_token'];
        Cache::put('odoo_token', $token, now()->addSeconds($token['expires_in']));
    }

    protected function refreshToken(): string
    {
        $refreshToken = Cache::get('access_token')['refresh_token'] ?? null;

        if (!$refreshToken) {
            return $this->login();
        }

        $response = Http::withHeaders([
            'Content-Type' => 'text/html',
        ])->post("{$this->baseUrl}/api/auth/refresh_token", [
            'refresh_token' => $refreshToken,
        ]);

        if (!$response->successful()) {
            // Si falla, intenta login completo
            return $this->login();
        }

        $token = $response->json();
        Cache::put('odoo_token', $token, now()->addSeconds($token['expires_in']));

        return $token['access_token'];
    }

    public function request(string $method, string $endpoint, array $data = [])
    {
        $this->getAccessToken();
        $url = "{$this->baseUrl}/api/" . ltrim($endpoint, '/');
        //echo "Token: {$this->token} Metodo: {$method} URL: {$url}\n";

        $response = Http::withHeaders([
            'Access-Token' => $this->token,
        ])->{$method}($url, $data);

        // echo "Respuesta: ". $response . "<br>";

        if ($response->status() === 403) {
            // Intenta refrescar el token y repetir la petición
            $token = $this->refreshToken();
            $response = Http::withToken($token)->{$method}($url, $data);
        }

        if (!$response->successful()) {
            echo "Error en la petición a Odoo: " . $response->body() . "\n";
        }

        return $response->json();
    }


    public function deleteAllPartners()
    {

        $result = $this->request('get', 'res.partner');

        $order = 0;
        if ($result['count'] > 0) {

            echo "Eliminando " . $result['count'] . " partners...\n";
            $partners = $result['results'];
            $ids = [];

            foreach ($partners as $partner) {
                $ids[] = $partner['id'];
            }

            foreach ($ids as $id) {
                $order++;
                try {
                    $res = $this->request('delete', 'res.partner/' . $id);
                    echo "{$order}: Partner ID {$id} eliminado correctamente.\n";
                    $ids_not_borrar[] = $id;
                } catch (\Exception $e) {
                    $ids_not_borrar[] = $id;
                    echo "Error: {$id}: " . $e->getMessage() . "\n";
                    continue;
                }
            }
            Cache::forever('odoo_id_errors', $ids_not_borrar);
        }

    }

    public function deleteAllProducts()
    {

        $result = $this->request('get', 'product.template', [
            'limit' => 10000 // Puedes ajustar el límite según tus necesidades
        ]);

        $order = 0;
        if ($result['count'] > 0) {

            echo "Eliminando " . $result['count'] . " products..\n";
            $items = $result['results'];
            $ids = [];

            foreach ($items as $item) {
                $ids[] = $item['id'];
            }

            $ids_not_borrar = Cache::get('odoo_id_products_errors') ?? [];
            echo count($ids_not_borrar) . " IDs no borrados previamente:\n";

            //quiero quitar de $ids todos los ids que esten en $ids_not_borrar
            if (is_array($ids_not_borrar)) {
                $ids = array_diff($ids, $ids_not_borrar);
            } else {
                $ids_not_borrar = [];
            }
            echo count($ids) . " IDs a borrar: <br>";

            foreach ($ids as $id) {
                $order++;
                try {
                    $res = $this->request('delete', 'product.template/' . $id);
                    $ids_not_borrar[] = $id;
                    echo "{$order}: Partner ID {$id} eliminado correctamente. Orden: <br>";
                } catch (\Exception $e) {
                    $ids_not_borrar[] = $id;
                    echo "{$order}: " . $e->getMessage() . "<br>";
                    continue;
                }
            }
            Cache::forever('odoo_id_products_errors', $ids_not_borrar);
        }
    }

    public function getPartnerById($id)
    {
        try {
            $response = $this->request('get', 'res.partner/' . $id);
            return $response;
        } catch (\Exception $e) {
            Log::error("Error al obtener el partner con ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    public function getPartnerByVat($vat)
    {
        try {
            $response = $this->request('get', 'res.partner', [
                'filters' => json_encode([['vat', '=', $vat]]),
                'limit' => 1
            ]);
            if ($response['count'] === 0) {
                Log::warning("No se encontró el partner con VAT {$vat}");
                return null;
            }
            return $this->getPartnerById($response['results'][0]['id']);


        } catch (\Exception $e) {
            Log::error("Error al obtener el partner con VAT {$vat}: " . $e->getMessage());
            return null;
        }
    }

    public function getProduct($id)
    {
        try {
            $response = $this->request('get', 'product.template/' . $id);
            return $response;
        } catch (\Exception $e) {
            Log::error("Error al obtener el producto con ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    public function getProductBySKU($sku)
    {
        try {
            $response = $this->request('get', 'product.template', [
                'filters' => json_encode([['default_code', '=', $sku]]),
                'limit' => 1
            ]);
            if ($response['count'] === 0) {
                Log::warning("No se encontró el producto con SKU {$sku}");
                return null;
            }
            return $this->getProduct($response['results'][0]['id']);
        } catch (\Exception $e) {
            Log::error("Error al obtener el producto con SKU {$sku}: " . $e->getMessage());
            return null;
        }
    }

    public function createProduct(array $data)
    {
        //EXAMPLE PRODUCT DATA
        /*
         * array:175 [▼ // app/Http/Controllers/Test/TestMtapayController.php:76
          "website_id" => null
          "website_published" => null
          "is_published" => null
          "can_publish" => true
          "website_url" => "/shop/codigo-test-27950"
          "is_seo_optimized" => null
          "website_meta_title" => null
          "website_meta_description" => null
          "website_meta_keywords" => null
          "website_meta_og_img" => null
          "seo_name" => null
          "image_1920" => "UklGRhb8AABXRUJQVlA4WAoAAAAgAAAANwQANwQASUNDUMgBAAAAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAA ▶"
          "image_1024" => "UklGRqrIAABXRUJQVlA4WAoAAAAgAAAA/wMA/wMASUNDUMgBAAAAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAA ▶"
          "image_512" => "UklGRjpdAABXRUJQVlA4WAoAAAAgAAAA/wEA/wEASUNDUMgBAAAAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAA ▶"
          "image_256" => "UklGRqIlAABXRUJQVlA4WAoAAAAgAAAA/wAA/wAASUNDUMgBAAAAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAA ▶"
          "image_128" => "UklGRuAOAABXRUJQVlA4WAoAAAAgAAAAfwAAfwAASUNDUMgBAAAAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAA ▶"
          "activity_ids" => null
          "activity_state" => null
          "activity_user_id" => null
          "activity_type_id" => null
          "activity_type_icon" => null
          "activity_date_deadline" => null
          "my_activity_date_deadline" => null
          "activity_summary" => null
          "activity_exception_decoration" => null
          "activity_exception_icon" => null
          "activity_calendar_event_id" => null
          "message_is_follower" => null
          "message_follower_ids" => array:1 [▶]
          "message_partner_ids" => array:1 [▶]
          "message_ids" => array:1 [▶]
          "has_message" => true
          "message_needaction" => null
          "message_needaction_counter" => 0
          "message_has_error" => null
          "message_has_error_counter" => 0
          "message_attachment_count" => 0
          "rating_ids" => null
          "website_message_ids" => null
          "message_has_sms_error" => null
          "name" => "TEST"
          "sequence" => 1
          "description" => "<div data-oe-version="1.2">NOTA</div>"
          "description_purchase" => null
          "description_sale" => null
          "type" => "consu"
          "combo_ids" => null
          "service_tracking" => "no"
          "categ_id" => array:2 [▶]
          "currency_id" => array:2 [▶]
          "cost_currency_id" => array:2 [▶]
          "list_price" => 99.0
          "standard_price" => 98.0
          "volume" => 0.66
          "volume_uom_name" => "m³"
          "weight" => 66.6
          "weight_uom_name" => "kg"
          "sale_ok" => true
          "purchase_ok" => true
          "uom_id" => array:2 [▶]
          "uom_name" => "Unidades"
          "uom_category_id" => array:2 [▶]
          "uom_po_id" => array:2 [▶]
          "company_id" => null
          "packaging_ids" => null
          "seller_ids" => null
          "variant_seller_ids" => null
          "active" => true
          "color" => 0
          "is_product_variant" => null
          "attribute_line_ids" => null
          "valid_product_template_attribute_line_ids" => null
          "product_variant_ids" => array:1 [▶]
          "product_variant_id" => array:2 [▶]
          "product_variant_count" => 1
          "barcode" => null
          "default_code" => "CODIGO"
          "pricelist_item_count" => 0
          "product_document_ids" => null
          "product_document_count" => 0
          "can_image_1024_be_zoomed" => true
          "has_configurable_attributes" => null
          "product_tooltip" => "Factura después de la entrega, según las cantidades entregadas, no en las órdenes."
          "is_favorite" => null
          "product_tag_ids" => null
          "product_properties" => null
          "id" => 27950
          "display_name" => "[CODIGO] TEST"
          "create_uid" => array:2 [▶]
          "create_date" => "2025-06-27 16:35:43"
          "write_uid" => array:2 [▶]
          "write_date" => "2025-06-27 17:03:57"
          "taxes_id" => array:1 [▶]
          "tax_string" => "(= $\u{A0}118 impuestos incluidos)"
          "supplier_taxes_id" => array:1 [▶]
          "property_account_income_id" => null
          "property_account_expense_id" => null
          "account_tag_ids" => null
          "fiscal_country_codes" => "CL"
          "is_storable" => true
          "responsible_id" => array:2 [▶]
          "property_stock_production" => array:2 [▶]
          "property_stock_inventory" => array:2 [▶]
          "sale_delay" => 0
          "tracking" => "none"
          "description_picking" => null
          "description_pickingout" => null
          "description_pickingin" => null
          "qty_available" => 0.0
          "virtual_available" => 0.0
          "incoming_qty" => 0.0
          "outgoing_qty" => 0.0
          "location_id" => null
          "warehouse_id" => null
          "has_available_route_ids" => true
          "route_ids" => array:1 [▶]
          "nbr_moves_in" => 0
          "nbr_moves_out" => 0
          "nbr_reordering_rules" => 0
          "reordering_min_qty" => 0.0
          "reordering_max_qty" => 0.0
          "route_from_categ_ids" => null
          "show_on_hand_qty_status_button" => true
          "show_forecasted_qty_status_button" => true
          "purchased_product_qty" => 0.0
          "purchase_method" => "receive"
          "purchase_line_warn" => "no-message"
          "purchase_line_warn_msg" => null
          "cost_method" => "standard"
          "valuation" => "manual_periodic"
          "lot_valuated" => null
          "allow_negative_stock" => null
          "property_account_creditor_price_difference" => null
          "service_type" => "manual"
          "sale_line_warn" => "no-message"
          "sale_line_warn_msg" => null
          "expense_policy" => "no"
          "visible_expense_policy" => true
          "sales_count" => 0.0
          "invoice_policy" => "delivery"
          "optional_product_ids" => null
          "product_brand_id" => array:2 [▶]
          "service_to_purchase" => null
          "location_ids" => null
          "qty_available_total" => 0.0
          "virtual_available_total" => 0.0
          "hs_code" => null
          "country_of_origin" => null
          "rating_last_value" => 0.0
          "rating_last_feedback" => null
          "rating_last_image" => null
          "rating_count" => 0
          "rating_avg" => 0.0
          "rating_avg_text" => "none"
          "rating_percentage_satisfaction" => -1.0
          "rating_last_text" => null
          "website_description" => null
          "description_ecommerce" => null
          "alternative_product_ids" => null
          "accessory_product_ids" => null
          "website_size_x" => 1
          "website_size_y" => 1
          "website_ribbon_id" => null
          "website_sequence" => 44900
          "public_categ_ids" => null
          "product_template_image_ids" => null
          "base_unit_count" => 0.0
          "base_unit_id" => null
          "base_unit_price" => 0.0
          "base_unit_name" => "Unidades"
          "compare_list_price" => 0.0
          "allow_out_of_stock_order" => true
          "available_threshold" => 5.0
          "show_availability" => null
          "out_of_stock_message" => null
        ]
         */

        try {
            $response = $this->request('post', 'product.template', $data);
            return $response;
        } catch (\Exception $e) {
            Log::error("Error al crear el producto: " . $e->getMessage());
            return null;
        }
    }

    public function createPartner(array $data)
    {

        try {
            $response = $this->request('post', 'res.partner', $data);
            return $response;
        } catch (\Exception $e) {
            Log::error("Error al crear el producto: " . $e->getMessage());
            return null;
        }
    }

    public function uploadAdempiereProducts(){


        $products = Product::where(function ($query) {
            $query->where('is_active', true)
                ->where('odoo_id', null)
                ->where('is_purchased', 'Y');
        })
            ->orWhere(function ($query) {
                $query->whereNotNull('sales_2years')
                    ->where('odoo_id', null)
                    ->where('sales_2years', '>', 0);
            })
            ->get();
        echo "Productos a sincronizar: " . $products->count() . "\n";

        $no_image = base64_encode((file_get_contents('https://artilec-chile.s3.dualstack.sa-east-1.amazonaws.com/catalogo/productos/no-img.jpg')));
        $new_products= [];
        $index = 0;
        foreach ($products as $product) {
            $index++;
            echo "Procesando producto {$index} de {$products->count()} - ID: {$product->sku}\n";

            $sku = $product->sku;
            // Verificar si el producto ya existe en Odoo
            $price = $product->price ? $product->price->price : ROUND($product->cost/0.65,0);
            $brand_id = $product->brand ? $product->brand->odoo_id : 331;

            $image_to_add = $product->image ? $product->image->image_base64 : $no_image;
            $category_id = $product->odoo_category_id;
            if(!$category_id) {
                $category_id = 1258; //otro - otro
                $product->odoo_category_id = $category_id;
                $product->odoo_category= 'OTRO-OTRO';
                $product->save();
            }

            // Crear el producto en Odoo
            $new_products[] = [
                'name' => $product->name,
                'default_code' => $sku,
                'list_price' => (int)$price,
                'standard_price' =>  (int)$product->cost,
                'type' => 'consu',
                'product_brand_id' => $brand_id, // ID de la marca
                'categ_id' => $category_id, // ID de la categoría
                'allow_out_of_stock_order' => false,
                "volume" =>ROUND($product->volume, 2),
                "volume_uom_name" => 'm³',
                "weight" => ROUND($product->weight, 2),
                "weight_uom_name" => 'kg',
                'description' => "Modelo:{$product->model};Indice:{$product->index}",
                "is_published" => true,
                "can_publish" => true,
                "invoice_policy" => 'delivery',
                "image_1920" => $image_to_add,
                "image_1024" => $image_to_add,
                "image_512" => $image_to_add,
                "image_256" => $image_to_add,
                "image_128" => $image_to_add,
            ];
        }

        $index = 0;
        foreach ($new_products as $new_product) {
            $index++;
            echo "Subiendo producto {$index} de " . count($new_products) . " - SKU: {$new_product['default_code']}\n";

            $created_product = $this->createProduct($new_product);
            if(!isset($created_product['id'])) {
                echo "Error al crear el producto {$new_product['default_code']}\n";
                continue;
            }else{
                $product_id = $created_product['id'];
                $product = Product::find($new_product['default_code']);
                $product->odoo_id = $product_id;
                $product->save();
                echo "Producto {$product->id} creado en Odoo con ID: {$created_product['id']}\n";
            }

        }
    }

    public function uploadAdempierePartners()
    {
        $partner = $this->getPartnerByVat('24633947-3');

        //dd($partner);


        $new_partner = [
            'company_type' => 'company', // Tipo de empresa company_type: 'person' o 'company',
            'name' => 'Test Partner 22',
            'vat' => '24633947-3',
            'email' => 'mtapay@artilec.com',
            "property_product_pricelist" => 1 ,  // lista de precios LISTA L2
            "currency_id" => 52 , // CLP
            "property_account_payable_id" => 132, // cuenta contable "Proveedores Nacionales"
            "property_account_receivable_id" => 55, // cuenta contable "Clientes Nacionales"
            "property_supplier_payment_term_id" => 1 , // condicion pago proveedor 1 = Contado
            "property_outbound_payment_method_line_id" => 15,  // metodo de pago en compras
            "property_inbound_payment_method_line_id" => 12, // metodo de pago en venta
            "property_stock_customer" => 5, // customers
            "property_stock_supplier" => 4, //vendos
            "property_purchase_currency_id" => 45 , // CLP
            "buyer_id" => 312 , // comprador comex@artilec.com
            "user_id" => 311, // vendedor , en este caso comercial@artilec.com
            "create_date" => "2024-01-01 00:00:00", // Fecha de creación
            'credit_limit' => 99999, // Limite de credito
            "l10n_cl_activity_description" => "Giro del socio", // Descripción de la actividad económica
            "l10n_cl_sii_taxpayer_type" => "3", // Tipo de contribuyente SII 3 = Persona Natural 1 = Afecto a IVa
            "l10n_cl_dte_email" => "ejemplo@dte.com", // Email para DTE
            "l10n_latam_identification_type_id" => 3, // 4 = RUT , 3 = ID Extranjero
            "street" => "Santa Marta de Huechuraba 6570",
            "street2" => null,
            "zip" => null,
            "city" => "Huechuraba",
            "state_id" => 1187 , // REGION METROPOLITANA
            "country_id" => 46 , // ID CHILE
            "kpb_comuna_id" => 52, // 52 = NUÑOA es la comuna de KPB
        ];

        $response = $this->request('put', 'res.partner/' . $partner['id'], $new_partner);
        dd( $response);


        dd($this->createPartner($new_partner));

        $partners = Partner::where('odoo_id', null)->get();
        echo "Partners a sincronizar: " . $partners->count() . "\n";

        $index = 0;
        foreach ($partners as $partner) {
            $index++;
            echo "Procesando partner {$index} de {$partners->count()} - Email: {$partner->email}\n";

            $odoo_partner = $this->getParterByVat($partner->vat);
            if (!$odoo_partner) {
                echo "Partner no encontrado: {$partner->vat}\n";
                continue;
            }

            $partner->odoo_id = $odoo_partner['id'] ?? null;
            $partner->save();
            echo "Partner {$partner->email} sincronizado con Odoo ID: {$partner->odoo_id}\n";
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $response = $this->request('get', 'res.users', [
                'filters' => json_encode([['login', '=', $email]]),
                'limit' => 1
            ]);
            if ($response['count'] === 0) {
                echo "No se encontraron usuarios en la base de datos\n";
                return null;
            }
            $user_found = $response['results'][0] ?? null;
            if ($user_found) {
                return $this->getUserById($user_found['id']);
            }
        } catch (\Exception $e) {
            echo "Error al obtener el usuario: " . $e->getMessage() . "\n";
            return null;
        }
    }

    public function getUserById($id)
    {
        try {
            $response = $this->request('get', 'res.users/' . $id);
            return $response;
        } catch (\Exception $e) {
            echo "Error al obtener el usuario con ID {$id}: " . $e->getMessage() . "\n";
            return null;
        }
    }

    public function syncUsers(){

        $users = User::all();

        echo "Usuarios a sincronizar: " . $users->count() . "\n";

        $index = 0;
        foreach ($users as $user) {
            $index++;
            echo "Procesando usuario {$index} de {$users->count()} - Email: {$user->email}\n";
            $odoo_user  = $this->getUserByEmail($user->email);
            if (!$odoo_user) {
                echo "Usuario no encontrado: {$user->email}\n";
                continue;
            }

            $user->odoo_id = $odoo_user['id'] ?? null;
            $user->save();
            echo "Usuario {$user->email} sincronizado con Odoo ID: {$user->odoo_id}\n";
        }
    }

}
