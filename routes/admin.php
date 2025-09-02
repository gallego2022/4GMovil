<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Servicios\DashboardController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\PedidoController;      
use App\Http\Controllers\Cliente\DetallePedidoController;
use App\Http\Controllers\Cliente\DireccionController;
use App\Http\Controllers\Admin\MetodoPagoController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\PedidoAdminController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\EspecificacionController;

// Solo accesibles por usuarios autenticados y administradores
Route::middleware(['auth', 'admin'])->group(function () {
// Dashboard
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.index');

// Productos (con prefijo admin)
Route::prefix('admin/productos')->name('admin.productos.')->group(function () {
    Route::resource('', ProductoController::class)->names([
        'index' => 'index',
        'create' => 'create',
        'store' => 'store',
        'show' => 'show',
        'edit' => 'edit',
        'update' => 'update',
        'destroy' => 'destroy'
    ]);
    Route::get('/listadoP', [ProductoController::class, 'listado'])->name('listadoP');
    Route::delete('/imagenes/{id}', [ProductoController::class, 'destroyImagen'])->name('imagenes.destroy');
});

// Rutas de productos sin prefijo (para compatibilidad) - MOVIDAS DENTRO DEL MIDDLEWARE
// Primero las rutas específicas
Route::get('/productos/listadoP', [ProductoController::class, 'listado'])->name('productos.listadoP');
Route::delete('/imagenes/{id}', [ProductoController::class, 'destroyImagen'])->name('imagenes.destroy');

// Después las rutas con parámetros dinámicos
Route::resource('productos', ProductoController::class);
    Route::resource('usuarios', UsuarioController::class);
    Route::get('/usuarios/{usuario}/asignar-rol', [UsuarioController::class, 'asignarRol'])->name('usuarios.asignarRol');
    Route::post('/usuarios/{usuario}/asignar-rol', [UsuarioController::class, 'updateRol'])->name('usuarios.updateRol');
    Route::patch('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggle');
// Categorias
    Route::resource('categorias', CategoriaController::class);
// Marcas
    Route::resource('marcas', MarcaController::class);

    // Especificaciones por Categoría
    Route::prefix('admin/especificaciones')->name('admin.especificaciones.')->group(function () {
        Route::get('/', [EspecificacionController::class, 'index'])->name('index');
        Route::get('/create', [EspecificacionController::class, 'create'])->name('create');
        Route::post('/', [EspecificacionController::class, 'store'])->name('store');
        Route::get('/{id}', [EspecificacionController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [EspecificacionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EspecificacionController::class, 'update'])->name('update');
        Route::delete('/{id}', [EspecificacionController::class, 'destroy'])->name('destroy');
        
        // Rutas adicionales
        Route::get('/categoria/{categoriaId}', [EspecificacionController::class, 'getByCategoria'])->name('by-categoria');
        Route::patch('/{id}/toggle-estado', [EspecificacionController::class, 'toggleEstado'])->name('toggle-estado');
        Route::post('/reordenar', [EspecificacionController::class, 'reordenar'])->name('reordenar');
    });

// Recursos generales
Route::resource('detalles-pedido', DetallePedidoController::class);
Route::resource('metodos-pago', MetodoPagoController::class);
Route::resource('pagos', PagoController::class);

// Gestión de pedidos (admin)
Route::prefix('admin/pedidos')->name('admin.pedidos.')->group(function () {
    Route::get('/', [PedidoAdminController::class, 'index'])->name('index');
    Route::get('/{pedido}', [PedidoAdminController::class, 'show'])->name('show');
    Route::put('/{pedido}/estado', [PedidoAdminController::class, 'updateEstado'])->name('updateEstado');
});

// Rutas de inventario
Route::prefix('admin/inventario')->name('admin.inventario.')->group(function () {
    Route::get('/', [InventarioController::class, 'dashboard'])->name('dashboard');
    Route::get('/alertas', [InventarioController::class, 'alertas'])->name('alertas');
    Route::get('/movimientos', [InventarioController::class, 'movimientos'])->name('movimientos');
    Route::get('/reporte', [InventarioController::class, 'reporte'])->name('reporte');
    Route::get('/productos-mas-vendidos', [InventarioController::class, 'productosMasVendidos'])->name('productos-mas-vendidos');
    Route::get('/valor-por-categoria', [InventarioController::class, 'valorPorCategoria'])->name('valor-por-categoria');
    Route::get('/exportar-reporte', [InventarioController::class, 'exportarReporte'])->name('exportar-reporte');
    
    // Rutas para exportación real
    Route::post('/exportar-pdf', [InventarioController::class, 'exportarPDF'])->name('exportar-pdf');
    Route::post('/exportar-excel', [InventarioController::class, 'exportarExcel'])->name('exportar-excel');
    
    // Rutas para movimientos de inventario
    Route::post('/registrar-entrada', [InventarioController::class, 'registrarEntrada'])->name('registrar-entrada');
    Route::post('/registrar-salida', [InventarioController::class, 'registrarSalida'])->name('registrar-salida');
    Route::post('/ajustar-stock', [InventarioController::class, 'ajustarStock'])->name('ajustar-stock');
    
    // Rutas para variantes de inventario
    Route::get('/variantes', [InventarioController::class, 'dashboardVariantes'])->name('variantes.dashboard');
    Route::get('/variantes/reporte', [InventarioController::class, 'reporteVariantes'])->name('variantes.reporte');
    Route::get('/variantes/movimientos', [InventarioController::class, 'movimientosVariantes'])->name('variantes.movimientos');
    Route::post('/variantes/registrar-entrada', [InventarioController::class, 'registrarEntradaVariante'])->name('variantes.registrar-entrada');
    Route::post('/variantes/registrar-salida', [InventarioController::class, 'registrarSalidaVariante'])->name('variantes.registrar-salida');
    Route::post('/variantes/ajustar-stock', [InventarioController::class, 'ajustarStockVariante'])->name('variantes.ajustar-stock');
});
});
