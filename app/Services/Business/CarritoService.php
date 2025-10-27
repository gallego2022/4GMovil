<?php

namespace App\Services\Business;

use App\Services\Base\BaseService;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Carrito;
use App\Models\CarritoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Exception;

class CarritoService extends BaseService
{
    /**
     * Obtiene el carrito del usuario autenticado o de sesión
     */
    public function getCart(): array
    {
        $this->logOperation('obteniendo_carrito', ['user_id' => Auth::id()]);

        try {
            if (Auth::check()) {
                $carrito = $this->getUserCart();
            } else {
                $carrito = $this->getSessionCart();
            }

            return $this->formatSuccessResponse($carrito, 'Carrito obtenido exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_carrito', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Agrega un producto al carrito
     */
    public function addToCart(Request $request): array
    {
        $this->logOperation('agregando_producto_carrito', [
            'user_id' => Auth::id(),
            'producto_id' => $request->input('producto_id')
        ]);

        return $this->executeInTransaction(function () use ($request) {
            // Validar datos de entrada
            $data = $this->validateAddToCartData($request);
            
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
                'cantidad' => $data['cantidad']
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
            'item_id' => $itemId
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
                'cantidad' => $data['cantidad']
            ]);

            return $this->formatSuccessResponse($result, 'Carrito actualizado exitosamente');

        }, 'actualizar item del carrito');
    }

    /**
     * Elimina un item del carrito
     */
    public function removeFromCart(int $itemId): array
    {
        $this->logOperation('eliminando_item_carrito', [
            'user_id' => Auth::id(),
            'item_id' => $itemId
        ]);

        return $this->executeInTransaction(function () use ($itemId) {
            if (Auth::check()) {
                $this->removeFromUserCart($itemId);
            } else {
                $this->removeFromSessionCart($itemId);
            }

            $this->logOperation('item_carrito_eliminado', [
                'user_id' => Auth::id(),
                'item_id' => $itemId
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

            foreach ($sessionCart as $item) {
                $this->addToUserCart($item);
            }

            // Limpiar carrito de sesión
            Session::forget('cart');

            $this->logOperation('carrito_sincronizado_exitosamente', [
                'user_id' => Auth::id(),
                'items_sincronizados' => count($sessionCart)
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
            'variante_id' => 'nullable|exists:variantes_producto,variante_id'
        ];

        $messages = [
            'producto_id.required' => 'El ID del producto es obligatorio',
            'producto_id.exists' => 'El producto no existe',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad mínima es 1',
            'cantidad.max' => 'La cantidad máxima es 100',
            'variante_id.exists' => 'La variante del producto no existe'
        ];

        $validator = validator($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            throw new Exception('Datos inválidos: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validated();
    }

    /**
     * Valida los datos para actualizar item del carrito
     */
    private function validateUpdateCartItemData(Request $request): array
    {
        $rules = [
            'cantidad' => 'required|integer|min:1|max:100'
        ];

        $messages = [
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad mínima es 1',
            'cantidad.max' => 'La cantidad máxima es 100'
        ];

        $validator = validator($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            throw new Exception('Datos inválidos: ' . implode(', ', $validator->errors()->all()));
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
            if (!$variante || $variante->producto_id != $productoId) {
                throw new Exception('La variante del producto no es válida');
            }
            $this->validateStock($variante->stock, $cantidad, 'variante del producto');
        } else {
            $producto = Producto::find($productoId);
            if (!$producto) {
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

        if (!$carrito) {
            $carrito = Carrito::create(['usuario_id' => Auth::id()]);
        }

        return [
            'id' => $carrito->id,
            'items' => $carrito->items,
            'total_items' => $carrito->items->sum('cantidad'),
            'total_precio' => $carrito->items->sum(function ($item) {
                $precio = $item->producto->precio;
                if ($item->variante) {
                    $precio += $item->variante->precio_adicional ?? 0;
                }
                return $precio * $item->cantidad;
            })
        ];
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
                'subtotal' => $precio * $item['cantidad']
            ];

            $totalItems += $item['cantidad'];
            $totalPrecio += $precio * $item['cantidad'];
        }

        return [
            'id' => 'session',
            'items' => $items,
            'total_items' => $totalItems,
            'total_precio' => $totalPrecio
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

        if ($existingItem) {
            // Actualizar cantidad existente
            $existingItem->update([
                'cantidad' => $existingItem->cantidad + $data['cantidad']
            ]);
        } else {
            // Crear nuevo item
            $carrito->items()->create([
                'producto_id' => $data['producto_id'],
                'variante_id' => $data['variante_id'] ?? null,
                'cantidad' => $data['cantidad']
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
            if ($item['producto_id'] == $data['producto_id'] && 
                ($item['variante_id'] ?? null) == ($data['variante_id'] ?? null)) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Actualizar cantidad existente
            $cartItems[$existingIndex]['cantidad'] += $data['cantidad'];
        } else {
            // Agregar nuevo item
            $cartItems[] = [
                'id' => $itemId,
                'producto_id' => $data['producto_id'],
                'variante_id' => $data['variante_id'] ?? null,
                'cantidad' => $data['cantidad']
            ];
        }

        Session::put('cart', $cartItems);
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
    private function removeFromSessionCart(int $itemId): void
    {
        $cartItems = Session::get('cart', []);
        
        $cartItems = array_filter($cartItems, function ($item) use ($itemId) {
            return $item['id'] != $itemId;
        });

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

        if (!$carrito || $carrito->items->isEmpty()) {
            return [
                'total_items' => 0,
                'total_precio' => 0,
                'items_count' => 0
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
            'items_count' => $carrito->items->count()
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
                'items_count' => 0
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
            'items_count' => count($cartItems)
        ];
    }
}
