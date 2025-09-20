<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\Pago;
use App\Services\StripeService;
use Illuminate\Support\Facades\Log;

class TestStripeDirect extends Command
{
    protected $signature = 'test:stripe-direct {pedido_id}';
    protected $description = 'Probar directamente el flujo de Stripe para un pedido específico';

    public function handle()
    {
        $pedidoId = $this->argument('pedido_id');
        
        $this->info("Probando flujo directo de Stripe para pedido #{$pedidoId}");
        
        // Buscar el pedido
        $pedido = Pedido::with(['detalles.producto', 'detalles.variante', 'estado'])->find($pedidoId);
        
        if (!$pedido) {
            $this->error("Pedido #{$pedidoId} no encontrado");
            return 1;
        }
        
        $this->info("Pedido encontrado:");
        $this->info("- ID: {$pedido->pedido_id}");
        $this->info("- Estado actual: {$pedido->estado->nombre} (ID: {$pedido->estado_id})");
        $this->info("- Total: $" . number_format($pedido->total, 0, ',', '.'));
        
        // Buscar el pago asociado
        $pago = Pago::where('pedido_id', $pedidoId)->first();
        
        if (!$pago) {
            $this->error("No se encontró pago para el pedido #{$pedidoId}");
            return 1;
        }
        
        $this->info("Pago encontrado:");
        $this->info("- ID: {$pago->pago_id}");
        $this->info("- Estado: {$pago->estado}");
        $this->info("- Referencia externa: {$pago->referencia_externa}");
        
        // Simular un PaymentIntent exitoso
        $paymentIntent = (object) [
            'id' => 'pi_test_' . time(),
            'amount' => $pedido->total * 100, // Convertir a centavos
            'currency' => 'usd',
            'status' => 'succeeded',
            'metadata' => [
                'pedido_id' => $pedidoId
            ]
        ];
        
        $this->info("Simulando PaymentIntent exitoso...");
        
        // Crear instancia del StripeService
        $stripeService = new StripeService();
        
        // Usar reflexión para acceder al método privado
        $reflection = new \ReflectionClass($stripeService);
        $method = $reflection->getMethod('handleSuccessfulPayment');
        $method->setAccessible(true);
        
        try {
            $this->info("Ejecutando handleSuccessfulPayment...");
            $method->invoke($stripeService, $pedido, $pago, $paymentIntent);
            $this->info("✅ handleSuccessfulPayment ejecutado exitosamente");
            
            // Recargar el pedido para ver los cambios
            $pedido->refresh();
            $this->info("Estado del pedido después: {$pedido->estado->nombre}");
            
            // Verificar movimientos de inventario
            $movimientosProducto = \App\Models\MovimientoInventario::where('pedido_id', $pedidoId)->get();
            $movimientosVariante = \App\Models\MovimientoInventarioVariante::whereHas('variante', function($query) use ($pedidoId) {
                $query->whereHas('producto', function($q) use ($pedidoId) {
                    $q->whereHas('detallesPedido', function($dq) use ($pedidoId) {
                        $dq->where('pedido_id', $pedidoId);
                    });
                });
            })->get();
            
            $this->info("Movimientos de inventario después de la prueba:");
            $this->info("- Movimientos de producto: " . $movimientosProducto->count());
            $this->info("- Movimientos de variante: " . $movimientosVariante->count());
            
            if ($movimientosProducto->count() > 0) {
                $this->info("Movimientos de producto:");
                foreach ($movimientosProducto as $movimiento) {
                    $this->info("  - Tipo: {$movimiento->tipo_movimiento}");
                    $this->info("  - Cantidad: {$movimiento->cantidad}");
                    $this->info("  - Motivo: {$movimiento->motivo}");
                }
            }
            
            if ($movimientosVariante->count() > 0) {
                $this->info("Movimientos de variante:");
                foreach ($movimientosVariante as $movimiento) {
                    $this->info("  - Tipo: {$movimiento->tipo}");
                    $this->info("  - Cantidad: {$movimiento->cantidad}");
                    $this->info("  - Motivo: {$movimiento->motivo}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Error al ejecutar handleSuccessfulPayment: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
