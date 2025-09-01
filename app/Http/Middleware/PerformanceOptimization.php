<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class PerformanceOptimization
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
        $response = $next($request);

        // Add performance headers
        $this->addPerformanceHeaders($response);

        // Enable compression if configured
        if (Config::get('optimization.compression.enabled')) {
            $this->enableCompression($response);
        }

        // Add caching headers for static assets
        if ($this->isStaticAsset($request)) {
            $this->addCachingHeaders($response);
        }

        return $response;
    }

    /**
     * Add performance optimization headers
     */
    private function addPerformanceHeaders($response)
    {
        // Security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Performance headers
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Enable HTTP/2 Server Push hints for critical resources
        if ($this->shouldAddServerPush($response)) {
            $this->addServerPushHeaders($response);
        }
    }

    /**
     * Enable compression for text-based responses
     */
    private function enableCompression($response)
    {
        $contentType = $response->headers->get('Content-Type', '');
        
        if ($this->isCompressible($contentType)) {
            // Enable gzip compression
            if (Config::get('optimization.compression.gzip')) {
                $response->headers->set('Content-Encoding', 'gzip');
            }
            
            // Enable Brotli compression if available
            if (Config::get('optimization.compression.brotli') && function_exists('brotli_compress')) {
                $response->headers->set('Content-Encoding', 'br');
            }
        }
    }

    /**
     * Add caching headers for static assets
     */
    private function addCachingHeaders($response)
    {
        $maxAge = 31536000; // 1 year for static assets
        
        $response->headers->set('Cache-Control', "public, max-age={$maxAge}, immutable");
        $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + $maxAge));
        $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T'));
    }

    /**
     * Check if the request is for a static asset
     */
    private function isStaticAsset(Request $request)
    {
        $path = $request->path();
        $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot'];
        
        foreach ($staticExtensions as $ext) {
            if (str_ends_with($path, '.' . $ext)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if content type is compressible
     */
    private function isCompressible($contentType)
    {
        $compressibleTypes = [
            'text/html',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'application/xml',
            'text/xml',
            'text/plain'
        ];
        
        foreach ($compressibleTypes as $type) {
            if (str_contains($contentType, $type)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if server push headers should be added
     */
    private function shouldAddServerPush($response)
    {
        $contentType = $response->headers->get('Content-Type', '');
        return str_contains($contentType, 'text/html');
    }

    /**
     * Add HTTP/2 Server Push headers for critical resources
     */
    private function addServerPushHeaders($response)
    {
        $criticalResources = [
            '/css/app.css',
            '/js/app.js',
            '/img/Logo_2.png'
        ];
        
        $pushHeaders = [];
        foreach ($criticalResources as $resource) {
            $pushHeaders[] = "<{$resource}>; rel=preload; as=style";
        }
        
        if (!empty($pushHeaders)) {
            $response->headers->set('Link', implode(', ', $pushHeaders));
        }
    }
} 