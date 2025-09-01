<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckGoogleConfig extends Command
{
    protected $signature = 'google:check-config';
    protected $description = 'Verifica la configuración completa de Google OAuth';

    public function handle()
    {
        $this->info('🔍 VERIFICANDO CONFIGURACIÓN DE GOOGLE OAUTH');
        $this->info('============================================');

        // Verificar configuración de servicios
        $googleConfig = config('services.google');
        
        $this->info('📱 CONFIGURACIÓN ACTUAL:');
        $this->line("  Client ID: " . ($googleConfig['client_id'] ? '✅ Configurado' : '❌ NO configurado'));
        $this->line("  Client Secret: " . ($googleConfig['client_secret'] ? '✅ Configurado' : '❌ NO configurado'));
        $this->line("  Redirect URI: " . ($googleConfig['redirect'] ? '✅ Configurado' : '❌ NO configurado'));

        $this->newLine();

        // Verificar variables de entorno
        $this->info('🌍 VARIABLES DE ENTORNO (.env):');
        $this->line("  GOOGLE_CLIENT_ID: " . (env('GOOGLE_CLIENT_ID') ? '✅ Presente' : '❌ Faltante'));
        $this->info("  GOOGLE_CLIENT_SECRET: " . (env('GOOGLE_CLIENT_SECRET') ? '✅ Presente' : '❌ Faltante'));
        $this->info("  GOOGLE_REDIRECT_URI: " . (env('GOOGLE_REDIRECT_URI') ? '✅ Presente' : '❌ Faltante'));

        $this->newLine();

        // Verificar que Socialite esté instalado
        $this->info('📦 DEPENDENCIAS:');
        if (class_exists('Laravel\Socialite\Facades\Socialite')) {
            $this->line("  Laravel Socialite: ✅ Instalado");
        } else {
            $this->error("  Laravel Socialite: ❌ NO instalado");
        }

        $this->newLine();

        // Verificar rutas
        $this->info('🛣️  RUTAS:');
        $redirectRoute = \Illuminate\Support\Facades\Route::getRoutes()->getByName('google.redirect');
        $callbackRoute = \Illuminate\Support\Facades\Route::getRoutes()->getByName('google.callback');
        
        $this->line("  Ruta de redirección: " . ($redirectRoute ? '✅ Existe' : '❌ NO existe'));
        $this->line("  Ruta de callback: " . ($callbackRoute ? '✅ Existe' : '❌ NO existe'));

        $this->newLine();

        // Recomendaciones
        $this->info('💡 RECOMENDACIONES:');
        
        if (!$googleConfig['client_secret']) {
            $this->error("  ❌ Agrega GOOGLE_CLIENT_SECRET en tu .env");
        }
        
        if (!$googleConfig['redirect']) {
            $this->error("  ❌ Agrega GOOGLE_REDIRECT_URI en tu .env");
        }
        
        if ($googleConfig['client_id'] && $googleConfig['client_secret'] && $googleConfig['redirect']) {
            $this->info("  ✅ Configuración completa - Google OAuth debería funcionar");
        } else {
            $this->warn("  ⚠️  Configuración incompleta - Google OAuth NO funcionará");
        }

        $this->newLine();
        $this->info('🔧 PARA CONFIGURAR:');
        $this->line("  1. Ve a https://console.developers.google.com");
        $this->line("  2. Selecciona tu proyecto");
        $this->line("  3. Ve a Credentials → OAuth 2.0 Client IDs");
        $this->line("  4. Copia Client Secret y Redirect URI");
        $this->line("  5. Actualiza tu archivo .env");
        
        return 0;
    }
}
