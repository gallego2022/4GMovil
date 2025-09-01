<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\MovimientoInventario;
use App\Models\VarianteProducto;
use App\Models\MovimientoInventarioVariante;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InventarioService
{
    /**
     * Obtener productos con stock bajo
     */
    public function getProductosStockBajo(): Collection
    {
        return Producto::with(['categoria', 'marca'])
            ->stockBajo()
            ->activos()
            ->orderBy('stock')
            ->get();
    }

    /**
     * Obtener productos con stock crítico
     */
    public function getProductosStockCritico(): Collection
    {
        return Producto::with(['categoria', 'marca'])
            ->stockCritico()
            ->activos()
            ->orderBy('stock')
            ->get();
    }

    /**
     * Obtener productos sin stock
     */
    public function getProductosSinStock(): Collection
    {
        return Producto::with(['categoria', 'marca'])
            ->sinStock()
            ->activos()
            ->get();
    }

    /**
     * Obtener productos con stock excesivo
     */
    public function getProductosStockExcesivo(): Collection
    {
        return Producto::activos()->get()->filter(function ($producto) {
            $stockOptimo = $this->calcularStockOptimo($producto->producto_id);
            return !empty($stockOptimo) && $producto->stock > $stockOptimo['stock_maximo_recomendado'];
        });
    }

    /**
     * Registrar entrada de inventario
     */
    public function registrarEntrada(int $productoId, int $cantidad, string $motivo, ?int $usuarioId = null, ?string $referencia = null): bool
    {
        try {
            $producto = Producto::findOrFail($productoId);
            $producto->registrarEntrada($cantidad, $motivo, $usuarioId, $referencia);
            return true;
        } catch (\Exception $e) {
            Log::error('Error al registrar entrada de inventario', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Registrar salida de inventario
     */
    public function registrarSalida(int $productoId, int $cantidad, string $motivo, ?int $usuarioId = null, ?int $pedidoId = null): bool
    {
        try {
            $producto = Producto::findOrFail($productoId);
            return $producto->registrarSalida($cantidad, $motivo, $usuarioId, $pedidoId);
        } catch (\Exception $e) {
            Log::error('Error al registrar salida de inventario', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Ajustar stock de un producto
     */
    public function ajustarStock(int $productoId, int $nuevoStock, string $motivo, ?int $usuarioId = null): bool
    {
        try {
            $producto = Producto::findOrFail($productoId);
            $producto->ajustarStock($nuevoStock, $motivo, $usuarioId);
            return true;
        } catch (\Exception $e) {
            Log::error('Error al ajustar stock', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener movimientos de un producto específico
     */
    public function getMovimientosProducto(int $productoId, ?Carbon $fechaInicio = null, ?Carbon $fechaFin = null): Collection
    {
        $query = MovimientoInventario::with(['producto', 'usuario'])
            ->where('producto_id', $productoId);

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Obtener reporte de movimientos
     */
    public function getReporteMovimientos(Carbon $fechaInicio, Carbon $fechaFin): array
    {
        $movimientos = MovimientoInventario::with(['producto', 'usuario'])
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->get();

        $resumen = [
            'total_entradas' => $movimientos->where('tipo_movimiento', 'entrada')->sum('cantidad'),
            'total_salidas' => $movimientos->where('tipo_movimiento', 'salida')->sum('cantidad'),
            'total_ajustes' => $movimientos->where('tipo_movimiento', 'ajuste')->count(),
            'total_devoluciones' => $movimientos->where('tipo_movimiento', 'devolucion')->sum('cantidad'),
            'valor_entradas' => $movimientos->where('tipo_movimiento', 'entrada')->sum(function ($mov) {
                return $mov->cantidad * ($mov->producto->costo_unitario ?? 0);
            }),
            'valor_salidas' => $movimientos->where('tipo_movimiento', 'salida')->sum(function ($mov) {
                return $mov->cantidad * ($mov->producto->costo_unitario ?? 0);
            }),
            'valor_devoluciones' => $movimientos->where('tipo_movimiento', 'devolucion')->sum(function ($mov) {
                return $mov->cantidad * ($mov->producto->costo_unitario ?? 0);
            }),
            'total_movimientos' => $movimientos->count(),
            'productos_afectados' => $movimientos->pluck('producto_id')->unique()->count()
        ];

        return [
            'movimientos' => $movimientos,
            'resumen' => $resumen
        ];
    }

    /**
     * Obtener valor total del inventario
     */
    public function getValorTotalInventario(): float
    {
        return Producto::activos()
            ->sum(DB::raw('stock * costo_unitario'));
    }

    /**
     * Obtener valor del inventario por categoría
     */
    public function getValorInventarioPorCategoria(): SupportCollection
    {
        return Categoria::with(['productos' => function ($query) {
            $query->activos();
        }])
        ->get()
        ->map(function ($categoria) {
            $valorTotal = $categoria->productos->sum(function ($producto) {
                return $producto->stock * $producto->costo_unitario;
            });

            return [
                'categoria' => $categoria,
                'valor_total' => $valorTotal,
                'productos_count' => $categoria->productos->count(),
                'stock_total' => $categoria->productos->sum('stock')
            ];
        })
        ->sortByDesc('valor_total');
    }

    /**
     * Obtener productos más vendidos (por cantidad de salidas)
     */
    public function getProductosMasVendidos(int $limite = 10, ?Carbon $fechaInicio = null, ?Carbon $fechaFin = null): Collection
    {
        $query = MovimientoInventario::with(['producto'])
            ->where('tipo_movimiento', 'salida');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $resultados = $query->select('producto_id', DB::raw('SUM(cantidad) as total_vendido'))
            ->groupBy('producto_id')
            ->orderByDesc('total_vendido')
            ->limit($limite)
            ->get();

        // Obtener los productos completos con la información de ventas
        $productosIds = $resultados->pluck('producto_id');
        $productos = Producto::with(['categoria', 'marca', 'imagenes'])
            ->whereIn('producto_id', $productosIds)
            ->get();

        // Combinar los productos con sus totales de venta
        return $productos->map(function ($producto) use ($resultados) {
            $ventaInfo = $resultados->where('producto_id', $producto->producto_id)->first();
            $producto->total_vendido = $ventaInfo ? $ventaInfo->total_vendido : 0;
            return $producto;
        })->sortByDesc('total_vendido');
    }

    /**
     * Obtener alertas de inventario (método original para compatibilidad)
     */
    public function getAlertasInventario(): array
    {
        return [
            'stock_critico' => $this->getProductosStockCritico()->count(),
            'stock_bajo' => $this->getProductosStockBajo()->count(),
            'sin_stock' => $this->getProductosSinStock()->count(),
            'stock_excesivo' => $this->getProductosStockExcesivo()->count(),
            'productos_inactivos' => Producto::where('activo', false)->count()
        ];
    }

    /**
     * Obtener alertas de inventario mejoradas
     */
    public function getAlertasInventarioMejoradas(): array
    {
        // Usar los mismos scopes que el modelo para consistencia
        $productosSinStock = Producto::sinStock()->activos()->count();
        $productosStockCritico = Producto::stockCritico()->activos()->count();
        $productosStockBajo = Producto::stockBajo()->activos()->count();
        $productosStockExcesivo = Producto::stockExcesivo()->activos()->count();
        
        // Calcular stock reservado alto
        $productosStockReservadoAlto = Producto::activos()
            ->where('stock_reservado', '>', 0)
            ->whereRaw('stock_reservado > stock * 0.3')
            ->count();
            
        $alertas = [
            'stock_critico' => $productosStockCritico,
            'stock_bajo' => $productosStockBajo,
            'sin_stock' => $productosSinStock,
            'stock_excesivo' => $productosStockExcesivo,
            'necesita_reabastecimiento' => $productosStockBajo + $productosStockCritico,
            'stock_reservado_alto' => $productosStockReservadoAlto,
            'productos_inactivos' => Producto::where('activo', false)->count()
        ];

        return $alertas;
    }

    /**
     * Obtener productos con alertas inteligentes
     */
    public function getProductosConAlertasInteligentes(): array
    {
        $productos = Producto::activos()->with(['categoria', 'marca'])->get();
        $productosConAlertas = [];

        foreach ($productos as $producto) {
            $stockOptimo = $this->calcularStockOptimo($producto->producto_id);
            $alertas = [];

            if (empty($stockOptimo)) {
                // Alertas tradicionales si no hay datos de venta
                if ($producto->stock <= 0) {
                    $alertas[] = 'sin_stock';
                } elseif ($producto->stockCritico) {
                    $alertas[] = 'stock_critico';
                } elseif ($producto->stockBajo) {
                    $alertas[] = 'stock_bajo';
                }
            } else {
                // Alertas inteligentes basadas en demanda
                if ($producto->stock_disponible <= 0) {
                    $alertas[] = 'sin_stock';
                } elseif ($producto->stock_disponible < $stockOptimo['stock_minimo_recomendado'] * 0.2) {
                    $alertas[] = 'stock_critico';
                } elseif ($producto->stock_disponible < $stockOptimo['stock_minimo_recomendado']) {
                    $alertas[] = 'stock_bajo';
                }

                if ($producto->stock_disponible < $stockOptimo['stock_minimo_recomendado']) {
                    $alertas[] = 'necesita_reabastecimiento';
                }

                if ($producto->stock > $stockOptimo['stock_maximo_recomendado']) {
                    $alertas[] = 'stock_excesivo';
                }

                if ($producto->stock_reservado > $producto->stock * 0.3) {
                    $alertas[] = 'stock_reservado_alto';
                }
            }

            if (!empty($alertas)) {
                $productosConAlertas[] = [
                    'producto' => $producto,
                    'alertas' => $alertas,
                    'stock_optimo' => $stockOptimo
                ];
            }
        }

        return $productosConAlertas;
    }

    /**
     * Generar reporte de inventario en PDF
     */
    public function generarReporteInventario(): array
    {
        $fechaActual = now();
        $mesAnterior = $fechaActual->copy()->subMonth();

        return [
            'fecha_reporte' => $fechaActual->format('d/m/Y H:i'),
            'resumen_general' => [
                'total_productos' => Producto::count(),
                'productos_activos' => Producto::activos()->count(),
                'productos_inactivos' => Producto::where('activo', false)->count(),
                'valor_total_inventario' => $this->getValorTotalInventario(),
                'stock_total' => Producto::activos()->sum('stock'),
                'productos_stock_bajo' => $this->getProductosStockBajo()->count(),
                'productos_stock_critico' => $this->getProductosStockCritico()->count(),
                'productos_sin_stock' => $this->getProductosSinStock()->count(),
                'productos_stock_excesivo' => $this->getProductosStockExcesivo()->count()
            ],
            'alertas' => $this->getAlertasInventario(),
            'productos_stock_bajo' => $this->getProductosStockBajo(),
            'productos_stock_critico' => $this->getProductosStockCritico(),
            'productos_sin_stock' => $this->getProductosSinStock(),
            'productos_stock_excesivo' => $this->getProductosStockExcesivo(),
            'valor_por_categoria' => $this->getValorInventarioPorCategoria(),
            'productos_mas_vendidos' => $this->getProductosMasVendidos(10, $mesAnterior, $fechaActual),
            'movimientos_mes' => $this->getReporteMovimientos($mesAnterior, $fechaActual)
        ];
    }

    /**
     * Calcular demanda promedio por período
     */
    public function calcularDemandaPromedio(int $productoId, int $dias = 30): array
    {
        $fechaInicio = now()->subDays($dias);
        
        $ventas = MovimientoInventario::where('producto_id', $productoId)
            ->where('tipo_movimiento', 'salida')
            ->where('created_at', '>=', $fechaInicio)
            ->get();

        $totalVendido = $ventas->sum('cantidad');
        $ventaPromedioDiaria = $dias > 0 ? $totalVendido / $dias : 0;
        $ventaPromedioSemanal = $ventaPromedioDiaria * 7;
        $ventaPromedioMensual = $ventaPromedioDiaria * 30;

        return [
            'total_vendido' => $totalVendido,
            'venta_promedio_diaria' => $ventaPromedioDiaria,
            'venta_promedio_semanal' => $ventaPromedioSemanal,
            'venta_promedio_mensual' => $ventaPromedioMensual,
            'dias_analizados' => $dias
        ];
    }

    /**
     * Calcular stock óptimo recomendado
     */
    public function calcularStockOptimo(int $productoId): array
    {
        $producto = Producto::find($productoId);
        if (!$producto) {
            return [];
        }

        $demanda = $this->calcularDemandaPromedio($productoId);
        $tiempoReposicion = 7; // Días promedio para reabastecer
        $factorSeguridad = 1.5; // 50% extra como seguridad

        $stockOptimo = $demanda['venta_promedio_diaria'] * $tiempoReposicion * $factorSeguridad;
        $stockMinimoRecomendado = $demanda['venta_promedio_diaria'] * $tiempoReposicion;
        $stockMaximoRecomendado = $stockOptimo * 2;

        return [
            'stock_actual' => $producto->stock,
            'stock_disponible' => $producto->stock_disponible,
            'stock_reservado' => $producto->stock_reservado,
            'stock_minimo_actual' => $producto->stock_minimo,
            'stock_maximo_actual' => $producto->stock_maximo,
            'stock_optimo_recomendado' => round($stockOptimo),
            'stock_minimo_recomendado' => round($stockMinimoRecomendado),
            'stock_maximo_recomendado' => round($stockMaximoRecomendado),
            'demanda' => $demanda,
            'tiempo_reposicion' => $tiempoReposicion,
            'factor_seguridad' => $factorSeguridad
        ];
    }

    /**
     * Obtener productos que necesitan reabastecimiento
     */
    public function getProductosNecesitanReabastecimiento(): Collection
    {
        return Producto::activos()->get()->filter(function ($producto) {
            $stockOptimo = $this->calcularStockOptimo($producto->producto_id);
            return !empty($stockOptimo) && $producto->stock_disponible < $stockOptimo['stock_minimo_recomendado'];
        });
    }

    // ==================== MÉTODOS PARA VARIANTES ====================

    /**
     * Obtener variantes con stock bajo
     */
    public function getVariantesStockBajo(): Collection
    {
        return VarianteProducto::with(['producto', 'imagenes'])
            ->where('disponible', true)
            ->whereRaw('stock_disponible <= stock_minimo')
            ->orderBy('stock_disponible')
            ->get();
    }

    /**
     * Obtener variantes sin stock
     */
    public function getVariantesSinStock(): Collection
    {
        return VarianteProducto::with(['producto', 'imagenes'])
            ->where('disponible', true)
            ->where('stock_disponible', 0)
            ->get();
    }

    /**
     * Obtener variantes que necesitan reposición
     */
    public function getVariantesNecesitanReposicion(): Collection
    {
        return VarianteProducto::with(['producto', 'imagenes'])
            ->where('disponible', true)
            ->whereRaw('stock_disponible <= stock_minimo')
            ->get();
    }

    /**
     * Registrar entrada de stock para una variante
     */
    public function registrarEntradaVariante(int $varianteId, int $cantidad, string $motivo, ?int $usuarioId = null, ?string $referencia = null): bool
    {
        try {
            $variante = VarianteProducto::findOrFail($varianteId);
            $resultado = $variante->registrarEntrada($cantidad, $motivo, $usuarioId, $referencia);
            
            if ($resultado) {
                Log::info('Entrada de stock registrada para variante', [
                    'variante_id' => $varianteId,
                    'cantidad' => $cantidad,
                    'motivo' => $motivo,
                    'usuario_id' => $usuarioId
                ]);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error al registrar entrada de stock para variante', [
                'variante_id' => $varianteId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Registrar salida de stock para una variante
     */
    public function registrarSalidaVariante(int $varianteId, int $cantidad, string $motivo, ?int $usuarioId = null, ?string $referencia = null): bool
    {
        try {
            $variante = VarianteProducto::findOrFail($varianteId);
            $resultado = $variante->registrarSalida($cantidad, $motivo, $usuarioId, $referencia);
            
            if ($resultado) {
                Log::info('Salida de stock registrada para variante', [
                    'variante_id' => $varianteId,
                    'cantidad' => $cantidad,
                    'motivo' => $motivo,
                    'usuario_id' => $usuarioId
                ]);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error al registrar salida de stock para variante', [
                'variante_id' => $varianteId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Ajustar stock de una variante
     */
    public function ajustarStockVariante(int $varianteId, int $nuevoStock, string $motivo, ?int $usuarioId = null): bool
    {
        try {
            $variante = VarianteProducto::findOrFail($varianteId);
            $stockAnterior = $variante->stock_disponible;
            $diferencia = $nuevoStock - $stockAnterior;

            if ($diferencia > 0) {
                return $this->registrarEntradaVariante($varianteId, $diferencia, $motivo, $usuarioId);
            } elseif ($diferencia < 0) {
                return $this->registrarSalidaVariante($varianteId, abs($diferencia), $motivo, $usuarioId);
            }
            
            return true; // No hay diferencia
        } catch (\Exception $e) {
            Log::error('Error al ajustar stock de variante', [
                'variante_id' => $varianteId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener reporte de inventario de variantes
     */
    public function getReporteInventarioVariantes(int $productoId = null): array
    {
        try {
            $query = VarianteProducto::with(['producto', 'imagenes'])
                ->select([
                    'variantes_producto.*',
                    DB::raw('(SELECT SUM(cantidad) FROM movimientos_inventario_variantes WHERE variante_id = variantes_producto.variante_id AND tipo_movimiento = "entrada") as total_entradas'),
                    DB::raw('(SELECT SUM(cantidad) FROM movimientos_inventario_variantes WHERE variante_id = variantes_producto.variante_id AND tipo_movimiento = "salida") as total_salidas')
                ]);

            if ($productoId) {
                $query->where('producto_id', $productoId);
            }

            $variantes = $query->get();

            return [
                'total_variantes' => $variantes->count(),
                'variantes_con_stock' => $variantes->where('stock_disponible', '>', 0)->count(),
                'variantes_sin_stock' => $variantes->where('stock_disponible', 0)->count(),
                'variantes_necesitan_reposicion' => $variantes->filter(function($v) { return $v->necesitaReposicion(); })->count(),
                'stock_total' => $variantes->sum('stock_disponible'),
                'valor_total_inventario' => $variantes->sum(function($v) { return $v->stock_disponible * $v->precio_final; }),
                'detalle_variantes' => $variantes->map(function($variante) {
                    return [
                        'variante_id' => $variante->variante_id,
                        'producto' => $variante->producto->nombre_producto,
                        'color' => $variante->nombre,
                        'stock_disponible' => $variante->stock_disponible,
                        'stock_minimo' => $variante->stock_minimo,
                        'stock_maximo' => $variante->stock_maximo,
                        'precio_unitario' => $variante->precio_final,
                        'valor_inventario' => $variante->stock_disponible * $variante->precio_final,
                        'necesita_reposicion' => $variante->necesitaReposicion(),
                        'disponible' => $variante->disponible,
                        'total_entradas' => $variante->total_entradas ?? 0,
                        'total_salidas' => $variante->total_salidas ?? 0
                    ];
                })
            ];
        } catch (\Exception $e) {
            Log::error('Error al generar reporte de inventario de variantes', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener alertas de inventario incluyendo variantes
     */
    public function getAlertasInventarioCompletas(): array
    {
        $alertasProductos = $this->getAlertasInventario();
        $variantesStockBajo = $this->getVariantesStockBajo()->count();
        $variantesSinStock = $this->getVariantesSinStock()->count();
        $variantesNecesitanReposicion = $this->getVariantesNecesitanReposicion()->count();

        return array_merge($alertasProductos, [
            'variantes_stock_bajo' => $variantesStockBajo,
            'variantes_sin_stock' => $variantesSinStock,
            'variantes_necesitan_reposicion' => $variantesNecesitanReposicion,
            'total_alertas_variantes' => $variantesStockBajo + $variantesSinStock + $variantesNecesitanReposicion
        ]);
    }
} 