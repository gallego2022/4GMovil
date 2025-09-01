<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;

class ResetGoogleOAuth extends Command
{
    protected $signature = 'google:reset {--force : Forzar reset sin preguntar}';
    protected $description = 'Resetea completamente la configuración de Google OAuth para desarrollo';

    public function handle()
    {
        $this->info('🔄 Reseteando configuración de Google OAuth...');

        if (!$this->option('force')) {
            if (!$this->confirm('¿Estás seguro de que quieres resetear Google OAuth? Esto eliminará todos los usuarios de Google.')) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        try {
            // 1. Eliminar usuarios de Google
            $googleUsers = Usuario::whereNotNull('google_id')->count();
            if ($googleUsers > 0) {
                $deleted = Usuario::whereNotNull('google_id')->delete();
                $this->info("✅ {$deleted} usuarios de Google eliminados.");
            }

            // 2. Eliminar usuarios sin contraseña (usuarios de Google)
            $noPasswordUsers = Usuario::whereNull('contrasena')->count();
            if ($noPasswordUsers > 0) {
                $deletedNoPassword = Usuario::whereNull('contrasena')->delete();
                $this->info("✅ {$deletedNoPassword} usuarios sin contraseña eliminados.");
            }

            // 3. Limpiar webhook events si existen
            if (class_exists('App\Models\WebhookEvent')) {
                $webhookEvents = \App\Models\WebhookEvent::count();
                if ($webhookEvents > 0) {
                    \App\Models\WebhookEvent::truncate();
                    $this->info("✅ {$webhookEvents} eventos de webhook limpiados.");
                }
            }

            // 4. Limpiar sesiones
            $this->call('session:table');
            // $this->call('session:clear'); // Este comando no existe en Laravel

            Log::info("Google OAuth reseteado completamente en desarrollo");

            $this->info('');
            $this->info('🎯 Google OAuth reseteado exitosamente!');
            $this->info('');
            $this->info('📋 Pasos siguientes:');
            $this->info('   1. Cerrar sesión de Google en el navegador');
            $this->info('   2. Borrar cookies y caché del navegador');
            $this->info('   3. Intentar login con Google nuevamente');
            $this->info('   4. O ejecutar: php artisan db:populate para crear datos por defecto');
            
        } catch (\Exception $e) {
            $this->error("❌ Error al resetear Google OAuth: " . $e->getMessage());
            Log::error("Error al resetear Google OAuth: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
