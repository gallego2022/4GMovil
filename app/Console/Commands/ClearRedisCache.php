<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Infrastructure\RedisCacheService;

class ClearRedisCache extends Command
{
    protected $signature = 'cache:clear-redis {--module= : Módulo específico a limpiar} {--all : Limpiar todo el caché}';
    protected $description = 'Limpia el caché Redis por módulo o completamente';

    protected $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    public function handle()
    {
        $this->info('🧹 Limpiando caché Redis...');
        $this->newLine();

        if ($this->option('all')) {
            $this->clearAllCache();
        } elseif ($module = $this->option('module')) {
            $this->clearModuleCache($module);
        } else {
            $this->showCacheStatus();
        }

        return Command::SUCCESS;
    }

    private function clearAllCache()
    {
        $this->info('🗑️ Limpiando todo el caché Redis...');
        
        try {
            $result = $this->cacheService->flush();
            if ($result) {
                $this->info('✅ Caché limpiado completamente');
            } else {
                $this->error('❌ Error al limpiar el caché');
            }
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
        }
    }

    private function clearModuleCache(string $module)
    {
        $this->info("🧹 Limpiando caché del módulo: {$module}");
        
        try {
            $count = 0;
            
            switch (strtolower($module)) {
                case 'productos':
                    $count = $this->cacheService->clearProductos();
                    break;
                case 'inventario':
                    $count = $this->cacheService->clearInventario();
                    break;
                case 'alertas':
                    $count = $this->cacheService->clearAlertas();
                    break;
                case 'dashboard':
                    $count = $this->cacheService->clearDashboard();
                    break;
                default:
                    $this->error("❌ Módulo '{$module}' no reconocido");
                    $this->line("Módulos disponibles: productos, inventario, alertas, dashboard");
                    return;
            }
            
            $this->info("✅ Limpiadas {$count} claves del módulo {$module}");
            
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
        }
    }

    private function showCacheStatus()
    {
        $this->info('📊 Estado actual del caché Redis:');
        $this->newLine();

        try {
            $stats = $this->cacheService->getStats();
            
            if (empty($stats)) {
                $this->error('❌ No se pudo obtener estadísticas de Redis');
                return;
            }

            $this->line("💾 Memoria usada: {$stats['used_memory']}");
            $this->line("🔗 Clientes conectados: {$stats['connected_clients']}");
            $this->line("📊 Comandos procesados: {$stats['total_commands_processed']}");
            $this->line("🎯 Tasa de aciertos: {$stats['hit_rate']}%");
            $this->line("⏱️ Tiempo activo: " . round($stats['uptime'] / 60, 1) . " minutos");

            $this->newLine();
            $this->info('🔑 Claves por módulo:');
            
            $keys = $this->cacheService->getKeys('*');
            $prefixes = [];
            
            foreach ($keys as $key) {
                $parts = explode(':', $key);
                if (isset($parts[0])) {
                    $prefixes[$parts[0]] = ($prefixes[$parts[0]] ?? 0) + 1;
                }
            }
            
            if (empty($prefixes)) {
                $this->line('  📭 No hay claves en caché');
            } else {
                foreach ($prefixes as $prefix => $count) {
                    $this->line("  • {$prefix}: {$count} claves");
                }
            }

            $this->newLine();
            $this->info('💡 Comandos disponibles:');
            $this->line('  php artisan cache:clear-redis --all');
            $this->line('  php artisan cache:clear-redis --module=productos');
            $this->line('  php artisan cache:clear-redis --module=inventario');
            $this->line('  php artisan cache:clear-redis --module=alertas');
            $this->line('  php artisan cache:clear-redis --module=dashboard');

        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
        }
    }
}
