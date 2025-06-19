<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\Producto;

// Rutas públicas (accesibles para todos)
Route::get('/', function () {
    $productos = Producto::all();
    return view('pages.landing.index', compact('productos'));
})->name('landing');

Route::get('/productosL', function () {
    $productos = App\Models\Producto::all();
    return view('pages.landing.productos', compact('productos'));
})->name('productos.lista');

Route::get('/nosotros', function () {
    return view('pages.landing.nosotros');
})->name('nosotros');

Route::get('/contactanos', function () {
    return view('pages.landing.contactanos');
})->name('contactanos');

Route::get('/servicio-tecnico', function () {
    return view('pages.landing.servicio-tecnico');
})->name('servicio-tecnico');

// Rutas que solo son accesibles para invitados (no autenticados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/registrar', [AuthController::class, 'registrar'])->name('registrar');
    Route::post('/logear', [AuthController::class, 'logear'])->name('logear');
});

