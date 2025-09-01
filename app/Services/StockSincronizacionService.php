<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\VarianteProducto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockSincronizacionService
{
    /**
     * Sincronizar stock de un producto específico con sus variantes
     */
    public function sincronizarProducto(int $productoId): array
    {
        try {
            $producto = Producto::with('variantes')->findOrFail($productoId);
            
            $stockAnterior = $producto->stock;
            $producto->sincronizarStockConVariantes();
            $stockNuevo = $producto->fresh()->stock;
            
            return [
                'success' => true,
                'producto_id' => $productoId,
                'nombre_producto' => $producto->nombre_producto,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'variantes_count' => $producto->variantes->count(),
                'cambio' => $stockNuevo - $stockAnterior
            ];
        } catch (\Exception $e) {
            Log::error('Error al sincronizar stock del producto', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Sincronizar stock de todos los productos
     */
    public function sincronizarTodosLosProductos(): array
    {
        $productos = Producto::with('variantes')->get();
        $resultados = [];
        $exitosos = 0;
        $fallidos = 0;

        foreach ($productos as $producto) {
            $resultado = $this->sincronizarProducto($producto->producto_id);
            $resultados[] = $resultado;
            
            if ($resultado['success']) {
                $exitosos++;
            } else {
                $fallidos++;
            }
        }

        return [
            'total_productos' => $productos->count(),
            'exitosos' => $exitosos,
            'fallidos' => $fallidos,
            'resultados' => $resultados
        ];
    }

    /**
     * Obtener reporte de sincronización de stock
     */
    public function obtenerReporteSincronizacion(): array
    {
        $productos = Producto::with('variantes')->get();
        
        $reporte = [
            'total_productos' => $productos->count(),
            'productos_con_variantes' => $productos->filter(fn($p) => $p->tieneVariantes())->count(),
            'productos_sin_variantes' => $productos->filter(fn($p) => !$p->tieneVariantes())->count(),
            'stock_total_sistema' => $productos->sum('stock'),
            'stock_total_variantes' => $productos->sum(fn($p) => $p->getStockTotalVariantesAttribute()),
            'productos_desincronizados' => [],
            'resumen_variantes' => []
        ];

        // Verificar productos desincronizados
        foreach ($productos as $producto) {
            if ($producto->tieneVariantes()) {
                $stockCalculado = $producto->getStockTotalVariantesAttribute();
                $stockActual = $producto->stock;
                
                if ($stockCalculado !== $stockActual) {
                    $reporte['productos_desincronizados'][] = [
                        'producto_id' => $producto->producto_id,
                        'nombre' => $producto->nombre_producto,
                        'stock_actual' => $stockActual,
                        'stock_calculado' => $stockCalculado,
                        'diferencia' => $stockCalculado - $stockActual
                    ];
                }
            }
        }

        // Resumen por variantes
        $variantes = VarianteProducto::with('producto')->get();
        $reporte['resumen_variantes'] = [
            'total_variantes' => $variantes->count(),
            'variantes_con_stock' => $variantes->where('stock_disponible', '>', 0)->count(),
            'variantes_sin_stock' => $variantes->where('stock_disponible', 0)->count(),
            'stock_total_variantes' => $variantes->sum('stock_disponible'),
            'colores_mas_populares' => $variantes->groupBy('nombre')
                ->map(fn($group) => $group->sum('stock_disponible'))
                ->sortDesc()
                ->take(5)
                ->toArray()
        ];

        return $reporte;
    }

    /**
     * Verificar integridad del stock entre productos y variantes
     */
    public function verificarIntegridadStock(): array
    {
        $productos = Producto::with('variantes')->get();
        $problemas = [];
        $advertencias = [];

        foreach ($productos as $producto) {
            // Verificar productos con variantes pero sin stock en el producto principal
            if ($producto->tieneVariantes() && $producto->stock > 0) {
                $stockVariantes = $producto->getStockTotalVariantesAttribute();
                if ($producto->stock !== $stockVariantes) {
                    $problemas[] = [
                        'tipo' => 'desincronizacion',
                        'producto_id' => $producto->producto_id,
                        'nombre' => $producto->nombre_producto,
                        'descripcion' => "Stock del producto ({$producto->stock}) no coincide con suma de variantes ({$stockVariantes})"
                    ];
                }
            }

            // Verificar productos sin variantes pero con stock
            if (!$producto->tieneVariantes() && $producto->stock > 0) {
                // Esto está bien, es un producto sin variantes
            }

            // Verificar variantes con stock pero producto sin stock
            if ($producto->tieneVariantes() && $producto->stock === 0) {
                $stockVariantes = $producto->getStockTotalVariantesAttribute();
                if ($stockVariantes > 0) {
                    $advertencias[] = [
                        'tipo' => 'stock_variantes_sin_producto',
                        'producto_id' => $producto->producto_id,
                        'nombre' => $producto->nombre_producto,
                        'descripcion' => "Producto sin stock pero variantes tienen {$stockVariantes} unidades"
                    ];
                }
            }
        }

        return [
            'problemas' => $problemas,
            'advertencias' => $advertencias,
            'total_problemas' => count($problemas),
            'total_advertencias' => count($advertencias)
        ];
    }

    /**
     * Corregir automáticamente problemas de sincronización
     */
    public function corregirSincronizacion(): array
    {
        $productos = Producto::with('variantes')->get();
        $corregidos = 0;
        $errores = 0;

        foreach ($productos as $producto) {
            try {
                if ($producto->tieneVariantes()) {
                    $stockAnterior = $producto->stock;
                    $producto->sincronizarStockConVariantes();
                    $stockNuevo = $producto->fresh()->stock;
                    
                    if ($stockAnterior !== $stockNuevo) {
                        $corregidos++;
                        Log::info('Stock corregido automáticamente', [
                            'producto_id' => $producto->producto_id,
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo' => $stockNuevo
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $errores++;
                Log::error('Error al corregir stock del producto', [
                    'producto_id' => $producto->producto_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'corregidos' => $corregidos,
            'errores' => $errores,
            'total_productos' => $productos->count()
        ];
    }
}
