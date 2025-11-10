<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
// use App\Http\Controllers\Cliente\EstadoPedidoController; // ELIMINADO
use App\Http\Controllers\Cliente\PedidoController;

Route::middleware(['auth', 'email.verified'])->group(function () {
    
    // Rutas de perfil para clientes
    Route::get('/perfil', [AuthController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [AuthController::class, 'actualizarPerfil'])->name('perfil.actualizar');
    Route::delete('/perfil/foto', [AuthController::class, 'eliminarFoto'])->name('perfil.eliminarFoto');

    // Rutas de cambio de contraseña para clientes
    Route::get('/cambiar-contrasena', [AuthController::class, 'formCambiarContrasena'])->name('cambiar.contrasena');
    Route::post('/cambiar-contrasena', [AuthController::class, 'cambiarContrasena'])->name('cambiar.contrasena.post');

    // Rutas de pedidos para clientes
    Route::prefix('cliente/pedidos')->name('pedidos.')->group(function () {
        Route::get('/historial', [PedidoController::class, 'historial'])->name('historial');
        Route::get('/{pedido}', [PedidoController::class, 'detalle'])->name('detalle');
        Route::post('/{pedido}/calificar', [PedidoController::class, 'calificar'])->name('calificar');
    });

    // Rutas de estados de pedido para clientes
    // Route::resource('estados-pedido', EstadoPedidoController::class); // ELIMINADO
});

// Rutas de logout para clientes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');