<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\StripeController;

class TestStripeWebhookLocal extends Command
{
    protected $signature = 'stripe:test-webhook-local {--event=payment_intent.succeeded} {--pedido=1}';
    protected $description = 'Probar webhook de Stripe localmente sin necesidad de URL pública';

    public function handle()
    {
        $this->info('🧪 Probando webhook de Stripe localmente...');

        $eventType = $this->option('event');
        $pedidoId = $this->option('pedido');
        
        $this->info("🎯 Evento a probar: {$eventType}");
        $this->info("📦 Pedido ID: {$pedidoId}");

        // Crear request simulado
        $request = $this->createMockRequest($eventType, $pedidoId);
        
        if (!$request) {
            $this->error("❌ Evento no soportado: {$eventType}");
            return 1;
        }

        try {
            // Instanciar el controlador y llamar al webhook
            $controller = new StripeController();
            $response = $controller->webhook($request);
            
            $this->info('✅ Webhook procesado correctamente');
            $this->info("📊 Código de respuesta: " . $response->getStatusCode());
            $this->info("📄 Respuesta: " . $response->getContent());

        } catch (\Exception $e) {
            $this->error('❌ Error al procesar webhook: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function createMockRequest($eventType, $pedidoId)
    {
        $payload = $this->getTestEventData($eventType, $pedidoId);
        
        if (!$payload) {
            return null;
        }

        // Convertir payload a JSON
        $jsonPayload = json_encode($payload);
        
        // Generar firma si tenemos el secret
        $signature = $this->generateSignature($jsonPayload);

        // Crear request simulado
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Stripe-Signature', $signature);
        
        // Establecer el contenido JSON directamente
        $request->initialize(
            [], // query
            [], // request
            [], // attributes
            [], // cookies
            [], // files
            ['HTTP_CONTENT_TYPE' => 'application/json'], // server
            $jsonPayload // content
        );

        return $request;
    }

    private function generateSignature($payload)
    {
        $secret = config('services.stripe.webhook.secret');
        
        if (!$secret) {
            // Si no hay secret, usar una firma de prueba
            return 't=' . time() . ',v1=test_signature';
        }

        // Generar timestamp
        $timestamp = time();
        
        // Crear la firma usando el método correcto de Stripe
        $signedPayload = $timestamp . '.' . $payload;
        $signature = hash_hmac('sha256', $signedPayload, $secret);
        
        // Formato de Stripe
        return 't=' . $timestamp . ',v1=' . $signature;
    }

    private function getTestEventData($eventType, $pedidoId)
    {
        $baseData = [
            'id' => 'evt_test_' . uniqid(),
            'object' => 'event',
            'api_version' => '2020-08-27',
            'created' => time(),
        ];

        switch ($eventType) {
            case 'payment_intent.succeeded':
                return array_merge($baseData, [
                    'type' => 'payment_intent.succeeded',
                    'data' => [
                        'object' => [
                            'id' => 'pi_test_' . uniqid(),
                            'object' => 'payment_intent',
                            'amount' => 7000000, // $70,000
                            'currency' => 'cop',
                            'status' => 'succeeded',
                            'metadata' => [
                                'pedido_id' => $pedidoId
                            ]
                        ]
                    ]
                ]);

            case 'payment_intent.payment_failed':
                return array_merge($baseData, [
                    'type' => 'payment_intent.payment_failed',
                    'data' => [
                        'object' => [
                            'id' => 'pi_test_' . uniqid(),
                            'object' => 'payment_intent',
                            'amount' => 7000000,
                            'currency' => 'cop',
                            'status' => 'requires_payment_method',
                            'metadata' => [
                                'pedido_id' => $pedidoId
                            ]
                        ]
                    ]
                ]);

            case 'payment_intent.canceled':
                return array_merge($baseData, [
                    'type' => 'payment_intent.canceled',
                    'data' => [
                        'object' => [
                            'id' => 'pi_test_' . uniqid(),
                            'object' => 'payment_intent',
                            'amount' => 7000000,
                            'currency' => 'cop',
                            'status' => 'canceled',
                            'metadata' => [
                                'pedido_id' => $pedidoId
                            ]
                        ]
                    ]
                ]);

            default:
                return null;
        }
    }
}
