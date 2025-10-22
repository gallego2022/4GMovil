<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\RedisCacheService;
use Illuminate\Support\Facades\Log;

class CacheInvalidationMiddleware
{
    protected $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Solo invalidar caché en métodos que modifican datos
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->invalidateRelevantCache($request);
        }

        return $response;
    }

    /**
     * Invalida caché relevante basado en la ruta
     */
    private function invalidateRelevantCache(Request $request)
    {
        $route = $request->route()->getName();
        
        try {
            // Invalidar caché basado en la ruta
            if (str_contains($route, 'productos')) {
                $this->cacheService->clearProductos();
                Log::info('Caché de productos invalidado', ['route' => $route]);
            }
            
            if (str_contains($route, 'inventario')) {
                $this->cacheService->clearInventario();
                Log::info('Caché de inventario invalidado', ['route' => $route]);
            }
            
            if (str_contains($route, 'alertas')) {
                $this->cacheService->clearAlertas();
                Log::info('Caché de alertas invalidado', ['route' => $route]);
            }
            
            if (str_contains($route, 'dashboard') || str_contains($route, 'admin')) {
                $this->cacheService->clearDashboard();
                Log::info('Caché de dashboard invalidado', ['route' => $route]);
            }
            
            // Invalidar caché de pedidos si se modifica el inventario
            if (str_contains($route, 'pedidos') || str_contains($route, 'checkout')) {
                $this->cacheService->clearInventario();
                $this->cacheService->clearAlertas();
                Log::info('Caché relacionado con pedidos invalidado', ['route' => $route]);
            }
            
        } catch (\Exception $e) {
            Log::warning('Error invalidando caché', [
                'route' => $route,
                'error' => $e->getMessage()
            ]);
        }
    }
}
