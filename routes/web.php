<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cliente\CheckoutController;
use App\Http\Controllers\Cliente\DireccionController;
use App\Http\Controllers\Servicios\StripeController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Publico\ContactoController;
use App\Http\Controllers\LocalizationController;


// Test de errores
Route::get('/test-errors', [App\Http\Controllers\Servicios\TestErrorController::class, 'testErrors'])
    ->name('test.errors');

// Google
Route::get('/auth/redirect/google', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/callback/google', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

// Rutas de autenticación
Route::middleware(['auth'])->group(function () {
    // Validación de contraseña actual
    Route::post('/validar-contrasena-actual', [AuthController::class, 'validarContrasenaActual'])
        ->name('validar.contrasena.actual');
});

// Verificación OTP
Route::get('/otp/verify', [App\Http\Controllers\Publico\OtpController::class, 'showVerificationForm'])->name('otp.verify.form');
Route::get('/otp/verify/register', [App\Http\Controllers\Publico\OtpController::class, 'showRegisterVerificationForm'])->name('otp.verify.register');
Route::post('/otp/send', [App\Http\Controllers\Publico\OtpController::class, 'sendOtp'])->name('otp.send');
Route::post('/otp/verify', [App\Http\Controllers\Publico\OtpController::class, 'verifyOtp'])->name('otp.verify');

// OTP para restablecimiento de contraseña
Route::post('/otp/password-reset/send', [App\Http\Controllers\Publico\OtpController::class, 'sendPasswordResetOtp'])->name('otp.password.reset.send');
Route::post('/otp/password-reset/verify', [App\Http\Controllers\Publico\OtpController::class, 'verifyPasswordResetOtp'])->name('otp.password.reset.verify');

// Limpiar códigos OTP expirados (mantenimiento)
Route::post('/otp/cleanup', [App\Http\Controllers\Publico\OtpController::class, 'limpiarExpirados'])->name('otp.cleanup');

// Sistema de verificación OTP implementado - rutas legacy eliminadas

// Recuperación de contraseña con OTP
Route::get('password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::get('password/reset/otp', [AuthController::class, 'showResetForm'])->name('password.reset.otp');
Route::post('password/reset/otp', [AuthController::class, 'showResetForm'])->name('password.reset.otp.post');
Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');

// Establecer contraseña para usuarios de Google
Route::post('/google/set-password', [AuthController::class, 'setPassword'])
    ->name('google.set-password')
    ->middleware('auth');

// Rutas de Stripe (con autenticación)
Route::middleware(['auth'])->group(function () {
    Route::get('/stripe/payment-form/{pedidoId}', [StripeController::class, 'showPaymentForm'])->name('stripe.payment');
    Route::post('/stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent'])->name('stripe.create-payment-intent');
    Route::post('/stripe/confirm-payment', [StripeController::class, 'confirmPayment'])->name('stripe.confirm-payment');
});

// Webhook de Stripe (sin middleware de auth)
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');

// Rutas de checkout
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'index'])->name('checkout.submit');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{pedido}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel/{pedido}', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    Route::post('/checkout/verificar-stock', [CheckoutController::class, 'verificarStock'])->name('checkout.verificar-stock');
});

// Rutas del carrito
Route::middleware(['auth'])->group(function () {
    Route::post('/carrito/agregar', [App\Http\Controllers\Cliente\CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::post('/carrito/actualizar', [App\Http\Controllers\Cliente\CarritoController::class, 'actualizar'])->name('carrito.actualizar');
    Route::post('/carrito/eliminar', [App\Http\Controllers\Cliente\CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::post('/carrito/limpiar', [App\Http\Controllers\Cliente\CarritoController::class, 'limpiar'])->name('carrito.limpiar');
    Route::get('/carrito/obtener', [App\Http\Controllers\Cliente\CarritoController::class, 'obtener'])->name('carrito.obtener');
    Route::get('/carrito/verificar-stock', [App\Http\Controllers\Cliente\CarritoController::class, 'verificarStock'])->name('carrito.verificar-stock');
});

// Rutas de productos con variantes
Route::get('/productos-variantes', [ProductoController::class, 'conVariantes'])->name('productos.variantes');
Route::get('/productos-variantes/{producto}', [ProductoController::class, 'detalleConVariantes'])->name('productos.variantes.show');
Route::get('/productos-variantes/{producto}/stock', [ProductoController::class, 'obtenerStock'])->name('productos.variantes.stock');
Route::get('/productos-variantes/{producto}/variantes', [ProductoController::class, 'obtenerVariantes'])->name('productos.variantes.lista');
Route::get('/productos-variantes/buscar', [ProductoController::class, 'buscarConVariantes'])->name('productos.variantes.buscar');
Route::get('/productos-variantes/categoria/{categoria}', [ProductoController::class, 'porCategoriaConVariantes'])->name('productos.variantes.categoria');
Route::get('/productos-variantes/stock-bajo', [ProductoController::class, 'stockBajo'])->name('productos.variantes.stock-bajo');
Route::get('/productos-variantes/sin-stock', [ProductoController::class, 'sinStock'])->name('productos.variantes.sin-stock');

// Rutas que requieren email verificado
Route::middleware(['auth', 'email.verified'])->group(function () {
    // Rutas de direcciones
    Route::resource('direcciones', DireccionController::class);
    
    // Rutas de pedidos
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'detalle'])->name('pedidos.show');
    Route::get('/pedidos', [PedidoController::class, 'historial'])->name('pedidos.index');
});

// Cargar rutas separadas
require __DIR__.'/admin.php';
require __DIR__.'/cliente.php';
require __DIR__.'/publico.php';

// Ruta para el formulario de contacto
Route::post('/contacto/enviar', [ContactoController::class, 'enviarFormulario'])->name('contacto.enviar');
Route::post('/servicio-tecnico/enviar', [ContactoController::class, 'enviarServicioTecnico'])->name('servicio-tecnico.enviar');

// Ruta API para obtener especificaciones por categoría
Route::get('/api/especificaciones/{categoriaId}', function ($categoriaId) {
    $especificaciones = \App\Models\EspecificacionCategoria::where('categoria_id', $categoriaId)
        ->where('activo', true)
        ->orderBy('orden', 'asc')
        ->get();
    
    return response()->json($especificaciones);
})->name('api.especificaciones.categoria');

// Ruta API para obtener valores disponibles de especificaciones por categoría
Route::get('/api/especificaciones/{categoriaId}/valores', function ($categoriaId) {
    $especificaciones = \App\Models\EspecificacionCategoria::where('categoria_id', $categoriaId)
        ->where('activo', true)
        ->orderBy('orden', 'asc')
        ->get();
    
    $valoresDisponibles = [];
    
    foreach ($especificaciones as $espec) {
        // Obtener valores únicos para esta especificación
        $valores = \App\Models\EspecificacionProducto::whereHas('especificacionCategoria', function ($query) use ($espec) {
            $query->where('especificacion_id', $espec->especificacion_id);
        })
        ->whereHas('producto', function ($query) use ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        })
        ->pluck('valor')
        ->unique()
        ->values()
        ->toArray();
        
        if (!empty($valores)) {
            $valoresDisponibles[$espec->nombre_campo] = [
                'etiqueta' => $espec->etiqueta,
                'tipo_campo' => $espec->tipo_campo,
                'unidad' => $espec->unidad,
                'valores' => $valores
            ];
        }
    }
    
    return response()->json($valoresDisponibles);
})->name('api.especificaciones.valores');

// Ruta API para obtener variantes de un producto
Route::get('/api/productos/{productoId}/variantes', function ($productoId) {
    $variantes = \App\Models\VarianteProducto::where('producto_id', $productoId)
        ->where('disponible', true)
        ->orderBy('nombre', 'asc')
        ->get();
    
    return response()->json([
        'success' => true,
        'variantes' => $variantes->map(function ($variante) {
            return [
                'variante_id' => $variante->variante_id,
                'nombre' => $variante->nombre,
                'codigo_color' => $variante->codigo_color,
                'precio_adicional' => $variante->precio_adicional,
                'stock_disponible' => $variante->stock_disponible,
                'descripcion' => $variante->descripcion,
                'disponible' => $variante->disponible
            ];
        })
    ]);
})->name('api.productos.variantes');

// Rutas de localización
Route::get('/localization/config', [LocalizationController::class, 'showConfigModal'])->name('localization.config');
Route::post('/localization/save', [LocalizationController::class, 'saveConfig'])->name('localization.save');
Route::get('/localization/change/{language}', [LocalizationController::class, 'changeLanguage'])->name('localization.change');
Route::get('/localization/current', [LocalizationController::class, 'getCurrentConfig'])->name('localization.current');

// Rutas de prueba
Route::get('/test-locale', function() {
    // Aplicar el locale de la sesión directamente
    $locale = session('locale', 'es');
    $currency = session('currency', 'COP');
    $country = session('country', 'CO');
    
    app()->setLocale($locale);
    
    return response()->json([
        'current_locale' => app()->getLocale(),
        'session_locale' => session('locale'),
        'session_currency' => session('currency'),
        'welcome_message' => __('messages.messages.welcome'),
        'formatted_price' => \App\Helpers\CurrencyHelper::formatPrice(150000)
    ]);
});

Route::get('/change-lang/{locale}', function($locale) {
    // Establecer en la sesión
    session(['locale' => $locale]);
    
    if ($locale === 'en') {
        session(['currency' => 'USD', 'country' => 'US']);
    } elseif ($locale === 'pt') {
        session(['currency' => 'BRL', 'country' => 'BR']);
    } else {
        session(['currency' => 'COP', 'country' => 'CO']);
    }
    
    // Establecer el locale actual
    app()->setLocale($locale);
    
    // Forzar guardar la sesión
    session()->save();
    
    // Obtener la URL de referencia (la página donde estaba el usuario)
    $referer = request()->header('referer', '/');
    
    // Retornar una página simple que recargue la página actual
    return response('
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Cambiando idioma...</title>
    </head>
    <body>
        <script>
            // Recargar la página actual
            window.location.href = "' . $referer . '";
        </script>
    </body>
    </html>
    ');
})->middleware('web');

// Ruta de prueba para verificar el cambio
Route::get('/test-change/{locale}', function($locale) {
    session(['locale' => $locale]);
    
    if ($locale === 'en') {
        session(['currency' => 'USD', 'country' => 'US']);
    } elseif ($locale === 'pt') {
        session(['currency' => 'BRL', 'country' => 'BR']);
    } else {
        session(['currency' => 'COP', 'country' => 'CO']);
    }
    
    app()->setLocale($locale);
    session()->save();
    
    return response()->json([
        'success' => true,
        'locale' => app()->getLocale(),
        'session_locale' => session('locale'),
        'session_currency' => session('currency'),
        'welcome' => __('messages.messages.welcome'),
        'price' => \App\Helpers\CurrencyHelper::formatPrice(150000)
    ]);
})->middleware('web');
