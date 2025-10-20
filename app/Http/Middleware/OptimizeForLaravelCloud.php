<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class OptimizeForLaravelCloud
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Optimizaciones específicas para Laravel Cloud
        $this->optimizeForLaravelCloud($request);

        $response = $next($request);

        // Optimizar respuesta
        $this->optimizeResponse($response);

        return $response;
    }

    /**
     * Optimizaciones específicas para Laravel Cloud
     */
    private function optimizeForLaravelCloud(Request $request): void
    {
        // Configurar headers de caché para assets estáticos
        if ($this->isStaticAsset($request)) {
            $this->setCacheHeaders();
        }

        // Optimizar consultas de base de datos
        $this->optimizeDatabaseQueries();

        // Configurar Redis para máximo rendimiento
        $this->optimizeRedis();
    }

    /**
     * Verificar si es un asset estático
     */
    private function isStaticAsset(Request $request): bool
    {
        $path = $request->path();
        return preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $path);
    }

    /**
     * Configurar headers de caché
     */
    private function setCacheHeaders(): void
    {
        $headers = [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s', time()) . ' GMT',
        ];

        foreach ($headers as $key => $value) {
            header("$key: $value");
        }
    }

    /**
     * Optimizar consultas de base de datos
     */
    private function optimizeDatabaseQueries(): void
    {
        // Configurar conexión de base de datos para máximo rendimiento
        config([
            'database.connections.mysql.options' => [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ]
        ]);
    }

    /**
     * Optimizar Redis
     */
    private function optimizeRedis(): void
    {
        try {
            if (Redis::ping()) {
                // Configurar Redis para máximo rendimiento
                Redis::config('set', 'maxmemory-policy', 'allkeys-lru');
                Redis::config('set', 'timeout', '300');
                Redis::config('set', 'tcp-keepalive', '60');
            }
        } catch (\Exception $e) {
            // Redis no disponible, continuar sin optimizaciones
            Log::debug('Redis no disponible para optimizaciones: ' . $e->getMessage());
        }
    }

    /**
     * Optimizar respuesta HTTP
     */
    private function optimizeResponse($response): void
    {
        // Comprimir respuesta si es posible
        if (function_exists('gzencode') && !$response->headers->has('Content-Encoding')) {
            $content = $response->getContent();
            if (strlen($content) > 1024) { // Solo comprimir si es mayor a 1KB
                $compressed = gzencode($content, 6); // Nivel de compresión balanceado
                if ($compressed !== false) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Vary', 'Accept-Encoding');
                }
            }
        }

        // Configurar headers de seguridad y rendimiento
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Configurar headers de caché para páginas dinámicas
        if (!$this->isStaticAsset(request())) {
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
    }
}
