<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\WebhookEndpoint;

class SetupStripeWebhookNgrok extends Command
{
    protected $signature = 'stripe:setup-webhook-ngrok {--port=8000}';
    protected $description = 'Configurar webhook de Stripe usando ngrok';

    public function handle()
    {
        $this->info('🔧 Configurando webhook de Stripe con ngrok...');

        $port = $this->option('port');
        
        // Verificar si ngrok está ejecutándose
        $this->info("🔍 Verificando ngrok en puerto {$port}...");
        
        try {
            $ngrokUrl = $this->getNgrokUrl($port);
            
            if (!$ngrokUrl) {
                $this->error("❌ No se pudo obtener la URL de ngrok");
                $this->newLine();
                $this->info('📋 Para usar ngrok:');
                $this->info('1. Instala ngrok: https://ngrok.com/download');
                $this->info('2. Ejecuta: ngrok http ' . $port);
                $this->info('3. Copia la URL HTTPS que aparece');
                $this->info('4. Ejecuta este comando de nuevo');
                return 1;
            }

            $webhookUrl = $ngrokUrl . '/stripe/webhook';
            $this->info("📡 URL del webhook: {$webhookUrl}");

            // Configurar Stripe
            Stripe::setApiKey(config('services.stripe.secret'));

            // Crear webhook endpoint
            $webhook = WebhookEndpoint::create([
                'url' => $webhookUrl,
                'enabled_events' => [
                    'payment_intent.succeeded',
                    'payment_intent.payment_failed',
                    'payment_intent.canceled',
                    'charge.succeeded',
                    'charge.failed',
                ],
                'description' => 'Webhook para 4GMovil (ngrok)',
            ]);

            $this->info('✅ Webhook creado exitosamente');
            $this->info("🆔 ID del webhook: {$webhook->id}");
            $this->info("🔑 Secret del webhook: {$webhook->secret}");

            // Mostrar información para .env
            $this->newLine();
            $this->warn('📝 Agrega esto a tu archivo .env:');
            $this->line("STRIPE_WEBHOOK_SECRET={$webhook->secret}");

            // Probar el webhook
            $this->newLine();
            $this->info('🧪 Probando webhook...');
            $this->call('stripe:test-webhook-local', [
                '--event' => 'payment_intent.succeeded',
                '--pedido' => '1'
            ]);

        } catch (\Exception $e) {
            $this->error('❌ Error al crear webhook: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function getNgrokUrl($port)
    {
        try {
            // Intentar obtener la URL de ngrok desde su API
            $response = file_get_contents("http://localhost:4040/api/tunnels");
            
            if ($response) {
                $data = json_decode($response, true);
                
                if (isset($data['tunnels']) && !empty($data['tunnels'])) {
                    foreach ($data['tunnels'] as $tunnel) {
                        if ($tunnel['proto'] === 'https' && strpos($tunnel['config']['addr'], "localhost:{$port}") !== false) {
                            return $tunnel['public_url'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Si no se puede conectar, pedir al usuario que ingrese la URL manualmente
            $this->warn('⚠️ No se pudo detectar ngrok automáticamente');
            $this->info('📝 Por favor, ingresa la URL de ngrok manualmente:');
            
            $ngrokUrl = $this->ask('URL de ngrok (ej: https://abc123.ngrok.io):');
            
            if ($ngrokUrl && filter_var($ngrokUrl, FILTER_VALIDATE_URL)) {
                return $ngrokUrl;
            }
        }

        return null;
    }
}
