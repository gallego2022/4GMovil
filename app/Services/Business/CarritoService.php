<?php

namespace App\Services\Business;

use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CarritoService extends BaseService
{
    /**
     * Obtiene el carrito del usuario autenticado o de sesión
     */
    public function getCart(): array
    {
        $this->logOperation('obteniendo_carrito', ['user_id' => Auth::id(), 'auth_check' => Auth::check()]);

        try {
            if (Auth::check()) {
                $carrito = $this->getUserCart();
                $this->logOperation('carrito_obtenido_usuario', [
                    'items_count' => count($carrito['items'] ?? []),
                    'total_items' => $carrito['total_items'] ?? 0,
                ]);
            } else {
                $carrito = $this->getSessionCart();
                $this->logOperation('carrito_obtenido_sesion', [
                    'items_count' => count($carrito['items'] ?? []),
                    'total_items' => $carrito['total_items'] ?? 0,
                ]);
            }

            $response = $this->formatSuccessResponse($carrito, 'Carrito obtenido exitosamente');

            $this->logOperation('carrito_formateado', [
                'response_success' => $response['success'] ?? false,
                'data_items_count' => count($response['data']['items'] ?? []),
            ]);

            return $response;

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_carrito', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 'error');
            throw $e;
        }
    }

    /**
     * Agrega un producto al carrito
     */
    public function addToCart(Request $request): array
    {
        $cantidadRecibida = $request->input('cantidad');

        $this->logOperation('agregando_producto_carrito', [
            'user_id' => Auth::id(),
            'producto_id' => $request->input('producto_id'),
            'cantidad_recibida' => $cantidadRecibida,
            'tipo_cantidad' => gettype($cantidadRecibida),
        ]);

        return $this->executeInTransaction(function () use ($request) {
            // Validar datos de entrada
            $data = $this->validateAddToCartData($request);

            $this->logOperation('datos_validados_agregar_carrito', [
                'producto_id' => $data['producto_id'],
                'variante_id' => $data['variante_id'] ?? null,
                'cantidad' => $data['cantidad'],
                'tipo_cantidad' => gettype($data['cantidad']),
            ]);

            // Verificar disponibilidad del producto
            $this->validateProductAvailability($data['producto_id'], $data['variante_id'] ?? null, $data['cantidad']);

            if (Auth::check()) {
                $result = $this->addToUserCart($data);
            } else {
                $result = $this->addToSessionCart($data);
            }

            $this->logOperation('producto_agregado_carrito', [
                'user_id' => Auth::id(),
                'producto_id' => $data['producto_id'],
                'cantidad' => $data['cantidad'],
            ]);

            return $this->formatSuccessResponse($result, 'Producto agregado al carrito exitosamente');

        }, 'agregar producto al carrito');
    }

    /**
     * Actualiza la cantidad de un item en el carrito
     */
    public function updateCartItem(int $itemId, Request $request): array
    {
        $this->logOperation('actualizando_item_carrito', [
            'user_id' => Auth::id(),
            'item_id' => $itemId,
        ]);

        return $this->executeInTransaction(function () use ($itemId, $request) {
            $data = $this->validateUpdateCartItemData($request);

            if (Auth::check()) {
                $result = $this->updateUserCartItem($itemId, $data);
            } else {
                $result = $this->updateSessionCartItem($itemId, $data);
            }

            $this->logOperation('item_carrito_actualizado', [
                'user_id' => Auth::id(),
                'item_id' => $itemId,
                'cantidad' => $data['cantidad'],
            ]);

            return $this->formatSuccessResponse($result, 'Carrito actualizado exitosamente');

        }, 'actualizar item del carrito');
    }

    /**
     * Elimina un item del carrito
     */
    public function removeFromCart($itemId): array
    {
        $this->logOperation('eliminando_item_carrito', [
            'user_id' => Auth::id(),
            'item_id' => $itemId,
        ]);

        return $this->executeInTransaction(function () use ($itemId) {
            if (Auth::check()) {
                // Para usuarios autenticados, el itemId es un entero
                $this->removeFromUserCart((int) $itemId);
            } else {
                // Para usuarios no autenticados, el itemId es un string
                $this->removeFromSessionCart((string) $itemId);
            }

            $this->logOperation('item_carrito_eliminado', [
                'user_id' => Auth::id(),
                'item_id' => $itemId,
            ]);

            return $this->formatSuccessResponse(null, 'Producto eliminado del carrito exitosamente');

        }, 'eliminar item del carrito');
    }

    /**
     * Limpia todo el carrito
     */
    public function clearCart(): array
    {
        $this->logOperation('limpiando_carrito', ['user_id' => Auth::id()]);

        try {
            if (Auth::check()) {
                $this->clearUserCart();
            } else {
                $this->clearSessionCart();
            }

            $this->logOperation('carrito_limpiado', ['user_id' => Auth::id()]);

            return $this->formatSuccessResponse(null, 'Carrito limpiado exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_limpiando_carrito', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene el resumen del carrito (total, cantidad de items, etc.)
     */
    public function getCartSummary(): array
    {
        try {
            if (Auth::check()) {
                $summary = $this->getUserCartSummary();
            } else {
                $summary = $this->getSessionCartSummary();
            }

            return $this->formatSuccessResponse($summary, 'Resumen del carrito obtenido');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_resumen_carrito', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Sincroniza el carrito de sesión con el carrito del usuario al hacer login
     */
    public function syncSessionCartWithUser(): array
    {
        $this->logOperation('sincronizando_carrito_sesion_usuario', ['user_id' => Auth::id()]);

        return $this->executeInTransaction(function () {
            $sessionCart = Session::get('cart', []);

            if (empty($sessionCart)) {
                return $this->formatSuccessResponse(null, 'No hay carrito de sesión para sincronizar');
            }

            $this->logOperation('carrito_sesion_antes_consolidacion', [
                'items_count' => count($sessionCart),
                'items' => $sessionCart,
            ]);

            // Consolidar items duplicados antes de sincronizar
            $consolidatedCart = [];
            foreach ($sessionCart as $item) {
                $productoId = $item['producto_id'] ?? $item['id'] ?? null;
                $varianteId = $item['variante_id'] ?? null;

                if (! $productoId) {
                    continue;
                }

                // Crear clave única para consolidar
                $key = $varianteId ? "{$productoId}-{$varianteId}" : (string) $productoId;

                if (isset($consolidatedCart[$key])) {
                    // Sumar cantidades si el item ya existe
                    $cantidadExistente = (int) ($consolidatedCart[$key]['cantidad'] ?? $consolidatedCart[$key]['quantity'] ?? 0);
                    $cantidadNueva = (int) ($item['cantidad'] ?? $item['quantity'] ?? 1);
                    $consolidatedCart[$key]['cantidad'] = $cantidadExistente + $cantidadNueva;

                    $this->logOperation('item_consolidado', [
                        'key' => $key,
                        'cantidad_anterior' => $cantidadExistente,
                        'cantidad_nueva' => $cantidadNueva,
                        'cantidad_total' => $consolidatedCart[$key]['cantidad'],
                        'item_original' => $item,
                    ]);
                } else {
                    // Agregar nuevo item consolidado
                    $cantidadRaw = $item['cantidad'] ?? $item['quantity'] ?? 1;
                    $consolidatedCart[$key] = [
                        'producto_id' => $productoId,
                        'variante_id' => $varianteId,
                        'cantidad' => is_numeric($cantidadRaw) ? (int) $cantidadRaw : 1,
                    ];
                }
            }

            $this->logOperation('carrito_consolidado', [
                'items_antes' => count($sessionCart),
                'items_despues' => count($consolidatedCart),
                'items_consolidados' => array_values($consolidatedCart),
            ]);

            // Sincronizar cada item consolidado directamente con la cantidad que tiene en la sesión
            // No usar addToUserCart porque suma, aquí queremos usar la cantidad exacta de la sesión
            $carrito = Carrito::firstOrCreate(['usuario_id' => Auth::id()]);

            foreach ($consolidatedCart as $item) {
                $productoId = $item['producto_id'] ?? null;
                $varianteId = $item['variante_id'] ?? null;
                $cantidad = (int) ($item['cantidad'] ?? 1);

                if (! $productoId || $cantidad <= 0) {
                    continue;
                }

                $this->logOperation('sincronizando_item', [
                    'producto_id' => $productoId,
                    'variante_id' => $varianteId,
                    'cantidad' => $cantidad,
                ]);

                try {
                    // Calcular el precio unitario del producto
                    $precioUnitario = $this->calculatePrecioUnitario($productoId, $varianteId);

                    // Verificar si el producto ya existe en el carrito del usuario
                    $existingItem = $carrito->items()
                        ->where('producto_id', $productoId)
                        ->where('variante_id', $varianteId)
                        ->first();

                    if ($existingItem) {
                        // Si ya existe, usar la cantidad de la sesión (reemplazar, no sumar)
                        $cantidadAnterior = $existingItem->cantidad;
                        $existingItem->update([
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precioUnitario,
                        ]);
                        $this->logOperation('item_usuario_actualizado', [
                            'producto_id' => $productoId,
                            'variante_id' => $varianteId,
                            'cantidad_anterior' => $cantidadAnterior,
                            'cantidad_nueva' => $cantidad,
                            'precio_unitario' => $precioUnitario,
                        ]);
                    } else {
                        // Si no existe, crear nuevo item con la cantidad de la sesión
                        $carrito->items()->create([
                            'producto_id' => $productoId,
                            'variante_id' => $varianteId,
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precioUnitario,
                        ]);
                        $this->logOperation('item_usuario_creado', [
                            'producto_id' => $productoId,
                            'variante_id' => $varianteId,
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precioUnitario,
                        ]);
                    }
                } catch (Exception $e) {
                    $this->logOperation('error_sincronizando_item_carrito', [
                        'item' => $item,
                        'error' => $e->getMessage(),
                    ], 'warning');
                    // Continuar con el siguiente item aunque falle uno
                }
            }

            // Limpiar carrito de sesión solo después de sincronizar exitosamente
            Session::forget('cart');

            $this->logOperation('carrito_sincronizado_exitosamente', [
                'user_id' => Auth::id(),
                'items_antes_consolidacion' => count($sessionCart),
                'items_despues_consolidacion' => count($consolidatedCart),
            ]);

            return $this->formatSuccessResponse(null, 'Carrito sincronizado exitosamente');

        }, 'sincronizar carrito de sesión con usuario');
    }

    /**
     * Valida los datos para agregar al carrito
     */
    private function validateAddToCartData(Request $request): array
    {
        $rules = [
            'producto_id' => 'required|exists:productos,producto_id',
            'cantidad' => 'required|integer|min:1|max:100',
            'variante_id' => 'nullable|exists:variantes_producto,variante_id',
        ];

        // Asegurar que cantidad sea un entero
        $request->merge([
            'cantidad' => (int) $request->input('cantidad', 1),
        ]);

        $messages = [
            'producto_id.required' => 'El ID del producto es obligatorio',
            'producto_id.exists' => 'El producto no existe',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad mínima es 1',
            'cantidad.max' => 'La cantidad máxima es 100',
            'variante_id.exists' => 'La variante del producto no existe',
        ];

        $validator = validator($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new Exception('Datos inválidos: '.implode(', ', $validator->errors()->all()));
        }

        return $validator->validated();
    }

    /**
     * Valida los datos para actualizar item del carrito
     */
    private function validateUpdateCartItemData(Request $request): array
    {
        $rules = [
            'cantidad' => 'required|integer|min:1|max:100',
        ];

        $messages = [
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad mínima es 1',
            'cantidad.max' => 'La cantidad máxima es 100',
        ];

        $validator = validator($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new Exception('Datos inválidos: '.implode(', ', $validator->errors()->all()));
        }

        return $validator->validated();
    }

    /**
     * Valida la disponibilidad del producto
     */
    private function validateProductAvailability(int $productoId, ?int $varianteId, int $cantidad): void
    {
        if ($varianteId) {
            $variante = VarianteProducto::find($varianteId);
            if (! $variante || $variante->producto_id != $productoId) {
                throw new Exception('La variante del producto no es válida');
            }
            $this->validateStock($variante->stock, $cantidad, 'variante del producto');
        } else {
            $producto = Producto::find($productoId);
            if (! $producto) {
                throw new Exception('El producto no existe');
            }
            $this->validateStock($producto->stock, $cantidad, $producto->nombre_producto);
        }
    }

    /**
     * Obtiene el carrito del usuario autenticado
     */
    private function getUserCart(): array
    {
        $carrito = Carrito::where('usuario_id', Auth::id())
            ->with(['items.producto', 'items.variante'])
            ->first();

        $this->logOperation('getUserCart_inicio', [
            'carrito_existe' => $carrito ? 'si' : 'no',
            'user_id' => Auth::id(),
        ]);

        if (! $carrito) {
            $carrito = Carrito::create(['usuario_id' => Auth::id()]);
            // Recargar para obtener la relación items
            $carrito->load(['items.producto', 'items.variante']);
            $this->logOperation('carrito_creado', ['carrito_id' => $carrito->id]);
        } else {
            // Refrescar la relación para asegurar que tenemos los items más recientes
            $carrito->load(['items.producto', 'items.variante']);
            $this->logOperation('carrito_cargado', [
                'carrito_id' => $carrito->id,
                'items_count' => $carrito->items->count(),
            ]);
        }

        // Convertir los items a un formato de array que el frontend pueda usar
        $items = [];
        foreach ($carrito->items as $item) {
            // Validar que el producto exista
            if (! $item->producto) {
                $this->logOperation('producto_no_encontrado_en_item_carrito', [
                    'item_id' => $item->id,
                    'producto_id' => $item->producto_id,
                ], 'warning');

                continue;
            }

            $itemData = [
                'id' => $item->id,
                'producto_id' => $item->producto_id,
                'variante_id' => $item->variante_id,
                'cantidad' => $item->cantidad,
                'producto' => [
                    'producto_id' => $item->producto->producto_id,
                    'nombre_producto' => $item->producto->nombre_producto,
                    'precio' => (float) $item->producto->precio,
                    'imagen_url' => $item->producto->imagen_url ?? null,
                ],
                'variante' => $item->variante ? [
                    'variante_id' => $item->variante->variante_id,
                    'nombre' => $item->variante->nombre,
                    'nombre_variante' => $item->variante->nombre,
                    'precio_adicional' => (float) ($item->variante->precio_adicional ?? 0),
                    'codigo_color' => $item->variante->codigo_color ?? null,
                ] : null,
            ];

            $items[] = $itemData;
        }

        $totalPrecio = 0;
        foreach ($items as $item) {
            $precio = $item['producto']['precio'];
            if ($item['variante']) {
                $precio += $item['variante']['precio_adicional'];
            }
            $totalPrecio += $precio * $item['cantidad'];
        }

        $result = [
            'id' => $carrito->id,
            'items' => $items,
            'total_items' => array_sum(array_column($items, 'cantidad')),
            'total_precio' => $totalPrecio,
        ];

        $this->logOperation('getUserCart_resultado', [
            'items_count' => count($items),
            'total_items' => $result['total_items'],
            'total_precio' => $result['total_precio'],
            'user_id' => Auth::id(),
        ]);

        return $result;
    }

    /**
     * Obtiene el carrito de sesión
     */
    private function getSessionCart(): array
    {
        $cartItems = Session::get('cart', []);
        $items = [];
        $totalItems = 0;
        $totalPrecio = 0;

        foreach ($cartItems as $item) {
            $producto = Producto::find($item['producto_id']);
            $variante = null;

            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
            }

            $precio = $producto->precio;
            if ($variante) {
                $precio += $variante->precio_adicional ?? 0;
            }

            $items[] = [
                'id' => $item['id'],
                'producto' => $producto,
                'variante' => $variante,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $precio,
                'subtotal' => $precio * $item['cantidad'],
            ];

            $totalItems += $item['cantidad'];
            $totalPrecio += $precio * $item['cantidad'];
        }

        return [
            'id' => 'session',
            'items' => $items,
            'total_items' => $totalItems,
            'total_precio' => $totalPrecio,
        ];
    }

    /**
     * Agrega producto al carrito del usuario
     */
    private function addToUserCart(array $data): array
    {
        $carrito = Carrito::firstOrCreate(['usuario_id' => Auth::id()]);

        // Verificar si el producto ya existe en el carrito
        $existingItem = $carrito->items()
            ->where('producto_id', $data['producto_id'])
            ->where('variante_id', $data['variante_id'] ?? null)
            ->first();

        // Calcular el precio unitario del producto
        $precioUnitario = $this->calculatePrecioUnitario($data['producto_id'], $data['variante_id'] ?? null);

        if ($existingItem) {
            // Actualizar cantidad existente - sumar la nueva cantidad
            $cantidadAnterior = $existingItem->cantidad;
            $nuevaCantidad = (int) $data['cantidad'];
            $cantidadTotal = $cantidadAnterior + $nuevaCantidad;

            $existingItem->update([
                'cantidad' => $cantidadTotal,
                'precio_unitario' => $precioUnitario,
            ]);

            $this->logOperation('item_usuario_actualizado', [
                'producto_id' => $data['producto_id'],
                'variante_id' => $data['variante_id'] ?? null,
                'cantidad_anterior' => $cantidadAnterior,
                'cantidad_agregada' => $nuevaCantidad,
                'cantidad_total' => $cantidadTotal,
                'precio_unitario' => $precioUnitario,
            ]);
        } else {
            // Crear nuevo item
            $carrito->items()->create([
                'producto_id' => $data['producto_id'],
                'variante_id' => $data['variante_id'] ?? null,
                'cantidad' => (int) $data['cantidad'],
                'precio_unitario' => $precioUnitario,
            ]);

            $this->logOperation('item_usuario_creado', [
                'producto_id' => $data['producto_id'],
                'variante_id' => $data['variante_id'] ?? null,
                'cantidad' => (int) $data['cantidad'],
                'precio_unitario' => $precioUnitario,
            ]);
        }

        return $this->getUserCart();
    }

    /**
     * Agrega producto al carrito de sesión
     */
    private function addToSessionCart(array $data): array
    {
        $cartItems = Session::get('cart', []);
        $itemId = uniqid();

        // Verificar si el producto ya existe
        $existingIndex = null;
        foreach ($cartItems as $index => $item) {
            // Comparar usando producto_id o id, y variante_id
            $itemProductoId = $item['producto_id'] ?? $item['id'] ?? null;
            $itemVarianteId = $item['variante_id'] ?? null;

            if ($itemProductoId == $data['producto_id'] &&
                $itemVarianteId == ($data['variante_id'] ?? null)) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Actualizar cantidad existente - sumar la nueva cantidad
            $cantidadExistente = (int) ($cartItems[$existingIndex]['cantidad'] ?? $cartItems[$existingIndex]['quantity'] ?? 0);
            $nuevaCantidad = (int) $data['cantidad'];
            $cartItems[$existingIndex]['cantidad'] = $cantidadExistente + $nuevaCantidad;
            // Asegurar que también tenga producto_id si solo tenía id
            if (! isset($cartItems[$existingIndex]['producto_id']) && isset($cartItems[$existingIndex]['id'])) {
                $cartItems[$existingIndex]['producto_id'] = $cartItems[$existingIndex]['id'];
            }

            $this->logOperation('item_sesion_actualizado', [
                'producto_id' => $data['producto_id'],
                'variante_id' => $data['variante_id'] ?? null,
                'cantidad_anterior' => $cantidadExistente,
                'cantidad_agregada' => $nuevaCantidad,
                'cantidad_total' => $cartItems[$existingIndex]['cantidad'],
            ]);
        } else {
            // Agregar nuevo item
            $cartItems[] = [
                'id' => $itemId,
                'producto_id' => $data['producto_id'],
                'variante_id' => $data['variante_id'] ?? null,
                'cantidad' => (int) $data['cantidad'],
            ];
        }

        Session::put('cart', $cartItems);

        $this->logOperation('item_agregado_sesion', [
            'producto_id' => $data['producto_id'],
            'variante_id' => $data['variante_id'] ?? null,
            'cantidad' => $data['cantidad'],
            'total_items_sesion' => count($cartItems),
        ]);

        return $this->getSessionCart();
    }

    /**
     * Actualiza item en el carrito del usuario
     */
    private function updateUserCartItem(int $itemId, array $data): array
    {
        $item = CarritoItem::where('id', $itemId)
            ->whereHas('carrito', function ($query) {
                $query->where('usuario_id', Auth::id());
            })
            ->firstOrFail();

        $item->update(['cantidad' => $data['cantidad']]);

        return $this->getUserCart();
    }

    /**
     * Actualiza item en el carrito de sesión
     */
    private function updateSessionCartItem(int $itemId, array $data): array
    {
        $cartItems = Session::get('cart', []);

        foreach ($cartItems as &$item) {
            if ($item['id'] == $itemId) {
                $item['cantidad'] = $data['cantidad'];
                break;
            }
        }

        Session::put('cart', $cartItems);

        return $this->getSessionCart();
    }

    /**
     * Elimina item del carrito del usuario
     */
    private function removeFromUserCart(int $itemId): void
    {
        $item = CarritoItem::where('id', $itemId)
            ->whereHas('carrito', function ($query) {
                $query->where('usuario_id', Auth::id());
            })
            ->firstOrFail();

        $item->delete();
    }

    /**
     * Elimina item del carrito de sesión
     */
    private function removeFromSessionCart(string $itemId): void
    {
        $cartItems = Session::get('cart', []);

        $cartItems = array_filter($cartItems, function ($item) use ($itemId) {
            // Comparar como string para manejar tanto IDs numéricos como strings
            return (string) $item['id'] !== (string) $itemId;
        });

        // Reindexar el array para evitar gaps
        $cartItems = array_values($cartItems);

        Session::put('cart', $cartItems);
    }

    /**
     * Limpia el carrito del usuario
     */
    private function clearUserCart(): void
    {
        $carrito = Carrito::where('usuario_id', Auth::id())->first();
        if ($carrito) {
            $carrito->items()->delete();
        }
    }

    /**
     * Limpia el carrito de sesión
     */
    private function clearSessionCart(): void
    {
        Session::forget('cart');
    }

    /**
     * Obtiene resumen del carrito del usuario
     */
    private function getUserCartSummary(): array
    {
        $carrito = Carrito::where('usuario_id', Auth::id())
            ->with(['items.producto', 'items.variante'])
            ->first();

        if (! $carrito || $carrito->items->isEmpty()) {
            return [
                'total_items' => 0,
                'total_precio' => 0,
                'items_count' => 0,
            ];
        }

        $totalItems = $carrito->items->sum('cantidad');
        $totalPrecio = $carrito->items->sum(function ($item) {
            $precio = $item->producto->precio;
            if ($item->variante) {
                $precio += $item->variante->precio_adicional ?? 0;
            }

            return $precio * $item->cantidad;
        });

        return [
            'total_items' => $totalItems,
            'total_precio' => $totalPrecio,
            'items_count' => $carrito->items->count(),
        ];
    }

    /**
     * Obtiene resumen del carrito de sesión
     */
    private function getSessionCartSummary(): array
    {
        $cartItems = Session::get('cart', []);

        if (empty($cartItems)) {
            return [
                'total_items' => 0,
                'total_precio' => 0,
                'items_count' => 0,
            ];
        }

        $totalItems = 0;
        $totalPrecio = 0;

        foreach ($cartItems as $item) {
            $producto = Producto::find($item['producto_id']);
            $variante = null;

            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
            }

            $precio = $producto->precio;
            if ($variante) {
                $precio += $variante->precio_adicional ?? 0;
            }

            $totalItems += $item['cantidad'];
            $totalPrecio += $precio * $item['cantidad'];
        }

        return [
            'total_items' => $totalItems,
            'total_precio' => $totalPrecio,
            'items_count' => count($cartItems),
        ];
    }

    /**
     * Calcula el precio unitario de un producto (incluyendo variante si existe)
     */
    private function calculatePrecioUnitario(int $productoId, ?int $varianteId): float
    {
        $producto = Producto::find($productoId);
        if (! $producto) {
            throw new Exception("Producto con ID {$productoId} no encontrado");
        }

        $precio = (float) $producto->precio;

        if ($varianteId) {
            $variante = VarianteProducto::find($varianteId);
            if ($variante) {
                $precio += (float) ($variante->precio_adicional ?? 0);
            }
        }

        return $precio;
    }
}
