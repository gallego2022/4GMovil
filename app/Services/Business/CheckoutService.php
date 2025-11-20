<?php

namespace App\Services\Business;

use App\Models\Carrito;
use App\Models\Direccion;
use App\Models\MetodoPago;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\Base\BaseService;
use App\Services\PedidoNotificationService;
use App\Services\ReservaStockService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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

            $this->logOperation('carrito_obtenido_para_checkout', [
                'cart_items' => count($cart),
                'user_id' => Auth::id(),
                'cart_data' => $cart,
            ]);

            // Validar carrito
            $this->validateCart($cart);

            // Validar productos y variantes
            $this->validateProductsAvailability($cart);

            // Obtener datos necesarios
            $direcciones = $this->getUserAddresses();
            $metodosPago = $this->getActivePaymentMethods();

            // Guardar carrito en sesión para mantenerlo durante el checkout
            // Esto es importante para cuando el usuario crea/edita direcciones
            // SIEMPRE guardar el carrito en checkout_cart, incluso si viene de BD
            Session::put('checkout_cart', $cart);

            // Si el usuario está autenticado y el carrito viene de BD, también sincronizar con checkout_cart
            // para asegurar que se preserve durante las redirecciones
            if (Auth::check() && ! empty($cart)) {
                $this->logOperation('checkout_cart_guardado', [
                    'cart_items' => count($cart),
                    'user_id' => Auth::id(),
                ]);
            }

            $this->logOperation('checkout_preparado_exitosamente', [
                'cart_items' => count($cart),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => true,
                'cart' => $cart,
                'direcciones' => $direcciones,
                'metodosPago' => $metodosPago,
            ];

        } catch (Exception $e) {
            $this->logOperation('error_preparando_checkout', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ], 'error');

            throw $e;
        }
    }

    /**
     * Procesa el checkout - Solo para Stripe
     */
    public function processCheckout(Request $request): array
    {
        return $this->executeInTransaction(function () use ($request) {
            // Validar datos del checkout
            $checkoutData = $this->validateCheckoutData($request);

            // Obtener carrito de sesión o base de datos
            $cart = Session::get('checkout_cart', []);

            // Si no hay carrito en sesión y el usuario está autenticado, obtener de BD
            if (empty($cart) && Auth::check()) {
                $carrito = Carrito::where('usuario_id', Auth::id())
                    ->with(['items.producto', 'items.variante'])
                    ->first();

                if ($carrito && ! $carrito->items->isEmpty()) {
                    $cart = $this->convertDatabaseCartToArray($carrito);
                }
            }

            if (empty($cart)) {
                throw new Exception('El carrito está vacío');
            }

            // Validar disponibilidad de stock
            $this->validateStockAvailability($cart);

            // Verificar que solo se use Stripe
            $metodoPago = MetodoPago::find($checkoutData['metodo_pago_id']);
            if (! $metodoPago || strtolower($metodoPago->nombre) !== 'stripe') {
                throw new Exception('Solo se acepta pago con Stripe');
            }

            // Crear pedido con estado pendiente
            $pedido = $this->createOrder($checkoutData, $cart);

            // Crear detalles del pedido
            $this->createOrderDetails($pedido, $cart);

            // Crear pago con estado pendiente
            $pago = $this->createPayment($pedido, $checkoutData);

            // Reservar stock temporalmente
            $this->reservarStockParaPedido($cart, $pedido->pedido_id);

            $this->logOperation('checkout_stripe_preparado', [
                'pedido_id' => $pedido->pedido_id,
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => true,
                'pedido_id' => $pedido->pedido_id,
                'pago_id' => $pago->pago_id,
                'redirect_to_stripe' => true,
                'message' => 'Pedido creado, redirigiendo a Stripe',
            ];

        }, 'procesamiento de checkout');
    }

    /**
     * Obtiene el carrito de la request, sesión o base de datos según autenticación
     */
    private function getCartFromRequest(Request $request): array
    {
        // Si el usuario está autenticado, SIEMPRE obtener el carrito de la base de datos primero
        if (Auth::check()) {
            // Obtener el carrito con todas las relaciones necesarias
            $carrito = Carrito::where('usuario_id', Auth::id())
                ->with([
                    'items' => function ($query) {
                        $query->with(['producto', 'variante']);
                    },
                ])
                ->first();

            $this->logOperation('verificando_carrito_bd', [
                'carrito_existe' => $carrito ? 'si' : 'no',
                'items_count' => $carrito ? $carrito->items->count() : 0,
                'user_id' => Auth::id(),
            ]);

            // Si hay carrito en BD, verificar si tiene items
            if ($carrito) {
                // Asegurar que los items estén cargados
                if (! $carrito->relationLoaded('items')) {
                    $carrito->load(['items.producto', 'items.variante']);
                }

                // Verificar que los items tengan productos cargados
                $itemsConProductos = $carrito->items->filter(function ($item) {
                    return $item->producto !== null;
                });

                if ($itemsConProductos->count() > 0) {
                    $this->logOperation('carrito_obtenido_de_bd', [
                        'cart_items' => $itemsConProductos->count(),
                        'user_id' => Auth::id(),
                    ]);

                    // Convertir carrito de BD al formato esperado
                    $convertedCart = $this->convertDatabaseCartToArray($carrito);

                    $this->logOperation('carrito_convertido_de_bd', [
                        'items_convertidos' => count($convertedCart),
                        'user_id' => Auth::id(),
                    ]);

                    return $convertedCart;
                } else {
                    $this->logOperation('carrito_bd_sin_items_validos', [
                        'items_totales' => $carrito->items->count(),
                        'items_con_productos' => 0,
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            // Si no hay carrito en BD o está vacío, intentar obtener de checkout_cart (sesión del checkout)
            $checkoutCart = Session::get('checkout_cart', []);
            if (! empty($checkoutCart)) {
                $this->logOperation('carrito_obtenido_de_checkout_cart', [
                    'cart_items' => count($checkoutCart),
                    'user_id' => Auth::id(),
                ]);

                return $checkoutCart;
            }

            // Si no hay checkout_cart, intentar obtener de request o sesión normal
            if ($request->isMethod('post')) {
                $cart = json_decode($request->input('cart'), true) ?? [];
                if (! empty($cart)) {
                    $this->logOperation('carrito_obtenido_por_post', ['cart_items' => count($cart)]);

                    return $this->convertSessionCartToArray($cart);
                }
            }

            $cart = Session::get('cart', []);
            if (! empty($cart)) {
                $this->logOperation('carrito_obtenido_de_sesion', ['cart_items' => count($cart)]);

                return $this->convertSessionCartToArray($cart);
            }

            // Si no hay carrito en ningún lugar, retornar vacío
            $this->logOperation('carrito_vacio', [
                'user_id' => Auth::id(),
                'mensaje' => 'No se encontró carrito en BD, checkout_cart, request ni sesión',
            ]);

            return [];
        }

        // Si no está autenticado, obtener de request o sesión
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
     * Convierte el carrito de la base de datos al formato esperado
     */
    public function convertDatabaseCartToArray($carrito): array
    {
        $cart = [];

        // Asegurar que los items estén cargados
        if (! $carrito->relationLoaded('items')) {
            $carrito->load(['items.producto', 'items.variante']);
        }

        foreach ($carrito->items as $item) {
            // Validar que el producto exista
            if (! $item->producto) {
                $this->logOperation('producto_no_encontrado_en_item', [
                    'item_id' => $item->id,
                    'producto_id' => $item->producto_id,
                ], 'warning');

                continue;
            }

            $precio = $item->producto->precio;
            if ($item->variante) {
                $precio += $item->variante->precio_adicional ?? 0;
            }

            $cart[] = [
                'id' => (string) $item->id,
                'producto_id' => $item->producto_id,
                'variante_id' => $item->variante_id,
                'cantidad' => $item->cantidad,
                'quantity' => $item->cantidad,
                'name' => $item->producto->nombre_producto.($item->variante ? ' - '.$item->variante->nombre : ''),
                'price' => $precio,
            ];
        }

        $this->logOperation('carrito_convertido', [
            'items_originales' => $carrito->items->count(),
            'items_convertidos' => count($cart),
        ]);

        return $cart;
    }

    /**
     * Convierte el carrito de sesión al formato esperado
     */
    private function convertSessionCartToArray(array $sessionCart): array
    {
        $cart = [];

        foreach ($sessionCart as $item) {
            $producto = Producto::find($item['producto_id'] ?? $item['id'] ?? null);
            if (! $producto) {
                continue;
            }

            $variante = null;
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
            }

            $precio = $producto->precio;
            if ($variante) {
                $precio += $variante->precio_adicional ?? 0;
            }

            $cart[] = [
                'id' => $item['id'] ?? uniqid(),
                'producto_id' => $item['producto_id'] ?? $item['id'],
                'variante_id' => $item['variante_id'] ?? null,
                'cantidad' => $item['cantidad'] ?? $item['quantity'] ?? 1,
                'quantity' => $item['cantidad'] ?? $item['quantity'] ?? 1,
                'name' => $producto->nombre_producto.($variante ? ' - '.$variante->nombre : ''),
                'price' => $precio,
            ];
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

            if (! $productoId) {
                throw new Exception('ID de producto no encontrado en item del carrito');
            }

            $producto = Producto::find($productoId);
            if (! $producto) {
                throw new Exception('Uno o más productos ya no están disponibles');
            }

            // Si tiene variante, validar que la variante exista
            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                if (! $variante || $variante->producto_id != $producto->producto_id) {
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
            'notas' => 'nullable|string|max:500',
        ];

        $messages = [
            'direccion_id.required' => 'Debes seleccionar una dirección de envío',
            'direccion_id.exists' => 'La dirección seleccionada no es válida',
            'metodo_pago_id.required' => 'Debes seleccionar un método de pago',
            'metodo_pago_id.exists' => 'El método de pago seleccionado no es válido',
        ];

        $validator = validator($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new Exception('Datos de checkout inválidos: '.implode(', ', $validator->errors()->all()));
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

            if (! $productoId) {
                throw new Exception('ID de producto no encontrado en item del carrito');
            }

            $producto = Producto::find($productoId);

            // Manejar tanto 'cantidad' como 'quantity' para compatibilidad
            $cantidad = $item['cantidad'] ?? $item['quantity'] ?? 1;

            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                if (! $variante) {
                    throw new Exception("Variante no encontrada para el producto '{$producto->nombre_producto}'");
                }
                if (! $variante->tieneStockSuficiente($cantidad)) {
                    $stockDisponible = $variante->stock_disponible;
                    $nombreVariante = $variante->nombre ?? 'variante';
                    throw new Exception("Stock insuficiente para '{$producto->nombre_producto} ({$nombreVariante})'. Disponible: {$stockDisponible}, Solicitado: {$cantidad}");
                }
            } else {
                if (! $producto->tieneStockSuficiente($cantidad)) {
                    $stockDisponible = $producto->stock_disponible;
                    throw new Exception("Stock insuficiente para '{$producto->nombre_producto}'. Disponible: {$stockDisponible}, Solicitado: {$cantidad}");
                }
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
            'notas' => $checkoutData['notas'] ?? null,
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

            if (! $productoId) {
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
                'subtotal' => $precio * $cantidad,
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
            'fecha_pago' => now(),
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

            if (! $productoId) {
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

            if (! $productoId) {
                throw new Exception('ID de producto no encontrado en item del carrito');
            }

            $producto = Producto::find($productoId);
            $precio = $producto->precio;
            $nombre = $producto->nombre_producto;

            if (isset($item['variante_id'])) {
                $variante = VarianteProducto::find($item['variante_id']);
                $precio += $variante->precio_adicional ?? 0;
                $nombre .= ' - '.$variante->nombre;
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
                'subtotal' => $subtotal,
            ];
        }

        return [
            'items' => $items,
            'total' => $total,
            'total_items' => count($items),
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

            if (! $metodoPago) {
                Log::warning('Método de pago no encontrado', [
                    'metodo_pago_id' => $metodoPagoId,
                    'pedido_id' => $pedido->pedido_id,
                ]);

                return;
            }

            // NO enviar correo para Stripe en el checkout - se enviará después del pago
            if (strtolower($metodoPago->nombre) === 'stripe') {
                Log::info('Método Stripe seleccionado, no se envía correo en checkout', [
                    'pedido_id' => $pedido->pedido_id,
                    'metodo_pago' => $metodoPago->nombre,
                ]);

                return;
            }

            // Verificar si es un método que requiere confirmación manual
            $requiereConfirmacionManual = $this->metodoRequiereConfirmacionManual($metodoPago);

            if (! $requiereConfirmacionManual) {
                // Para métodos como efectivo o transferencia, enviar correo inmediatamente
                // ya que el pedido se confirma automáticamente
                $notificationService = new PedidoNotificationService;
                $notificationService->confirmarPedidoMetodoNoStripe($pedido, $metodoPago->nombre);

                Log::info('Correo de confirmación enviado para método no-Stripe', [
                    'pedido_id' => $pedido->pedido_id,
                    'metodo_pago' => $metodoPago->nombre,
                ]);
            } else {
                // Para métodos que requieren confirmación manual, también enviar notificación al administrador
                // pero no el correo de confirmación al cliente
                $this->notificarAdministradores($pedido, $metodoPago->nombre);

                Log::info('Método de pago requiere confirmación manual, notificación enviada al administrador', [
                    'pedido_id' => $pedido->pedido_id,
                    'metodo_pago' => $metodoPago->nombre,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de confirmación en checkout', [
                'pedido_id' => $pedido->pedido_id,
                'metodo_pago_id' => $metodoPagoId,
                'error' => $e->getMessage(),
            ]);
            // No lanzar excepción para no afectar el flujo del checkout
        }
    }

    /**
     * Determinar si un método de pago requiere confirmación manual
     */
    private function metodoRequiereConfirmacionManual($metodoPago): bool
    {
        if (! $metodoPago) {
            return false;
        }

        // Métodos que requieren confirmación manual (efectivo, transferencia)
        // Stripe NO requiere confirmación manual porque se confirma automáticamente
        $metodosManuales = ['Efectivo', 'Transferencia Bancaria', 'efectivo', 'transferencia bancaria', 'transferencia'];

        return in_array($metodoPago->nombre, $metodosManuales) ||
               in_array(strtolower($metodoPago->nombre), $metodosManuales);
    }

    /**
     * Notifica a los administradores sobre un nuevo pedido.
     */
    private function notificarAdministradores($pedido, $metodoPagoNombre)
    {
        try {
            $adminNotificationService = new \App\Services\AdminNotificationService;
            $adminNotificationService->notificarPedidoNuevo($pedido, $metodoPagoNombre);
        } catch (\Exception $e) {
            Log::error('Error al notificar administradores', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Actualiza el stock de los productos (descuenta definitivamente)
     */
    private function updateProductStock(array $cart): void
    {
        foreach ($cart as $item) {
            // Manejar tanto 'id' como 'producto_id' para compatibilidad
            $productoId = $item['producto_id'] ?? $item['id'] ?? null;
            $cantidad = $item['cantidad'] ?? $item['quantity'] ?? 1;

            if (! $productoId) {
                continue; // Saltar si no hay ID de producto
            }

            if (isset($item['variante_id'])) {
                // Para variantes, usar el método que registra movimientos
                $variante = \App\Models\VarianteProducto::find($item['variante_id']);
                if ($variante) {
                    $variante->registrarSalida(
                        $cantidad,
                        'Venta - Checkout',
                        \Illuminate\Support\Facades\Auth::id(),
                        'checkout_venta'
                    );
                }
            } else {
                // Para productos sin variantes, usar el método que registra movimientos
                $producto = \App\Models\Producto::find($productoId);
                if ($producto) {
                    $producto->registrarSalida(
                        $cantidad,
                        'Venta - Checkout',
                        \Illuminate\Support\Facades\Auth::id(),
                        null // No hay pedido_id en este punto
                    );
                }
            }
        }
    }

    /**
     * Reserva stock para un pedido (sin descontar definitivamente)
     */
    private function reservarStockParaPedido(array $cart, string $pedidoId): void
    {
        foreach ($cart as $item) {
            // Manejar tanto 'id' como 'producto_id' para compatibilidad
            $productoId = $item['producto_id'] ?? $item['id'] ?? null;
            $cantidad = $item['cantidad'] ?? $item['quantity'] ?? 1;

            if (! $productoId) {
                continue; // Saltar si no hay ID de producto
            }

            if (isset($item['variante_id'])) {
                // Para variantes, crear reserva temporal
                $this->reservaStockService->crearReservaVariante(
                    $item['variante_id'],
                    Auth::id(),
                    $cantidad,
                    $pedidoId,
                    "Reserva - Pedido #{$pedidoId}",
                    60 // 60 minutos de reserva
                );
            } else {
                // Para productos sin variantes, usar el sistema de reserva existente
                $producto = \App\Models\Producto::find($productoId);
                if ($producto) {
                    $producto->reservarStock(
                        $cantidad,
                        "Reserva - Pedido #{$pedidoId}",
                        Auth::id(),
                        (int) $pedidoId
                    );
                }
            }
        }
    }

    /**
     * Confirma un pedido y descuenta el stock definitivamente
     */
    public function confirmarPedido(string $pedidoId): array
    {
        return $this->executeInTransaction(function () use ($pedidoId) {
            // Obtener el pedido
            $pedido = \App\Models\Pedido::findOrFail($pedidoId);

            // Verificar que el pedido pertenece al usuario autenticado
            if ($pedido->usuario_id !== Auth::id()) {
                throw new Exception('No tienes permisos para confirmar este pedido');
            }

            // Verificar que el pedido está en estado pendiente
            if ($pedido->estado_id !== 1) { // 1 = pendiente
                throw new Exception('El pedido no está en estado pendiente');
            }

            // Obtener los detalles del pedido
            $detalles = $pedido->detalles;

            // Verificar si el pedido tiene variantes antes de confirmar reservas
            $tieneVariantes = $detalles->contains(function ($detalle) {
                return $detalle->variante_id !== null;
            });

            // Si tiene variantes, confirmar todas las reservas del pedido una sola vez
            if ($tieneVariantes) {
                $this->reservaStockService->confirmarReservasPedido($pedidoId, Auth::id());
            }

            // Descontar stock definitivamente
            foreach ($detalles as $detalle) {
                if ($detalle->variante_id) {
                    // Para variantes, las reservas ya se confirmaron arriba
                    // No hacer nada más aquí
                } else {
                    // Para productos sin variantes, liberar stock reservado y registrar salida
                    $producto = \App\Models\Producto::find($detalle->producto_id);
                    if ($producto) {
                        // Liberar stock reservado primero
                        if ($producto->stock_reservado > 0) {
                            $producto->liberarStockReservado(
                                $detalle->cantidad,
                                "Confirmación de pedido #{$pedidoId}",
                                Auth::id(),
                                (int) $pedidoId
                            );
                        }
                        // Registrar salida
                        $producto->registrarSalida(
                            $detalle->cantidad,
                            "Venta Confirmada - Pedido #{$pedidoId}",
                            Auth::id(),
                            (int) $pedidoId
                        );
                    }
                }
            }

            // Actualizar estado del pedido a confirmado
            $pedido->update(['estado_id' => 2]); // 2 = confirmado

            // Actualizar estado del pago
            $pago = $pedido->pago;
            if ($pago) {
                $pago->update(['estado' => 'confirmado']);
            }

            // Enviar notificaciones
            $this->enviarCorreoConfirmacionSiEsNecesario($pedido, $pago->metodo_id);

            $this->logOperation('pedido_confirmado_exitosamente', [
                'pedido_id' => $pedidoId,
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => true,
                'message' => 'Pedido confirmado exitosamente. El stock ha sido descontado definitivamente.',
            ];

        }, 'confirmación de pedido');
    }
}
