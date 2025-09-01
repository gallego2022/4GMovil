<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;

class ClearGoogleUsers extends Command
{
    protected $signature = 'google:clear-users {--force : Forzar limpieza sin preguntar}';
    protected $description = 'Limpia usuarios de Google OAuth para resolver conflictos en desarrollo';

    public function handle()
    {
        $this->info('🧹 Limpiando usuarios de Google OAuth...');

        // Contar usuarios de Google
        $googleUsers = Usuario::whereNotNull('google_id')->count();
        $this->info("📊 Usuarios de Google encontrados: {$googleUsers}");

        if ($googleUsers === 0) {
            $this->info('✅ No hay usuarios de Google para limpiar.');
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("¿Estás seguro de que quieres eliminar {$googleUsers} usuarios de Google? Esto es irreversible.")) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        try {
            // Eliminar usuarios de Google
            $deleted = Usuario::whereNotNull('google_id')->delete();
            
            $this->info("✅ {$deleted} usuarios de Google eliminados exitosamente.");
            
            // Limpiar también usuarios sin contraseña (usuarios de Google)
            $noPasswordUsers = Usuario::whereNull('contrasena')->count();
            if ($noPasswordUsers > 0) {
                $deletedNoPassword = Usuario::whereNull('contrasena')->delete();
                $this->info("✅ {$deletedNoPassword} usuarios sin contraseña eliminados.");
            }
            
            Log::info("Usuarios de Google eliminados en desarrollo: {$deleted}");
            
        } catch (\Exception $e) {
            $this->error("❌ Error al eliminar usuarios: " . $e->getMessage());
            Log::error("Error al limpiar usuarios de Google: " . $e->getMessage());
            return 1;
        }

        $this->info('');
        $this->info('🎯 Ahora puedes:');
        $this->info('   1. Intentar login con Google nuevamente');
        $this->info('   2. O ejecutar: php artisan db:populate para crear datos por defecto');
        
        return 0;
    }
}
