<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\Pago;
use App\Models\WebhookEvent;

class TestStripeWebhookBehavior extends Command
{
    protected $signature = 'stripe:test-behavior {--pedido=1}';
    protected $description = 'Probar el comportamiento modificado de webhooks de Stripe';

    public function handle()
    {
        $pedidoId = $this->option('pedido');
        
        $this->info("🧪 Probando comportamiento de webhooks de Stripe...");
        $this->info("📦 Pedido ID: {$pedidoId}");
        
        // Buscar el pedido
        $pedido = Pedido::find($pedidoId);
        if (!$pedido) {
            $this->error("❌ Pedido {$pedidoId} no encontrado");
            return 1;
        }
        
        $this->info("✅ Pedido encontrado:");
        $this->info("   - Estado actual: {$pedido->estado_id}");
        $this->info("   - Usuario: " . ($pedido->usuario->nombre ?? 'N/A'));
        $this->info("   - Total: $" . number_format($pedido->total, 0, ',', '.'));
        
        // Verificar si tiene pago
        $pago = $pedido->pago;
        if ($pago) {
            $this->info("💰 Pago existente:");
            $this->info("   - Estado: {$pago->estado}");
            $this->info("   - Método: " . ($pago->metodoPago->nombre_metodo ?? 'N/A'));
            $this->info("   - Fecha: {$pago->fecha_pago}");
        } else {
            $this->info("💳 Sin pago registrado");
        }
        
        // Verificar webhooks existentes
        $webhooks = WebhookEvent::where('pedido_id', $pedidoId)->get();
        if ($webhooks->count() > 0) {
            $this->info("📡 Webhooks registrados:");
            foreach ($webhooks as $webhook) {
                $this->info("   - {$webhook->event_type} (Estado: {$webhook->status})");
            }
        } else {
            $this->info("📡 Sin webhooks registrados");
        }
        
        $this->info("");
        $this->info("🎯 Comportamiento esperado:");
        $this->info("   - El pedido debe mantener estado_id = 1 (Pendiente)");
        $this->info("   - Solo el estado del pago debe cambiar");
        $this->info("   - Los webhooks NO deben modificar el estado del pedido");
        
        return 0;
    }
}
