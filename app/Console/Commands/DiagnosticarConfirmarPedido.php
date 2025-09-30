<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\Business\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DiagnosticarConfirmarPedido extends Command
{
    protected $signature = 'pedido:diagnosticar-confirmar {--usuario-id=1} {--pedido-id=}';
    protected $description = 'Diagnostica el error al confirmar un pedido';

    public function handle(): int
    {
        $this->info('🔍 Iniciando diagnóstico de confirmación de pedido...');
        
        try {
            // Obtener parámetros
            $usuarioId = $this->option('usuario-id');
            $pedidoId = $this->option('pedido-id');
            
            // Verificar que el usuario existe
            $usuario = Usuario::find($usuarioId);
            if (!$usuario) {
                $this->error("❌ Usuario con ID {$usuarioId} no encontrado");
                return 1;
            }
            
            $this->info("✅ Usuario: {$usuario->nombre} (ID: {$usuario->id})");
            
            // Autenticar como el usuario
            Auth::login($usuario);
            $this->info("✅ Usuario autenticado correctamente");
            
            // Si no se especifica pedido, buscar el último
            if (!$pedidoId) {
                $pedido = Pedido::where('usuario_id', $usuarioId)
                    ->where('estado_id', 1) // pendiente
                    ->latest()
                    ->first();
                
                if (!$pedido) {
                    $this->error("❌ No se encontró ningún pedido pendiente para el usuario");
                    return 1;
                }
                
                $pedidoId = $pedido->pedido_id;
            }
            
            $this->info("✅ Pedido ID: {$pedidoId}");
            
            // Obtener el pedido
            $pedido = Pedido::find($pedidoId);
            if (!$pedido) {
                $this->error("❌ Pedido con ID {$pedidoId} no encontrado");
                return 1;
            }
            
            $this->info("✅ Pedido encontrado:");
            $this->info("   - Usuario ID: {$pedido->usuario_id}");
            $this->info("   - Estado ID: {$pedido->estado_id}");
            $this->info("   - Total: {$pedido->total}");
            
            // Verificar detalles del pedido
            $detalles = $pedido->detalles;
            $this->info("✅ Detalles del pedido: " . $detalles->count() . " items");
            
            foreach ($detalles as $detalle) {
                $this->info("   - Producto ID: {$detalle->producto_id}, Cantidad: {$detalle->cantidad}");
                if ($detalle->variante_id) {
                    $this->info("     Variante ID: {$detalle->variante_id}");
                }
            }
            
            // Verificar pago
            $pago = $pedido->pago;
            if ($pago) {
                $this->info("✅ Pago encontrado:");
                $this->info("   - Estado: {$pago->estado}");
                $this->info("   - Método ID: {$pago->metodo_id}");
            } else {
                $this->warn("⚠️ No se encontró pago para este pedido");
            }
            
            // Crear instancia del servicio
            $checkoutService = app(CheckoutService::class);
            
            // Intentar confirmar el pedido
            $this->info("\n🔄 Intentando confirmar el pedido...");
            
            try {
                $resultado = $checkoutService->confirmarPedido($pedidoId);
                
                $this->info("✅ Confirmación exitosa:");
                $this->info(json_encode($resultado, JSON_PRETTY_PRINT));
                
                // Verificar estado después de confirmar
                $pedidoActualizado = Pedido::find($pedidoId);
                $this->info("\n📊 Estado después de confirmar:");
                $this->info("   - Estado ID: {$pedidoActualizado->estado_id}");
                
                if ($pedidoActualizado->pago) {
                    $this->info("   - Estado del pago: {$pedidoActualizado->pago->estado}");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error al confirmar pedido:");
                $this->error("   - Mensaje: " . $e->getMessage());
                $this->error("   - Archivo: " . $e->getFile() . ":" . $e->getLine());
                $this->error("   - Stack trace:");
                $this->error($e->getTraceAsString());
                return 1;
            }
            
            $this->info("\n✅ Diagnóstico completado exitosamente");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error durante el diagnóstico: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}