<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\ArancelController;
use App\Http\Controllers\Api\OrdenPagoController;

// Auth pública
Route::post('login', [AuthController::class, 'login']);
// Cambia la línea 10 para que quede limpia así:
Route::get('ordenes/{id}/pdf', [OrdenPagoController::class, 'generarPdf']);
// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Empresas
    Route::get('empresas/search', [EmpresaController::class, 'search']);
    Route::apiResource('empresas', EmpresaController::class);

    // Aranceles
    Route::get('aranceles/search', [ArancelController::class, 'search']);
    Route::apiResource('aranceles', ArancelController::class);

    // Órdenes de pago
    Route::apiResource('ordenes', OrdenPagoController::class)
        ->parameters(['ordenes' => 'ordenPago']);
});