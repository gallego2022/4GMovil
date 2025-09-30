<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Producto;
use App\Services\Business\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProbarVerificarStock extends Command
{
    protected $signature = 'checkout:probar-verificar-stock {--usuario-id=1} {--producto-id=1}';
    protected $description = 'Prueba específicamente el endpoint de verificar stock para identificar errores';

    public function handle(): int
    {
        $this->info('📦 Iniciando prueba específica de verificar stock...');
        
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
            $this->info("✅ Stock actual: {$producto->stock}");
            
            // Autenticar como el usuario
            Auth::login($usuario);
            $this->info("✅ Usuario autenticado correctamente");
            
            // Crear instancia del servicio
            $checkoutService = app(CheckoutService::class);
            
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
            
            // Probar verificarStock directamente
            $this->info("\n📦 Probando verificarStock directamente...");
            
            try {
                $stockData = $checkoutService->verificarStock($cart);
                
                $this->info("✅ verificarStock exitoso:");
                $this->info("   - Disponible: " . ($stockData['disponible'] ? 'true' : 'false'));
                if (isset($stockData['errores'])) {
                    $this->info("   - Errores: " . count($stockData['errores']));
                    if (!empty($stockData['errores'])) {
                        foreach ($stockData['errores'] as $error) {
                            $this->info("     * {$error}");
                        }
                    }
                }
                if (isset($stockData['productos_info'])) {
                    $this->info("   - Productos verificados: " . count($stockData['productos_info']));
                    foreach ($stockData['productos_info'] as $info) {
                        $this->info("     * Producto: {$info['nombre']} - Disponible: " . ($info['disponible'] ? 'true' : 'false'));
                    }
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error en verificarStock:");
                $this->error("   - Mensaje: " . $e->getMessage());
                $this->error("   - Archivo: " . $e->getFile() . ":" . $e->getLine());
                $this->error("   - Stack trace:");
                $this->error($e->getTraceAsString());
                return 1;
            }
            
            // Probar el endpoint HTTP
            $this->info("\n🌐 Probando endpoint HTTP verificar-stock...");
            
            try {
                // Simular request HTTP
                $request = new Request();
                $request->merge(['cart' => $cart]);
                
                // Llamar al controlador directamente
                $controller = app(\App\Http\Controllers\Cliente\CheckoutController::class);
                $response = $controller->verificarStock($request);
                
                $this->info("✅ Endpoint HTTP exitoso:");
                $this->info("   - Status: " . $response->getStatusCode());
                $this->info("   - Content: " . $response->getContent());
                
            } catch (\Exception $e) {
                $this->error("❌ Error en endpoint HTTP:");
                $this->error("   - Mensaje: " . $e->getMessage());
                $this->error("   - Archivo: " . $e->getFile() . ":" . $e->getLine());
                $this->error("   - Stack trace:");
                $this->error($e->getTraceAsString());
                return 1;
            }
            
            $this->info("\n✅ Prueba de verificar stock completada exitosamente");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error durante la prueba: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}