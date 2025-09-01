<?php

namespace App\Services;

use App\Models\VarianteProducto;
use App\Models\MovimientoInventarioVariante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventarioVarianteService
{
    /**
     * Registrar entrada de stock para una variante
     */
    public function registrarEntrada(int $varianteId, int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): array
    {
        try {
            $variante = VarianteProducto::findOrFail($varianteId);
            
            $resultado = $variante->registrarEntrada($cantidad, $motivo, $usuarioId, $referencia);
            
            if ($resultado) {
                return [
                    'success' => true,
                    'message' => "Entrada de {$cantidad} unidades registrada para {$variante->nombre}",
                    'stock_anterior' => $variante->stock_disponible - $cantidad,
                    'stock_nuevo' => $variante->stock_disponible
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Error al registrar la entrada de stock'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al registrar entrada de stock para variante', [
                'variante_id' => $varianteId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Registrar salida de stock para una variante
     */
    public function registrarSalida(int $varianteId, int $cantidad, string $motivo, int $usuarioId, ?string $referencia = null): array
    {
        try {
            $variante = VarianteProducto::findOrFail($varianteId);
            
            $resultado = $variante->registrarSalida($cantidad, $motivo, $usuarioId, $referencia);
            
            if ($resultado) {
                return [
                    'success' => true,
                    'message' => "Salida de {$cantidad} unidades registrada para {$variante->nombre}",
                    'stock_anterior' => $variante->stock_disponible + $cantidad,
                    'stock_nuevo' => $variante->stock_disponible
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Error al registrar la salida de stock'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al registrar salida de stock para variante', [
                'variante_id' => $varianteId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener reporte de inventario por variante
     */
    public function obtenerReporteInventario(int $productoId = null): array
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

            $reporte = [
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

            return [
                'success' => true,
                'data' => $reporte
            ];

        } catch (\Exception $e) {
            Log::error('Error al generar reporte de inventario', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error al generar reporte: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener variantes que necesitan reposición
     */
    public function obtenerVariantesNecesitanReposicion(): array
    {
        try {
            $variantes = VarianteProducto::with('producto')
                ->where('disponible', true)
                ->whereRaw('stock_disponible <= stock_minimo')
                ->get();

            return [
                'success' => true,
                'data' => $variantes->map(function($variante) {
                    return [
                        'variante_id' => $variante->variante_id,
                        'producto' => $variante->producto->nombre_producto,
                        'color' => $variante->nombre,
                        'stock_actual' => $variante->stock_disponible,
                        'stock_minimo' => $variante->stock_minimo,
                        'cantidad_recomendada' => $variante->stock_maximo - $variante->stock_disponible,
                        'ultima_actualizacion' => $variante->updated_at
                    ];
                })
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener variantes que necesitan reposición', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ajustar stock de una variante
     */
    public function ajustarStock(int $varianteId, int $nuevoStock, string $motivo, int $usuarioId): array
    {
        try {
            $variante = VarianteProducto::findOrFail($varianteId);
            $stockAnterior = $variante->stock_disponible;
            $diferencia = $nuevoStock - $stockAnterior;

            if ($diferencia > 0) {
                // Es una entrada
                return $this->registrarEntrada($varianteId, $diferencia, $motivo, $usuarioId);
            } elseif ($diferencia < 0) {
                // Es una salida
                return $this->registrarSalida($varianteId, abs($diferencia), $motivo, $usuarioId);
            } else {
                return [
                    'success' => true,
                    'message' => 'No hay diferencia en el stock'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Error al ajustar stock de variante', [
                'variante_id' => $varianteId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
