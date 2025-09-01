<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\WebhookEndpoint;

class SetupStripeWebhook extends Command
{
    protected $signature = 'stripe:setup-webhook {--url=}';
    protected $description = 'Configurar webhook de Stripe';

    public function handle()
    {
        $this->info('🔧 Configurando webhook de Stripe...');

        // Configurar Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        // URL del webhook
        $webhookUrl = $this->option('url');
        
        if (!$webhookUrl) {
            $this->warn('⚠️ No se proporcionó URL del webhook');
            $this->newLine();
            $this->info('📋 Para desarrollo local, necesitas:');
            $this->info('1. Instalar ngrok: https://ngrok.com/download');
            $this->info('2. Ejecutar: ngrok http 8000');
            $this->info('3. Usar la URL de ngrok como parámetro --url');
            $this->newLine();
            $this->info('Ejemplo:');
            $this->line('php artisan stripe:setup-webhook --url=https://abc123.ngrok.io/stripe/webhook');
            return 1;
        }
        
        $this->info("📡 URL del webhook: {$webhookUrl}");

        try {
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
                'description' => 'Webhook para 4GMovil',
            ]);

            $this->info('✅ Webhook creado exitosamente');
            $this->info("🆔 ID del webhook: {$webhook->id}");
            $this->info("🔑 Secret del webhook: {$webhook->secret}");

            // Mostrar información para .env
            $this->newLine();
            $this->warn('📝 Agrega esto a tu archivo .env:');
            $this->line("STRIPE_WEBHOOK_SECRET={$webhook->secret}");

        } catch (\Exception $e) {
            $this->error('❌ Error al crear webhook: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
