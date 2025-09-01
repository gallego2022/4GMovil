<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CarritoController extends Controller
{
    /**
     * Agregar producto al carrito
     */
    public function agregar(Request $request)
    {
        try {
            $request->validate([
                'producto_id' => 'required|integer|exists:productos,producto_id',
                'variante_id' => 'nullable|integer|exists:variantes_producto,variante_id',
                'cantidad' => 'required|integer|min:1|max:100'
            ]);

            $producto = Producto::findOrFail($request->producto_id);
            $varianteId = $request->variante_id;
            $cantidad = $request->cantidad;

            // Verificar que el producto esté activo
            if (!$producto->activo) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no está disponible'
                ], 400);
            }

            // Si se especifica una variante, verificar que pertenezca al producto
            if ($varianteId) {
                $variante = VarianteProducto::findOrFail($varianteId);
                
                if ($variante->producto_id !== $producto->producto_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La variante no pertenece al producto seleccionado'
                    ], 400);
                }

                // Verificar stock de la variante específica
                if (!$variante->tieneStockSuficiente($cantidad)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para el color '{$variante->nombre}'. Disponible: {$variante->stock_disponible}"
                    ], 400);
                }

                // Verificar que la variante esté disponible
                if (!$variante->disponible) {
                    return response()->json([
                        'success' => false,
                        'message' => "El color '{$variante->nombre}' no está disponible"
                    ], 400);
                }

                $precioUnitario = $producto->precio + $variante->precio_adicional;
                $nombreVariante = $variante->nombre;
                $codigoColor = $variante->codigo_color;
            } else {
                // Producto sin variante
                if (!$producto->tieneStockSuficienteReal($cantidad)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente para este producto'
                    ], 400);
                }

                $precioUnitario = $producto->precio;
                $nombreVariante = null;
                $codigoColor = null;
            }

            // Obtener el carrito actual
            $cart = session('cart', []);

            // Crear clave única para el item
            $itemKey = $varianteId ? "{$producto->producto_id}_{$varianteId}" : $producto->producto_id;

            // Verificar si el item ya existe en el carrito
            if (isset($cart[$itemKey])) {
                $nuevaCantidad = $cart[$itemKey]['quantity'] + $cantidad;
                
                // Verificar stock nuevamente con la cantidad total
                if ($varianteId) {
                    if (!$variante->tieneStockSuficiente($nuevaCantidad)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Stock insuficiente para el color '{$variante->nombre}'. Disponible: {$variante->stock_disponible}"
                        ], 400);
                    }
                } else {
                    if (!$producto->tieneStockSuficienteReal($nuevaCantidad)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Stock insuficiente para este producto'
                        ], 400);
                    }
                }

                $cart[$itemKey]['quantity'] = $nuevaCantidad;
                $cart[$itemKey]['price_total'] = $precioUnitario * $nuevaCantidad;
            } else {
                // Agregar nuevo item al carrito
                $cart[$itemKey] = [
                    'id' => $producto->producto_id,
                    'name' => $producto->nombre_producto,
                    'price' => $precioUnitario,
                    'price_total' => $precioUnitario * $cantidad,
                    'quantity' => $cantidad,
                    'variante_id' => $varianteId,
                    'variante_nombre' => $nombreVariante,
                    'codigo_color' => $codigoColor,
                    'imagen' => $producto->imagenes->first()?->url ?? null
                ];
            }

            // Guardar el carrito en la sesión
            session(['cart' => $cart]);

            // Calcular total del carrito
            $totalCarrito = collect($cart)->sum('price_total');
            $cantidadItems = collect($cart)->sum('quantity');

            Log::info('Producto agregado al carrito', [
                'producto_id' => $producto->producto_id,
                'variante_id' => $varianteId,
                'cantidad' => $cantidad,
                'total_carrito' => $totalCarrito,
                'items_carrito' => count($cart)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito correctamente',
                'data' => [
                    'cart' => $cart,
                    'total_carrito' => $totalCarrito,
                    'cantidad_items' => $cantidadItems,
                    'item_agregado' => [
                        'producto' => $producto->nombre_producto,
                        'variante' => $nombreVariante,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'precio_total' => $precioUnitario * $cantidad
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al agregar producto al carrito', [
                'producto_id' => $request->producto_id,
                'variante_id' => $request->variante_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al agregar producto al carrito'
            ], 500);
        }
    }

    /**
     * Actualizar cantidad de un item en el carrito
     */
    public function actualizar(Request $request)
    {
        try {
            $request->validate([
                'item_key' => 'required|string',
                'cantidad' => 'required|integer|min:1|max:100'
            ]);

            $cart = session('cart', []);
            $itemKey = $request->item_key;
            $nuevaCantidad = $request->cantidad;

            if (!isset($cart[$itemKey])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El item no existe en el carrito'
                ], 400);
            }

            $item = $cart[$itemKey];
            $producto = Producto::findOrFail($item['id']);
            $varianteId = $item['variante_id'];

            // Verificar stock
            if ($varianteId) {
                $variante = VarianteProducto::findOrFail($varianteId);
                
                if (!$variante->tieneStockSuficiente($nuevaCantidad)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para el color '{$variante->nombre}'. Disponible: {$variante->stock_disponible}"
                    ], 400);
                }
            } else {
                if (!$producto->tieneStockSuficienteReal($nuevaCantidad)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente para este producto'
                    ], 400);
                }
            }

            // Actualizar cantidad
            $cart[$itemKey]['quantity'] = $nuevaCantidad;
            $cart[$itemKey]['price_total'] = $item['price'] * $nuevaCantidad;

            session(['cart' => $cart]);

            $totalCarrito = collect($cart)->sum('price_total');
            $cantidadItems = collect($cart)->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Cantidad actualizada correctamente',
                'data' => [
                    'cart' => $cart,
                    'total_carrito' => $totalCarrito,
                    'cantidad_items' => $cantidadItems
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar carrito', [
                'item_key' => $request->item_key,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el carrito'
            ], 500);
        }
    }

    /**
     * Eliminar item del carrito
     */
    public function eliminar(Request $request)
    {
        try {
            $request->validate([
                'item_key' => 'required|string'
            ]);

            $cart = session('cart', []);
            $itemKey = $request->item_key;

            if (!isset($cart[$itemKey])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El item no existe en el carrito'
                ], 400);
            }

            unset($cart[$itemKey]);
            session(['cart' => $cart]);

            $totalCarrito = collect($cart)->sum('price_total');
            $cantidadItems = collect($cart)->sum('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Item eliminado del carrito',
                'data' => [
                    'cart' => $cart,
                    'total_carrito' => $totalCarrito,
                    'cantidad_items' => $cantidadItems
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar item del carrito', [
                'item_key' => $request->item_key,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar item del carrito'
            ], 500);
        }
    }

    /**
     * Limpiar carrito
     */
    public function limpiar()
    {
        try {
            session()->forget('cart');

            return response()->json([
                'success' => true,
                'message' => 'Carrito limpiado correctamente',
                'data' => [
                    'cart' => [],
                    'total_carrito' => 0,
                    'cantidad_items' => 0
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al limpiar carrito', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar el carrito'
            ], 500);
        }
    }

    /**
     * Obtener carrito actual
     */
    public function obtener()
    {
        try {
            $cart = session('cart', []);
            $totalCarrito = collect($cart)->sum('price_total');
            $cantidadItems = collect($cart)->sum('quantity');

            return response()->json([
                'success' => true,
                'data' => [
                    'cart' => $cart,
                    'total_carrito' => $totalCarrito,
                    'cantidad_items' => $cantidadItems
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener carrito', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el carrito'
            ], 500);
        }
    }

    /**
     * Verificar stock de items en el carrito
     */
    public function verificarStock()
    {
        try {
            $cart = session('cart', []);
            $errores = [];
            $itemsActualizados = [];

            foreach ($cart as $itemKey => $item) {
                $producto = Producto::find($item['id']);
                $varianteId = $item['variante_id'];
                $cantidad = $item['quantity'];

                if (!$producto) {
                    $errores[] = "El producto '{$item['name']}' ya no existe";
                    continue;
                }

                if ($varianteId) {
                    $variante = VarianteProducto::find($varianteId);
                    
                    if (!$variante) {
                        $errores[] = "La variante del producto '{$item['name']}' ya no existe";
                        continue;
                    }

                    if (!$variante->tieneStockSuficiente($cantidad)) {
                        $errores[] = "Stock insuficiente para '{$item['name']}' ({$variante->nombre}). Disponible: {$variante->stock_disponible}";
                    }

                    if (!$variante->disponible) {
                        $errores[] = "El color '{$variante->nombre}' del producto '{$item['name']}' ya no está disponible";
                    }
                } else {
                    if (!$producto->tieneStockSuficienteReal($cantidad)) {
                        $errores[] = "Stock insuficiente para '{$item['name']}'";
                    }
                }

                if (!$producto->activo) {
                    $errores[] = "El producto '{$item['name']}' ya no está disponible";
                }
            }

            return response()->json([
                'success' => empty($errores),
                'errores' => $errores,
                'data' => [
                    'cart' => $cart,
                    'total_carrito' => collect($cart)->sum('price_total'),
                    'cantidad_items' => collect($cart)->sum('quantity')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar stock del carrito', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar stock del carrito'
            ], 500);
        }
    }
}
