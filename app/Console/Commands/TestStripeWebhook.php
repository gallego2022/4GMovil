<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestStripeWebhook extends Command
{
    protected $signature = 'stripe:test-webhook {--event=payment_intent.succeeded}';
    protected $description = 'Probar webhook de Stripe con eventos simulados';

    public function handle()
    {
        $this->info('🧪 Probando webhook de Stripe...');

        $eventType = $this->option('event');
        $webhookUrl = config('app.url') . '/stripe/webhook';
        
        $this->info("📡 URL del webhook: {$webhookUrl}");
        $this->info("🎯 Evento a probar: {$eventType}");

        // Datos de prueba
        $testData = $this->getTestEventData($eventType);
        
        if (!$testData) {
            $this->error("❌ Evento no soportado: {$eventType}");
            return 1;
        }

        try {
            // Simular evento de Stripe
            $response = Http::post($webhookUrl, $testData['payload']);
            
            if ($response->successful()) {
                $this->info('✅ Webhook respondió correctamente');
                $this->info("📊 Código de respuesta: {$response->status()}");
                $this->info("📄 Respuesta: " . $response->body());
            } else {
                $this->error('❌ Webhook falló');
                $this->error("📊 Código de respuesta: {$response->status()}");
                $this->error("📄 Respuesta: " . $response->body());
            }

        } catch (\Exception $e) {
            $this->error('❌ Error al probar webhook: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function getTestEventData($eventType)
    {
        $baseData = [
            'id' => 'evt_test_' . uniqid(),
            'object' => 'event',
            'api_version' => '2020-08-27',
            'created' => time(),
        ];

        switch ($eventType) {
            case 'payment_intent.succeeded':
                return [
                    'payload' => array_merge($baseData, [
                        'type' => 'payment_intent.succeeded',
                        'data' => [
                            'object' => [
                                'id' => 'pi_test_' . uniqid(),
                                'object' => 'payment_intent',
                                'amount' => 7000000, // $70,000
                                'currency' => 'cop',
                                'status' => 'succeeded',
                                'metadata' => [
                                    'pedido_id' => '1'
                                ]
                            ]
                        ]
                    ])
                ];

            case 'payment_intent.payment_failed':
                return [
                    'payload' => array_merge($baseData, [
                        'type' => 'payment_intent.payment_failed',
                        'data' => [
                            'object' => [
                                'id' => 'pi_test_' . uniqid(),
                                'object' => 'payment_intent',
                                'amount' => 7000000,
                                'currency' => 'cop',
                                'status' => 'requires_payment_method',
                                'metadata' => [
                                    'pedido_id' => '1'
                                ]
                            ]
                        ]
                    ])
                ];

            default:
                return null;
        }
    }
}
