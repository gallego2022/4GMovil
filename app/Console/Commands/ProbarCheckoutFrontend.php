<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\MetodoPago;
use App\Models\Direccion;
use App\Services\Business\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProbarCheckoutFrontend extends Command
{
    protected $signature = 'checkout:probar-frontend {--usuario-id=2} {--producto-id=1}';
    protected $description = 'Prueba el flujo completo de checkout simulando el frontend';

    public function handle(): int
    {
        $this->info('🌐 Iniciando prueba completa de checkout simulando frontend...');
        
        try {
            // Obtener parámetros
            $usuarioId = $this->option('usuario-id');
            $productoId = $this->option('producto-id');
            
            // Verificar que el usuario existe
            $usuario = Usuario::find($usuarioId);
            if (!$usuario) {
                $this->error("❌ Usuario con ID {$usuarioId} no encontrado");
                return 1;
            }
            
            // Verificar que el producto existe
            $producto = Producto::find($productoId);
            if (!$producto) {
                $this->error("❌ Producto con ID {$productoId} no encontrado");
                return 1;
            }
            
            // Verificar que existe método de pago en efectivo
            $metodoEfectivo = MetodoPago::where('nombre', 'Efectivo')->first();
            if (!$metodoEfectivo) {
                $this->error("❌ Método de pago 'Efectivo' no encontrado");
                return 1;
            }
            
            // Verificar que existe al menos una dirección
            $direccion = Direccion::where('usuario_id', $usuarioId)->first();
            if (!$direccion) {
                $this->error("❌ No hay direcciones para el usuario {$usuarioId}");
                return 1;
            }
            
            $this->info("✅ Usuario: {$usuario->nombre} (ID: {$usuario->id})");
            $this->info("✅ Producto: {$producto->nombre_producto} (ID: {$producto->id})");
            $this->info("✅ Stock inicial: {$producto->stock}");
            $this->info("✅ Método de pago: {$metodoEfectivo->nombre}");
            $this->info("✅ Dirección: {$direccion->nombre_destinatario}");
            
            // Autenticar como el usuario
            Auth::login($usuario);
            $this->info("✅ Usuario autenticado correctamente");
            
            // Crear instancia del servicio
            $checkoutService = app(CheckoutService::class);
            
            // 1. Simular carrito en sesión (como lo hace el frontend)
            $cart = [
                [
                    'id' => $productoId,
                    'producto_id' => $productoId,
                    'cantidad' => 1,
                    'quantity' => 1,
                    'precio' => $producto->precio
                ]
            ];
            
            Session::put('cart', $cart);
            $this->info("✅ Carrito simulado en sesión");
            
            // 2. Probar prepareCheckout (como lo hace la vista)
            $this->info("\n🛒 Probando prepareCheckout (vista checkout)...");
            
            try {
                $request = new Request();
                $resultadoPrepare = $checkoutService->prepareCheckout($request);
                
                $this->info("✅ prepareCheckout exitoso:");
                $this->info("   - Success: " . ($resultadoPrepare['success'] ? 'true' : 'false'));
                $this->info("   - Cart items: " . count($resultadoPrepare['cart']));
                $this->info("   - Direcciones: " . count($resultadoPrepare['direcciones']));
                $this->info("   - Métodos de pago: " . count($resultadoPrepare['metodosPago']));
                
                // Verificar que hay direcciones y métodos de pago
                if (empty($resultadoPrepare['direcciones'])) {
                    $this->error("❌ No hay direcciones disponibles para el checkout");
                    return 1;
                }
                
                if (empty($resultadoPrepare['metodosPago'])) {
                    $this->error("❌ No hay métodos de pago disponibles para el checkout");
                    return 1;
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error en prepareCheckout:");
                $this->error("   - Mensaje: " . $e->getMessage());
                $this->error("   - Archivo: " . $e->getFile() . ":" . $e->getLine());
                return 1;
            }
            
            // 3. Probar verificarStock (como lo hace el JavaScript)
            $this->info("\n📦 Probando verificarStock (JavaScript)...");
            
            try {
                $stockData = $checkoutService->verificarStock($cart);
                
                $this->info("✅ verificarStock exitoso:");
                $this->info("   - Disponible: " . ($stockData['disponible'] ? 'true' : 'false'));
                if (isset($stockData['errores'])) {
                    $this->info("   - Errores: " . count($stockData['errores']));
                }
                
                // Simular la respuesta del endpoint HTTP
                $httpResponse = [
                    'success' => true,
                    'data' => $stockData
                ];
                
                $this->info("✅ Respuesta HTTP simulada:");
                $this->info("   - Success: " . ($httpResponse['success'] ? 'true' : 'false'));
                $this->info("   - Data disponible: " . ($httpResponse['data']['disponible'] ? 'true' : 'false'));
                
                // Simular la lógica del JavaScript
                if (!$httpResponse['success'] || !$httpResponse['data']['disponible']) {
                    $this->error("❌ El JavaScript detectaría un problema de stock");
                    return 1;
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error en verificarStock:");
                $this->error("   - Mensaje: " . $e->getMessage());
                return 1;
            }
            
            // 4. Simular datos de checkout con pago en efectivo (como lo envía el formulario)
            $checkoutData = [
                'direccion_id' => $direccion->direccion_id,
                'metodo_pago_id' => $metodoEfectivo->metodo_id,
                'notas' => 'Prueba de pago en efectivo desde frontend'
            ];
            
            $this->info("\n💳 Probando processCheckout (formulario frontend)...");
            
            try {
                $request = new Request($checkoutData);
                $resultadoProcess = $checkoutService->processCheckout($request);
                
                $this->info("✅ processCheckout exitoso:");
                $this->info("   - Success: " . ($resultadoProcess['success'] ? 'true' : 'false'));
                $this->info("   - Pedido ID: " . ($resultadoProcess['pedido_id'] ?? 'N/A'));
                $this->info("   - Pago ID: " . ($resultadoProcess['pago_id'] ?? 'N/A'));
                $this->info("   - Redirect to Stripe: " . ($resultadoProcess['redirect_to_stripe'] ? 'true' : 'false'));
                $this->info("   - Requiere confirmación: " . ($resultadoProcess['requiere_confirmacion'] ? 'true' : 'false'));
                $this->info("   - Redirect to confirm: " . ($resultadoProcess['redirect_to_confirm'] ? 'true' : 'false'));
                $this->info("   - Mensaje: " . ($resultadoProcess['message'] ?? 'N/A'));
                
                // Verificar que el flujo es correcto para pago en efectivo
                if (!$resultadoProcess['redirect_to_confirm']) {
                    $this->error("❌ ERROR: Debería redirigir a confirmación para pago en efectivo");
                    return 1;
                }
                
                if ($resultadoProcess['redirect_to_stripe']) {
                    $this->error("❌ ERROR: No debería redirigir a Stripe para pago en efectivo");
                    return 1;
                }
                
                // Verificar que el stock NO se descontó
                $productoDespues = Producto::find($productoId);
                $this->info("\n📊 Verificando stock después del checkout:");
                $this->info("   - Stock inicial: {$producto->stock}");
                $this->info("   - Stock después: {$productoDespues->stock}");
                
                if ($productoDespues->stock == $producto->stock) {
                    $this->info("✅ CORRECTO: El stock NO se descontó (está reservado)");
                } else {
                    $this->error("❌ ERROR: El stock se descontó cuando debería estar reservado");
                    return 1;
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error en processCheckout:");
                $this->error("   - Mensaje: " . $e->getMessage());
                $this->error("   - Archivo: " . $e->getFile() . ":" . $e->getLine());
                $this->error("   - Stack trace:");
                $this->error($e->getTraceAsString());
                return 1;
            }
            
            $this->info("\n✅ Prueba de checkout frontend completada exitosamente");
            $this->info("🎯 El flujo debería funcionar correctamente en el navegador");
            $this->info("🔗 URL de checkout: http://localhost:8000/checkout");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error durante la prueba: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}