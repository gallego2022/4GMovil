<?php

namespace App\Providers;

use App\Models\EstadoPedido;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Observers\ProductoObserver;
use App\Services\Infrastructure\RedisCacheService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar RedisCacheService como singleton
        $this->app->singleton(RedisCacheService::class, function ($app) {
            return new RedisCacheService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar el Observer para Producto
        Producto::observe(ProductoObserver::class);

        $this->ensureStripePaymentMethodConfigured();
        $this->ensureDefaultOrderStatuses();
    }

    private function ensureStripePaymentMethodConfigured(): void
    {
        try {
            if (! Schema::hasTable('metodos_pago')) {
                return;
            }

            $publishableKey = config('services.stripe.key');
            $secretKey = config('services.stripe.secret');

            if (! $publishableKey || ! $secretKey) {
                Log::warning('Stripe no se configuró porque faltan STRIPE_KEY o STRIPE_SECRET en el entorno.');

                return;
            }

            MetodoPago::updateOrCreate([
                'nombre' => 'Stripe',
            ], [
                'descripcion' => 'Pago con tarjeta de crédito/débito a través de Stripe',
                'configuracion' => [
                    'publishable_key' => $publishableKey,
                    'secret_key' => $secretKey,
                    'webhook_secret' => config('services.stripe.webhook.secret'),
                    'webhook_tolerance' => config('services.stripe.webhook.tolerance', 300),
                    'mode' => app()->environment('production') ? 'live' : 'test',
                ],
                'estado' => true,
            ]);

            Log::info('Método de pago Stripe verificado/creado automáticamente.');
        } catch (\Throwable $e) {
            Log::error('No se pudo registrar Stripe automáticamente', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function ensureDefaultOrderStatuses(): void
    {
        try {
            if (! Schema::hasTable('estados_pedido')) {
                return;
            }

            $defaultStates = [
                [
                    'nombre' => 'Pendiente',
                    'descripcion' => 'Pedido recibido y pendiente de procesamiento',
                    'color' => '#fbbf24',
                    'orden' => 1,
                ],
                [
                    'nombre' => 'Confirmado',
                    'descripcion' => 'Pedido confirmado y en preparación',
                    'color' => '#3b82f6',
                    'orden' => 2,
                ],
                [
                    'nombre' => 'Cancelado',
                    'descripcion' => 'Pedido cancelado por el cliente o sistema',
                    'color' => '#ef4444',
                    'orden' => 3,
                ],
            ];

            foreach ($defaultStates as $state) {
                EstadoPedido::updateOrCreate(
                    ['nombre' => $state['nombre']],
                    [
                        'descripcion' => $state['descripcion'],
                        'color' => $state['color'],
                        'orden' => $state['orden'],
                        'estado' => true,
                    ]
                );
            }

            Log::info('Estados de pedido verificados/creados automáticamente.');
        } catch (\Throwable $e) {
            Log::error('No se pudieron registrar los estados de pedido automáticamente', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
