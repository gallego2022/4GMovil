<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigureCacheForEnvironment extends Command
{
    protected $signature = 'cache:configure-environment';
    protected $description = 'Configura el caché según el entorno (Docker/Laravel Cloud)';

    public function handle()
    {
        $this->info('🔧 Configurando caché según el entorno...');
        $this->newLine();

        // Detectar entorno
        $environment = $this->detectEnvironment();
        $this->info("🌍 Entorno detectado: {$environment}");

        switch ($environment) {
            case 'docker':
                $this->configureDockerCache();
                break;
            case 'laravel-cloud':
                $this->configureLaravelCloudCache();
                break;
            case 'local':
                $this->configureLocalCache();
                break;
            default:
                $this->configureDefaultCache();
        }

        $this->newLine();
        $this->info('✅ Configuración completada!');
        $this->showNextSteps($environment);

        return Command::SUCCESS;
    }

    private function detectEnvironment(): string
    {
        // Verificar si está en Docker
        if (File::exists('/.dockerenv') || File::exists('/proc/1/cgroup') && str_contains(File::get('/proc/1/cgroup'), 'docker')) {
            return 'docker';
        }

        // Verificar si está en Laravel Cloud
        if (env('LARAVEL_CLOUD', false) || env('APP_ENV') === 'production' && str_contains(env('APP_URL', ''), 'laravel-cloud')) {
            return 'laravel-cloud';
        }

        // Verificar si Redis está disponible
        try {
            if (class_exists('Redis')) {
                return 'local';
            }
            return 'default';
        } catch (\Exception $e) {
            return 'default';
        }
    }

    private function configureDockerCache()
    {
        $this->info('🐳 Configurando caché para Docker...');
        
        $this->line('📝 Variables de entorno recomendadas:');
        $this->line('CACHE_DRIVER=redis');
        $this->line('REDIS_HOST=redis');
        $this->line('REDIS_PORT=6379');
        $this->line('REDIS_PASSWORD=null');
        
        $this->newLine();
        $this->info('🚀 Comandos para iniciar:');
        $this->line('docker-compose -f docker-compose.cache.yml up -d redis');
        $this->line('php artisan cache:clear');
        $this->line('php artisan test:cache-performance-fallback');
    }

    private function configureLaravelCloudCache()
    {
        $this->info('☁️ Configurando caché para Laravel Cloud...');
        
        $this->line('📝 Variables de entorno recomendadas:');
        $this->line('CACHE_DRIVER=database');
        $this->line('CACHE_PREFIX=4gmovil_cache_');
        
        $this->newLine();
        $this->info('🚀 Comandos para configurar:');
        $this->line('php artisan cache:table');
        $this->line('php artisan cache:clear');
        $this->line('php artisan cache:setup-cloud --driver=database');
        
        $this->warn('⚠️ Nota: Redis no está disponible en Laravel Cloud');
        $this->info('💡 Database cache es la mejor opción para producción');
    }

    private function configureLocalCache()
    {
        $this->info('💻 Configurando caché para desarrollo local...');
        
        $this->line('📝 Variables de entorno recomendadas:');
        $this->line('CACHE_DRIVER=redis');
        $this->line('REDIS_HOST=127.0.0.1');
        $this->line('REDIS_PORT=6379');
        
        $this->newLine();
        $this->info('🚀 Comandos para configurar:');
        $this->line('php artisan cache:clear');
        $this->line('php artisan test:cache-performance-fallback');
        
        $this->info('💡 Redis está disponible - máximo rendimiento');
    }

    private function configureDefaultCache()
    {
        $this->info('🔧 Configurando caché por defecto...');
        
        $this->line('📝 Variables de entorno recomendadas:');
        $this->line('CACHE_DRIVER=file');
        $this->line('CACHE_PREFIX=4gmovil_cache_');
        
        $this->newLine();
        $this->info('🚀 Comandos para configurar:');
        $this->line('php artisan cache:clear');
        $this->line('php artisan cache:setup-cloud --driver=file');
        
        $this->warn('⚠️ Redis no está disponible');
        $this->info('💡 File cache como fallback');
    }

    private function showNextSteps(string $environment)
    {
        $this->newLine();
        $this->info('📋 Próximos pasos:');
        
        switch ($environment) {
            case 'docker':
                $this->line('1. Iniciar Redis: docker-compose -f docker-compose.cache.yml up -d');
                $this->line('2. Configurar variables de entorno');
                $this->line('3. Probar: php artisan test:cache-performance-fallback');
                break;
                
            case 'laravel-cloud':
                $this->line('1. Configurar variables en Laravel Cloud');
                $this->line('2. Ejecutar: php artisan cache:table');
                $this->line('3. Probar: php artisan test:cache-performance-fallback');
                break;
                
            case 'local':
                $this->line('1. Verificar Redis funcionando');
                $this->line('2. Configurar variables de entorno');
                $this->line('3. Probar: php artisan test:cache-performance-fallback');
                break;
                
            default:
                $this->line('1. Configurar variables de entorno');
                $this->line('2. Ejecutar: php artisan cache:setup-cloud');
                $this->line('3. Probar: php artisan test:cache-performance-fallback');
        }
        
        $this->newLine();
        $this->info('📚 Documentación:');
        $this->line('• Guía completa: LARAVEL_CLOUD_CACHE_GUIDE.md');
        $this->line('• Docker: docker-compose.cache.yml');
        $this->line('• Configuración: config/cache-cloud.php');
    }
}
