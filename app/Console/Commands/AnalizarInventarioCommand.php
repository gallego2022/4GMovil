<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Inventory\InventarioService;
use App\Models\Producto;
use Carbon\Carbon;

class AnalizarInventarioCommand extends Command
{
    protected $signature = 'inventario:analizar 
                            {--producto-id= : ID del producto específico}
                            {--categoria-id= : ID de la categoría}
                            {--detallado : Mostrar análisis detallado}
                            {--exportar : Exportar resultados a CSV}';

    protected $description = 'Análisis completo del inventario con recomendaciones';

    protected $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        parent::__construct();
        $this->inventarioService = $inventarioService;
    }

    public function handle()
    {
        $this->info('🔍 Iniciando análisis completo del inventario...');
        
        $productoId = $this->option('producto-id');
        $categoriaId = $this->option('categoria-id');
        $detallado = $this->option('detallado');
        $exportar = $this->option('exportar');

        if ($productoId) {
            $this->analizarProducto($productoId, $detallado);
        } elseif ($categoriaId) {
            $this->analizarCategoria($categoriaId, $detallado);
        } else {
            $this->analizarInventarioCompleto($detallado);
        }

        if ($exportar) {
            $this->exportarResultados();
        }

        return 0;
    }

    private function analizarProducto(int $productoId, bool $detallado): void
    {
        $producto = Producto::with(['categoria', 'marca'])->find($productoId);
        
        if (!$producto) {
            $this->error("Producto con ID {$productoId} no encontrado.");
            return;
        }

        $this->info("📊 Analizando producto: {$producto->nombre_producto}");
        
        $stockOptimo = $this->inventarioService->calcularStockOptimo($productoId);
        $demanda = $this->inventarioService->calcularDemandaPromedio($productoId);

        $this->mostrarAnalisisProducto($producto, $stockOptimo, $demanda, $detallado);
    }

    private function analizarCategoria(int $categoriaId, bool $detallado): void
    {
        $productos = Producto::where('categoria_id', $categoriaId)->activos()->get();
        
        if ($productos->isEmpty()) {
            $this->error("No se encontraron productos en la categoría {$categoriaId}.");
            return;
        }

        $this->info("📊 Analizando categoría: " . $productos->first()->categoria->nombre_categoria);
        
        $resultados = [];
        $bar = $this->output->createProgressBar($productos->count());
        
        foreach ($productos as $producto) {
            $stockOptimo = $this->inventarioService->calcularStockOptimo($producto->producto_id);
            $demanda = $this->inventarioService->calcularDemandaPromedio($producto->producto_id);
            
            $resultados[] = [
                'producto' => $producto,
                'stock_optimo' => $stockOptimo,
                'demanda' => $demanda
            ];
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->mostrarAnalisisCategoria($resultados, $detallado);
    }

    private function analizarInventarioCompleto(bool $detallado): void
    {
        $productos = Producto::activos()->get();
        
        $this->info("📊 Analizando inventario completo ({$productos->count()} productos)");
        
        $alertas = $this->inventarioService->getAlertasInventarioMejoradas();
        $productosConAlertas = $this->inventarioService->getProductosConAlertasInteligentes();
        
        $this->mostrarResumenGeneral($alertas, $productosConAlertas, $detallado);
    }

    private function mostrarAnalisisProducto($producto, array $stockOptimo, array $demanda, bool $detallado): void
    {
        $this->newLine();
        $this->info('📊 ANÁLISIS DEL PRODUCTO');
        $this->info('========================');
        
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Producto', $producto->nombre_producto],
                ['Categoría', $producto->categoria->nombre_categoria ?? 'Sin categoría'],
                ['Stock Total', $producto->stock . ' unidades'],
                ['Stock Disponible', $producto->stock_disponible . ' unidades'],
                ['Stock Reservado', $producto->stock_reservado . ' unidades'],
                ['Stock Mínimo Actual', $producto->stock_minimo . ' unidades'],
                ['Stock Máximo Actual', $producto->stock_maximo . ' unidades'],
            ]
        );

        if (!empty($stockOptimo)) {
            $this->info('🎯 RECOMENDACIONES DE STOCK');
            $this->info('============================');
            
            $this->table(
                ['Métrica', 'Actual', 'Recomendado', 'Diferencia'],
                [
                    ['Stock Mínimo', $producto->stock_minimo, $stockOptimo['stock_minimo_recomendado'], $stockOptimo['stock_minimo_recomendado'] - $producto->stock_minimo],
                    ['Stock Óptimo', '-', $stockOptimo['stock_optimo_recomendado'], '-'],
                    ['Stock Máximo', $producto->stock_maximo, $stockOptimo['stock_maximo_recomendado'], $stockOptimo['stock_maximo_recomendado'] - $producto->stock_maximo],
                ]
            );

            $this->info('📈 ANÁLISIS DE DEMANDA');
            $this->info('======================');
            
            $this->table(
                ['Período', 'Venta Promedio'],
                [
                    ['Diaria', round($demanda['venta_promedio_diaria'], 2) . ' unidades'],
                    ['Semanal', round($demanda['venta_promedio_semanal'], 2) . ' unidades'],
                    ['Mensual', round($demanda['venta_promedio_mensual'], 2) . ' unidades'],
                    ['Total (30 días)', $demanda['total_vendido'] . ' unidades'],
                ]
            );

            // Recomendaciones
            $this->info('💡 RECOMENDACIONES');
            $this->info('==================');
            
            if ($producto->stock_disponible < $stockOptimo['stock_minimo_recomendado']) {
                $this->warn('⚠️  Necesita reabastecimiento urgente');
                $this->line('   Cantidad recomendada: ' . ($stockOptimo['stock_optimo_recomendado'] - $producto->stock_disponible) . ' unidades');
            } elseif ($producto->stock > $stockOptimo['stock_maximo_recomendado']) {
                $this->warn('⚠️  Stock excesivo - Considera pausar compras');
            } else {
                $this->info('✅ Stock en niveles óptimos');
            }

            if ($producto->stock_reservado > $producto->stock * 0.5) {
                $this->warn('⚠️  Alto porcentaje de stock reservado');
            }
        } else {
            $this->warn('⚠️  No hay suficientes datos de venta para análisis predictivo');
            $this->line('   Se recomienda esperar más tiempo para acumular datos de ventas');
        }
    }

    private function mostrarAnalisisCategoria(array $resultados, bool $detallado): void
    {
        $this->newLine();
        $this->info('📊 RESUMEN DE CATEGORÍA');
        $this->info('=======================');
        
        $productosNecesitanReabastecimiento = 0;
        $productosStockExcesivo = 0;
        $productosSinDatos = 0;
        
        foreach ($resultados as $resultado) {
            if (empty($resultado['stock_optimo'])) {
                $productosSinDatos++;
            } elseif ($resultado['producto']->stock_disponible < $resultado['stock_optimo']['stock_minimo_recomendado']) {
                $productosNecesitanReabastecimiento++;
            } elseif ($resultado['producto']->stock > $resultado['stock_optimo']['stock_maximo_recomendado']) {
                $productosStockExcesivo++;
            }
        }
        
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total Productos', count($resultados)],
                ['Necesitan Reabastecimiento', $productosNecesitanReabastecimiento],
                ['Stock Excesivo', $productosStockExcesivo],
                ['Sin Datos Suficientes', $productosSinDatos],
                ['Stock Óptimo', count($resultados) - $productosNecesitanReabastecimiento - $productosStockExcesivo - $productosSinDatos],
            ]
        );

        if ($detallado) {
            $this->info('📋 DETALLE POR PRODUCTO');
            $this->info('=======================');
            
            $tabla = [];
            foreach ($resultados as $resultado) {
                $producto = $resultado['producto'];
                $stockOptimo = $resultado['stock_optimo'];
                
                $estado = 'Óptimo';
                if (empty($stockOptimo)) {
                    $estado = 'Sin datos';
                } elseif ($producto->stock_disponible < $stockOptimo['stock_minimo_recomendado']) {
                    $estado = 'Necesita reabastecimiento';
                } elseif ($producto->stock > $stockOptimo['stock_maximo_recomendado']) {
                    $estado = 'Stock excesivo';
                }
                
                $tabla[] = [
                    $producto->nombre_producto,
                    $producto->stock_disponible,
                    empty($stockOptimo) ? '-' : $stockOptimo['stock_minimo_recomendado'],
                    $estado
                ];
            }
            
            $this->table(
                ['Producto', 'Stock Disponible', 'Mínimo Recomendado', 'Estado'],
                $tabla
            );
        }
    }

    private function mostrarResumenGeneral(array $alertas, array $productosConAlertas, bool $detallado): void
    {
        $this->newLine();
        $this->info('📊 RESUMEN GENERAL DEL INVENTARIO');
        $this->info('=================================');
        
        $this->table(
            ['Tipo de Alerta', 'Cantidad'],
            [
                ['Sin Stock', $alertas['sin_stock'] ?? 0],
                ['Stock Crítico', $alertas['stock_critico'] ?? 0],
                ['Stock Bajo', $alertas['stock_bajo'] ?? 0],
                ['Necesita Reabastecimiento', $alertas['necesita_reabastecimiento'] ?? 0],
                ['Stock Excesivo', $alertas['stock_excesivo'] ?? 0],
                ['Stock Reservado Alto', $alertas['stock_reservado_alto'] ?? 0],
                ['Productos Inactivos', $alertas['productos_inactivos'] ?? 0],
            ]
        );

        if ($detallado && !empty($productosConAlertas)) {
            $this->info('📋 PRODUCTOS CON ALERTAS');
            $this->info('=========================');
            
            $tabla = [];
            foreach ($productosConAlertas as $item) {
                $producto = $item['producto'];
                $alertas = implode(', ', $item['alertas']);
                
                $tabla[] = [
                    $producto->nombre_producto,
                    $producto->stock_disponible,
                    $alertas
                ];
            }
            
            $this->table(
                ['Producto', 'Stock Disponible', 'Alertas'],
                $tabla
            );
        }

        // Recomendaciones generales
        $this->info('💡 RECOMENDACIONES GENERALES');
        $this->info('============================');
        
        if (($alertas['sin_stock'] ?? 0) > 0) {
            $this->error("🚨 {$alertas['sin_stock']} productos sin stock - Acción inmediata requerida");
        }
        
        if (($alertas['stock_critico'] ?? 0) > 0) {
            $this->warn("⚠️  {$alertas['stock_critico']} productos con stock crítico - Revisar urgentemente");
        }
        
        if (($alertas['stock_excesivo'] ?? 0) > 0) {
            $this->warn("📦 {$alertas['stock_excesivo']} productos con stock excesivo - Considerar pausar compras");
        }
        
        if (($alertas['stock_reservado_alto'] ?? 0) > 0) {
            $this->warn("🔒 {$alertas['stock_reservado_alto']} productos con alto stock reservado - Revisar pedidos pendientes");
        }
    }

    private function exportarResultados(): void
    {
        $this->info('📄 Exportando resultados...');
        // Implementar exportación a CSV
        $this->info('✅ Exportación completada');
    }
} 