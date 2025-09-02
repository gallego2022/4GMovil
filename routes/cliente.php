<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Cliente\EstadoPedidoController;
use App\Http\Controllers\Admin\PedidoController;

Route::middleware(['auth', 'email.verified'])->group(function () {
    // Rutas de logout para clientes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
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
    });

    // Rutas de resenas para clientes (consolidadas en ProductoController)
    Route::prefix('productos/{productoId}/resenas')->name('productos.resenas.')->group(function () {
        Route::get('/', [ProductoController::class, 'resenasIndex'])->name('index');
        Route::get('/create', [ProductoController::class, 'resenasCreate'])->name('create');
        Route::post('/', [ProductoController::class, 'resenasStore'])->name('store');
        Route::get('/{resenaId}/edit', [ProductoController::class, 'resenasEdit'])->name('edit');
        Route::put('/{resenaId}', [ProductoController::class, 'resenasUpdate'])->name('update');
        Route::delete('/{resenaId}', [ProductoController::class, 'resenasDestroy'])->name('destroy');
    });
    // Rutas de estados de pedido para clientes
    Route::resource('estados-pedido', EstadoPedidoController::class);
});
