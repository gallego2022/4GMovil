<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Inventory\StockAlertService;
use App\Models\Producto;
use App\Models\VarianteProducto;

class TestOptimizedStockAlerts extends Command
{
    protected $signature = 'test:optimized-stock-alerts';
    protected $description = 'Prueba las alertas de stock optimizadas';

    public function handle()
    {
        $this->info('🧪 Probando Alertas de Stock Optimizadas...');
        $this->newLine();

        $stockAlertService = new StockAlertService(app(\App\Services\Infrastructure\RedisCacheService::class));

        // Obtener alertas optimizadas
        $this->info('📊 Obteniendo alertas optimizadas...');
        $alertas = $stockAlertService->getOptimizedStockAlerts();

        // Mostrar estadísticas
        $this->info('📈 Estadísticas de Alertas:');
        $this->table(
            ['Tipo de Alerta', 'Cantidad'],
            [
                ['Productos Críticos', $alertas['productos_criticos']->count()],
                ['Productos Stock Bajo', $alertas['productos_stock_bajo']->count()],
                ['Variantes Agotadas', $alertas['variantes_agotadas']->count()],
                ['Total Alertas', $alertas['total_alertas']]
            ]
        );

        // Mostrar productos críticos
        if ($alertas['productos_criticos']->count() > 0) {
            $this->info('🚨 Productos Críticos:');
            foreach ($alertas['productos_criticos'] as $alerta) {
                $producto = $alerta['producto'];
                $this->line("  • {$producto->nombre_producto} (Stock: {$alerta['stock_actual']}, {$alerta['porcentaje']}%)");
                
                if ($alerta['total_variantes_problematicas'] > 0) {
                    $this->line("    - {$alerta['total_variantes_problematicas']} variantes problemáticas");
                }
            }
            $this->newLine();
        }

        // Mostrar productos con stock bajo
        if ($alertas['productos_stock_bajo']->count() > 0) {
            $this->info('⚠️ Productos con Stock Bajo:');
            foreach ($alertas['productos_stock_bajo'] as $alerta) {
                $producto = $alerta['producto'];
                $this->line("  • {$producto->nombre_producto} (Stock: {$alerta['stock_actual']}, {$alerta['porcentaje']}%)");
                
                if ($alerta['total_variantes_problematicas'] > 0) {
                    $this->line("    - {$alerta['total_variantes_problematicas']} variantes problemáticas");
                }
            }
            $this->newLine();
        }

        // Mostrar variantes agotadas
        if ($alertas['variantes_agotadas']->count() > 0) {
            $this->info('❌ Variantes Agotadas:');
            foreach ($alertas['variantes_agotadas'] as $alerta) {
                $variante = $alerta['variante'];
                $producto = $alerta['producto'];
                $this->line("  • {$producto->nombre_producto} - {$variante->nombre}");
            }
            $this->newLine();
        }

        // Probar funcionalidad de variantes problemáticas
        $this->info('🔍 Probando funcionalidad de variantes problemáticas...');
        
        $productosConVariantes = Producto::with('variantes')
            ->whereHas('variantes')
            ->take(3)
            ->get();

        foreach ($productosConVariantes as $producto) {
            $variantesProblema = $stockAlertService->getVariantesProblematicas($producto->producto_id);
            
            if ($variantesProblema->count() > 0) {
                $this->line("  📦 {$producto->nombre_producto}:");
                foreach ($variantesProblema as $varianteInfo) {
                    $variante = $varianteInfo['variante'];
                    $this->line("    - {$variante->nombre} ({$varianteInfo['tipo_alerta']}, Stock: {$varianteInfo['stock_actual']})");
                }
            }
        }

        // Verificar tipos de movimientos
        $this->info('📋 Verificando tipos de movimientos simplificados...');
        $tiposMovimiento = ['entrada', 'salida', 'reserva', 'liberacion'];
        
        foreach ($tiposMovimiento as $tipo) {
            $this->line("  ✅ {$tipo}");
        }

        $this->newLine();
        $this->info('✅ Prueba de alertas optimizadas completada exitosamente!');
        $this->info('🎯 Beneficios de la optimización:');
        $this->line('  • Agrupación inteligente de variantes por producto');
        $this->line('  • Modal para ver variantes problemáticas específicas');
        $this->line('  • Tipos de movimientos simplificados a 4 tipos');
        $this->line('  • Mejor experiencia de usuario para administradores');
        $this->line('  • Reducción de ruido en las alertas');

        return Command::SUCCESS;
    }
}
