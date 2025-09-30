<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Producto;
use App\Services\Business\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProbarCheckoutWeb extends Command
{
    protected $signature = 'checkout:probar-web {--usuario-id=1} {--producto-id=1}';
    protected $description = 'Prueba el acceso a la página de checkout para verificar que no hay errores 500';

    public function handle(): int
    {
        $this->info('🌐 Iniciando prueba de acceso a checkout web...');
        
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
            
            $this->info("✅ Usuario: {$usuario->nombre} (ID: {$usuario->id})");
            $this->info("✅ Producto: {$producto->nombre_producto} (ID: {$producto->id})");
            
            // Autenticar como el usuario
            Auth::login($usuario);
            $this->info("✅ Usuario autenticado correctamente");
            
            // Simular carrito en sesión
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
            
            // Crear instancia del servicio
            $checkoutService = app(CheckoutService::class);
            
            // Probar el método prepareCheckout
            $this->info("\n🛒 Probando prepareCheckout...");
            
            try {
                $request = new Request();
                $resultado = $checkoutService->prepareCheckout($request);
                
                $this->info("✅ prepareCheckout exitoso:");
                $this->info("   - Success: " . ($resultado['success'] ? 'true' : 'false'));
                $this->info("   - Cart items: " . count($resultado['cart']));
                $this->info("   - Direcciones: " . count($resultado['direcciones']));
                $this->info("   - Métodos de pago: " . count($resultado['metodosPago']));
                
            } catch (\Exception $e) {
                $this->error("❌ Error en prepareCheckout:");
                $this->error("   - Mensaje: " . $e->getMessage());
                $this->error("   - Archivo: " . $e->getFile() . ":" . $e->getLine());
                return 1;
            }
            
            // Probar verificar stock
            $this->info("\n📦 Probando verificarStock...");
            
            try {
                $stockData = $checkoutService->verificarStock($cart);
                
                $this->info("✅ verificarStock exitoso:");
                $this->info("   - Disponible: " . ($stockData['disponible'] ? 'true' : 'false'));
                if (isset($stockData['errores'])) {
                    $this->info("   - Errores: " . count($stockData['errores']));
                }
                if (isset($stockData['productos_info'])) {
                    $this->info("   - Productos verificados: " . count($stockData['productos_info']));
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error en verificarStock:");
                $this->error("   - Mensaje: " . $e->getMessage());
                return 1;
            }
            
            $this->info("\n✅ Prueba de checkout web completada exitosamente");
            $this->info("🎯 El checkout debería funcionar correctamente en el navegador");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error durante la prueba: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}