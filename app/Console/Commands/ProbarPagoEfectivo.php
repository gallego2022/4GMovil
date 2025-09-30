<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\MetodoPago;
use App\Models\Pedido;
use App\Services\Business\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProbarPagoEfectivo extends Command
{
    protected $signature = 'pago:probar-efectivo {--usuario-id=1} {--producto-id=1}';
    protected $description = 'Prueba el flujo de pago en efectivo para verificar que el stock se reserva correctamente';

    public function handle(): int
    {
        $this->info('💰 Iniciando prueba de pago en efectivo...');
        
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
            
            $this->info("✅ Usuario: {$usuario->nombre} (ID: {$usuario->id})");
            $this->info("✅ Producto: {$producto->nombre_producto} (ID: {$producto->id})");
            $this->info("✅ Stock inicial: {$producto->stock}");
            $this->info("✅ Método de pago: {$metodoEfectivo->nombre}");
            
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
                    'cantidad' => 2,
                    'quantity' => 2,
                    'precio' => $producto->precio
                ]
            ];
            
            Session::put('cart', $cart);
            $this->info("✅ Carrito simulado en sesión");
            
            // 2. Simular datos de checkout
            $checkoutData = [
                'direccion_id' => 1, // Asumiendo que existe
                'metodo_pago_id' => $metodoEfectivo->metodo_id,
                'notas' => 'Prueba de pago en efectivo'
            ];
            
            // 3. Procesar checkout (esto debería reservar stock, no descontarlo)
            $this->info("\n🛒 Procesando checkout con pago en efectivo...");
            
            $request = new Request($checkoutData);
            $resultado = $checkoutService->processCheckout($request);
            
            $this->info("Resultado del checkout:");
            $this->info(json_encode($resultado, JSON_PRETTY_PRINT));
            
            // 4. Verificar que el stock NO se descontó inmediatamente
            $productoDespues = Producto::find($productoId);
            $this->info("\n📊 Verificando stock después del checkout:");
            $this->info("Stock inicial: {$producto->stock}");
            $this->info("Stock después: {$productoDespues->stock}");
            
            if ($productoDespues->stock == $producto->stock) {
                $this->info("✅ CORRECTO: El stock NO se descontó inmediatamente");
            } else {
                $this->error("❌ ERROR: El stock se descontó inmediatamente");
                return 1;
            }
            
            // 5. Verificar que se creó el pedido
            $pedido = Pedido::where('usuario_id', $usuarioId)->latest()->first();
            if ($pedido) {
                $this->info("✅ Pedido creado: ID {$pedido->pedido_id}");
                $this->info("Estado del pedido: {$pedido->estado_id} (1=pendiente)");
                
                // 6. Simular confirmación del pedido
                $this->info("\n✅ Confirmando pedido...");
                $resultadoConfirmacion = $checkoutService->confirmarPedido($pedido->pedido_id);
                
                $this->info("Resultado de confirmación:");
                $this->info(json_encode($resultadoConfirmacion, JSON_PRETTY_PRINT));
                
                // 7. Verificar que AHORA SÍ se descontó el stock
                $productoFinal = Producto::find($productoId);
                $this->info("\n📊 Verificando stock después de confirmar:");
                $this->info("Stock inicial: {$producto->stock}");
                $this->info("Stock después de confirmar: {$productoFinal->stock}");
                
                $stockEsperado = $producto->stock - 2; // Se pidieron 2 unidades
                if ($productoFinal->stock == $stockEsperado) {
                    $this->info("✅ CORRECTO: El stock se descontó correctamente después de confirmar");
                } else {
                    $this->error("❌ ERROR: El stock no se descontó correctamente después de confirmar");
                    return 1;
                }
                
                // 8. Verificar estado del pedido
                $pedidoActualizado = Pedido::find($pedido->pedido_id);
                $this->info("Estado del pedido después de confirmar: {$pedidoActualizado->estado_id} (2=confirmado)");
                
            } else {
                $this->error("❌ No se creó el pedido");
                return 1;
            }
            
            $this->info("\n✅ Prueba de pago en efectivo completada exitosamente");
            $this->info("🎯 El flujo funciona correctamente:");
            $this->info("   - Stock se reserva al crear el pedido");
            $this->info("   - Stock se descuenta solo al confirmar el pago");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error durante la prueba: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}