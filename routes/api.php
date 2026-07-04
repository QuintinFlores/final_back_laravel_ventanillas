<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\ArancelController;
use App\Http\Controllers\Api\OrdenPagoController;

// Auth pública
Route::post('login', [AuthController::class, 'login']);
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

    // UBICACIÓN DE LA NUEVA RUTA: Justo aquí para que use el controlador de órdenes
    Route::get('aranceles/{id}/siguiente-secuencial', [OrdenPagoController::class, 'obtenerSiguienteSecuencial']);

    Route::apiResource('aranceles', ArancelController::class);

    // Órdenes de pago
    Route::apiResource('ordenes', OrdenPagoController::class)
        ->parameters(['ordenes' => 'ordenPago']);
});

Route::get('/reparar-usuarios', function () {
    $nuevoHash = \Illuminate\Support\Facades\Hash::make('password');
    \Illuminate\Support\Facades\DB::table('usuarios')
        ->whereIn('username', ['rquino', 'mibanez', 'dlucero', 'jhilaquita'])
        ->update([
            'password_hash' => $nuevoHash,
            'activo' => true
        ]);
    return "Todos los usuarios (incluida Lucero) parchados con éxito con la contraseña: password";
});
