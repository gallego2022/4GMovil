<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WebhookEvent;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Log;

class ProcessFailedWebhooks extends Command
{
    protected $signature = 'webhooks:process-failed {--max-attempts=3} {--limit=10}';
    protected $description = 'Procesar webhooks fallidos con reintentos';

    public function handle()
    {
        $maxAttempts = $this->option('max-attempts');
        $limit = $this->option('limit');

        $this->info("🔄 Procesando webhooks fallidos (máximo {$maxAttempts} intentos, límite {$limit})...");

        $failedWebhooks = WebhookEvent::failed()
            ->where('attempts', '<', $maxAttempts)
            ->limit($limit)
            ->get();

        if ($failedWebhooks->isEmpty()) {
            $this->info('✅ No hay webhooks fallidos para procesar');
            return 0;
        }

        $this->info("📊 Encontrados {$failedWebhooks->count()} webhooks fallidos");

        $processed = 0;
        $stillFailed = 0;

        foreach ($failedWebhooks as $webhook) {
            $this->info("🔄 Procesando webhook {$webhook->stripe_event_id} (intento " . ($webhook->attempts + 1) . ")");

            try {
                // Marcar como procesando
                $webhook->update([
                    'status' => 'processing',
                    'attempts' => $webhook->attempts + 1,
                    'last_attempt_at' => now(),
                ]);

                // Procesar el webhook
                $this->processWebhook($webhook);

                // Marcar como procesado
                $webhook->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                    'error_message' => null,
                ]);

                $this->info("✅ Webhook {$webhook->stripe_event_id} procesado exitosamente");
                $processed++;

            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $this->error("❌ Error procesando webhook {$webhook->stripe_event_id}: {$errorMessage}");

                // Marcar como fallido
                $webhook->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                ]);

                $stillFailed++;
                Log::error("Webhook processing failed: {$webhook->stripe_event_id} - {$errorMessage}");
            }
        }

        $this->newLine();
        $this->info("📊 Resumen:");
        $this->info("  ✅ Procesados exitosamente: {$processed}");
        $this->info("  ❌ Aún fallidos: {$stillFailed}");

        return 0;
    }

    private function processWebhook(WebhookEvent $webhook)
    {
        $controller = new StripeController();
        
        // Crear request simulado
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');
        $request->headers->set('Stripe-Signature', 'retry_signature');
        
        $request->initialize(
            [], [], [], [], [],
            ['HTTP_CONTENT_TYPE' => 'application/json'],
            json_encode($webhook->payload)
        );

        // Procesar el webhook
        $response = $controller->webhook($request);
        
        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Webhook processing failed with status: ' . $response->getStatusCode());
        }
    }
}
