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

// Verificación de Redis (solo para desarrollo)
Route::get('/redis-status', function () {
    if (!app()->environment('local', 'testing')) {
        abort(404);
    }
    
    $status = [
        'timestamp' => now()->toDateTimeString(),
        'environment' => app()->environment(),
        'configuration' => [
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_connection' => config('queue.default'),
        ],
        'redis' => [
            'host' => config('database.redis.default.host'),
            'port' => config('database.redis.default.port'),
            'connected' => false,
            'ping' => null,
            'error' => null,
        ],
        'services' => [
            'cache' => ['working' => false, 'error' => null],
            'session' => ['working' => false, 'error' => null],
            'queue' => ['working' => false, 'error' => null],
        ]
    ];
    
    // Verificar conexión Redis
    try {
        $pong = \Illuminate\Support\Facades\Redis::ping();
        $status['redis']['connected'] = true;
        $status['redis']['ping'] = $pong;
    } catch (\Exception $e) {
        $status['redis']['error'] = $e->getMessage();
    }
    
    // Verificar caché
    try {
        \Illuminate\Support\Facades\Cache::put('test_redis_status', 'test_value', 60);
        $value = \Illuminate\Support\Facades\Cache::get('test_redis_status');
        $status['services']['cache']['working'] = ($value === 'test_value');
        \Illuminate\Support\Facades\Cache::forget('test_redis_status');
    } catch (\Exception $e) {
        $status['services']['cache']['error'] = $e->getMessage();
    }
    
    // Verificar sesiones
    try {
        session(['test_redis_status' => 'test_value']);
        $value = session('test_redis_status');
        $status['services']['session']['working'] = ($value === 'test_value');
        session()->forget('test_redis_status');
    } catch (\Exception $e) {
        $status['services']['session']['error'] = $e->getMessage();
    }
    
    // Verificar colas
    try {
        $queue = \Illuminate\Support\Facades\Queue::connection('redis');
        $status['services']['queue']['working'] = true;
    } catch (\Exception $e) {
        $status['services']['queue']['error'] = $e->getMessage();
    }
    
    return response()->json($status, 200, [], JSON_PRETTY_PRINT);
})->name('redis.status');

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
Route::post('password/reset/otp', [AuthController::class, 'sendResetLinkEmail'])->name('password.reset.otp.post');
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
    Route::get('/checkout/confirm/{pedido}', [CheckoutController::class, 'showConfirm'])->name('checkout.confirm');
    Route::get('/checkout/success/{pedido}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel/{pedido}', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    Route::post('/checkout/verificar-stock', [CheckoutController::class, 'verificarStock'])->name('checkout.verificar-stock');
    Route::post('/checkout/confirmar/{pedido}', [CheckoutController::class, 'confirmarPedido'])->name('checkout.confirmar');
});

// Rutas del carrito
Route::middleware(['auth'])->group(function () {
    Route::get('/carrito', [App\Http\Controllers\Cliente\CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar', [App\Http\Controllers\Cliente\CarritoController::class, 'addToCart'])->name('carrito.agregar');
    Route::post('/carrito/actualizar/{itemId}', [App\Http\Controllers\Cliente\CarritoController::class, 'updateItem'])->name('carrito.actualizar');
    Route::delete('/carrito/eliminar/{itemId}', [App\Http\Controllers\Cliente\CarritoController::class, 'removeItem'])->name('carrito.eliminar');
    Route::post('/carrito/limpiar', [App\Http\Controllers\Cliente\CarritoController::class, 'clear'])->name('carrito.limpiar');
    Route::get('/carrito/resumen', [App\Http\Controllers\Cliente\CarritoController::class, 'summary'])->name('carrito.resumen');
    Route::get('/carrito/json', [App\Http\Controllers\Cliente\CarritoController::class, 'getCartJson'])->name('carrito.json');
    Route::get('/carrito/verificar-stock', [App\Http\Controllers\Cliente\CarritoController::class, 'verificarStock'])->name('carrito.verificar-stock');
    Route::post('/carrito/sincronizar', [App\Http\Controllers\Cliente\CarritoController::class, 'sync'])->name('carrito.sincronizar');
    Route::get('/carrito/mini', [App\Http\Controllers\Cliente\CarritoController::class, 'mini'])->name('carrito.mini');
    Route::get('/carrito/vacio', [App\Http\Controllers\Cliente\CarritoController::class, 'isEmpty'])->name('carrito.vacio');
});

// Rutas del carrito para usuarios no autenticados (solo sesión)
Route::group(['prefix' => 'carrito', 'as' => 'carrito.'], function () {
    Route::get('/obtener', [App\Http\Controllers\Cliente\CarritoController::class, 'getCartJson'])->name('obtener');
    Route::post('/agregar', [App\Http\Controllers\Cliente\CarritoController::class, 'addToCart'])->name('agregar');
    Route::post('/actualizar/{itemId}', [App\Http\Controllers\Cliente\CarritoController::class, 'updateItem'])->name('actualizar');
    Route::delete('/eliminar/{itemId}', [App\Http\Controllers\Cliente\CarritoController::class, 'removeItem'])->name('eliminar');
    Route::post('/limpiar', [App\Http\Controllers\Cliente\CarritoController::class, 'clear'])->name('limpiar');
    Route::get('/verificar-stock', [App\Http\Controllers\Cliente\CarritoController::class, 'verificarStock'])->name('verificar-stock');
});

// Rutas de productos con variantes - Redirigidas a la vista principal
Route::get('/productos-variantes', function() {
    return redirect()->route('productos.index')->with('info', 'La vista de productos con variantes ha sido consolidada en la vista principal de productos.');
})->name('productos.variantes');

Route::get('/productos-variantes/{producto}', function($producto) {
    return redirect()->route('productos.show', $producto)->with('info', 'Redirigido a la vista principal del producto.');
})->name('productos.variantes.show');

Route::get('/productos-variantes/{producto}/stock', function($producto) {
    return redirect()->route('productos.show', $producto)->with('info', 'Redirigido a la vista principal del producto.');
})->name('productos.variantes.stock');

// Demo del Sistema de Carga - ELIMINADO (vista no utilizada)

Route::get('/productos-variantes/{producto}/variantes', function($producto) {
    return redirect()->route('productos.show', $producto)->with('info', 'Redirigido a la vista principal del producto.');
})->name('productos.variantes.lista');

Route::get('/productos-variantes/buscar', function() {
    return redirect()->route('productos.index')->with('info', 'La búsqueda de productos con variantes está disponible en la vista principal.');
})->name('productos.variantes.buscar');

Route::get('/productos-variantes/categoria/{categoria}', function($categoria) {
    return redirect()->route('productos.categoria', $categoria)->with('info', 'Redirigido a la vista de categoría de productos.');
})->name('productos.variantes.categoria');

Route::get('/productos-variantes/stock-bajo', function() {
    return redirect()->route('productos.index')->with('info', 'La información de stock bajo está disponible en la vista principal de productos.');
})->name('productos.variantes.stock-bajo');

Route::get('/productos-variantes/sin-stock', function() {
    return redirect()->route('productos.index')->with('info', 'La información de productos sin stock está disponible en la vista principal.');
})->name('productos.variantes.sin-stock');

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
        ->where('estado', true)
        ->orderBy('orden', 'asc')
        ->get();
    
    return response()->json($especificaciones);
})->name('api.especificaciones.categoria');

// Ruta API para obtener valores disponibles de especificaciones por categoría
Route::get('/api/especificaciones/{categoriaId}/valores', function ($categoriaId) {
    $especificaciones = \App\Models\EspecificacionCategoria::where('categoria_id', $categoriaId)
        ->where('estado', true)
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
Route::get('/api/productos/{producto}/variantes', function ($producto) {
    $variantes = \App\Models\VarianteProducto::where('producto_id', $producto)
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
    $allowedLanguages = ['es', 'en', 'pt'];
    
    if (!in_array($locale, $allowedLanguages)) {
        $locale = 'es';
    }
    
    // Configuraciones por idioma
    $languageConfigs = [
        'es' => ['country' => 'CO', 'currency' => 'COP'],
        'en' => ['country' => 'US', 'currency' => 'USD'],
        'pt' => ['country' => 'BR', 'currency' => 'BRL'],
    ];
    
    $config = $languageConfigs[$locale] ?? $languageConfigs['es'];
    
    // Establecer en la sesión
    session([
        'locale' => $locale,
        'currency' => $config['currency'],
        'country' => $config['country']
    ]);
    
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
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
            .loading { color: #3B82F6; }
        </style>
    </head>
    <body>
        <div class="loading">
            <h2>Cambiando idioma a ' . strtoupper($locale) . '...</h2>
            <p>Redirigiendo...</p>
        </div>
        <script>
            // Recargar la página actual después de un breve delay
            setTimeout(function() {
                window.location.href = "' . $referer . '";
            }, 1000);
        </script>
    </body>
    </html>
    ');
})->middleware('web');

// Ruta de prueba para verificar el cambio
Route::get('/test-change/{locale}', function($locale) {
    $allowedLanguages = ['es', 'en', 'pt'];
    
    if (!in_array($locale, $allowedLanguages)) {
        $locale = 'es';
    }
    
    // Configuraciones por idioma
    $languageConfigs = [
        'es' => ['country' => 'CO', 'currency' => 'COP'],
        'en' => ['country' => 'US', 'currency' => 'USD'],
        'pt' => ['country' => 'BR', 'currency' => 'BRL'],
    ];
    
    $config = $languageConfigs[$locale] ?? $languageConfigs['es'];
    
    session([
        'locale' => $locale,
        'currency' => $config['currency'],
        'country' => $config['country']
    ]);
    
    app()->setLocale($locale);
    session()->save();
    
    return response()->json([
        'success' => true,
        'locale' => app()->getLocale(),
        'session_locale' => session('locale'),
        'session_currency' => session('currency'),
        'session_country' => session('country'),
        'welcome' => __('messages.welcome'),
        'price_cop' => \App\Helpers\CurrencyHelper::formatPrice(150000, 'COP'),
        'price_current' => \App\Helpers\CurrencyHelper::formatPrice(150000),
        'currency_symbol' => \App\Helpers\CurrencyHelper::getCurrencySymbol(),
        'test_translations' => [
            'product_show_warranty' => __('messages.product_show.warranty'),
            'product_show_available' => __('messages.product_show.available'),
            'product_show_add_to_cart' => __('messages.product_show.add_to_cart'),
        ]
    ]);
})->middleware('web');
