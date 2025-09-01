<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Middleware\RequireAdminRole;
use App\Http\Middleware\RequireEmailVerification;
use App\Http\Middleware\AssetCacheMiddleware;
use App\Http\Middleware\PerformanceOptimization;
use App\Http\Middleware\ExceptionHandlerMiddleware;

class TestMiddlewareQuick extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'middleware:quick-test {--middleware= : Test specific middleware}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quick test for custom middlewares';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Prueba rápida de middlewares personalizados...');
        $this->newLine();

        $specificMiddleware = $this->option('middleware');

        if ($specificMiddleware) {
            $this->testSpecificMiddleware($specificMiddleware);
        } else {
            $this->testAllMiddlewares();
        }

        $this->newLine();
        $this->info('✅ Prueba completada');
    }

    /**
     * Test all middlewares
     */
    private function testAllMiddlewares()
    {
        $middlewares = [
            'RequireAdminRole' => [$this, 'testRequireAdminRole'],
            'RequireEmailVerification' => [$this, 'testRequireEmailVerification'],
            'AssetCacheMiddleware' => [$this, 'testAssetCacheMiddleware'],
            'PerformanceOptimization' => [$this, 'testPerformanceOptimization'],
            'ExceptionHandlerMiddleware' => [$this, 'testExceptionHandlerMiddleware']
        ];

        $results = [];

        foreach ($middlewares as $name => $test) {
            $this->info("🔍 Probando: {$name}");
            try {
                $result = call_user_func($test);
                $results[$name] = $result;
                $this->displayResult($name, $result);
            } catch (\Exception $e) {
                $results[$name] = ['status' => 'error', 'message' => $e->getMessage()];
                $this->error("❌ {$name}: Error - " . $e->getMessage());
            }
            $this->newLine();
        }

        $this->displaySummary($results);
    }

    /**
     * Test specific middleware
     */
    private function testSpecificMiddleware($middlewareName)
    {
        $this->info("🔍 Probando middleware específico: {$middlewareName}");
        
        $middlewares = [
            'RequireAdminRole' => [$this, 'testRequireAdminRole'],
            'RequireEmailVerification' => [$this, 'testRequireEmailVerification'],
            'AssetCacheMiddleware' => [$this, 'testAssetCacheMiddleware'],
            'PerformanceOptimization' => [$this, 'testPerformanceOptimization'],
            'ExceptionHandlerMiddleware' => [$this, 'testExceptionHandlerMiddleware']
        ];

        if (isset($middlewares[$middlewareName])) {
            $result = call_user_func($middlewares[$middlewareName]);
            $this->displayResult($middlewareName, $result);
        } else {
            $this->error("❌ Middleware '{$middlewareName}' no encontrado");
        }
    }

    /**
     * Test RequireAdminRole
     */
    private function testRequireAdminRole()
    {
        $middleware = new RequireAdminRole();
        $request = Request::create('/admin/test', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return response('Success');
        });
        
        if ($response->getStatusCode() === 302) {
            return ['status' => 'success', 'message' => '✅ Redirige correctamente sin autenticación'];
        }
        
        return ['status' => 'warning', 'message' => '⚠️ Comportamiento inesperado'];
    }

    /**
     * Test RequireEmailVerification
     */
    private function testRequireEmailVerification()
    {
        $middleware = new RequireEmailVerification();
        $request = Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return response('Success');
        });
        
        if ($response->getContent() === 'Success') {
            return ['status' => 'success', 'message' => '✅ Permite acceso sin usuario (correcto)'];
        }
        
        return ['status' => 'warning', 'message' => '⚠️ Comportamiento inesperado'];
    }

    /**
     * Test AssetCacheMiddleware
     */
    private function testAssetCacheMiddleware()
    {
        $middleware = new AssetCacheMiddleware();
        $request = Request::create('/css/test.css', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return response('CSS content');
        });
        
        $cacheControl = $response->headers->get('Cache-Control');
        if ($cacheControl && str_contains($cacheControl, 'max-age=31536000')) {
            return ['status' => 'success', 'message' => '✅ Headers de cache configurados correctamente'];
        }
        
        return ['status' => 'warning', 'message' => '⚠️ Headers de cache no configurados'];
    }

    /**
     * Test PerformanceOptimization
     */
    private function testPerformanceOptimization()
    {
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
            return ['status' => 'success', 'message' => "✅ {$headersPresent}/4 headers de seguridad configurados"];
        }
        
        return ['status' => 'warning', 'message' => "⚠️ Solo {$headersPresent}/4 headers de seguridad configurados"];
    }

    /**
     * Test ExceptionHandlerMiddleware
     */
    private function testExceptionHandlerMiddleware()
    {
        $middleware = new ExceptionHandlerMiddleware();
        $request = Request::create('/test', 'GET');
        
        try {
            $response = $middleware->handle($request, function ($request) {
                throw new \Exception('Test exception');
            });
            
            if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
                return ['status' => 'success', 'message' => '✅ Excepción manejada correctamente'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => '❌ Error manejando excepción: ' . $e->getMessage()];
        }
        
        return ['status' => 'warning', 'message' => '⚠️ Comportamiento inesperado'];
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
                $this->info("  {$message}");
                break;
            case 'warning':
                $this->warn("  {$message}");
                break;
            case 'error':
                $this->error("  {$message}");
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
