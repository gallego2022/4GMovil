<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\Pago;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\Http;

class GenerateLocalWebhook extends Command
{
    protected $signature = 'webhook:generate-local {--pedido=} {--event=payment_intent.succeeded}';
    protected $description = 'Generar webhook localmente para simular evento de Stripe';

    public function handle()
    {
        $pedidoId = $this->option('pedido');
        $eventType = $this->option('event');
        
        if (!$pedidoId) {
            $this->error("❌ Debes especificar el ID del pedido: --pedido=ID");
            return 1;
        }
        
        $this->info("🔄 Generando webhook local...");
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
        
        // Generar el webhook localmente
        $this->info("📡 Generando webhook local...");
        
        try {
            // Crear el evento del webhook directamente
            $webhookEvent = WebhookEvent::create([
                'stripe_event_id' => 'local_' . uniqid(),
                'event_type' => $eventType,
                'pedido_id' => $pedidoId,
                'payload' => [
                    'type' => $eventType,
                    'data' => [
                        'object' => [
                            'id' => 'pi_local_' . uniqid(),
                            'status' => 'succeeded',
                            'amount' => $pedido->total * 100,
                            'currency' => 'cop',
                            'metadata' => [
                                'pedido_id' => $pedidoId,
                                'usuario_id' => $pedido->usuario_id,
                            ]
                        ]
                    ]
                ],
                'status' => 'pending',
                'attempts' => 1,
                'last_attempt_at' => now(),
            ]);
            
            $this->info("✅ Webhook generado localmente con ID: {$webhookEvent->id}");
            
            // Procesar el webhook
            $this->info("🔄 Procesando webhook...");
            
            // Llamar al método del controlador directamente
            $stripeController = app(\App\Http\Controllers\StripeController::class);
            
            // Crear un request simulado
            $request = new \Illuminate\Http\Request();
            $request->merge([
                'type' => $eventType,
                'data' => [
                    'object' => [
                        'id' => 'pi_local_' . uniqid(),
                        'status' => 'succeeded',
                        'amount' => $pedido->total * 100,
                        'currency' => 'cop',
                        'metadata' => [
                            'pedido_id' => $pedidoId,
                            'usuario_id' => $pedido->usuario_id,
                        ]
                    ]
                ]
            ]);
            
            // Procesar el webhook
            $response = $stripeController->webhook($request);
            
            if ($response->getStatusCode() === 200) {
                $this->info("✅ Webhook procesado exitosamente");
                
                // Verificar el resultado
                $this->info("🔍 Verificando resultado...");
                
                // Recargar el pedido
                $pedido->refresh();
                
                // Verificar si se creó/actualizó el pago
                $pagoNuevo = $pedido->pago;
                if ($pagoNuevo) {
                    $this->info("💰 Pago después del webhook:");
                    $this->info("   - Estado: {$pagoNuevo->estado}");
                    $this->info("   - Fecha: {$pagoNuevo->fecha_pago}");
                    $this->info("   - Referencia: {$pagoNuevo->referencia}");
                }
                
                // Verificar webhook en la base de datos
                $webhookEvent->refresh();
                $this->info("📡 Webhook en BD:");
                $this->info("   - Estado: {$webhookEvent->status}");
                $this->info("   - Procesado: " . ($webhookEvent->processed_at ? 'Sí' : 'No'));
                
                // Verificar que el estado del pedido NO cambió
                $this->info("📋 Estado del pedido:");
                $this->info("   - Después del webhook: {$pedido->estado_id}");
                
                if ($pedido->estado_id == 1) {
                    $this->info("✅ Estado del pedido se mantuvo como 'Pendiente'");
                } else {
                    $this->warn("⚠️  Estado del pedido cambió a: {$pedido->estado_id}");
                }
                
            } else {
                $this->error("❌ Error al procesar webhook: " . $response->getStatusCode());
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
        
        $this->info("");
        $this->info("🎉 Webhook local generado y procesado exitosamente!");
        
        return 0;
    }
}
