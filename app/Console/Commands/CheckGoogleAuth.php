<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

class CheckGoogleAuth extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'google:check';

    /**
     * The console command description.
     */
    protected $description = 'Verificar configuración de Google OAuth';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔍 Verificando configuración de Google OAuth...");
        $this->line("");

        // Verificar variables de entorno
        $this->checkEnvironmentVariables();
        
        // Verificar configuración de servicios
        $this->checkServicesConfig();
        
        // Probar conexión con Google
        $this->testGoogleConnection();

        return Command::SUCCESS;
    }

    /**
     * Verificar variables de entorno
     */
    private function checkEnvironmentVariables()
    {
        $this->info("📋 Variables de entorno:");
        
        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirectUri = env('GOOGLE_REDIRECT_URI');
        
        $this->line("  🔑 GOOGLE_CLIENT_ID: " . ($clientId ? "✅ Configurado" : "❌ No configurado"));
        $this->line("  🔐 GOOGLE_CLIENT_SECRET: " . ($clientSecret ? "✅ Configurado" : "❌ No configurado"));
        $this->line("  🔗 GOOGLE_REDIRECT_URI: " . ($redirectUri ?: "❌ No configurado"));
        
        if (empty($clientId) || $clientId === 'your-google-client-id-here') {
            $this->error("  ⚠️  GOOGLE_CLIENT_ID no está configurado correctamente");
        }
        
        if (empty($clientSecret) || $clientSecret === 'your-google-client-secret-here') {
            $this->error("  ⚠️  GOOGLE_CLIENT_SECRET no está configurado correctamente");
        }
        
        $this->line("");
    }

    /**
     * Verificar configuración de servicios
     */
    private function checkServicesConfig()
    {
        $this->info("⚙️  Configuración de servicios:");
        
        $config = config('services.google');
        
        if (empty($config)) {
            $this->error("  ❌ Configuración de Google no encontrada en config/services.php");
            return;
        }
        
        $this->line("  📦 Client ID: " . ($config['client_id'] ? "✅ Configurado" : "❌ No configurado"));
        $this->line("  🔐 Client Secret: " . ($config['client_secret'] ? "✅ Configurado" : "❌ No configurado"));
        $this->line("  🔗 Redirect URI: " . ($config['redirect'] ?: "❌ No configurado"));
        
        $this->line("");
    }

    /**
     * Probar conexión con Google
     */
    private function testGoogleConnection()
    {
        $this->info("🌐 Probando conexión con Google:");
        
        try {
            // Verificar que Socialite esté disponible
            if (!class_exists('Laravel\Socialite\Facades\Socialite')) {
                $this->error("  ❌ Laravel Socialite no está instalado");
                return;
            }
            
            $this->line("  ✅ Laravel Socialite disponible");
            
            // Intentar crear el driver de Google
            $driver = Socialite::driver('google');
            $this->line("  ✅ Driver de Google creado correctamente");
            
            // Verificar configuración básica
            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
            
            if (empty($clientId) || empty($clientSecret)) {
                $this->error("  ❌ Credenciales de Google no configuradas");
                $this->line("");
                $this->warn("📝 Para configurar Google OAuth:");
                $this->line("  1. Ve a https://console.developers.google.com/");
                $this->line("  2. Crea un nuevo proyecto o selecciona uno existente");
                $this->line("  3. Habilita la API de Google+ y Google OAuth2");
                $this->line("  4. Ve a 'Credenciales' > 'Crear credenciales' > 'ID de cliente OAuth 2.0'");
                $this->line("  5. Configura las URIs de redirección autorizadas:");
                $this->line("     - http://localhost:8000/auth/callback/google (desarrollo)");
                $this->line("     - https://tu-dominio.com/auth/callback/google (producción)");
                $this->line("  6. Actualiza las variables de entorno en tu archivo .env");
                return;
            }
            
            $this->line("  ✅ Credenciales configuradas");
            $this->line("  🎉 Google OAuth configurado correctamente");
            
        } catch (\Exception $e) {
            $this->error("  ❌ Error al probar conexión: " . $e->getMessage());
            Log::error('Error verificando Google OAuth: ' . $e->getMessage());
        }
        
        $this->line("");
    }
}
