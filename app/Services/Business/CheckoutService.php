<?php

namespace App\Services\Business;

use App\Services\Base\BaseService;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Direccion;
use App\Models\MetodoPago;
use App\Services\ReservaStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Exception;

class CheckoutService extends BaseService
{
    protected $reservaStockService;

    public function __construct(ReservaStockService $reservaStockService)
    {
        $this->reservaStockService = $reservaStockService;
    }

    /**
     * Prepara los datos del checkout
     */
    public function prepareCheckout(Request $request): array
    {
        $this->logOperation('preparando_checkout', ['user_id' => Auth::id()]);

        try {
            // Obtener carrito
            $cart = $this->getCartFromRequest($request);
            
            // Validar carrito
            $this->validateCart($cart);
            
            // Validar productos y variantes
            $this->validateProductsAvailability($cart);
            
            // Obtener datos necesarios
            $direcciones = $this->getUserAddresses();
            $metodosPago = $this->getActivePaymentMethods();
            
            // Guardar carrito en sesión
            Session::put('cart', $cart);
            
            $this->logOperation('checkout_preparado_exitosamente', [
                'cart_items' => count($cart),
                'user_id' => Auth::id()
            ]);

            return [
                'success' => true,
                'cart' => $cart,
                'direcciones' => $direcciones,
                'metodosPago' => $metodosPago
            ];

        } catch (Exception $e) {
            $this->logOperation('error_preparando_checkout', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ], 'error');
            
            throw $e;
        }
    }

    /**
     * Procesa el checkout
     */
    public function processCheckout(Request $request): array
    {
        $this->logOperation('procesando_checkout', ['user_id' => Auth::id()]);

        return $this->executeInTransaction(function () use ($request) {
            // Validar datos del checkout
            $checkoutData = $this->validateCheckoutData($request);
            
            // Obtener carrito de sesión
            $cart = Session::get('cart', []);
            if (empty($cart)) {
                throw new Exception('El carrito está vacío');
            }

            // Validar disponibilidad de stock
            $this->validateStockAvailability($cart);
            
            // Reservar stock temporalmente
            // $reservaId = $this->reservaStockService->reservarStock($cart, Auth::id());
            
            // Crear pedido
            $pedido = $this->createOrder($checkoutData, $cart);
            
            // Crear detalles del pedido
            $this->createOrderDetails($pedido, $cart);
            
            // Crear pago
            $pago = $this->createPayment($pedido, $checkoutData);
            
            // Limpiar carrito y reserva
            Session::forget('cart');
            // $this->reservaStockService->confirmarReserva($reservaId);
            
            $this->logOperation('checkout_procesado_exitosamente', [
                'pedido_id' => $pedido->id,
                'user_id' => Auth::id()
            ]);

            return [
                'success' => true,
                'pedido_id' => $pedido->id,
                'pago_id' => $pago->id,
                'message' => 'Pedido procesado exitosamente'
            ];

        }, 'procesamiento de checkout');
    }

    /**
     * Obtiene el carrito de la request o sesión
     */
    private function getCartFromRequest(Request $request): array
    {
        if ($request->isMethod('post')) {
            $cart = json_decode($request->input('cart'), true) ?? [];
            $this->logOperation('carrito_obtenido_por_post', ['cart_items' => count($cart)]);
        } else {
            $cart = Session::get('cart', []);
            $this->logOperation('carrito_obtenido_de_sesion', ['cart_items' => count($cart)]);
        }

        return $cart;
    }

    /**
     * Valida que el carrito no esté vacío
     */
    private function validateCart(array $cart): void
    {
        if (empty($cart)) {
            throw new Exception('Tu carrito está vacío');
        }
    }

    /**
     * Valida la disponibilidad de productos y variantes
     */
    private function validateProductsAvailability(array $cart): void
    {
        foreach ($cart as $item) {
            $producto = Producto::find($item['id']);
            if (!$producto) {
                throw new Exception('Uno o más productos ya no están disponibles');
            }

            // Si tiene variante, validar que la variante exista
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                if (!$variante || $variante->producto_id != $producto->producto_id) {
                    throw new Exception('Uno o más productos ya no están disponibles');
                }
            }
        }
    }

    /**
     * Obtiene las direcciones del usuario
     */
    private function getUserAddresses()
    {
        return Direccion::where('usuario_id', Auth::id())->get();
    }

    /**
     * Obtiene los métodos de pago activos
     */
    private function getActivePaymentMethods()
    {
        return MetodoPago::where('estado', 1)->get();
    }

    /**
     * Valida los datos del checkout
     */
    private function validateCheckoutData(Request $request): array
    {
        $rules = [
            'direccion_id' => 'required|exists:direcciones,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'notas' => 'nullable|string|max:500'
        ];

        $messages = [
            'direccion_id.required' => 'Debes seleccionar una dirección de envío',
            'direccion_id.exists' => 'La dirección seleccionada no es válida',
            'metodo_pago_id.required' => 'Debes seleccionar un método de pago',
            'metodo_pago_id.exists' => 'El método de pago seleccionado no es válido'
        ];

        $validator = validator($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            throw new Exception('Datos de checkout inválidos: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validated();
    }

    /**
     * Valida la disponibilidad de stock
     */
    private function validateStockAvailability(array $cart): void
    {
        foreach ($cart as $item) {
            $producto = Producto::find($item['id']);
            
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                $this->validateStock($variante->stock, $item['cantidad'], $producto->nombre_producto);
            } else {
                $this->validateStock($producto->stock, $item['cantidad'], $producto->nombre_producto);
            }
        }
    }

    /**
     * Crea el pedido
     */
    private function createOrder(array $checkoutData, array $cart)
    {
        $pedido = \App\Models\Pedido::create([
            'usuario_id' => Auth::id(),
            'direccion_id' => $checkoutData['direccion_id'],
            'metodo_pago_id' => $checkoutData['metodo_pago_id'],
            'notas' => $checkoutData['notas'] ?? null,
            'estado' => 'pendiente',
            'total' => $this->calculateTotal($cart)
        ]);

        return $pedido;
    }

    /**
     * Crea los detalles del pedido
     */
    private function createOrderDetails($pedido, array $cart): void
    {
        foreach ($cart as $item) {
            $producto = Producto::find($item['id']);
            $precio = $producto->precio;
            
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                $precio += $variante->precio_adicional ?? 0;
            }

            \App\Models\DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $producto->id,
                'variante_id' => $item['variante_id'] ?? null,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $precio,
                'subtotal' => $precio * $item['cantidad']
            ]);
        }
    }

    /**
     * Crea el pago
     */
    private function createPayment($pedido, array $checkoutData)
    {
        return \App\Models\Pago::create([
            'pedido_id' => $pedido->id,
            'metodo_pago_id' => $checkoutData['metodo_pago_id'],
            'monto' => $pedido->total,
            'estado' => 'pendiente'
        ]);
    }

    /**
     * Calcula el total del carrito
     */
    private function calculateTotal(array $cart): float
    {
        $total = 0;
        
        foreach ($cart as $item) {
            $producto = Producto::find($item['id']);
            $precio = $producto->precio;
            
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                $precio += $variante->precio_adicional ?? 0;
            }
            
            $total += $precio * $item['cantidad'];
        }
        
        return $total;
    }

    /**
     * Obtiene el resumen del checkout
     */
    public function getCheckoutSummary(array $cart): array
    {
        $items = [];
        $total = 0;
        
        foreach ($cart as $item) {
            $producto = Producto::find($item['id']);
            $precio = $producto->precio;
            $nombre = $producto->nombre_producto;
            
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                $precio += $variante->precio_adicional ?? 0;
                $nombre .= ' - ' . $variante->nombre;
            }
            
            $subtotal = $precio * $item['cantidad'];
            $total += $subtotal;
            
            $items[] = [
                'producto' => $producto,
                'variante' => $variante ?? null,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $precio,
                'subtotal' => $subtotal
            ];
        }
        
        return [
            'items' => $items,
            'total' => $total,
            'total_items' => count($items)
        ];
    }
}
