<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\EstadoPedidoController;
use App\Http\Controllers\PedidoController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/perfil', [AuthController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [AuthController::class, 'actualizarPerfil'])->name('perfil.actualizar');
    Route::delete('/perfil/foto', [AuthController::class, 'eliminarFoto'])->name('perfil.eliminarFoto');

    Route::get('/cambiar-contrasena', [AuthController::class, 'formCambiarContrasena'])->name('cambiar.contrasena');
    Route::post('/cambiar-contrasena', [AuthController::class, 'cambiarContrasena'])->name('cambiar.contrasena.post');

    // Rutas de pedidos para clientes
    Route::prefix('cliente/pedidos')->name('pedidos.')->group(function () {
        Route::get('/historial', [PedidoController::class, 'historial'])->name('historial');
        Route::get('/{pedido}', [PedidoController::class, 'detalle'])->name('detalle');
    });

    Route::resource('resenas', ResenaController::class);
    Route::resource('estados-pedido', EstadoPedidoController::class);
});
