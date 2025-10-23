<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetGoogleAuth extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'google:reset {--force : Forzar reset sin confirmación}';

    /**
     * The console command description.
     */
    protected $description = 'Resetear configuración de Google OAuth';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('¿Estás seguro de que quieres resetear la configuración de Google OAuth?')) {
                $this->info('Operación cancelada.');
                return Command::SUCCESS;
            }
        }

        $this->info("🔄 Reseteando configuración de Google OAuth...");
        $this->line("");

        // Limpiar caché
        $this->clearCache();
        
        // Mostrar instrucciones
        $this->showInstructions();
        
        // Limpiar logs relacionados
        $this->clearLogs();

        $this->info("✅ Reset completado. Sigue las instrucciones para configurar Google OAuth.");
        
        return Command::SUCCESS;
    }

    /**
     * Limpiar caché
     */
    private function clearCache()
    {
        $this->info("🧹 Limpiando caché...");
        
        try {
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('cache:clear');
            
            $this->line("  ✅ Caché limpiado");
        } catch (\Exception $e) {
            $this->error("  ❌ Error limpiando caché: " . $e->getMessage());
        }
        
        $this->line("");
    }

    /**
     * Mostrar instrucciones
     */
    private function showInstructions()
    {
        $this->info("📋 INSTRUCCIONES PARA CONFIGURAR GOOGLE OAUTH:");
        $this->line("");
        
        $this->line("1. 🌐 Ve a Google Cloud Console:");
        $this->line("   https://console.developers.google.com/");
        $this->line("");
        
        $this->line("2. 📁 Crea o selecciona un proyecto");
        $this->line("");
        
        $this->line("3. 🔧 Habilita las APIs necesarias:");
        $this->line("   - Google+ API");
        $this->line("   - Google OAuth2 API");
        $this->line("");
        
        $this->line("4. 🔑 Crea credenciales OAuth 2.0:");
        $this->line("   - Ve a 'APIs y servicios' > 'Credenciales'");
        $this->line("   - Haz clic en 'Crear credenciales' > 'ID de cliente OAuth 2.0'");
        $this->line("   - Selecciona 'Aplicación web'");
        $this->line("");
        
        $this->line("5. 🔗 Configura URIs de redirección:");
        $this->line("   - http://localhost:8000/auth/callback/google (desarrollo)");
        $this->line("   - https://tu-dominio.com/auth/callback/google (producción)");
        $this->line("");
        
        $this->line("6. 📝 Actualiza tu archivo .env:");
        $this->line("   GOOGLE_CLIENT_ID=tu-client-id-real");
        $this->line("   GOOGLE_CLIENT_SECRET=tu-client-secret-real");
        $this->line("");
        
        $this->line("7. ✅ Verifica la configuración:");
        $this->line("   docker-compose exec app php artisan google:check");
        $this->line("");
    }

    /**
     * Limpiar logs relacionados
     */
    private function clearLogs()
    {
        $this->info("🗑️  Limpiando logs relacionados...");
        
        try {
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                // Crear backup del log actual
                $backupFile = storage_path('logs/laravel-backup-' . date('Y-m-d-H-i-s') . '.log');
                copy($logFile, $backupFile);
                
                // Limpiar el log actual
                file_put_contents($logFile, '');
                
                $this->line("  ✅ Logs limpiados (backup creado)");
            }
        } catch (\Exception $e) {
            $this->warn("  ⚠️  No se pudieron limpiar los logs: " . $e->getMessage());
        }
        
        $this->line("");
    }
}
