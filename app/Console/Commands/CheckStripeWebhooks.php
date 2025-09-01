<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\WebhookEndpoint;

class CheckStripeWebhooks extends Command
{
    protected $signature = 'stripe:check-webhooks';
    protected $description = 'Verificar webhooks configurados en Stripe';

    public function handle()
    {
        $this->info('🔍 Verificando webhooks de Stripe...');

        // Configurar Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Obtener todos los webhooks
            $webhooks = WebhookEndpoint::all(['limit' => 10]);
            
            if ($webhooks->data) {
                $this->info('📡 Webhooks encontrados:');
                
                foreach ($webhooks->data as $webhook) {
                    $this->newLine();
                    $this->line("🆔 ID: {$webhook->id}");
                    $this->line("📡 URL: {$webhook->url}");
                    $this->line("📊 Estado: " . ($webhook->status === 'enabled' ? '✅ Habilitado' : '❌ Deshabilitado'));
                    $this->line("📝 Descripción: {$webhook->description}");
                    
                    if ($webhook->enabled_events) {
                        $this->line("🎯 Eventos habilitados:");
                        foreach ($webhook->enabled_events as $event) {
                            $this->line("  • {$event}");
                        }
                    }
                    
                    $this->line("📅 Creado: " . date('Y-m-d H:i:s', $webhook->created));
                }
            } else {
                $this->warn('⚠️ No se encontraron webhooks configurados');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error al verificar webhooks: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
