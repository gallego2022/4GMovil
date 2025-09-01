<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssetCacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Solo aplicar cache a assets estáticos
        if ($this->isAsset($request->path())) {
            $this->setCacheHeaders($response, $request->path());
        }
        
        return $response;
    }
    
    /**
     * Verificar si la ruta es un asset estático
     */
    private function isAsset(string $path): bool
    {
        $assetExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'woff', 'woff2', 'ttf', 'eot'];
        
        foreach ($assetExtensions as $extension) {
            if (str_ends_with($path, '.' . $extension)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Establecer headers de cache según el tipo de asset
     */
    private function setCacheHeaders(Response $response, string $path): void
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        switch ($extension) {
            case 'css':
            case 'js':
                $this->setLongTermCache($response);
                break;
                
            case 'png':
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'svg':
            case 'webp':
                $this->setLongTermCache($response);
                break;
                
            case 'woff':
            case 'woff2':
            case 'ttf':
            case 'eot':
                $this->setLongTermCache($response);
                break;
                
            default:
                $this->setShortTermCache($response);
                break;
        }
    }
    
    /**
     * Establecer cache a largo plazo (1 año)
     */
    private function setLongTermCache(Response $response): void
    {
        $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        $response->headers->set('Expires', 'Thu, 31 Dec 2026 23:59:59 GMT');
        $response->headers->set('ETag', '');
        $response->headers->set('Last-Modified', '');
    }
    
    /**
     * Establecer cache a corto plazo (1 hora)
     */
    private function setShortTermCache(Response $response): void
    {
        $response->headers->set('Cache-Control', 'public, max-age=3600');
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
    }
}
