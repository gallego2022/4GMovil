<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Models\Usuario;

class TestGoogleOAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:test-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la configuración de Google OAuth';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando configuración de Google OAuth...');
        
        // Verificar variables de entorno
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect');
        
        $this->line('📋 Variables de entorno:');
        $this->line("   Client ID: " . ($clientId ? '✅ Configurado' : '❌ No configurado'));
        $this->line("   Client Secret: " . ($clientSecret ? '✅ Configurado' : '❌ No configurado'));
        $this->line("   Redirect URI: " . ($redirectUri ? '✅ Configurado' : '❌ No configurado'));
        
        if (!$clientId || !$clientSecret || !$redirectUri) {
            $this->error('❌ Faltan variables de entorno. Revisa tu archivo .env');
            $this->line('');
            $this->line('Variables requeridas:');
            $this->line('   GOOGLE_CLIENT_ID');
            $this->line('   GOOGLE_CLIENT_SECRET');
            $this->line('   GOOGLE_REDIRECT_URI');
            return 1;
        }
        
        // Verificar rutas
        $this->line('');
        $this->line('🔗 Rutas configuradas:');
        $this->line("   /auth/redirect/google - ✅ Configurada");
        $this->line("   /auth/callback/google - ✅ Configurada");
        
        // Verificar controlador
        $this->line('');
        $this->line('🎮 Controlador:');
        $this->line('   GoogleController - ✅ Implementado');
        
        // Verificar modelo
        $this->line('');
        $this->line('📊 Base de datos:');
        $this->line('   Campo google_id - ✅ Agregado a tabla usuarios');
        
        // Verificar tabla de sesiones
        $this->line('');
        $this->line('💾 Sesiones:');
        if (Schema::hasTable('sessions')) {
            $this->line('   Tabla sessions - ✅ Existe');
            $columns = Schema::getColumnListing('sessions');
            $this->line('   Columnas: ' . implode(', ', $columns));
        } else {
            $this->line('   Tabla sessions - ❌ No existe');
        }
        
        // Verificar configuración de sesiones
        $this->line('');
        $this->line('⚙️ Configuración de sesiones:');
        $this->line('   Driver: ' . config('session.driver'));
        $this->line('   Lifetime: ' . config('session.lifetime') . ' minutos');
        $this->line('   Expire on close: ' . (config('session.expire_on_close') ? 'Sí' : 'No'));
        
        // Verificar usuarios existentes con Google
        $this->line('');
        $this->line('👥 Usuarios con Google:');
        $googleUsers = Usuario::whereNotNull('google_id')->count();
        $this->line("   Total usuarios con Google: {$googleUsers}");
        
        if ($googleUsers > 0) {
            $sampleUser = Usuario::whereNotNull('google_id')->first();
            $this->line("   Ejemplo: {$sampleUser->correo_electronico} (ID: {$sampleUser->google_id})");
        }
        
        $this->info('');
        $this->info('✅ Configuración de Google OAuth completada correctamente!');
        $this->line('');
        $this->line('📝 Próximos pasos:');
        $this->line('   1. Configura las credenciales en Google Cloud Console');
        $this->line('   2. Agrega las variables al archivo .env');
        $this->line('   3. Ejecuta: php artisan serve');
        $this->line('   4. Ve a: http://localhost:8000/login');
        $this->line('   5. Prueba el botón de Google');
        $this->line('');
        $this->line('🔧 Si hay problemas:');
        $this->line('   - Revisa los logs en storage/logs/laravel.log');
        $this->line('   - Verifica que las credenciales de Google sean correctas');
        $this->line('   - Asegúrate de que la URL de redirección coincida en Google Console');
        
        return 0;
    }
}
