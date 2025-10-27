<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\MovimientoInventario;
use App\Models\VarianteProducto;
use App\Models\Categoria;
use App\Services\RedisCacheService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InventarioService
{
    protected $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Obtener productos con stock bajo (60% del stock inicial)
     */
    public function getProductosStockBajo(): Collection
    {
        return Producto::with(['categoria', 'marca'])
            ->whereHas('variantes', function($query) {
                $query->where('stock', '>', 0);
            })
            ->get()
            ->filter(function($producto) {
                return $producto->stock_bajo;
            });
    }

    /**
     * Obtener productos con stock crítico (20% del stock inicial)
     */
    public function getProductosStockCritico(): Collection
    {
        return Producto::with(['categoria', 'marca'])
            ->whereHas('variantes', function($query) {
                $query->where('stock', '>', 0);
            })
            ->get()
            ->filter(function($producto) {
                return $producto->stock_critico;
            });
    }

    /**
     * Obtener productos sin stock
     */
    public function getProductosSinStock(): Collection
    {
        return Producto::with(['categoria', 'marca'])
            ->whereHas('variantes', function($query) {
                $query->where('stock', 0);
            })
            ->get()
            ->filter(function($producto) {
                return $producto->sin_stock;
            });
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
            ->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_movimiento', 'desc')
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
            $query->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);
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
     * Obtener alertas de inventario con la nueva lógica
     */
    public function getAlertasInventario(): array
    {
        $productosStockBajo = $this->getProductosStockBajo();
        $productosStockCritico = $this->getProductosStockCritico();
        $productosSinStock = $this->getProductosSinStock();

        return [
            'productos_stock_bajo' => $productosStockBajo->count(),
            'productos_stock_critico' => $productosStockCritico->count(),
            'productos_sin_stock' => $productosSinStock->count(),
            'total_alertas' => $productosStockBajo->count() + $productosStockCritico->count() + $productosSinStock->count(),
            'productos_stock_bajo_lista' => $productosStockBajo,
            'productos_stock_critico_lista' => $productosStockCritico,
            'productos_sin_stock_lista' => $productosSinStock
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
                } elseif ($producto->stock_disponible < $stockOptimo['stock_recomendado'] * 0.2) {
                    $alertas[] = 'stock_critico';
                } elseif ($producto->stock_disponible < $stockOptimo['stock_recomendado']) {
                    $alertas[] = 'stock_bajo';
                }

                if ($producto->stock_disponible < $stockOptimo['stock_recomendado']) {
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
            'stock_maximo_actual' => $producto->stock_maximo,
            'stock_optimo_recomendado' => round($stockOptimo),
            'stock_recomendado' => round($stockMinimoRecomendado),
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
            return !empty($stockOptimo) && $producto->stock_disponible < $stockOptimo['stock_recomendado'];
        });
    }

    // ==================== MÉTODOS PARA VARIANTES ====================

    /**
     * Obtener variantes con stock bajo
     */
    public function getVariantesStockBajo(): Collection
    {
        return VarianteProducto::with(['producto', 'imagenes'])
            ->whereRaw('stock_disponible <= stock')
            ->orderBy('stock_disponible')
            ->get();
    }

    /**
     * Obtener variantes sin stock
     */
    public function getVariantesSinStock(): Collection
    {
        return VarianteProducto::with(['producto', 'imagenes'])
            ->where('stock_disponible', 0)
            ->get();
    }

    /**
     * Obtener variantes que necesitan reposición
     */
    public function getVariantesNecesitanReposicion(): Collection
    {
        return VarianteProducto::with(['producto', 'imagenes'])
            ->whereRaw('stock_disponible <= stock')
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
     * Obtener datos para reporte de inventario
     */
    public function getReporteData(array $filtros = []): array
    {
        try {
            $fechaInicio = $filtros['fecha_inicio'] ?? now()->subMonth();
            $fechaFin = $filtros['fecha_fin'] ?? now();
            $categoriaId = $filtros['categoria_id'] ?? null;
            $marcaId = $filtros['marca_id'] ?? null;
            $tipoReporte = $filtros['tipo_reporte'] ?? 'general';
            $incluirVariantes = $filtros['incluir_variantes'] ?? true;

            // Obtener productos con sus relaciones
            $query = Producto::with(['categoria', 'marca', 'variantes']);

            if ($categoriaId) {
                $query->where('categoria_id', $categoriaId);
            }

            if ($marcaId) {
                $query->where('marca_id', $marcaId);
            }

            $productos = $query->get();

            // Obtener movimientos (tabla unificada) solo de variantes para el reporte
            $movimientos = MovimientoInventario::with(['variante.producto', 'usuario'])
                ->whereNotNull('variante_id')
                ->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin])
                ->orderBy('fecha_movimiento', 'desc')
                ->get();

            // Calcular estadísticas
            $estadisticas = $this->calcularEstadisticasReporte($productos, $movimientos, $fechaInicio, $fechaFin);

            // Obtener categorías y marcas para filtros
            $categorias = \App\Models\Categoria::orderBy('nombre')->get();
            $marcas = \App\Models\Marca::orderBy('nombre')->get();

            return [
                'productos' => $productos,
                'movimientos' => $movimientos,
                'estadisticas' => $estadisticas,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'filtros' => $filtros,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'tipo_reporte' => $tipoReporte,
                'incluir_variantes' => $incluirVariantes
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener datos de reporte', [
                'filtros' => $filtros,
                'error' => $e->getMessage()
            ]);
            
            return [
                'productos' => collect(),
                'movimientos' => collect(),
                'estadisticas' => [],
                'categorias' => collect(),
                'marcas' => collect(),
                'filtros' => $filtros,
                'fecha_inicio' => now()->subMonth(),
                'fecha_fin' => now(),
                'tipo_reporte' => 'general',
                'incluir_variantes' => true
            ];
        }
    }

    /**
     * Calcular estadísticas para el reporte
     */
    private function calcularEstadisticasReporte($productos, $movimientos, $fechaInicio, $fechaFin): array
    {
        $totalProductos = $productos->count();
        $totalVariantes = $productos->sum(function($producto) {
            return $producto->variantes->count();
        });
        
        $stockTotal = $productos->sum('stock');
        $valorInventario = $productos->sum(function($producto) {
            return $producto->stock * $producto->precio;
        });

        $movimientosEntrada = $movimientos->where('tipo_movimiento', 'entrada')->sum('cantidad');
        $movimientosSalida = $movimientos->where('tipo_movimiento', 'salida')->sum('cantidad');

        // Cálculo de umbrales basado en la lógica documentada (60% bajo, 20% crítico del stock inicial)
        $productosStockCritico = $productos->filter(function($producto) {
            $stockInicial = $producto->stock_inicial ?? ($producto->stock ?? 0);
            $umbralCritico = $stockInicial > 0 ? (int) ceil(($stockInicial * 20) / 100) : 5;
            return ($producto->stock ?? 0) <= $umbralCritico;
        })->count();

        $productosStockBajo = $productos->filter(function($producto) {
            $stockInicial = $producto->stock_inicial ?? ($producto->stock ?? 0);
            $umbralBajo = $stockInicial > 0 ? (int) ceil(($stockInicial * 60) / 100) : 10;
            $umbralCritico = $stockInicial > 0 ? (int) ceil(($stockInicial * 20) / 100) : 5;
            $stockActual = $producto->stock ?? 0;
            return $stockActual <= $umbralBajo && $stockActual > $umbralCritico;
        })->count();

        $productosSinStock = $productos->filter(function($producto){
            return ($producto->stock ?? 0) <= 0;
        })->count();

        return [
            'total_productos' => $totalProductos,
            'total_variantes' => $totalVariantes,
            'stock_total' => $stockTotal,
            'valor_inventario' => $valorInventario,
            'movimientos_entrada' => $movimientosEntrada,
            'movimientos_salida' => $movimientosSalida,
            'productos_stock_bajo' => $productosStockBajo,
            'productos_stock_critico' => $productosStockCritico,
            'productos_sin_stock' => $productosSinStock,
            'periodo' => [
                'inicio' => $fechaInicio->format('d/m/Y'),
                'fin' => $fechaFin->format('d/m/Y')
            ]
        ];
    }

    /**
     * Obtener reporte de inventario de variantes
     */
    public function getReporteInventarioVariantes(?int $productoId = null): array
    {
        try {
            $query = VarianteProducto::with(['producto', 'imagenes'])
                ->select([
                    'variantes_producto.*',
                    DB::raw('(SELECT SUM(cantidad) FROM movimientos_inventario WHERE variante_id = variantes_producto.variante_id AND tipo_movimiento = "entrada") as total_entradas'),
                    DB::raw('(SELECT SUM(cantidad) FROM movimientos_inventario WHERE variante_id = variantes_producto.variante_id AND tipo_movimiento = "salida") as total_salidas')
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
                        'stock' => $variante->stock,
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

    /**
     * Obtener datos del dashboard de inventario
     */
    public function getDashboardData(): array
    {
        return $this->cacheService->remember('inventario:dashboard', 600, function () {
            return [
                'alertas' => $this->getAlertasInventarioCompletas(),
                'productosStockBajo' => $this->getProductosStockBajo(),
                'productosStockCritico' => $this->getProductosStockCritico(),
                'valorTotal' => $this->getValorTotalInventario(),
                'variantesStockBajo' => $this->getVariantesStockBajo(),
                'variantesSinStock' => $this->getVariantesSinStock(),
                'reporteVariantes' => $this->getReporteInventarioVariantes(),
                'productosConVariantes' => $this->getProductosConVariantes(),
                'stockTotalVariantes' => $this->getStockTotalVariantes(),
                'valorTotalVariantes' => $this->getValorTotalVariantes()
            ];
        });
    }

    /**
     * Obtener productos que tienen variantes
     */
    public function getProductosConVariantes(): Collection
    {
        return Producto::with(['variantes', 'categoria', 'marca'])
            ->whereHas('variantes')
            ->activos()
            ->get();
    }

    /**
     * Obtener stock total de todas las variantes
     */
    public function getStockTotalVariantes(): int
    {
        return VarianteProducto::sum('stock');
    }

    /**
     * Obtener valor total de todas las variantes
     */
    public function getValorTotalVariantes(): float
    {
        return VarianteProducto::get()
            ->sum(function($variante) {
                return $variante->stock * $variante->precio_final;
            });
    }

    /**
     * Obtener datos de fallback para el dashboard
     */
    public function getDashboardDataFallback(): array
    {
        return [
            'alertas' => [
                'stock_critico' => 0,
                'stock_bajo' => 0,
                'sin_stock' => 0,
                'stock_excesivo' => 0,
                'necesita_reabastecimiento' => 0,
                'stock_reservado_alto' => 0,
                'productos_inactivos' => 0
            ],
            'productosStockBajo' => collect([]),
            'productosStockCritico' => collect([]),
            'valorTotal' => 0,
            'variantesStockBajo' => collect([]),
            'variantesSinStock' => collect([]),
            'reporteVariantes' => []
        ];
    }


    /**
     * Obtener datos de movimientos
     */
    public function getMovimientosData(array $filtros): array
    {
        $fechaInicio = $filtros['fecha_inicio'] ?? now()->subMonth();
        $fechaFin = $filtros['fecha_fin'] ?? now();
        $productoId = $filtros['producto_id'] ?? null;
        $tipo = $filtros['tipo'] ?? null;
        $usuarioId = $filtros['usuario_id'] ?? null;

        // Obtener movimientos desde la tabla unificada, restringiendo a variantes para esta vista
        $query = MovimientoInventario::with(['variante.producto.categoria', 'variante.producto.marca', 'usuario'])
            ->whereNotNull('variante_id')
            ->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_movimiento', 'desc');

        // Aplicar filtros
        if ($productoId) {
            $query->whereHas('variante', function($q) use ($productoId) {
                $q->where('producto_id', $productoId);
            });
        }

        if ($tipo) {
            $query->where('tipo_movimiento', $tipo);
        }

        if ($usuarioId) {
            $query->where('usuario_id', $usuarioId);
        }

        $movimientos = $query->get();

        // Calcular resumen
        $resumen = [
            'total_entradas' => $movimientos->where('tipo_movimiento', 'entrada')->sum('cantidad'),
            'total_salidas' => $movimientos->where('tipo_movimiento', 'salida')->sum('cantidad'),
            'total_ajustes' => $movimientos->where('tipo_movimiento', 'ajuste')->sum('cantidad'),
            'variantes_afectadas' => $movimientos->unique('variante_id')->count(),
            'productos_afectados' => $movimientos->unique(function($m) {
                return $m->variante->producto_id;
            })->count(),
            'movimientos_totales' => $movimientos->count()
        ];

        // Obtener productos para filtros
        $productos = Producto::orderBy('nombre_producto')->get();

        // Obtener producto específico si se filtró
        $producto = $productoId ? Producto::find($productoId) : null;

        return [
            'movimientos' => $movimientos,
            'productos' => $productos,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'productoId' => $productoId,
            'producto' => $producto,
            'resumen' => $resumen,
            'tipo' => $tipo,
            'usuarioId' => $usuarioId
        ];
    }

    /**
     * Obtener resumen de inventario para un período
     */
    public function getResumenInventario(Carbon $fechaInicio, Carbon $fechaFin): array
    {
        try {
        $movimientos = MovimientoInventario::whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin])->get();
        
        return [
            'total_entradas' => $movimientos->where('tipo_movimiento', 'entrada')->sum('cantidad'),
            'total_salidas' => $movimientos->where('tipo_movimiento', 'salida')->sum('cantidad'),
            'total_ajustes' => $movimientos->where('tipo_movimiento', 'ajuste')->sum('cantidad'),
                'productos_afectados' => $movimientos->unique('producto_id')->count(),
                'valor_entradas' => $movimientos->where('tipo_movimiento', 'entrada')->sum(function($m) {
                    $producto = Producto::find($m->producto_id);
                    return $producto ? $m->cantidad * $producto->precio_final : 0;
                }),
                'valor_salidas' => $movimientos->where('tipo_movimiento', 'salida')->sum(function($m) {
                    $producto = Producto::find($m->producto_id);
                    return $producto ? $m->cantidad * $producto->precio_final : 0;
                })
            ];
        } catch (\Exception $e) {
            Log::error('Error al obtener resumen de inventario', ['error' => $e->getMessage()]);
            return [];
        }
    }



    /**
     * Obtener valor de inventario por marca
     */
    public function getValorInventarioPorMarca(): array
    {
        try {
            $productos = Producto::with('marca')->activos()->get();
            $valorPorMarca = [];
            
            foreach ($productos as $producto) {
                $marca = $producto->marca->nombre ?? 'Sin Marca';
                if (!isset($valorPorMarca[$marca])) {
                    $valorPorMarca[$marca] = 0;
                }
                $valorPorMarca[$marca] += $producto->stock * $producto->precio_final;
            }
            
            return $valorPorMarca;
        } catch (\Exception $e) {
            Log::error('Error al obtener valor por marca', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Obtener rotación de inventario
     */
    public function getRotacionInventario(int $dias = 30): array
    {
        try {
            $fechaInicio = now()->subDays($dias);
            $movimientos = MovimientoInventario::whereBetween('fecha_movimiento', [$fechaInicio, now()])
                ->where('tipo_movimiento', 'salida')
                ->get();
            
            $productos = Producto::activos()->get();
            $rotacion = [];
            
            foreach ($productos as $producto) {
                $ventas = $movimientos->where('producto_id', $producto->producto_id)->sum('cantidad');
                $stockPromedio = $producto->stock;
                $rotacion[$producto->producto_id] = [
                    'producto' => $producto->nombre_producto,
                    'ventas' => $ventas,
                    'stock_promedio' => $stockPromedio,
                    'indice_rotacion' => $stockPromedio > 0 ? $ventas / $stockPromedio : 0
                ];
            }
            
            return $rotacion;
        } catch (\Exception $e) {
            Log::error('Error al obtener rotación de inventario', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Obtener movimientos por tipo
     */
    public function getMovimientosByTipo(string $tipo, Carbon $fechaInicio, Carbon $fechaFin, ?int $productoId = null, ?int $usuarioId = null): array
    {
        try {
            $query = MovimientoInventario::with(['producto', 'usuario'])
                ->where('tipo_movimiento', $tipo)
                ->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);

            if ($productoId) {
                $query->where('producto_id', $productoId);
            }

            if ($usuarioId) {
                $query->where('usuario_id', $usuarioId);
            }

            $movimientos = $query->orderBy('fecha_movimiento', 'desc')->get();

            return [
                'movimientos' => $movimientos,
                'total' => $movimientos->count(),
                'suma_cantidad' => $movimientos->sum('cantidad')
            ];
        } catch (\Exception $e) {
            Log::error('Error al obtener movimientos por tipo', ['error' => $e->getMessage()]);
            return [
                'movimientos' => collect(),
                'total' => 0,
                'suma_cantidad' => 0
            ];
        }
    }

    /**
     * Obtener productos con rotación lenta
     */
    public function getProductosRotacionLenta(int $dias = 30): Collection
    {
        try {
            $rotacion = $this->getRotacionInventario($dias);
            $productosLentos = collect($rotacion)->filter(function($item) {
                return $item['indice_rotacion'] < 0.5; // Menos de 0.5 rotaciones por período
            });
            
            return $productosLentos;
        } catch (\Exception $e) {
            Log::error('Error al obtener productos con rotación lenta', ['error' => $e->getMessage()]);
            return collect([]);
        }
    }

    /**
     * Obtener productos con rotación rápida
     */
    public function getProductosRotacionRapida(int $dias = 30): Collection
    {
        try {
            $rotacion = $this->getRotacionInventario($dias);
            $productosRapidos = collect($rotacion)->filter(function($item) {
                return $item['indice_rotacion'] > 2.0; // Más de 2 rotaciones por período
            });
            
            return $productosRapidos;
        } catch (\Exception $e) {
            Log::error('Error al obtener productos con rotación rápida', ['error' => $e->getMessage()]);
            return collect([]);
        }
    }
} 