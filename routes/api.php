<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\API\APIController;

Route::get('/ping', function () {
    return response()->json(['message' => 'Pong!']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('v1')->group(function () {

        Route::prefix('external')->group(function () {
            Route::get('/user', [APIController::class, 'getUser']);
            Route::get('/products', [APIController::class, 'getProducts']);
        });

        Route::prefix('rma')->group(function () {
            Route::get('invoice/{documentno}', [APIController::class, 'getRmaInvoice']);
            Route::post('invoice/{documentno}', [APIController::class, 'postRmaInvoice']);
        });

        // Aquí puedes agregar más rutas protegidas

        Route::fallback(function () {
            return response()->json(['message' => 'No encontrado'], JsonResponse::HTTP_NOT_FOUND);
        });
    });

    Route::fallback(function () {
        return response()->json(['message' => 'No encontrado'], JsonResponse::HTTP_NOT_FOUND);
    });
});
