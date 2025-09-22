<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\Pago;
use App\Services\StripeService;

class SimulateStripePayment extends Command
{
    protected $signature = 'test:simulate-stripe-payment {pedido_id}';
    protected $description = 'Simular un pago exitoso de Stripe para probar el inventario';

    public function handle()
    {
        $pedidoId = $this->argument('pedido_id');
        
        $this->info("🧪 Simulando pago exitoso de Stripe para pedido {$pedidoId}...");
        
        $pedido = Pedido::with(['detalles.producto', 'detalles.variante', 'usuario'])->find($pedidoId);
        
        if (!$pedido) {
            $this->error("❌ Pedido {$pedidoId} no encontrado");
            return 1;
        }
        
        $this->info("✅ Pedido encontrado:");
        $this->info("   - Estado actual: {$pedido->estado_id}");
        $this->info("   - Usuario: " . ($pedido->usuario->nombre ?? 'N/A'));
        $this->info("   - Total: $" . number_format($pedido->total, 0, ',', '.'));
        $this->info("   - Detalles: " . $pedido->detalles->count());
        
        // Crear un pago simulado
        $pago = Pago::create([
            'pedido_id' => $pedido->pedido_id,
            'metodo_pago_id' => 1, // Stripe
            'monto' => $pedido->total,
            'estado' => 'pendiente',
            'referencia_externa' => 'pi_test_' . uniqid(),
            'fecha_pago' => now()
        ]);
        
        $this->info("✅ Pago simulado creado: {$pago->pago_id}");
        
        // Simular Payment Intent
        $paymentIntent = (object) [
            'id' => 'pi_test_' . uniqid(),
            'status' => 'succeeded',
            'amount' => $pedido->total * 100, // Stripe usa centavos
            'currency' => 'cop'
        ];
        
        $this->info("✅ Payment Intent simulado: {$paymentIntent->id}");
        
        // Usar reflexión para acceder al método privado
        $stripeService = new StripeService();
        $reflection = new \ReflectionClass($stripeService);
        $method = $reflection->getMethod('handleSuccessfulPayment');
        $method->setAccessible(true);
        
        try {
            $this->info("🔄 Ejecutando handleSuccessfulPayment...");
            $method->invoke($stripeService, $pedido, $pago, $paymentIntent);
            $this->info("✅ handleSuccessfulPayment ejecutado exitosamente");
        } catch (\Exception $e) {
            $this->error("❌ Error al ejecutar handleSuccessfulPayment: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }
        
        // Verificar movimientos de inventario
        $movimientosInventario = \App\Models\MovimientoInventario::where('pedido_id', $pedidoId)->count();
        $movimientosVariantes = \App\Models\MovimientoInventario::whereNotNull('variante_id')->whereHas('variante', function($query) use ($pedidoId) {
            $query->whereHas('detallesPedido', function($q) use ($pedidoId) {
                $q->where('pedido_id', $pedidoId);
            });
        })->count();
        
        $this->info("📊 Resultados:");
        $this->info("   - Movimientos de inventario: {$movimientosInventario}");
        $this->info("   - Movimientos de variantes: {$movimientosVariantes}");
        
        // Mostrar los movimientos creados
        if ($movimientosInventario > 0) {
            $this->info("📋 Movimientos de inventario:");
            $movimientos = \App\Models\MovimientoInventario::where('pedido_id', $pedidoId)->get();
            foreach ($movimientos as $movimiento) {
                $this->info("   - {$movimiento->tipo}: {$movimiento->cantidad} unidades");
            }
        }
        
        if ($movimientosVariantes > 0) {
            $this->info("📋 Movimientos de variantes:");
            $movimientos = \App\Models\MovimientoInventario::whereNotNull('variante_id')->whereHas('variante', function($query) use ($pedidoId) {
                $query->whereHas('detallesPedido', function($q) use ($pedidoId) {
                    $q->where('pedido_id', $pedidoId);
                });
            })->get();
            foreach ($movimientos as $movimiento) {
                $this->info("   - {$movimiento->tipo}: {$movimiento->cantidad} unidades (Variante: {$movimiento->variante->nombre})");
            }
        }
        
        return 0;
    }
}
