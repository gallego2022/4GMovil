<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\Pago;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\Http;

class SimulateStripeWebhook extends Command
{
    protected $signature = 'stripe:simulate-webhook {--pedido=1} {--event=payment_intent.succeeded}';
    protected $description = 'Simular el envío de un webhook desde Stripe';

    public function handle()
    {
        $pedidoId = $this->option('pedido');
        $eventType = $this->option('event');
        
        $this->info("🔄 Simulando webhook de Stripe...");
        $this->info("📦 Pedido ID: {$pedidoId}");
        $this->info("🎯 Evento: {$eventType}");
        
        // Buscar el pedido
        $pedido = Pedido::find($pedidoId);
        if (!$pedido) {
            $this->error("❌ Pedido {$pedidoId} no encontrado");
            return 1;
        }
        
        $this->info("✅ Pedido encontrado:");
        $this->info("   - Estado: {$pedido->estado_id}");
        $this->info("   - Total: $" . number_format($pedido->total, 0, ',', '.'));
        
        // Verificar si ya tiene pago
        $pagoExistente = $pedido->pago;
        if ($pagoExistente) {
            $this->info("💰 Pago existente:");
            $this->info("   - Estado: {$pagoExistente->estado}");
            $this->info("   - Fecha: {$pagoExistente->fecha_pago}");
        } else {
            $this->info("💳 Sin pago registrado");
        }
        
        // Simular el webhook llamando directamente al endpoint
        $this->info("📡 Enviando webhook simulado...");
        
        try {
            $response = Http::post(url('/stripe/webhook'), [
                'type' => $eventType,
                'data' => [
                    'object' => [
                        'id' => 'pi_simulated_' . uniqid(),
                        'status' => 'succeeded',
                        'amount' => $pedido->total * 100, // En centavos
                        'currency' => 'cop',
                        'metadata' => [
                            'pedido_id' => $pedidoId,
                            'usuario_id' => $pedido->usuario_id,
                        ]
                    ]
                ]
            ]);
            
            if ($response->successful()) {
                $this->info("✅ Webhook procesado exitosamente");
                $this->info("📊 Código de respuesta: " . $response->status());
                
                // Verificar el resultado
                $this->info("🔍 Verificando resultado...");
                
                // Recargar el pedido
                $pedido->refresh();
                
                // Verificar si se creó el pago
                $pagoNuevo = $pedido->pago;
                if ($pagoNuevo) {
                    $this->info("💰 Pago creado/actualizado:");
                    $this->info("   - Estado: {$pagoNuevo->estado}");
                    $this->info("   - Fecha: {$pagoNuevo->fecha_pago}");
                    $this->info("   - Referencia: {$pagoNuevo->referencia}");
                }
                
                // Verificar webhook en la base de datos
                $webhookEvent = WebhookEvent::where('pedido_id', $pedidoId)
                    ->where('event_type', $eventType)
                    ->latest()
                    ->first();
                
                if ($webhookEvent) {
                    $this->info("📡 Webhook registrado en BD:");
                    $this->info("   - ID: {$webhookEvent->id}");
                    $this->info("   - Estado: {$webhookEvent->status}");
                    $this->info("   - Procesado: " . ($webhookEvent->processed_at ? 'Sí' : 'No'));
                }
                
                // Verificar que el estado del pedido NO cambió
                $this->info("📋 Estado del pedido:");
                $this->info("   - Antes: " . ($pedido->getOriginal('estado_id') ?? 'N/A'));
                $this->info("   - Después: {$pedido->estado_id}");
                
                if ($pedido->estado_id == 1) {
                    $this->info("✅ Estado del pedido se mantuvo como 'Pendiente'");
                } else {
                    $this->warn("⚠️  Estado del pedido cambió a: {$pedido->estado_id}");
                }
                
            } else {
                $this->error("❌ Error al procesar webhook");
                $this->error("📊 Código: " . $response->status());
                $this->error("📄 Respuesta: " . $response->body());
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
