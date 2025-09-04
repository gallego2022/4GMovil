<?php

namespace App\Services\Business;

use App\Services\Base\BaseService;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Direccion;
use App\Models\MetodoPago;
use App\Services\ReservaStockService;
use App\Services\PedidoNotificationService;
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
            
            // Crear pedido
            $pedido = $this->createOrder($checkoutData, $cart);
            
            // Crear detalles del pedido
            $this->createOrderDetails($pedido, $cart);
            
            // Crear pago
            $pago = $this->createPayment($pedido, $checkoutData);
            
            // Verificar si es Stripe para manejo especial
            $metodoPago = MetodoPago::find($checkoutData['metodo_pago_id']);
            
            if ($metodoPago && strtolower($metodoPago->nombre) === 'stripe') {
                // Para Stripe, solo crear el pedido y redirigir al pago
                $this->logOperation('checkout_stripe_preparado', [
                    'pedido_id' => $pedido->pedido_id,
                    'user_id' => Auth::id()
                ]);

                return [
                    'success' => true,
                    'pedido_id' => $pedido->pedido_id,
                    'pago_id' => $pago->pago_id,
                    'redirect_to_stripe' => true,
                    'message' => 'Pedido creado, redirigiendo a Stripe'
                ];
            } else {
                // Para métodos no-Stripe, procesar completamente y enviar correo
                $this->enviarCorreoConfirmacionSiEsNecesario($pedido, $checkoutData['metodo_pago_id']);
                
                $this->logOperation('checkout_procesado_exitosamente', [
                    'pedido_id' => $pedido->pedido_id,
                    'user_id' => Auth::id()
                ]);

                return [
                    'success' => true,
                    'pedido_id' => $pedido->pedido_id,
                    'pago_id' => $pago->pago_id,
                    'redirect_to_stripe' => false,
                    'message' => 'Pedido procesado exitosamente'
                ];
            }

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
            // Manejar tanto 'id' como 'producto_id' para compatibilidad
            $productoId = $item['producto_id'] ?? $item['id'] ?? null;
            
            if (!$productoId) {
                throw new Exception('ID de producto no encontrado en item del carrito');
            }
            
            $producto = Producto::find($productoId);
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
            'direccion_id' => 'required|exists:direcciones,direccion_id',
            'metodo_pago_id' => 'required|exists:metodos_pago,metodo_id',
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
            // Manejar tanto 'id' como 'producto_id' para compatibilidad
            $productoId = $item['producto_id'] ?? $item['id'] ?? null;
            
            if (!$productoId) {
                throw new Exception('ID de producto no encontrado en item del carrito');
            }
            
            $producto = Producto::find($productoId);
            
            // Manejar tanto 'cantidad' como 'quantity' para compatibilidad
            $cantidad = $item['cantidad'] ?? $item['quantity'] ?? 1;
            
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                $this->validateStock($variante->stock, $cantidad, $producto->nombre_producto);
            } else {
                $this->validateStock($producto->stock, $cantidad, $producto->nombre_producto);
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
            'estado_id' => 1, // Estado pendiente
            'fecha_pedido' => now(),
            'total' => $this->calculateTotal($cart),
            'notas' => $checkoutData['notas'] ?? null
        ]);

        return $pedido;
    }

    /**
     * Crea los detalles del pedido
     */
    private function createOrderDetails($pedido, array $cart): void
    {
        foreach ($cart as $item) {
            // Manejar tanto 'id' como 'producto_id' para compatibilidad
            $productoId = $item['producto_id'] ?? $item['id'] ?? null;
            
            if (!$productoId) {
                throw new Exception('ID de producto no encontrado en item del carrito');
            }
            
            $producto = Producto::find($productoId);
            $precio = $producto->precio;
            
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                $precio += $variante->precio_adicional ?? 0;
            }

            // Manejar tanto 'cantidad' como 'quantity' para compatibilidad
            $cantidad = $item['cantidad'] ?? $item['quantity'] ?? 1;
            
            \App\Models\DetallePedido::create([
                'pedido_id' => $pedido->pedido_id,
                'producto_id' => $producto->producto_id,
                'variante_id' => $item['variante_id'] ?? null,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $precio * $cantidad
            ]);
        }
    }

    /**
     * Crea el pago
     */
    private function createPayment($pedido, array $checkoutData)
    {
        return \App\Models\Pago::create([
            'pedido_id' => $pedido->pedido_id,
            'metodo_id' => $checkoutData['metodo_pago_id'],
            'monto' => $pedido->total,
            'estado' => 'pendiente',
            'fecha_pago' => now()
        ]);
    }

    /**
     * Calcula el total del carrito
     */
    private function calculateTotal(array $cart): float
    {
        $total = 0;
        
        foreach ($cart as $item) {
            // Manejar tanto 'id' como 'producto_id' para compatibilidad
            $productoId = $item['producto_id'] ?? $item['id'] ?? null;
            
            if (!$productoId) {
                throw new Exception('ID de producto no encontrado en item del carrito');
            }
            
            $producto = Producto::find($productoId);
            $precio = $producto->precio;
            
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                $precio += $variante->precio_adicional ?? 0;
            }
            
            // Manejar tanto 'cantidad' como 'quantity' para compatibilidad
            $cantidad = $item['cantidad'] ?? $item['quantity'] ?? 1;
            $total += $precio * $cantidad;
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
            // Manejar tanto 'id' como 'producto_id' para compatibilidad
            $productoId = $item['producto_id'] ?? $item['id'] ?? null;
            
            if (!$productoId) {
                throw new Exception('ID de producto no encontrado en item del carrito');
            }
            
            $producto = Producto::find($productoId);
            $precio = $producto->precio;
            $nombre = $producto->nombre_producto;
            
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                $precio += $variante->precio_adicional ?? 0;
                $nombre .= ' - ' . $variante->nombre;
            }
            
            // Manejar tanto 'cantidad' como 'quantity' para compatibilidad
            $cantidad = $item['cantidad'] ?? $item['quantity'] ?? 1;
            $subtotal = $precio * $cantidad;
            $total += $subtotal;
            
            $items[] = [
                'producto' => $producto,
                'variante' => $variante ?? null,
                'cantidad' => $cantidad,
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

    /**
     * Verifica el stock disponible para el carrito
     */
    public function verificarStock(array $cart): array
    {
        return $this->reservaStockService->verificarStockCarrito($cart);
    }

    /**
     * Enviar correo de confirmación si es necesario
     * Solo se envía para métodos de pago que no requieren confirmación posterior
     */
    private function enviarCorreoConfirmacionSiEsNecesario($pedido, int $metodoPagoId): void
    {
        try {
            // Obtener información del método de pago
            $metodoPago = MetodoPago::find($metodoPagoId);
            
            if (!$metodoPago) {
                Log::warning('Método de pago no encontrado', [
                    'metodo_pago_id' => $metodoPagoId,
                    'pedido_id' => $pedido->pedido_id
                ]);
                return;
            }

            // NO enviar correo para Stripe en el checkout - se enviará después del pago
            if (strtolower($metodoPago->nombre) === 'stripe') {
                Log::info('Método Stripe seleccionado, no se envía correo en checkout', [
                    'pedido_id' => $pedido->pedido_id,
                    'metodo_pago' => $metodoPago->nombre
                ]);
                return;
            }

            // Verificar si es un método que requiere confirmación manual
            $requiereConfirmacionManual = $this->metodoRequiereConfirmacionManual($metodoPago);
            
            if (!$requiereConfirmacionManual) {
                // Para métodos como efectivo o transferencia, enviar correo inmediatamente
                // ya que el pedido se confirma automáticamente
                $notificationService = new PedidoNotificationService();
                $notificationService->confirmarPedidoMetodoNoStripe($pedido, $metodoPago->nombre);
                
                Log::info('Correo de confirmación enviado para método no-Stripe', [
                    'pedido_id' => $pedido->pedido_id,
                    'metodo_pago' => $metodoPago->nombre
                ]);
            } else {
                // Para métodos que requieren confirmación manual, también enviar notificación al administrador
                // pero no el correo de confirmación al cliente
                $this->notificarAdministradores($pedido, $metodoPago->nombre);
                
                Log::info('Método de pago requiere confirmación manual, notificación enviada al administrador', [
                    'pedido_id' => $pedido->pedido_id,
                    'metodo_pago' => $metodoPago->nombre
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de confirmación en checkout', [
                'pedido_id' => $pedido->pedido_id,
                'metodo_pago_id' => $metodoPagoId,
                'error' => $e->getMessage()
            ]);
            // No lanzar excepción para no afectar el flujo del checkout
        }
    }

    /**
     * Determinar si un método de pago requiere confirmación manual
     */
    private function metodoRequiereConfirmacionManual(MetodoPago $metodoPago): bool
    {
        // Métodos que requieren confirmación manual (efectivo, transferencia)
        // Stripe NO requiere confirmación manual porque se confirma automáticamente
        $metodosManuales = ['Efectivo', 'Transferencia Bancaria'];
        
        return in_array($metodoPago->nombre, $metodosManuales);
    }

    /**
     * Notifica a los administradores sobre un nuevo pedido.
     */
    private function notificarAdministradores($pedido, $metodoPagoNombre)
    {
        try {
            $adminNotificationService = new \App\Services\AdminNotificationService();
            $adminNotificationService->notificarPedidoNuevo($pedido, $metodoPagoNombre);
        } catch (\Exception $e) {
            Log::error('Error al notificar administradores', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
