<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DiagnoseGoogleAuth extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'google:diagnose';

    /**
     * The console command description.
     */
    protected $description = 'Diagnosticar problemas específicos del login con Google';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Diagnóstico completo de Google OAuth...");
        $this->line("");

        $this->checkCredentials();
        $this->checkRedirectUri();
        $this->testGoogleApi();
        $this->checkRoutes();
        $this->checkDatabase();

        return Command::SUCCESS;
    }

    /**
     * Verificar credenciales
     */
    private function checkCredentials()
    {
        $this->info("🔑 Verificando credenciales:");
        
        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        
        // Verificar formato del Client ID
        if (preg_match('/^\d+$/', $clientId)) {
            $this->error("  ❌ Client ID parece ser solo números: {$clientId}");
            $this->line("  💡 Debería ser algo como: 123456789-abcdefg.apps.googleusercontent.com");
        } elseif (strpos($clientId, '.apps.googleusercontent.com') !== false) {
            $this->line("  ✅ Client ID tiene formato correcto");
        } else {
            $this->warn("  ⚠️  Client ID no tiene formato estándar: {$clientId}");
        }
        
        // Verificar formato del Client Secret
        if (strpos($clientSecret, 'GOCSPX-') === 0) {
            $this->line("  ✅ Client Secret tiene formato correcto");
        } elseif (strlen($clientSecret) < 20) {
            $this->error("  ❌ Client Secret parece muy corto: {$clientSecret}");
        } else {
            $this->warn("  ⚠️  Client Secret no tiene formato estándar");
        }
        
        $this->line("");
    }

    /**
     * Verificar URI de redirección
     */
    private function checkRedirectUri()
    {
        $this->info("🔗 Verificando URI de redirección:");
        
        $redirectUri = env('GOOGLE_REDIRECT_URI');
        $expectedUri = 'http://localhost:8000/auth/callback/google';
        
        if ($redirectUri === $expectedUri) {
            $this->line("  ✅ URI de redirección correcta: {$redirectUri}");
        } else {
            $this->error("  ❌ URI de redirección incorrecta: {$redirectUri}");
            $this->line("  💡 Debería ser: {$expectedUri}");
        }
        
        $this->line("");
    }

    /**
     * Probar API de Google
     */
    private function testGoogleApi()
    {
        $this->info("🌐 Probando conexión con Google API:");
        
        try {
            $clientId = env('GOOGLE_CLIENT_ID');
            
            // Probar endpoint de Google OAuth
            $response = Http::timeout(10)->get("https://accounts.google.com/.well-known/openid_configuration");
            
            if ($response->successful()) {
                $this->line("  ✅ Conexión con Google API exitosa");
                
                $data = $response->json();
                if (isset($data['authorization_endpoint'])) {
                    $this->line("  ✅ Endpoint de autorización disponible");
                }
            } else {
                $this->error("  ❌ Error conectando con Google API: " . $response->status());
            }
            
        } catch (\Exception $e) {
            $this->error("  ❌ Error de conexión: " . $e->getMessage());
        }
        
        $this->line("");
    }

    /**
     * Verificar rutas
     */
    private function checkRoutes()
    {
        $this->info("🛣️  Verificando rutas de Google OAuth:");
        
        $routes = [
            'google.redirect' => '/auth/redirect/google',
            'google.callback' => '/auth/callback/google'
        ];
        
        foreach ($routes as $name => $path) {
            try {
                $route = \Route::getRoutes()->getByName($name);
                if ($route) {
                    $this->line("  ✅ Ruta '{$name}' configurada: {$path}");
                } else {
                    $this->error("  ❌ Ruta '{$name}' no encontrada");
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Error verificando ruta '{$name}': " . $e->getMessage());
            }
        }
        
        $this->line("");
    }

    /**
     * Verificar base de datos
     */
    private function checkDatabase()
    {
        $this->info("🗄️  Verificando base de datos:");
        
        try {
            // Verificar que la tabla usuarios existe
            $tableExists = \Schema::hasTable('usuarios');
            if ($tableExists) {
                $this->line("  ✅ Tabla 'usuarios' existe");
                
                // Verificar columnas necesarias
                $columns = ['google_id', 'correo_electronico', 'nombre_usuario'];
                foreach ($columns as $column) {
                    if (\Schema::hasColumn('usuarios', $column)) {
                        $this->line("  ✅ Columna '{$column}' existe");
                    } else {
                        $this->error("  ❌ Columna '{$column}' no encontrada");
                    }
                }
                
                // Contar usuarios con Google ID
                $googleUsers = \DB::table('usuarios')->whereNotNull('google_id')->count();
                $this->line("  📊 Usuarios con Google ID: {$googleUsers}");
                
            } else {
                $this->error("  ❌ Tabla 'usuarios' no existe");
            }
            
        } catch (\Exception $e) {
            $this->error("  ❌ Error verificando base de datos: " . $e->getMessage());
        }
        
        $this->line("");
    }
}

