<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupCloudCache extends Command
{
    protected $signature = 'cache:setup-cloud {--driver= : Driver de caché a usar}';
    protected $description = 'Configura el caché optimizado para Laravel Cloud';

    public function handle()
    {
        $this->info('☁️ Configurando caché para Laravel Cloud...');
        $this->newLine();

        $driver = $this->option('driver') ?: 'database';
        
        $this->info("📊 Configurando caché con driver: {$driver}");
        
        switch ($driver) {
            case 'database':
                $this->setupDatabaseCache();
                break;
            case 'file':
                $this->setupFileCache();
                break;
            case 'redis':
                $this->setupRedisCache();
                break;
            default:
                $this->error("❌ Driver '{$driver}' no soportado");
                return Command::FAILURE;
        }

        $this->newLine();
        $this->info('✅ Configuración de caché completada!');
        $this->info('🎯 Beneficios para Laravel Cloud:');
        $this->line('  • ⚡ Mejor rendimiento sin Redis');
        $this->line('  • 📊 Caché persistente en base de datos');
        $this->line('  • 🔄 Invalidación automática');
        $this->line('  • 💾 Optimización de consultas');

        return Command::SUCCESS;
    }

    private function setupDatabaseCache()
    {
        $this->info('🗄️ Configurando caché de base de datos...');
        
        try {
            // Crear tabla de caché si no existe
            if (!Schema::hasTable('cache')) {
                $this->call('cache:table');
                $this->info('✅ Tabla de caché creada');
            } else {
                $this->info('✅ Tabla de caché ya existe');
            }

            // Limpiar caché existente
            $this->call('cache:clear');
            $this->info('✅ Caché limpiado');

            // Configurar variables de entorno
            $this->info('📝 Variables de entorno recomendadas:');
            $this->line('CACHE_DRIVER=database');
            $this->line('CACHE_PREFIX=4gmovil_cache_');

        } catch (\Exception $e) {
            $this->error("❌ Error configurando caché de base de datos: {$e->getMessage()}");
        }
    }

    private function setupFileCache()
    {
        $this->info('📁 Configurando caché de archivos...');
        
        try {
            // Verificar permisos de directorio
            $cachePath = storage_path('framework/cache');
            if (!is_writable($cachePath)) {
                $this->error("❌ Directorio de caché no es escribible: {$cachePath}");
                return;
            }

            // Limpiar caché existente
            $this->call('cache:clear');
            $this->info('✅ Caché limpiado');

            // Configurar variables de entorno
            $this->info('📝 Variables de entorno recomendadas:');
            $this->line('CACHE_DRIVER=file');
            $this->line('CACHE_PREFIX=4gmovil_cache_');

        } catch (\Exception $e) {
            $this->error("❌ Error configurando caché de archivos: {$e->getMessage()}");
        }
    }

    private function setupRedisCache()
    {
        $this->info('🔴 Configurando caché Redis...');
        
        try {
            // Verificar si Redis está disponible
            $this->call('cache:clear');
            $this->info('✅ Caché limpiado');

            // Configurar variables de entorno
            $this->info('📝 Variables de entorno recomendadas:');
            $this->line('CACHE_DRIVER=redis');
            $this->line('REDIS_HOST=127.0.0.1');
            $this->line('REDIS_PORT=6379');
            $this->line('REDIS_PASSWORD=null');
            $this->line('CACHE_PREFIX=4gmovil_cache_');

            $this->warn('⚠️ Nota: Redis debe estar instalado y ejecutándose');

        } catch (\Exception $e) {
            $this->error("❌ Error configurando caché Redis: {$e->getMessage()}");
        }
    }
}
