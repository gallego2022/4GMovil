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

class ProbarCheckoutEfectivo extends Command
{
    protected $signature = 'checkout:probar-efectivo-web {--usuario-id=1} {--producto-id=1}';
    protected $description = 'Prueba el flujo completo de checkout con pago en efectivo para identificar errores';

    public function handle(): int
    {
        $this->info('💰 Iniciando prueba completa de checkout con pago en efectivo...');
        
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
                $this->warn("⚠️ No hay direcciones para el usuario. Usando dirección existente...");
                $direccion = Direccion::first();
                if (!$direccion) {
                    $this->error("❌ No hay direcciones en el sistema. Creando una...");
                    $direccion = Direccion::create([
                        'usuario_id' => $usuarioId,
                        'nombre_destinatario' => 'Usuario de Prueba',
                        'nombre_direccion' => 'Dirección de Prueba',
                        'direccion_completa' => 'Calle 123 #45-67',
                        'ciudad' => 'Bogotá',
                        'departamento' => 'Cundinamarca',
                        'codigo_postal' => '110111',
                        'telefono' => '3001234567',
                        'es_principal' => true
                    ]);
                    $this->info("✅ Dirección de prueba creada");
                } else {
                    // Actualizar la dirección existente para el usuario de prueba
                    $direccion->update(['usuario_id' => $usuarioId]);
                    $this->info("✅ Dirección existente asignada al usuario");
                }
            }
            
            $this->info("✅ Usuario: {$usuario->nombre} (ID: {$usuario->id})");
            $this->info("✅ Producto: {$producto->nombre_producto} (ID: {$producto->id})");
            $this->info("✅ Stock inicial: {$producto->stock}");
            $this->info("✅ Método de pago: {$metodoEfectivo->nombre}");
            $this->info("✅ Dirección: {$direccion->nombre_direccion}");
            
            // Autenticar como el usuario
            Auth::login($usuario);
            $this->info("✅ Usuario autenticado correctamente");
            
            // Crear instancia del servicio
            $checkoutService = app(CheckoutService::class);
            
            // 1. Simular carrito en sesión
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
            
            // 2. Probar prepareCheckout
            $this->info("\n🛒 Probando prepareCheckout...");
            
            try {
                $request = new Request();
                $resultadoPrepare = $checkoutService->prepareCheckout($request);
                
                $this->info("✅ prepareCheckout exitoso:");
                $this->info("   - Success: " . ($resultadoPrepare['success'] ? 'true' : 'false'));
                $this->info("   - Cart items: " . count($resultadoPrepare['cart']));
                $this->info("   - Direcciones: " . count($resultadoPrepare['direcciones']));
                $this->info("   - Métodos de pago: " . count($resultadoPrepare['metodosPago']));
                
            } catch (\Exception $e) {
                $this->error("❌ Error en prepareCheckout:");
                $this->error("   - Mensaje: " . $e->getMessage());
                $this->error("   - Archivo: " . $e->getFile() . ":" . $e->getLine());
                return 1;
            }
            
            // 3. Simular datos de checkout con pago en efectivo
            $checkoutData = [
                'direccion_id' => $direccion->direccion_id,
                'metodo_pago_id' => $metodoEfectivo->metodo_id,
                'notas' => 'Prueba de pago en efectivo'
            ];
            
            $this->info("\n💳 Probando processCheckout con pago en efectivo...");
            
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
                
                // Verificar que el stock NO se descontó
                $productoDespues = Producto::find($productoId);
                $this->info("\n📊 Verificando stock después del checkout:");
                $this->info("   - Stock inicial: {$producto->stock}");
                $this->info("   - Stock después: {$productoDespues->stock}");
                
                if ($productoDespues->stock == $producto->stock) {
                    $this->info("✅ CORRECTO: El stock NO se descontó (está reservado)");
                } else {
                    $this->error("❌ ERROR: El stock se descontó cuando debería estar reservado");
                }
                
                // Verificar que se creó el pedido
                if (isset($resultadoProcess['pedido_id'])) {
                    $pedido = \App\Models\Pedido::find($resultadoProcess['pedido_id']);
                    if ($pedido) {
                        $this->info("\n📋 Pedido creado:");
                        $this->info("   - ID: {$pedido->pedido_id}");
                        $this->info("   - Estado: {$pedido->estado_id} (1=pendiente)");
                        $this->info("   - Total: {$pedido->total}");
                        
                        // Verificar pago
                        $pago = $pedido->pago;
                        if ($pago) {
                            $this->info("   - Pago estado: {$pago->estado}");
                            $this->info("   - Método pago: " . ($pago->metodoPago->nombre ?? 'N/A'));
                        }
                    }
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error en processCheckout:");
                $this->error("   - Mensaje: " . $e->getMessage());
                $this->error("   - Archivo: " . $e->getFile() . ":" . $e->getLine());
                $this->error("   - Stack trace:");
                $this->error($e->getTraceAsString());
                return 1;
            }
            
            $this->info("\n✅ Prueba de checkout con pago en efectivo completada exitosamente");
            $this->info("🎯 El flujo debería funcionar correctamente en el navegador");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error durante la prueba: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}