<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Middleware\RequireAdminRole;
use App\Http\Middleware\RequireEmailVerification;
use App\Http\Middleware\AssetCacheMiddleware;
use App\Http\Middleware\PerformanceOptimization;
use App\Http\Middleware\ExceptionHandlerMiddleware;
use App\Http\Middleware\VerifyCsrfToken;

class TestMiddlewareStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'middleware:status {--middleware= : Test specific middleware}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check status of all registered middlewares';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando estado de middlewares...');
        $this->newLine();

        $specificMiddleware = $this->option('middleware');

        if ($specificMiddleware) {
            $this->checkSpecificMiddleware($specificMiddleware);
        } else {
            $this->checkAllMiddlewares();
        }

        $this->newLine();
        $this->info('✅ Verificación completada');
    }

    /**
     * Check all middlewares
     */
    private function checkAllMiddlewares()
    {
        $middlewares = [
            'RequireAdminRole' => 'admin',
            'RequireEmailVerification' => 'email.verification',
            'AssetCacheMiddleware' => 'asset.cache',
            'PerformanceOptimization' => 'performance',
            'ExceptionHandlerMiddleware' => 'exception.handler',
            'VerifyCsrfToken' => 'csrf'
        ];

        $results = [];

        foreach ($middlewares as $name => $alias) {
            $this->info("🔍 Verificando: {$name}");
            $result = $this->checkMiddleware($name, $alias);
            $results[$name] = $result;
            $this->displayResult($name, $result);
            $this->newLine();
        }

        $this->displaySummary($results);
    }

    /**
     * Check specific middleware
     */
    private function checkSpecificMiddleware($middlewareName)
    {
        $this->info("🔍 Verificando middleware específico: {$middlewareName}");
        
        $middlewares = [
            'RequireAdminRole' => 'admin',
            'RequireEmailVerification' => 'email.verification',
            'AssetCacheMiddleware' => 'asset.cache',
            'PerformanceOptimization' => 'performance',
            'ExceptionHandlerMiddleware' => 'exception.handler',
            'VerifyCsrfToken' => 'csrf'
        ];

        if (isset($middlewares[$middlewareName])) {
            $result = $this->checkMiddleware($middlewareName, $middlewares[$middlewareName]);
            $this->displayResult($middlewareName, $result);
        } else {
            $this->error("❌ Middleware '{$middlewareName}' no encontrado");
        }
    }

    /**
     * Check individual middleware
     */
    private function checkMiddleware($name, $alias)
    {
        try {
            switch ($name) {
                case 'RequireAdminRole':
                    return $this->checkRequireAdminRole();
                
                case 'RequireEmailVerification':
                    return $this->checkRequireEmailVerification();
                
                case 'AssetCacheMiddleware':
                    return $this->checkAssetCacheMiddleware();
                
                case 'PerformanceOptimization':
                    return $this->checkPerformanceOptimization();
                
                case 'ExceptionHandlerMiddleware':
                    return $this->checkExceptionHandlerMiddleware();
                
                case 'VerifyCsrfToken':
                    return $this->checkVerifyCsrfToken();
                
                default:
                    return [
                        'status' => 'error',
                        'message' => "Middleware '{$name}' no reconocido"
                    ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => "Error verificando {$name}: " . $e->getMessage()
            ];
        }
    }

    /**
     * Check RequireAdminRole middleware
     */
    private function checkRequireAdminRole()
    {
        try {
            $middleware = new RequireAdminRole();
            $request = Request::create('/admin/test', 'GET');
            
            $response = $middleware->handle($request, function ($request) {
                return response('Success');
            });
            
            if ($response->getStatusCode() === 302) {
                return ['status' => 'success', 'message' => 'Middleware funciona correctamente - redirige sin autenticación'];
            }
            
            return ['status' => 'warning', 'message' => 'Middleware responde pero comportamiento inesperado'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Check RequireEmailVerification middleware
     */
    private function checkRequireEmailVerification()
    {
        try {
            $middleware = new RequireEmailVerification();
            $request = Request::create('/test', 'GET');
            
            $response = $middleware->handle($request, function ($request) {
                return response('Success');
            });
            
            if ($response->getContent() === 'Success') {
                return ['status' => 'success', 'message' => 'Middleware funciona correctamente - permite acceso sin usuario'];
            }
            
            return ['status' => 'warning', 'message' => 'Middleware responde pero comportamiento inesperado'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Check AssetCacheMiddleware
     */
    private function checkAssetCacheMiddleware()
    {
        try {
            $middleware = new AssetCacheMiddleware();
            $request = Request::create('/css/test.css', 'GET');
            
            $response = $middleware->handle($request, function ($request) {
                return response('CSS content');
            });
            
            $cacheControl = $response->headers->get('Cache-Control');
            if ($cacheControl && str_contains($cacheControl, 'max-age=31536000')) {
                return ['status' => 'success', 'message' => 'Headers de cache configurados correctamente'];
            }
            
            return ['status' => 'warning', 'message' => 'Headers de cache no configurados'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Check PerformanceOptimization
     */
    private function checkPerformanceOptimization()
    {
        try {
            $middleware = new PerformanceOptimization();
            $request = Request::create('/test', 'GET');
            
            $response = $middleware->handle($request, function ($request) {
                return response('Test content');
            });
            
            $securityHeaders = [
                'X-Content-Type-Options',
                'X-Frame-Options',
                'X-XSS-Protection',
                'Referrer-Policy'
            ];

            $headersPresent = 0;
            foreach ($securityHeaders as $header) {
                if ($response->headers->get($header)) {
                    $headersPresent++;
                }
            }

            if ($headersPresent >= 3) {
                return ['status' => 'success', 'message' => "{$headersPresent}/4 headers de seguridad configurados"];
            }
            
            return ['status' => 'warning', 'message' => "Solo {$headersPresent}/4 headers de seguridad configurados"];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Check ExceptionHandlerMiddleware
     */
    private function checkExceptionHandlerMiddleware()
    {
        try {
            $middleware = new ExceptionHandlerMiddleware();
            $request = Request::create('/test', 'GET');
            
            $response = $middleware->handle($request, function ($request) {
                throw new \Exception('Test exception');
            });
            
            if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
                return ['status' => 'success', 'message' => 'Excepción manejada correctamente'];
            }
            
            return ['status' => 'warning', 'message' => 'Middleware responde pero no maneja excepción como esperado'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Check VerifyCsrfToken
     */
    private function checkVerifyCsrfToken()
    {
        try {
            $middleware = new VerifyCsrfToken();
            $request = Request::create('/stripe/webhook', 'POST');
            
            $response = $middleware->handle($request, function ($request) {
                return response('Success');
            });
            
            if ($response->getContent() === 'Success') {
                return ['status' => 'success', 'message' => 'Ruta excluida funciona correctamente'];
            }
            
            return ['status' => 'warning', 'message' => 'Middleware responde pero comportamiento inesperado'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Display result
     */
    private function displayResult($name, $result)
    {
        $status = $result['status'];
        $message = $result['message'];

        switch ($status) {
            case 'success':
                $this->info("  ✅ {$message}");
                break;
            case 'warning':
                $this->warn("  ⚠️  {$message}");
                break;
            case 'error':
                $this->error("  ❌ {$message}");
                break;
        }
    }

    /**
     * Display summary
     */
    private function displaySummary($results)
    {
        $this->info('📊 RESUMEN:');
        
        $successCount = 0;
        $warningCount = 0;
        $errorCount = 0;

        foreach ($results as $result) {
            switch ($result['status']) {
                case 'success':
                    $successCount++;
                    break;
                case 'warning':
                    $warningCount++;
                    break;
                case 'error':
                    $errorCount++;
                    break;
            }
        }

        $this->info("  ✅ Funcionando: {$successCount}");
        $this->warn("  ⚠️  Advertencias: {$warningCount}");
        $this->error("  ❌ Errores: {$errorCount}");
    }
}
