<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\WebhookEndpoint;

class CleanupStripeWebhooks extends Command
{
    protected $signature = 'stripe:cleanup-webhooks';
    protected $description = 'Eliminar todos los webhooks existentes de Stripe';

    public function handle()
    {
        $this->info('🧹 Limpiando webhooks existentes de Stripe...');

        // Configurar Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Obtener todos los webhooks
            $webhooks = WebhookEndpoint::all(['limit' => 10]);
            
            if ($webhooks->data) {
                $this->info('📡 Webhooks encontrados: ' . count($webhooks->data));
                
                foreach ($webhooks->data as $webhook) {
                    $this->info("🗑️ Eliminando webhook: {$webhook->id}");
                    
                    try {
                        $webhook->delete();
                        $this->info("✅ Webhook {$webhook->id} eliminado exitosamente");
                    } catch (\Exception $e) {
                        $this->error("❌ Error al eliminar webhook {$webhook->id}: " . $e->getMessage());
                    }
                }
                
                $this->info('🎉 Limpieza completada');
            } else {
                $this->warn('⚠️ No se encontraron webhooks para eliminar');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error al limpiar webhooks: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
