<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\WebhookSignature;

class GenerateStripeSignature extends Command
{
    protected $signature = 'stripe:generate-signature {payload} {--secret=}';
    protected $description = 'Generar firma de Stripe para webhooks';

    public function handle()
    {
        $payload = $this->argument('payload');
        $secret = $this->option('secret') ?: config('services.stripe.webhook.secret');

        if (!$secret) {
            $this->error('❌ No se encontró el secret del webhook');
            $this->info('📝 Agrega STRIPE_WEBHOOK_SECRET a tu archivo .env');
            return 1;
        }

        try {
            // Generar timestamp
            $timestamp = time();
            
            // Crear la firma
            $signedPayload = $timestamp . '.' . $payload;
            $signature = hash_hmac('sha256', $signedPayload, $secret);
            
            // Formato de Stripe
            $stripeSignature = 't=' . $timestamp . ',v1=' . $signature;
            
            $this->info('✅ Firma generada exitosamente');
            $this->newLine();
            $this->line("🕒 Timestamp: {$timestamp}");
            $this->line("🔑 Firma: {$stripeSignature}");
            $this->newLine();
            $this->warn('📝 Usa esta firma en el header Stripe-Signature:');
            $this->line($stripeSignature);

        } catch (\Exception $e) {
            $this->error('❌ Error al generar firma: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
