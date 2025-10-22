<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RedisCacheService;
use App\Services\InventarioService;
use App\Services\OptimizedStockAlertService;
use App\Services\Business\ProductoServiceOptimizadoCorregido;
use Illuminate\Support\Facades\DB;

class TestRedisCachePerformance extends Command
{
    protected $signature = 'test:redis-cache-performance';
    protected $description = 'Prueba el rendimiento del caché Redis';

    protected $cacheService;
    protected $inventarioService;
    protected $alertService;
    protected $productoService;

    public function __construct(
        RedisCacheService $cacheService,
        InventarioService $inventarioService,
        OptimizedStockAlertService $alertService,
        ProductoServiceOptimizadoCorregido $productoService
    ) {
        parent::__construct();
        $this->cacheService = $cacheService;
        $this->inventarioService = $inventarioService;
        $this->alertService = $alertService;
        $this->productoService = $productoService;
    }

    public function handle()
    {
        $this->info('🚀 Probando rendimiento del caché Redis...');
        $this->newLine();

        // Verificar conexión Redis
        $this->info('📡 Verificando conexión Redis...');
        try {
            $stats = $this->cacheService->getStats();
            if (empty($stats)) {
                $this->error('❌ No se pudo conectar a Redis');
                return Command::FAILURE;
            }
            $this->line("  ✅ Redis conectado - Memoria: {$stats['used_memory']}");
            $this->line("  📊 Clientes conectados: {$stats['connected_clients']}");
            $this->line("  🎯 Tasa de aciertos: {$stats['hit_rate']}%");
        } catch (\Exception $e) {
            $this->error("❌ Error conectando a Redis: {$e->getMessage()}");
            return Command::FAILURE;
        }

        $this->newLine();

        // Prueba 1: Dashboard de inventario
        $this->info('📊 Prueba 1: Dashboard de inventario');
        $this->testDashboardPerformance();

        // Prueba 2: Alertas optimizadas
        $this->info('🔔 Prueba 2: Alertas optimizadas');
        $this->testAlertsPerformance();

        // Prueba 3: Productos
        $this->info('🛍️ Prueba 3: Lista de productos');
        $this->testProductsPerformance();

        // Prueba 4: Estadísticas de caché
        $this->info('📈 Prueba 4: Estadísticas finales');
        $this->showFinalStats();

        $this->newLine();
        $this->info('✅ Pruebas de rendimiento completadas!');
        $this->info('🎯 Beneficios del caché Redis:');
        $this->line('  • ⚡ Respuestas más rápidas');
        $this->line('  • 📊 Menos carga en la base de datos');
        $this->line('  • 🔄 Mejor escalabilidad');
        $this->line('  • 💾 Uso eficiente de memoria');

        return Command::SUCCESS;
    }

    private function testDashboardPerformance()
    {
        $times = [];
        
        // Primera ejecución (sin caché)
        $start = microtime(true);
        $dashboard = $this->inventarioService->getDashboardData();
        $times['sin_cache'] = microtime(true) - $start;
        
        // Segunda ejecución (con caché)
        $start = microtime(true);
        $dashboard = $this->inventarioService->getDashboardData();
        $times['con_cache'] = microtime(true) - $start;
        
        $this->line("  ⏱️ Sin caché: " . round($times['sin_cache'] * 1000, 2) . "ms");
        $this->line("  ⚡ Con caché: " . round($times['con_cache'] * 1000, 2) . "ms");
        
        $improvement = round((($times['sin_cache'] - $times['con_cache']) / $times['sin_cache']) * 100, 1);
        $this->line("  🚀 Mejora: {$improvement}%");
    }

    private function testAlertsPerformance()
    {
        $times = [];
        
        // Primera ejecución (sin caché)
        $start = microtime(true);
        $alertas = $this->alertService->getOptimizedStockAlerts();
        $times['sin_cache'] = microtime(true) - $start;
        
        // Segunda ejecución (con caché)
        $start = microtime(true);
        $alertas = $this->alertService->getOptimizedStockAlerts();
        $times['con_cache'] = microtime(true) - $start;
        
        $this->line("  ⏱️ Sin caché: " . round($times['sin_cache'] * 1000, 2) . "ms");
        $this->line("  ⚡ Con caché: " . round($times['con_cache'] * 1000, 2) . "ms");
        
        $improvement = round((($times['sin_cache'] - $times['con_cache']) / $times['sin_cache']) * 100, 1);
        $this->line("  🚀 Mejora: {$improvement}%");
        $this->line("  📊 Total alertas: {$alertas['total_alertas']}");
    }

    private function testProductsPerformance()
    {
        $times = [];
        
        // Primera ejecución (sin caché)
        $start = microtime(true);
        $productos = $this->productoService->getAllProducts();
        $times['sin_cache'] = microtime(true) - $start;
        
        // Segunda ejecución (con caché)
        $start = microtime(true);
        $productos = $this->productoService->getAllProducts();
        $times['con_cache'] = microtime(true) - $start;
        
        $this->line("  ⏱️ Sin caché: " . round($times['sin_cache'] * 1000, 2) . "ms");
        $this->line("  ⚡ Con caché: " . round($times['con_cache'] * 1000, 2) . "ms");
        
        $improvement = round((($times['sin_cache'] - $times['con_cache']) / $times['sin_cache']) * 100, 1);
        $this->line("  🚀 Mejora: {$improvement}%");
    }

    private function showFinalStats()
    {
        $stats = $this->cacheService->getStats();
        
        $this->line("  💾 Memoria usada: {$stats['used_memory']}");
        $this->line("  🔗 Clientes conectados: {$stats['connected_clients']}");
        $this->line("  📊 Comandos procesados: {$stats['total_commands_processed']}");
        $this->line("  🎯 Tasa de aciertos: {$stats['hit_rate']}%");
        $this->line("  ⏱️ Tiempo activo: " . round($stats['uptime'] / 60, 1) . " minutos");
        
        // Mostrar claves en caché
        $keys = $this->cacheService->getKeys('*');
        $this->line("  🔑 Claves en caché: " . count($keys));
        
        if (count($keys) > 0) {
            $this->line("  📋 Tipos de claves:");
            $prefixes = [];
            foreach ($keys as $key) {
                $parts = explode(':', $key);
                if (isset($parts[0])) {
                    $prefixes[$parts[0]] = ($prefixes[$parts[0]] ?? 0) + 1;
                }
            }
            
            foreach ($prefixes as $prefix => $count) {
                $this->line("    • {$prefix}: {$count} claves");
            }
        }
    }
}
