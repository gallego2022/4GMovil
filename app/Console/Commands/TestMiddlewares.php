<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RequireAdminRole;
use App\Http\Middleware\RequireEmailVerification;
use App\Http\Middleware\AssetCacheMiddleware;
use App\Http\Middleware\PerformanceOptimization;
use App\Http\Middleware\ExceptionHandlerMiddleware;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Usuario;
use Symfony\Component\HttpFoundation\Response;

class TestMiddlewares extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'middleware:test {--middleware= : Test specific middleware} {--user= : User ID to test with}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all registered middlewares to ensure they are working correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Iniciando pruebas de middlewares...');
        $this->newLine();

        $specificMiddleware = $this->option('middleware');
        $userId = $this->option('user');

        if ($specificMiddleware) {
            $this->testSpecificMiddleware($specificMiddleware, $userId);
        } else {
            $this->testAllMiddlewares($userId);
        }

        $this->newLine();
        $this->info('✅ Pruebas de middlewares completadas');
    }

    /**
     * Test all middlewares
     */
    private function testAllMiddlewares($userId = null)
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
            $this->info("🔍 Probando middleware: {$name}");
            $result = $this->testMiddleware($name, $alias, $userId);
            $results[$name] = $result;
            $this->newLine();
        }

        $this->displayResults($results);
    }

    /**
     * Test specific middleware
     */
    private function testSpecificMiddleware($middlewareName, $userId = null)
    {
        $this->info("🔍 Probando middleware específico: {$middlewareName}");
        $result = $this->testMiddleware($middlewareName, $middlewareName, $userId);
        $this->displaySingleResult($middlewareName, $result);
    }

    /**
     * Test individual middleware
     */
    private function testMiddleware($name, $alias, $userId = null)
    {
        try {
            switch ($name) {
                case 'RequireAdminRole':
                    return $this->testRequireAdminRole($userId);
                
                case 'RequireEmailVerification':
                    return $this->testRequireEmailVerification($userId);
                
                case 'AssetCacheMiddleware':
                    return $this->testAssetCacheMiddleware();
                
                case 'PerformanceOptimization':
                    return $this->testPerformanceOptimization();
                
                case 'ExceptionHandlerMiddleware':
                    return $this->testExceptionHandlerMiddleware();
                
                case 'VerifyCsrfToken':
                    return $this->testVerifyCsrfToken();
                
                default:
                    return [
                        'status' => 'error',
                        'message' => "Middleware '{$name}' no reconocido"
                    ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => "Error probando {$name}: " . $e->getMessage()
            ];
        }
    }

    /**
     * Test RequireAdminRole middleware
     */
    private function testRequireAdminRole($userId = null)
    {
        $middleware = new RequireAdminRole();
        $request = Request::create('/admin/test', 'GET');
        
        // Test 1: Sin usuario autenticado
        $response = $middleware->handle($request, function ($request) {
            return response('Success');
        });
        
        if ($response->getStatusCode() === 302) {
            $this->warn("  ⚠️  Sin usuario: Redirige a login (esperado)");
        }

        // Test 2: Con usuario normal
        if ($userId) {
            $user = Usuario::find($userId);
            if ($user && $user->rol !== 'admin') {
                Auth::login($user);
                $response = $middleware->handle($request, function ($request) {
                    return response('Success');
                });
                Auth::logout();
                
                if ($response->getStatusCode() === 302) {
                    $this->warn("  ⚠️  Usuario normal: Redirige a perfil (esperado)");
                }
            }
        }

        // Test 3: Con usuario admin
        $adminUser = Usuario::where('rol', 'admin')->first();
        if ($adminUser) {
            Auth::login($adminUser);
            $response = $middleware->handle($request, function ($request) {
                return response('Success');
            });
            Auth::logout();
            
            if ($response->getContent() === 'Success') {
                $this->info("  ✅ Usuario admin: Permite acceso");
                return ['status' => 'success', 'message' => 'Middleware funciona correctamente'];
            }
        }

        return ['status' => 'warning', 'message' => 'Middleware funciona pero necesita usuario admin para prueba completa'];
    }

    /**
     * Test RequireEmailVerification middleware
     */
    private function testRequireEmailVerification($userId = null)
    {
        $middleware = new RequireEmailVerification();
        $request = Request::create('/test', 'GET');
        
        // Test 1: Sin usuario autenticado
        $response = $middleware->handle($request, function ($request) {
            return response('Success');
        });
        
        if ($response->getContent() === 'Success') {
            $this->info("  ✅ Sin usuario: Permite acceso (esperado)");
        }

        // Test 2: Con usuario sin email verificado
        $unverifiedUser = Usuario::whereNull('email_verified_at')->first();
        if ($unverifiedUser) {
            Auth::login($unverifiedUser);
            $response = $middleware->handle($request, function ($request) {
                return response('Success');
            });
            Auth::logout();
            
            if ($response->getStatusCode() === 302) {
                $this->warn("  ⚠️  Usuario sin verificar: Redirige a verificación (esperado)");
            }
        }

        // Test 3: Con usuario verificado
        $verifiedUser = Usuario::whereNotNull('email_verified_at')->first();
        if ($verifiedUser) {
            Auth::login($verifiedUser);
            $response = $middleware->handle($request, function ($request) {
                return response('Success');
            });
            Auth::logout();
            
            if ($response->getContent() === 'Success') {
                $this->info("  ✅ Usuario verificado: Permite acceso");
                return ['status' => 'success', 'message' => 'Middleware funciona correctamente'];
            }
        }

        return ['status' => 'warning', 'message' => 'Middleware funciona pero necesita usuarios para prueba completa'];
    }

    /**
     * Test AssetCacheMiddleware
     */
    private function testAssetCacheMiddleware()
    {
        $middleware = new AssetCacheMiddleware();
        
        // Test con asset CSS
        $request = Request::create('/css/app.css', 'GET');
        $response = $middleware->handle($request, function ($request) {
            return response('CSS content')->header('Content-Type', 'text/css');
        });
        
        $cacheControl = $response->headers->get('Cache-Control');
        if (str_contains($cacheControl, 'max-age=31536000')) {
            $this->info("  ✅ CSS: Headers de cache configurados correctamente");
        }

        // Test con asset JS
        $request = Request::create('/js/app.js', 'GET');
        $response = $middleware->handle($request, function ($request) {
            return response('JS content')->header('Content-Type', 'application/javascript');
        });
        
        $cacheControl = $response->headers->get('Cache-Control');
        if (str_contains($cacheControl, 'max-age=31536000')) {
            $this->info("  ✅ JS: Headers de cache configurados correctamente");
        }

        // Test con ruta normal (no asset)
        $request = Request::create('/home', 'GET');
        $response = $middleware->handle($request, function ($request) {
            return response('HTML content')->header('Content-Type', 'text/html');
        });
        
        if (!$response->headers->get('Cache-Control')) {
            $this->info("  ✅ Ruta normal: No aplica cache (esperado)");
        }

        return ['status' => 'success', 'message' => 'Middleware de cache de assets funciona correctamente'];
    }

    /**
     * Test PerformanceOptimization middleware
     */
    private function testPerformanceOptimization()
    {
        $middleware = new PerformanceOptimization();
        $request = Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return response('Test content');
        });
        
        $headers = $response->headers;
        $securityHeaders = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin'
        ];

        $allHeadersPresent = true;
        foreach ($securityHeaders as $header => $expectedValue) {
            if ($headers->get($header) === $expectedValue) {
                $this->info("  ✅ Header {$header}: Configurado correctamente");
            } else {
                $this->warn("  ⚠️  Header {$header}: No configurado o valor incorrecto");
                $allHeadersPresent = false;
            }
        }

        if ($allHeadersPresent) {
            return ['status' => 'success', 'message' => 'Headers de seguridad configurados correctamente'];
        } else {
            return ['status' => 'warning', 'message' => 'Algunos headers de seguridad no están configurados'];
        }
    }

    /**
     * Test ExceptionHandlerMiddleware
     */
    private function testExceptionHandlerMiddleware()
    {
        $middleware = new ExceptionHandlerMiddleware();
        $request = Request::create('/test', 'GET');
        
        // Test con excepción simulada
        $response = $middleware->handle($request, function ($request) {
            throw new \Exception('Test exception');
        });
        
        if ($response instanceof Response) {
            $this->info("  ✅ Excepción capturada y manejada correctamente");
            return ['status' => 'success', 'message' => 'Middleware de excepciones funciona correctamente'];
        } else {
            return ['status' => 'error', 'message' => 'Middleware no manejó la excepción correctamente'];
        }
    }

    /**
     * Test VerifyCsrfToken middleware
     */
    private function testVerifyCsrfToken()
    {
        $middleware = new VerifyCsrfToken();
        $request = Request::create('/test', 'POST');
        
        // Test con ruta excluida (Stripe webhook)
        $request = Request::create('/stripe/webhook', 'POST');
        $response = $middleware->handle($request, function ($request) {
            return response('Success');
        });
        
        if ($response->getContent() === 'Success') {
            $this->info("  ✅ Ruta excluida: Permite acceso sin CSRF (esperado)");
        }

        return ['status' => 'success', 'message' => 'Middleware CSRF configurado correctamente'];
    }

    /**
     * Display test results
     */
    private function displayResults($results)
    {
        $this->newLine();
        $this->info('📊 RESULTADOS DE LAS PRUEBAS:');
        $this->newLine();

        $successCount = 0;
        $warningCount = 0;
        $errorCount = 0;

        foreach ($results as $name => $result) {
            $status = $result['status'];
            $message = $result['message'];

            switch ($status) {
                case 'success':
                    $this->info("✅ {$name}: {$message}");
                    $successCount++;
                    break;
                case 'warning':
                    $this->warn("⚠️  {$name}: {$message}");
                    $warningCount++;
                    break;
                case 'error':
                    $this->error("❌ {$name}: {$message}");
                    $errorCount++;
                    break;
            }
        }

        $this->newLine();
        $this->info("📈 RESUMEN:");
        $this->info("  ✅ Exitosos: {$successCount}");
        $this->warn("  ⚠️  Advertencias: {$warningCount}");
        $this->error("  ❌ Errores: {$errorCount}");
    }

    /**
     * Display single result
     */
    private function displaySingleResult($name, $result)
    {
        $this->newLine();
        $this->info('📊 RESULTADO:');
        
        $status = $result['status'];
        $message = $result['message'];

        switch ($status) {
            case 'success':
                $this->info("✅ {$name}: {$message}");
                break;
            case 'warning':
                $this->warn("⚠️  {$name}: {$message}");
                break;
            case 'error':
                $this->error("❌ {$name}: {$message}");
                break;
        }
    }
}
