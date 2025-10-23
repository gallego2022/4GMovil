<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Producto;
use App\Observers\ProductoObserver;
use App\Services\RedisCacheService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar RedisCacheService como singleton
        $this->app->singleton(RedisCacheService::class, function ($app) {
            return new RedisCacheService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar el Observer para Producto
        Producto::observe(ProductoObserver::class);
    }
}
