<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use App\Mail\CorreoVerificacionPrueba;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DireccionController;

// Rutas de autenticación
Route::middleware(['auth'])->group(function () {
    // Validación de contraseña actual
    Route::post('/validar-contrasena-actual', [AuthController::class, 'validarContrasenaActual'])
        ->name('validar.contrasena.actual');
});

// Verificación de correo
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/perfil')->with('status', 'Correo verificado correctamente');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('message', '¡Correo de verificación enviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Recuperación de contraseña
Route::get('password/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Rutas de checkout
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{pedido}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Rutas de direcciones
    Route::resource('direcciones', DireccionController::class);
});

// Cargar rutas separadas
require __DIR__.'/publico.php';
require __DIR__.'/cliente.php';
require __DIR__.'/admin.php';
