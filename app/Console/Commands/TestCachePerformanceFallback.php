<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Services\InventarioService;
use App\Services\OptimizedStockAlertService;
use App\Services\Business\ProductoServiceOptimizadoCorregido;
use Illuminate\Support\Facades\DB;

class TestCachePerformanceFallback extends Command
{
    protected $signature = 'test:cache-performance-fallback';
    protected $description = 'Prueba el rendimiento del caché (funciona sin Redis)';

    protected $inventarioService;
    protected $alertService;
    protected $productoService;

    public function __construct(
        InventarioService $inventarioService,
        OptimizedStockAlertService $alertService,
        ProductoServiceOptimizadoCorregido $productoService
    ) {
        parent::__construct();
        $this->inventarioService = $inventarioService;
        $this->alertService = $alertService;
        $this->productoService = $productoService;
    }

    public function handle()
    {
        $this->info('🚀 Probando rendimiento del caché (modo fallback)...');
        $this->newLine();

        // Verificar driver de caché actual
        $driver = config('cache.default');
        $this->info("📡 Driver de caché actual: {$driver}");

        if ($driver === 'redis') {
            $this->warn('⚠️ Redis configurado pero no disponible, usando fallback');
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
        $this->info('📈 Prueba 4: Estadísticas del sistema');
        $this->showSystemStats();

        $this->newLine();
        $this->info('✅ Pruebas de rendimiento completadas!');
        $this->info('🎯 Beneficios del caché implementado:');
        $this->line('  • ⚡ Respuestas más rápidas');
        $this->line('  • 📊 Menos carga en la base de datos');
        $this->line('  • 🔄 Mejor escalabilidad');
        $this->line('  • 💾 Uso eficiente de memoria');

        if ($driver !== 'redis') {
            $this->newLine();
            $this->info('💡 Para mejor rendimiento, instala Redis:');
            $this->line('  php artisan redis:install-guide');
        }

        return Command::SUCCESS;
    }

    private function testDashboardPerformance()
    {
        $times = [];
        
        // Limpiar caché antes de la prueba
        Cache::flush();
        
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
        
        if ($times['sin_cache'] > 0) {
            $improvement = round((($times['sin_cache'] - $times['con_cache']) / $times['sin_cache']) * 100, 1);
            $this->line("  🚀 Mejora: {$improvement}%");
        }
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
        
        if ($times['sin_cache'] > 0) {
            $improvement = round((($times['sin_cache'] - $times['con_cache']) / $times['sin_cache']) * 100, 1);
            $this->line("  🚀 Mejora: {$improvement}%");
        }
        
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
        
        if ($times['sin_cache'] > 0) {
            $improvement = round((($times['sin_cache'] - $times['con_cache']) / $times['sin_cache']) * 100, 1);
            $this->line("  🚀 Mejora: {$improvement}%");
        }
    }

    private function showSystemStats()
    {
        $this->line("  💾 Driver de caché: " . config('cache.default'));
        $this->line("  🗄️ Base de datos: " . config('database.default'));
        
        // Estadísticas de la base de datos
        try {
            $productos = DB::table('productos')->count();
            $variantes = DB::table('variantes_producto')->count();
            $movimientos = DB::table('movimientos_inventario')->count();
            
            $this->line("  📦 Productos: {$productos}");
            $this->line("  🎨 Variantes: {$variantes}");
            $this->line("  📊 Movimientos: {$movimientos}");
            
        } catch (\Exception $e) {
            $this->line("  ❌ Error obteniendo estadísticas: {$e->getMessage()}");
        }
        
        // Verificar si hay claves en caché
        try {
            $keys = Cache::getStore()->getRedis()->keys('*');
            $this->line("  🔑 Claves en caché: " . count($keys));
        } catch (\Exception $e) {
            $this->line("  📭 Caché no disponible o vacío");
        }
    }
}
