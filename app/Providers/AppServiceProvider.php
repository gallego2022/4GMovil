<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Producto;
use App\Observers\ProductoObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
