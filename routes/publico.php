<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Publico\LandingController;
use App\Http\Controllers\Publico\ProductoPublicoController;
use App\Http\Controllers\Publico\SearchController;


// Rutas públicas (accesibles para todos)

// Ruta para ver la página de inicio
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Ruta para ver el catálogo de productos
Route::get('/productosL', [LandingController::class, 'catalogo'])->name('productos.lista');

// Ruta para ver producto individual
Route::get('/productos/{producto}', [ProductoPublicoController::class, 'show'])->name('productos.show');

// Ruta para crear reseñas públicas
Route::post('/productos/{producto}/resenas', [ProductoPublicoController::class, 'storeResena'])->name('productos.resenas.store');

// Rutas para filtros y búsqueda de productos (AJAX y directo)
Route::post('/productos/filtrados', [LandingController::class, 'productosFiltrados'])->name('productos.filtrados');
Route::get('/productos/filtrados', [LandingController::class, 'productosFiltrados'])->name('productos.filtrados.get');

// Ruta para ver la página de nosotros
Route::get('/nosotros', function () {
    \App\Helpers\ViewHelper::applyLocalization();
    return view('pages.landing.nosotros');
})->name('nosotros');

// Ruta para ver la página de contactanos
Route::get('/contactanos', function () {
    \App\Helpers\ViewHelper::applyLocalization();
    return view('pages.landing.contactanos');
})->name('contactanos');

// Ruta para ver la página de servicio técnico
Route::get('/servicio-tecnico', function () {
    \App\Helpers\ViewHelper::applyLocalization();
    return view('pages.landing.servicio-tecnico');
})->name('servicio-tecnico');

// Ruta para ver la página de servicios
Route::get('/servicios', function () {
    \App\Helpers\ViewHelper::applyLocalization();
    return view('pages.landing.servicio-tecnico');
})->name('servicios');

// Búsqueda unificada (productos y páginas)
Route::get('/buscar', [SearchController::class, 'index'])->name('buscar');
// Sugerencias en tiempo real (JSON)
Route::get('/buscar/sugerencias', [SearchController::class, 'sugerencias'])->name('buscar.sugerencias');

// Rutas que solo son accesibles para invitados (no autenticados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/registrar', [AuthController::class, 'registrar'])->name('registrar');
    Route::post('/logear', [AuthController::class, 'logear'])->name('logear');
});

