<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\WebhookEndpoint;

class SetupStripeWebhookManual extends Command
{
    protected $signature = 'stripe:setup-webhook-manual';
    protected $description = 'Configurar webhook de Stripe manualmente';

    public function handle()
    {
        $this->info('🔧 Configurando webhook de Stripe manualmente...');
        $this->newLine();
        
        $this->info('📋 Instrucciones:');
        $this->info('1. Instala ngrok desde: https://ngrok.com/download');
        $this->info('2. Ejecuta en una nueva terminal: ngrok http 8000');
        $this->info('3. Copia la URL HTTPS que aparece (ej: https://abc123.ngrok.io)');
        $this->newLine();

        // Solicitar URL de ngrok
        $ngrokUrl = $this->ask('🔗 Ingresa la URL de ngrok (ej: https://abc123.ngrok.io):');
        
        if (!$ngrokUrl || !filter_var($ngrokUrl, FILTER_VALIDATE_URL)) {
            $this->error('❌ URL inválida');
            return 1;
        }

        $webhookUrl = $ngrokUrl . '/stripe/webhook';
        $this->info("📡 URL del webhook: {$webhookUrl}");

        // Confirmar
        if (!$this->confirm('¿Deseas crear el webhook con esta URL?')) {
            $this->info('❌ Operación cancelada');
            return 0;
        }

        try {
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
            if ($this->confirm('¿Deseas probar el webhook ahora?')) {
                $this->info('🧪 Probando webhook...');
                $this->call('stripe:test-webhook-local', [
                    '--event' => 'payment_intent.succeeded',
                    '--pedido' => '1'
                ]);
            }

        } catch (\Exception $e) {
            $this->error('❌ Error al crear webhook: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
