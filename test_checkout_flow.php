<?php

require_once 'vendor/autoload.php';

use App\Services\Business\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Test del flujo completo del checkout...\n\n";

try {
    // Simular autenticación
    $user = \App\Models\Usuario::find(1);
    if ($user) {
        Auth::login($user);
        echo "✅ Usuario autenticado: {$user->nombre}\n";
    } else {
        echo "⚠️ No se pudo autenticar usuario\n";
        exit(1);
    }
    
    // Simular carrito con 'quantity' (como viene del frontend)
    $cart = [
        [
            'id' => 1,
            'producto_id' => 1,
            'name' => 'Producto Test 1',
            'quantity' => 2,
            'price' => 10000
        ],
        [
            'id' => 2,
            'producto_id' => 2,
            'name' => 'Producto Test 2',
            'quantity' => 1,
            'price' => 15000
        ]
    ];
    
    echo "\n🛒 Carrito de prueba:\n";
    foreach ($cart as $item) {
        echo "  - {$item['name']}: {$item['quantity']} x $" . number_format($item['price'], 0, ',', '.') . "\n";
    }
    
    // Probar el CheckoutService
    echo "\n🔍 Probando CheckoutService...\n";
    
    try {
        $checkoutService = app(CheckoutService::class);
        
        // Simular request para prepareCheckout
        $request = new Request();
        $request->setMethod('POST');
        $request->merge(['cart' => json_encode($cart)]);
        
        echo "  - Probando prepareCheckout...\n";
        $result = $checkoutService->prepareCheckout($request);
        
        if ($result['success']) {
            echo "    ✅ prepareCheckout exitoso\n";
            echo "    - Carrito: " . count($result['cart']) . " items\n";
            echo "    - Direcciones: " . count($result['direcciones']) . " disponibles\n";
            echo "    - Métodos de pago: " . count($result['metodosPago']) . " disponibles\n";
            
            // Verificar que el carrito se procesó correctamente
            echo "\n🔍 Verificando procesamiento del carrito...\n";
            foreach ($result['cart'] as $item) {
                $cantidad = $item['cantidad'] ?? $item['quantity'] ?? 'N/A';
                echo "  - Producto: {$item['name']}, Cantidad: {$cantidad}\n";
            }
            
            // Verificar que el carrito está en sesión
            echo "\n🔍 Verificando carrito en sesión...\n";
            $cartInSession = Session::get('cart', []);
            if (!empty($cartInSession)) {
                echo "    ✅ Carrito guardado en sesión: " . count($cartInSession) . " items\n";
            } else {
                echo "    ❌ Carrito no está en sesión\n";
            }
            
            // Probar la creación de un pedido simple
            echo "\n🔍 Probando creación de pedido...\n";
            
            // Obtener una dirección y método de pago válidos
            $direccion = $result['direcciones']->first();
            $metodoPago = $result['metodosPago']->first();
            
            if ($direccion && $metodoPago) {
                echo "  - Dirección seleccionada: {$direccion->nombre_destinatario}\n";
                echo "  - Método de pago: {$metodoPago->nombre}\n";
                
                // Simular request para processCheckout
                $checkoutRequest = new Request();
                $checkoutRequest->merge([
                    'direccion_id' => $direccion->direccion_id,
                    'metodo_pago_id' => $metodoPago->metodo_id,
                    'notas' => 'Test de checkout'
                ]);
                
                echo "  - Probando processCheckout...\n";
                
                try {
                    $pedidoResult = $checkoutService->processCheckout($checkoutRequest);
                    echo "    ✅ Pedido creado exitosamente\n";
                    echo "    - ID del pedido: {$pedidoResult['pedido_id']}\n";
                    echo "    - ID del pago: {$pedidoResult['pago_id']}\n";
                    echo "    - Mensaje: {$pedidoResult['message']}\n";
                    
                    // Verificar que el carrito sigue en sesión (no se limpió automáticamente)
                    echo "\n🔍 Verificando carrito después de processCheckout...\n";
                    $cartAfterProcess = Session::get('cart', []);
                    if (!empty($cartAfterProcess)) {
                        echo "    ✅ Carrito sigue en sesión: " . count($cartAfterProcess) . " items\n";
                        echo "    ℹ️ El carrito se limpiará solo después de la redirección exitosa\n";
                    } else {
                        echo "    ❌ Carrito fue limpiado prematuramente\n";
                    }
                    
                    // Limpiar el pedido de prueba
                    echo "\n🧹 Limpiando pedido de prueba...\n";
                    \App\Models\Pedido::destroy($pedidoResult['pedido_id']);
                    echo "    ✅ Pedido de prueba eliminado\n";
                    
                } catch (Exception $e) {
                    echo "    ❌ Error al procesar checkout: " . $e->getMessage() . "\n";
                    echo "    📍 Archivo: " . $e->getFile() . "\n";
                    echo "    📍 Línea: " . $e->getLine() . "\n";
                }
                
            } else {
                echo "  ⚠️ No se pudieron obtener dirección o método de pago válidos\n";
            }
            
        } else {
            echo "    ❌ prepareCheckout falló\n";
        }
        
    } catch (Exception $e) {
        echo "    ❌ Error en CheckoutService: " . $e->getMessage() . "\n";
        echo "    📍 Archivo: " . $e->getFile() . "\n";
        echo "    📍 Línea: " . $e->getLine() . "\n";
    }
    
    echo "\n✨ Test del flujo del checkout completado!\n";
    
} catch (Exception $e) {
    echo "❌ Error durante el test: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . "\n";
    echo "📍 Línea: " . $e->getLine() . "\n";
}
