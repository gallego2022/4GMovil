<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DiagnoseGoogleOAuth extends Command
{
    protected $signature = 'google:diagnose';
    protected $description = 'Diagnostica problemas con Google OAuth';

    public function handle()
    {
        $this->info('🔍 DIAGNÓSTICO DE GOOGLE OAUTH');
        $this->info('================================');

        // 1. Verificar estructura de la tabla usuarios
        $this->info('📊 ESTRUCTURA DE LA TABLA USUARIOS:');
        if (Schema::hasTable('usuarios')) {
            $columns = Schema::getColumnListing('usuarios');
            $this->line('  Columnas disponibles: ' . implode(', ', $columns));
            
            if (in_array('google_id', $columns)) {
                $this->line('  ✅ Campo google_id existe');
            } else {
                $this->error('  ❌ Campo google_id NO existe');
            }
            
            if (in_array('correo_electronico', $columns)) {
                $this->line('  ✅ Campo correo_electronico existe');
            } else {
                $this->error('  ❌ Campo correo_electronico NO existe');
            }
        } else {
            $this->error('  ❌ Tabla usuarios NO existe');
        }

        $this->newLine();

        // 2. Verificar datos en la tabla usuarios
        $this->info('👥 DATOS EN LA TABLA USUARIOS:');
        try {
            $totalUsers = Usuario::count();
            $this->line("  Total de usuarios: {$totalUsers}");
            
            $googleUsers = Usuario::whereNotNull('google_id')->count();
            $this->line("  Usuarios con google_id: {$googleUsers}");
            
            $noPasswordUsers = Usuario::whereNull('contrasena')->count();
            $this->line("  Usuarios sin contraseña: {$noPasswordUsers}");
            
            if ($totalUsers > 0) {
                $sampleUser = Usuario::first();
                $this->line("  Usuario de ejemplo:");
                $this->line("    - ID: {$sampleUser->usuario_id}");
                $this->line("    - Email: {$sampleUser->correo_electronico}");
                $this->line("    - Google ID: " . ($sampleUser->google_id ?: 'NULL'));
                $this->line("    - Contraseña: " . ($sampleUser->contrasena ? 'SÍ' : 'NO'));
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Error al consultar usuarios: " . $e->getMessage());
        }

        $this->newLine();

        // 3. Verificar configuración de Google
        $this->info('⚙️ CONFIGURACIÓN DE GOOGLE:');
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirect = config('services.google.redirect');
        
        $this->line("  Client ID: " . ($clientId ? '✅ Configurado' : '❌ NO configurado'));
        $this->line("  Client Secret: " . ($clientSecret ? '✅ Configurado' : '❌ NO configurado'));
        $this->line("  Redirect URI: " . ($redirect ? '✅ Configurado' : '❌ NO configurado'));
        
        if (!$clientId || !$clientSecret || !$redirect) {
            $this->warn("  ⚠️  Variables de entorno faltantes en .env");
        }

        $this->newLine();

        // 4. Verificar logs recientes
        $this->info('📝 LOGS RECIENTES:');
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $googleLogs = substr_count($logContent, 'Google');
            $this->line("  Logs con 'Google': {$googleLogs}");
            
            if ($googleLogs > 0) {
                $this->line("  Últimas líneas con Google:");
                $lines = explode("\n", $logContent);
                $googleLines = array_filter($lines, function($line) {
                    return stripos($line, 'google') !== false;
                });
                $recentGoogleLines = array_slice($googleLines, -5);
                foreach ($recentGoogleLines as $line) {
                    $this->line("    " . trim($line));
                }
            }
        } else {
            $this->line("  ❌ Archivo de log no encontrado");
        }

        $this->newLine();

        // 5. Recomendaciones
        $this->info('💡 RECOMENDACIONES:');
        if ($totalUsers === 0) {
            $this->line("  ✅ Base de datos vacía - puedes hacer migrate:fresh");
        } elseif ($googleUsers > 0) {
            $this->line("  ⚠️  Hay usuarios de Google - considera limpiarlos");
        }
        
        if (!$clientId || !$clientSecret) {
            $this->line("  ❌ Configura las variables de Google en .env");
        }
        
        $this->line("  🔄 Para reset completo: php artisan google:reset --force");
        $this->line("  🗑️  Para migración fresca: php artisan migrate:fresh --seed");
        
        return 0;
    }
}
