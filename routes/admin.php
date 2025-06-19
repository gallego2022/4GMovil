<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ImagenProductoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\DetallePedidoController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\Admin\PedidoAdminController;

// Solo accesibles por usuarios autenticados y administradores
Route::middleware(['auth', 'verified'])->group(function () {
// Dashboard
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.index');
// Productos
    Route::resource('productos', ProductoController::class)->except(['show']);
    Route::get('/productos/listadoP', [ProductoController::class, 'listado'])->name('productos.listadoP');
    Route::delete('/imagenes/{id}', [ImagenProductoController::class, 'destroy'])->name('imagenes.destroy');

    Route::resource('usuarios', UsuarioController::class);
    Route::get('/usuarios/{usuario}/asignar-rol', [UsuarioController::class, 'asignarRol'])->name('usuarios.asignarRol');
    Route::post('/usuarios/{usuario}/asignar-rol', [UsuarioController::class, 'updateRol'])->name('usuarios.updateRol');
    Route::patch('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggle');
// Categorias
    Route::resource('categorias', CategoriaController::class);
// Marcas
    Route::resource('marcas', MarcaController::class);

// Recursos generales
Route::resource('detalles-pedido', DetallePedidoController::class);
Route::resource('direcciones', DireccionController::class);
Route::resource('metodos-pago', MetodoPagoController::class);
Route::resource('pagos', PagoController::class);

// Gestión de pedidos (admin)
Route::prefix('admin/pedidos')->name('admin.pedidos.')->group(function () {
    Route::get('/', [PedidoAdminController::class, 'index'])->name('index');
    Route::get('/{pedido}', [PedidoAdminController::class, 'show'])->name('show');
    Route::put('/{pedido}/estado', [PedidoAdminController::class, 'updateEstado'])->name('updateEstado');
});
});
