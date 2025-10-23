<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Servicios\DashboardController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\PedidoController;      
// use App\Http\Controllers\Cliente\DetallePedidoController; // ELIMINADO
use App\Http\Controllers\Cliente\DireccionController;
use App\Http\Controllers\Admin\MetodoPagoController;
// use App\Http\Controllers\Admin\PagoController; // ELIMINADO
use App\Http\Controllers\Admin\PedidoAdminController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\EspecificacionController;
use App\Http\Controllers\Admin\OptimizedStockAlertController;

// Solo accesibles por usuarios autenticados y administradores
Route::middleware(['auth', 'admin', \App\Http\Middleware\CacheInvalidationMiddleware::class])->group(function () {
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
    Route::delete('/{producto}/imagenes/{imagen}', [ProductoController::class, 'destroyImagen'])->name('imagenes.destroy');
});

// Rutas de productos sin prefijo (para compatibilidad) - MOVIDAS DENTRO DEL MIDDLEWARE
// Primero las rutas específicas
Route::get('/productos/listadoP', [ProductoController::class, 'listado'])->name('productos.listadoP');
Route::delete('/productos/{producto}/imagenes/{imagen}', [ProductoController::class, 'destroyImagen'])->name('imagenes.destroy');

// Después las rutas con parámetros dinámicos
Route::resource('productos', ProductoController::class);

// Rutas de variantes de productos
Route::prefix('productos/{producto}/variantes')->name('productos.variantes.')->group(function () {
    Route::get('/', [ProductoController::class, 'variantesIndex'])->name('index');
    Route::get('/create', [ProductoController::class, 'variantesCreate'])->name('create');
    Route::post('/', [ProductoController::class, 'variantesStore'])->name('store');
    Route::get('/{variante}/edit', [ProductoController::class, 'variantesEdit'])->name('edit');
    Route::put('/{variante}', [ProductoController::class, 'variantesUpdate'])->name('update');
    Route::delete('/{variante}', [ProductoController::class, 'variantesDestroy'])->name('destroy');
});

// Rutas de reseñas de productos
Route::prefix('productos/{producto}/resenas')->name('productos.resenas.')->group(function () {
    Route::get('/', [ProductoController::class, 'resenasIndex'])->name('index');
    Route::get('/create', [ProductoController::class, 'resenasCreate'])->name('create');
    Route::post('/', [ProductoController::class, 'resenasStore'])->name('store');
    Route::get('/{resena}/edit', [ProductoController::class, 'resenasEdit'])->name('edit');
    Route::put('/{resena}', [ProductoController::class, 'resenasUpdate'])->name('update');
    Route::delete('/{resena}', [ProductoController::class, 'resenasDestroy'])->name('destroy');
});

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
// Route::resource('detalles-pedido', DetallePedidoController::class); // ELIMINADO
Route::resource('metodos-pago', MetodoPagoController::class);
// Route::resource('pagos', PagoController::class); // ELIMINADO

// Gestión de pedidos (admin)
Route::prefix('admin/pedidos')->name('admin.pedidos.')->group(function () {
    Route::get('/', [PedidoAdminController::class, 'index'])->name('index');
    Route::get('/{pedido}', [PedidoAdminController::class, 'show'])->name('show');
    Route::put('/{pedido}/estado', [PedidoAdminController::class, 'updateEstado'])->name('updateEstado');
});

// Rutas de inventario
Route::prefix('admin/inventario')->name('admin.inventario.')->group(function () {
    Route::get('/', [InventarioController::class, 'dashboard'])->name('dashboard');
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
    
    // Rutas para alertas optimizadas
    Route::get('/alertas-optimizadas', [OptimizedStockAlertController::class, 'dashboard'])->name('alertas-optimizadas');
    Route::get('/alertas/variantes', [OptimizedStockAlertController::class, 'getVariantesProblematicas'])->name('alertas.variantes');
    Route::get('/alertas/estadisticas', [OptimizedStockAlertController::class, 'getEstadisticas'])->name('alertas.estadisticas');
});
});
