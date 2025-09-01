<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EjemploCompraController extends Controller
{
    /**
     * Ejemplo: Agregar producto con variante al carrito
     */
    public function agregarAlCarrito(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,producto_id',
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'cantidad' => 'required|integer|min:1'
        ]);

        try {
            $producto = Producto::findOrFail($request->producto_id);
            $variante = VarianteProducto::findOrFail($request->variante_id);
            $cantidad = $request->cantidad;

            // Verificar que la variante pertenece al producto
            if ($variante->producto_id !== $producto->producto_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'La variante no pertenece al producto seleccionado'
                ], 400);
            }

            // Verificar stock específico de la variante
            if (!$variante->tieneStockSuficiente($cantidad)) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente para el color '{$variante->nombre}'. Disponible: {$variante->stock_disponible}"
                ], 400);
            }

            // Verificar stock total del producto (opcional, para validación adicional)
            if (!$producto->tieneStockSuficienteReal($cantidad)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente del producto'
                ], 400);
            }

            // Reservar stock temporalmente
            if (!$variante->reservarStock($cantidad, 'Reserva carrito', auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo reservar el stock'
                ], 400);
            }

            // Agregar al carrito (aquí iría tu lógica de carrito)
            $itemCarrito = [
                'producto_id' => $producto->producto_id,
                'variante_id' => $variante->variante_id,
                'nombre_producto' => $producto->nombre_producto,
                'nombre_variante' => $variante->nombre,
                'cantidad' => $cantidad,
                'precio_unitario' => $producto->precio + $variante->precio_adicional,
                'precio_total' => ($producto->precio + $variante->precio_adicional) * $cantidad
            ];

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito correctamente',
                'data' => [
                    'item' => $itemCarrito,
                    'stock_disponible_variante' => $variante->fresh()->stock_disponible,
                    'stock_total_producto' => $producto->fresh()->stock
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al agregar producto al carrito', [
                'producto_id' => $request->producto_id,
                'variante_id' => $request->variante_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Ejemplo: Procesar compra confirmada
     */
    public function procesarCompra(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|integer|exists:productos,producto_id',
            'items.*.variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'items.*.cantidad' => 'required|integer|min:1'
        ]);

        try {
            $items = $request->items;
            $totalCompra = 0;
            $productosComprados = [];

            foreach ($items as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $variante = VarianteProducto::findOrFail($item['variante_id']);
                $cantidad = $item['cantidad'];

                // Verificar stock nuevamente (por si cambió desde la reserva)
                if (!$variante->tieneStockSuficiente($cantidad)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para {$producto->nombre_producto} ({$variante->nombre})"
                    ], 400);
                }

                // Confirmar la venta (la reserva ya se hizo, solo confirmar)
                $variante->confirmarReserva($cantidad, 'Venta confirmada', auth()->id(), 'PEDIDO-' . time());

                // Calcular precio
                $precioUnitario = $producto->precio + $variante->precio_adicional;
                $precioTotal = $precioUnitario * $cantidad;
                $totalCompra += $precioTotal;

                $productosComprados[] = [
                    'producto' => $producto->nombre_producto,
                    'variante' => $variante->nombre,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'precio_total' => $precioTotal,
                    'stock_restante_variante' => $variante->fresh()->stock_disponible,
                    'stock_restante_producto' => $producto->fresh()->stock
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Compra procesada correctamente',
                'data' => [
                    'total_compra' => $totalCompra,
                    'productos_comprados' => $productosComprados,
                    'fecha_compra' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al procesar compra', [
                'items' => $request->items,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la compra'
            ], 500);
        }
    }

    /**
     * Ejemplo: Obtener información de stock de un producto
     */
    public function obtenerStockProducto($productoId)
    {
        try {
            $producto = Producto::with('variantes')->findOrFail($productoId);

            $stockInfo = [
                'producto_id' => $producto->producto_id,
                'nombre_producto' => $producto->nombre_producto,
                'tiene_variantes' => $producto->tieneVariantes(),
                'stock_total' => $producto->stock,
                'stock_disponible' => $producto->stock_disponible_variantes,
                'estado_stock' => $producto->estado_stock_real,
                'necesita_reposicion' => $producto->necesitaReposicionVariantes(),
                'variantes' => []
            ];

            if ($producto->tieneVariantes()) {
                foreach ($producto->variantes as $variante) {
                    $stockInfo['variantes'][] = [
                        'variante_id' => $variante->variante_id,
                        'nombre' => $variante->nombre,
                        'codigo_color' => $variante->codigo_color,
                        'stock_disponible' => $variante->stock_disponible,
                        'stock_minimo' => $variante->stock_minimo,
                        'disponible' => $variante->disponible,
                        'precio_adicional' => $variante->precio_adicional,
                        'precio_final' => $variante->precio_final,
                        'necesita_reposicion' => $variante->necesitaReposicion(),
                        'estado_stock' => $variante->stock_disponible > 0 ? 'disponible' : 'sin_stock'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $stockInfo
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener stock del producto', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de stock'
            ], 500);
        }
    }

    /**
     * Ejemplo: Simular entrada de stock (para administradores)
     */
    public function simularEntradaStock(Request $request)
    {
        $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255'
        ]);

        try {
            $variante = VarianteProducto::with('producto')->findOrFail($request->variante_id);
            $cantidad = $request->cantidad;
            $motivo = $request->motivo;

            // Stock antes de la entrada
            $stockAnterior = $variante->stock_disponible;
            $stockProductoAnterior = $variante->producto->stock;

            // Registrar entrada
            $variante->registrarEntrada($cantidad, $motivo, auth()->id());

            // Obtener valores actualizados
            $variante->refresh();
            $variante->producto->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Entrada de stock registrada correctamente',
                'data' => [
                    'variante' => [
                        'nombre' => $variante->nombre,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $variante->stock_disponible,
                        'incremento' => $cantidad
                    ],
                    'producto' => [
                        'nombre' => $variante->producto->nombre_producto,
                        'stock_anterior' => $stockProductoAnterior,
                        'stock_nuevo' => $variante->producto->stock,
                        'incremento' => $variante->producto->stock - $stockProductoAnterior
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al registrar entrada de stock', [
                'variante_id' => $request->variante_id,
                'cantidad' => $request->cantidad,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar entrada de stock'
            ], 500);
        }
    }

    /**
     * Ejemplo: Reporte de inventario con sincronización
     */
    public function reporteInventario()
    {
        try {
            $productos = Producto::with('variantes')->get();
            $reporte = [
                'resumen_general' => [
                    'total_productos' => $productos->count(),
                    'productos_con_variantes' => $productos->filter(fn($p) => $p->tieneVariantes())->count(),
                    'productos_sin_variantes' => $productos->filter(fn($p) => !$p->tieneVariantes())->count(),
                    'stock_total_sistema' => $productos->sum('stock'),
                    'productos_sin_stock' => $productos->where('stock', 0)->count(),
                    'productos_necesitan_reposicion' => $productos->filter(fn($p) => $p->necesitaReposicionVariantes())->count()
                ],
                'productos_detalle' => []
            ];

            foreach ($productos as $producto) {
                $productoInfo = [
                    'producto_id' => $producto->producto_id,
                    'nombre' => $producto->nombre_producto,
                    'tiene_variantes' => $producto->tieneVariantes(),
                    'stock_total' => $producto->stock,
                    'stock_disponible' => $producto->stock_disponible_variantes,
                    'estado_stock' => $producto->estado_stock_real,
                    'necesita_reposicion' => $producto->necesitaReposicionVariantes(),
                    'variantes' => []
                ];

                if ($producto->tieneVariantes()) {
                    foreach ($producto->variantes as $variante) {
                        $productoInfo['variantes'][] = [
                            'variante_id' => $variante->variante_id,
                            'nombre' => $variante->nombre,
                            'stock_disponible' => $variante->stock_disponible,
                            'stock_minimo' => $variante->stock_minimo,
                            'disponible' => $variante->disponible,
                            'necesita_reposicion' => $variante->necesitaReposicion()
                        ];
                    }
                }

                $reporte['productos_detalle'][] = $productoInfo;
            }

            return response()->json([
                'success' => true,
                'data' => $reporte
            ]);

        } catch (\Exception $e) {
            Log::error('Error al generar reporte de inventario', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte'
            ], 500);
        }
    }
}
