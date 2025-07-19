<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\Adempiere\C_Order;
use App\Models\Adempiere\M_Inout;
use App\Models\API\ApiLog;
use App\Models\Catalog\Product;
use App\Models\Documents\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


/**
 * @OA\Info(
 *     title="Artilec presenta APILEC Documentación", version="1.0",
 *     description="Documentación de los endpoints de Artilec",
 *     version="1.0.0",
 *     x={
 *      "logo": {
 *        "url": "https://artilec-chile.s3.sa-east-1.amazonaws.com/logos/api/logo_color.png"
 *      }
 *    },
 *     @OA\Contact(
 *      email="informatica@artilec.com"
 *     ),
 * )
 * @OA\Server(
 *     url="https://api.artilec.com/"
 * )
 *
 * @OA\SecurityScheme(
 *      type="http",
 *      securityScheme="bearerAuth",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 *  )
 */
class APIController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Obtener el usuario autenticado
            $user = auth()->user();

            if ($user) {
                $user->last_connection_at = now();
                $user->ip_address = $request->ip();
                $user->last_request_uri = $request->getRequestUri();
                $user->user_agent = $request->userAgent();
                $user->save();

                $startTime = microtime(true); // Tiempo de inicio
                // Crear un nuevo registro de solicitud
                $log = new ApiLog();
                $log->user_id = $user->id;
                $log->endpoint = $request->getPathInfo();
                $log->method = $request->getMethod();
                $log->request_data = json_encode($request->all());
                $log->request_start_date = now(); // Registrar el tiempo de inicio
                $log->api_token = $user->api_token;

                // Guardar el registro de solicitud
                $log->save();

                if ($user->token_end_date && $user->token_end_date < now()) {
                    return response()->json(['error' => 'Token expirado. Contacte al administrador.'], 401);
                }
            }

            // Ejecutar el siguiente middleware o controlador
            $response = $next($request);

            if (isset($log)) {
                // Actualizar la fecha de finalización de la solicitud
                $log->request_end_date = now(); // Registrar el tiempo de finalización
                $endTime = microtime(true); // Tiempo de finalización
                $executionTime = $endTime - $startTime; // Diferencia en segundos

                $log->response_time = round($executionTime,2);
                $log->save();
            }

            return $response;
        });
    }

    /**
     * @OA\Examples(
     *        summary="User",
     *        example = "User1",
     *       value = {
     *           "name": "user 1"
     *         },
     *      )
     */
    /**
     * @OA\Get(
     *     path="/api/clientes/v1/usuario",
     *     tags={"Autentificación"},
     *     summary="Muestra la información del usuario",
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar info del usuario",
     *         content={
     *              @OA\MediaType(
     *                  mediaType="application/json",
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="errcode",
     *                          type="integer",
     *                          description="The response code"
     *                      ),
     *                      @OA\Property(
     *                          property="errmsg",
     *                          type="string",
     *                          description="The response message"
     *                      ),
     *                      @OA\Property(
     *                          property="data",
     *                          type="array",
     *                          description="The response data",
     *                          @OA\Items
     *                      ),
     *                      example={
     *                          "id": 99,
     *                          "rut": "12456874-0",
     *                          "name": "Pepe",
     *                          "email": "test@artilec.cl",
     *                          "token": "*******************************",
     *                          "token_end_date": "2099-05-02 04:20:01"
     *                      }
     *                  )
     *              )
     *          }
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     ),
     *     @OA\Response(
     *            response=401,
     *            description="No autentificado",
     *        ),
     *     security={{"bearerAuth":{}}}
     * )
     * @return \Illuminate\Http\JsonResponse;
     */
    public function getUser(Request $request)
    {
        $user = $request->user(); // Obtén el usuario autenticado

        // Verifica si el usuario existe y devuelve su información
        if ($user) {
            return response()->json([
                'id' => $user->id,
                'rut' => $user->rut,
                'name' => $user->name,
                'email' => $user->email,
                'token' => $user->api_token,
                'token_end_date' => $user->token_end_date
                // Agrega aquí los demás campos que desees devolver
            ]);
        } else {
            return response()->json([
                'message' => 'Usuario no encontrado.'
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/clientes/v1/productos",
     *     tags={"Productos"},
     *     summary="Muestra la información sobre los productos de Artilec",
     *     @OA\Parameter(
     *           name="sku",
     *           description="Solicitar un producto por SKU",
     *           required=false,
     *           in="path",
     *           @OA\Schema(
     *               type="string"
     *           )
     *       ),
     *     @OA\Parameter(
     *            name="marca",
     *            description="Solicitar productos por marcas",
     *            required=false,
     *            in="path",
     *            @OA\Schema(
     *                type="string"
     *            )
     *        ),
     *     @OA\Parameter(
     *             name="modelo",
     *             description="Solicitar productos por modelo",
     *             required=false,
     *             in="path",
     *             @OA\Schema(
     *                 type="string"
     *             )
     *         ),
     *     @OA\Parameter(
     *             name="categoria",
     *             description="Solicitar productos por categoria",
     *             required=false,
     *             in="path",
     *             @OA\Schema(
     *                 type="string"
     *             )
     *         ),
     *     @OA\Parameter(
     *              name="subcategoria",
     *              description="Solicitar productos por subcategoria",
     *              required=false,
     *              in="path",
     *              @OA\Schema(
     *                  type="string"
     *              )
     *          ),
     *     @OA\Response(
     *          response=200,
     *          description="Mostrar info del usuario",
     *          content={
     *               @OA\MediaType(
     *                   mediaType="application/json",
     *                   @OA\Schema(
     *                       @OA\Property(
     *                           property="errcode",
     *                           type="integer",
     *                           description="The response code"
     *                       ),
     *                       @OA\Property(
     *                           property="errmsg",
     *                           type="string",
     *                           description="The response message"
     *                       ),
     *                       @OA\Property(
     *                           property="data",
     *                           type="array",
     *                           description="The response data",
     *                           @OA\Items
     *                       ),
     *                       example={
     * "codigo": 2022741,
     * "sku": "24136",
     * "modelo": "Hood (MPO)",
     * "marca": "AJAX",
                                 * "categoria": "ALARMAS",
                                 * "subcategoria": "PANELES DE ALARMA",
                                 * "subsubcategoria": "ACCESORIOS",
                                 * "stock": 38,
                                 * "precio": 7380,
                                 * "imagenes": {},
                                 * "fichatecnica": {} ,
                                 * "seo": {
     *      "link": "",
                                 * "titulo": "",
                                 * "meta": "",
                                 * "keywords": "",
                                 * "descripcion": "<html><body><p>La cubierta MOTION PROTECT OUTDOOR, protege los sensores del sistema antienmascaramniento de la lluvia y la nieve.</p><p><br></p></body></html>"
                                 * }
                                 * },
     *                   )
     *               )
     *           }
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     ),
     *     @OA\Response(
     *           response=401,
     *           description="No autentificado",
     *       ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getProducts(Request $request)
    {
        try {
            $response = Product::get()->take(10);

            return response()->json($response);
        } catch (\Exception $e) {
            $response = ["error" => "No hay productos"];
            return response()->json($response);
        }
    }

    public function getOrder(Request $request){


        try {
            $user = $request->user();// Obtén el usuario autenticado
            $documentNo = $request->order;

            $order = C_Order::searchOrder($documentNo);

            if(!$user->is_admin){
                if($order->c_bpartner_id->rut <> $user->rut){
                    $response = ["error" => "No tienes permisos para ver esta orden"];
                    return response()->json($response,500);
                }
            }

            return response()->json($order);
        } catch (\Exception $e) {
            $response = ["error" => "Error al consultar. ".$e->getMessage()];
            return response()->json($response);
        }
    }

    public function requestConfirmPickingOrder(Request $request, $id)
    {

        try {
            $user = $request->user();// Obtén el usuario autenticado
            $order = C_Order::where('documentno', $id)->first();

            if ($user->is_admin && $order) {
                $order = $order->sendEmailConfirmPicking();
                return response()->json($order);
            } else {
                $response = ["error" => "No tienes permiso"];
                return response()->json($response, 500);
            }

        } catch (\Exception $e) {
            $response = ["error" => "Error al consultar. " . $e->getMessage()];
            return response()->json($response);
        }
    }

    public function requestCancelPickingOrder(Request $request, $id)
    {
        try {
            $user = $request->user();// Obtén el usuario autenticado
            $order = C_Order::where('documentno', $id)->first();
            $cancellation_reason = $request->cancellation_reason;
            $additional_comments = $request->additional_comments;
            $order->exemptreason = $cancellation_reason;
            $order->a_name = $additional_comments;
            $order->save();

            if ($user->is_admin && $order) {
                $order = $order->sendEmailCancelPicking();
                return response()->json($order);
            } else {
                $response = ["error" => "No tienes permiso"];
                return response()->json($response, 500);
            }

        } catch (\Exception $e) {
            $response = ["error" => "Error al consultar. " . $e->getMessage()];
            return response()->json($response);
        }
    }

    public function getRmaInvoice(Request $request, $documentno)
    {
        try {
            $user = $request->user();// Obtén el usuario autenticado
            $invoice = Invoice::where('documentno', $documentno)
                ->with('partner')
                ->with(['lines.product'])
                ->with('salesperson')
                ->where('is_salestrx', true)
                ->where('status', 'CO')
                ->first();

            if($invoice){
                $response = $invoice->toArray();
                return response()->json($response);
            }else{
                $response = ["error" => "No existe la factura"];
                return response()->json($response, 500);
            }

        } catch (\Exception $e) {
            $response = ["error" => "Error al consultar. " . $e->getMessage()];
            return response()->json($response);
        }
    }

    public function postRmaInvoice(Request $request, $documentno)
    {
        try {
            $user = $request->user();// Obtén el usuario autenticado
            $invoice = Invoice::where('documentno', $documentno)
                ->with('partner')
                ->with(['lines.product'])
                ->with('salesperson')
                ->where('is_salestrx', true)
                ->where('status', 'CO')
                ->first();

            if($invoice){
                $response = $invoice->toArray();
                return response()->json($response);
            }else{
                $response = ["error" => "No existe la factura"];
                return response()->json($response, 500);
            }

        } catch (\Exception $e) {
            $response = ["error" => "Error al consultar. " . $e->getMessage()];
            return response()->json($response);
        }
    }


}

