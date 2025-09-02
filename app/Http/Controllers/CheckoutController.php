<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Direccion;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Services\ReservaStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    protected $reservaStockService;

    public function __construct(ReservaStockService $reservaStockService)
    {
        $this->middleware('auth');
        $this->reservaStockService = $reservaStockService;
    }

    public function index(Request $request)
    {
        try {
            Log::info('Iniciando checkout.index');
            
            // Obtener el carrito del POST request o de la sesión
            $cart = [];
            if ($request->isMethod('post')) {
                $cart = json_decode($request->input('cart'), true) ?? [];
                Log::info('Carrito recibido por POST', ['cart' => $cart]);
            } else {
                $cart = session('cart', []);
                Log::info('Carrito obtenido de sesión', ['cart' => $cart]);
            }
            
            if (empty($cart)) {
                Log::warning('Intento de checkout con carrito vacío');
                return redirect()->route('landing')
                    ->with('mensaje', 'Tu carrito está vacío')
                    ->with('tipo', 'error');
            }

            // Validar que los productos existan y estén disponibles
            foreach ($cart as $item) {
                $producto = Producto::find($item['id']);
                if (!$producto) {
                    Log::warning('Producto no encontrado en checkout', ['producto_id' => $item['id']]);
                    return redirect()->route('landing')
                        ->with('mensaje', 'Uno o más productos ya no están disponibles')
                        ->with('tipo', 'error');
                }

                // Si tiene variante, validar que la variante exista
                if (isset($item['variante_id'])) {
                    $variante = VarianteProducto::find($item['variante_id']);
                    if (!$variante || $variante->producto_id != $producto->producto_id) {
                        Log::warning('Variante no encontrada o no válida en checkout', [
                            'variante_id' => $item['variante_id'],
                            'producto_id' => $producto->producto_id
                        ]);
                        return redirect()->route('landing')
                            ->with('mensaje', 'Uno o más productos ya no están disponibles')
                            ->with('tipo', 'error');
                    }
                }
            }

            // Obtener las direcciones del usuario
            $direcciones = Direccion::where('usuario_id', Auth::id())->get();
            Log::info('Direcciones del usuario obtenidas', ['count' => $direcciones->count()]);
            
            // Obtener los métodos de pago disponibles
            $metodosPago = MetodoPago::where('estado', 1)->get();
            Log::info('Métodos de pago activos obtenidos', ['count' => $metodosPago->count()]);

            // Guardar el carrito en la sesión para usarlo en el proceso de checkout
            session(['cart' => $cart]);
            Log::info('Carrito guardado en sesión');

            return view('checkout.index', compact('cart', 'direcciones', 'metodosPago'));
        } catch (\Exception $e) {
            Log::error('Error en checkout.index: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id()
            ]);
            return redirect()->route('landing')
                ->with('mensaje', 'Hubo un error al procesar tu solicitud. Por favor, intenta nuevamente.')
                ->with('tipo', 'error');
        }
    }

    public function process(Request $request)
    {
        try {
            Log::info('Iniciando checkout.process', ['request' => $request->all()]);

            // Validar los datos del formulario
            $validated = $request->validate([
                'direccion_id' => 'required|exists:direcciones,direccion_id',
                'metodo_pago_id' => 'required|exists:metodos_pago,metodo_id',
            ], [
                'direccion_id.required' => 'Debes seleccionar una dirección de envío',
                'direccion_id.exists' => 'La dirección seleccionada no es válida',
                'metodo_pago_id.required' => 'Debes seleccionar un método de pago',
                'metodo_pago_id.exists' => 'El método de pago seleccionado no es válido',
            ]);

            Log::info('Validación de formulario exitosa', ['validated' => $validated]);

            // Verificar que la dirección pertenezca al usuario
            $direccion = Direccion::where('direccion_id', $request->direccion_id)
                ->where('usuario_id', Auth::id())
                ->firstOrFail();

            Log::info('Dirección verificada', ['direccion_id' => $direccion->direccion_id]);

            DB::beginTransaction();
            Log::info('Iniciando transacción DB');

            // Obtener el carrito de la sesión
            $cart = session('cart', []);
            
            if (empty($cart)) {
                Log::warning('Intento de procesar checkout con carrito vacío');
                return redirect()->route('landing')
                    ->with('mensaje', 'Tu carrito está vacío')
                    ->with('tipo', 'error');
            }

            // Verificar stock usando el servicio de reservas
            $verificacionStock = $this->reservaStockService->verificarStockCarrito($cart);
            
            if (!$verificacionStock['disponible']) {
                DB::rollBack();
                $errores = implode('; ', $verificacionStock['errores']);
                Log::warning('Stock insuficiente en checkout', ['errores' => $verificacionStock['errores']]);
                return redirect()->route('landing')
                    ->with('mensaje', 'Stock insuficiente: ' . $errores)
                    ->with('tipo', 'error');
            }

            // Validar productos y calcular el total
            $total = 0;
            $productosValidados = [];
            
            foreach ($cart as $item) {
                $producto = Producto::findOrFail($item['id']);
                $varianteId = $item['variante_id'] ?? null;
                $cantidad = $item['quantity'];
                
                // Calcular precio (incluir precio adicional de variante si existe)
                $precio = $item['price'];
                if ($varianteId) {
                    $variante = VarianteProducto::find($varianteId);
                    if ($variante) {
                        $precio += $variante->precio_adicional;
                    }
                }
                
                $total += $precio * $cantidad;
                $productosValidados[] = [
                    'producto' => $producto,
                    'variante_id' => $varianteId,
                    'cantidad' => $cantidad,
                    'precio' => $precio
                ];
            }

            Log::info('Productos validados y total calculado', [
                'total' => $total,
                'productos_count' => count($productosValidados)
            ]);

            // Crear el pedido
            $pedido = Pedido::create([
                'usuario_id' => Auth::id(),
                'direccion_id' => $request->direccion_id,
                'fecha_pedido' => now(),
                'estado_id' => 1, // Estado inicial (pendiente)
                'total' => $total
            ]);

            Log::info('Pedido creado', ['pedido_id' => $pedido->pedido_id]);

            // Crear reservas de stock usando el servicio
            $resultadoReservas = $this->reservaStockService->crearReservasCarrito(
                $cart,
                Auth::id(),
                $pedido->pedido_id
            );

            if (!empty($resultadoReservas['errores'])) {
                DB::rollBack();
                $errores = implode('; ', $resultadoReservas['errores']);
                Log::error('Error al crear reservas', ['errores' => $resultadoReservas['errores']]);
                return redirect()->route('landing')
                    ->with('mensaje', 'Error al procesar el pedido: ' . $errores)
                    ->with('tipo', 'error');
            }

            // Crear los detalles del pedido
            foreach ($productosValidados as $item) {
                DetallePedido::create([
                    'pedido_id' => $pedido->pedido_id,
                    'producto_id' => $item['producto']->producto_id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio']
                ]);
            }

            Log::info('Detalles del pedido creados y reservas establecidas');

            // Verificar si el método de pago es Stripe
            $metodoPago = MetodoPago::find($request->metodo_pago_id);
            
            if ($metodoPago && $metodoPago->nombre === 'Stripe') {
                // Procesar pago con Stripe
                try {
                    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                    
                    // Crear el pago en la base de datos
                    $pago = Pago::create([
                        'pedido_id' => $pedido->pedido_id,
                        'metodo_id' => $request->metodo_pago_id,
                        'monto' => $total,
                        'estado' => 'pendiente',
                        'fecha_pago' => now(),
                        'referencia' => 'STRIPE-' . time()
                    ]);

                    // Crear sesión de Stripe
                    $session = $stripe->checkout->sessions->create([
                        'payment_method_types' => ['card'],
                        'line_items' => [
                            [
                                'price_data' => [
                                    'currency' => 'cop',
                                    'product_data' => [
                                        'name' => 'Pedido #' . $pedido->pedido_id,
                                    ],
                                    'unit_amount' => $total * 100, // Stripe usa centavos
                                ],
                                'quantity' => 1,
                            ],
                        ],
                        'mode' => 'payment',
                        'success_url' => route('checkout.success', $pedido->pedido_id) . '?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => route('checkout.cancel', $pedido->pedido_id),
                        'metadata' => [
                            'pedido_id' => $pedido->pedido_id,
                            'pago_id' => $pago->pago_id
                        ]
                    ]);

                    // Actualizar el pago con la sesión de Stripe
                    $pago->update([
                        'referencia' => $session->id,
                        'estado' => 'procesando'
                    ]);

                    DB::commit();
                    Log::info('Pago con Stripe iniciado', [
                        'pedido_id' => $pedido->pedido_id,
                        'session_id' => $session->id
                    ]);

                    // Limpiar el carrito
                    session()->forget('cart');

                    // Redirigir a Stripe
                    return redirect($session->url);

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error al procesar pago con Stripe: ' . $e->getMessage(), [
                        'pedido_id' => $pedido->pedido_id,
                        'error' => $e->getMessage()
                    ]);
                    
                    return redirect()->route('landing')
                        ->with('mensaje', 'Error al procesar el pago. Por favor, intenta nuevamente.')
                        ->with('tipo', 'error');
                }
            } else {
                // Pago en efectivo o transferencia
                try {
                    // Crear el pago
                    Pago::create([
                        'pedido_id' => $pedido->pedido_id,
                        'metodo_id' => $request->metodo_pago_id,
                        'monto' => $total,
                        'estado' => 'pendiente',
                        'fecha_pago' => now(),
                        'referencia' => 'EFECTIVO-' . time()
                    ]);

                    // Confirmar las reservas de stock (convertir en ventas reales)
                    foreach ($cart as $item) {
                        $varianteId = $item['variante_id'] ?? null;
                        $cantidad = $item['quantity'];

                        if ($varianteId) {
                            // Confirmar venta de variante
                            $variante = VarianteProducto::find($varianteId);
                            if ($variante) {
                                $variante->confirmarReserva($cantidad, 'Venta confirmada - Pedido #' . $pedido->pedido_id, Auth::id(), $pedido->pedido_id);
                            }
                        } else {
                            // Confirmar venta de producto sin variante
                            $producto = Producto::find($item['id']);
                            if ($producto) {
                                // Liberar reserva y registrar venta real
                                $producto->liberarStockReservado($cantidad, 'Liberar reserva - Pedido #' . $pedido->pedido_id, Auth::id(), $pedido->pedido_id);
                                $producto->registrarSalida($cantidad, 'Venta confirmada - Pedido #' . $pedido->pedido_id, Auth::id(), $pedido->pedido_id);
                            }
                        }
                    }

                    DB::commit();
                    Log::info('Pedido procesado exitosamente', [
                        'pedido_id' => $pedido->pedido_id,
                        'total' => $total
                    ]);

                    // Limpiar el carrito
                    session()->forget('cart');

                    // Redirigir a la página de éxito
                    return redirect()->route('checkout.success', $pedido->pedido_id)
                        ->with('mensaje', '¡Pedido realizado con éxito!')
                        ->with('tipo', 'success');

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error al procesar pedido: ' . $e->getMessage(), [
                        'pedido_id' => $pedido->pedido_id,
                        'error' => $e->getMessage()
                    ]);
                    
                    return redirect()->route('landing')
                        ->with('mensaje', 'Error al procesar el pedido. Por favor, intenta nuevamente.')
                        ->with('tipo', 'error');
                }
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en checkout', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error en checkout.process: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id()
            ]);
            return redirect()->route('landing')
                ->with('mensaje', 'Hubo un error al procesar tu pedido. Por favor, intenta nuevamente.')
                ->with('tipo', 'error');
        }
    }

    public function success($pedido_id)
    {
        try {
            Log::info('Iniciando checkout.success', ['pedido_id' => $pedido_id]);

            // Buscar el pedido y verificar que pertenezca al usuario autenticado
            $pedido = Pedido::with([
                'detalles.producto',
                'direccion',
                'pago.metodoPago',
                'estado'
            ])->where('usuario_id', Auth::id())
                ->where('pedido_id', $pedido_id)
                ->firstOrFail();

            Log::info('Pedido encontrado y verificado', [
                'pedido_id' => $pedido->pedido_id,
                'usuario_id' => $pedido->pedido_id
            ]);

            return view('checkout.success', compact('pedido'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de acceso a pedido no existente o no autorizado', [
                'pedido_id' => $pedido_id,
                'user_id' => Auth::id()
            ]);
            return redirect()->route('landing')
                ->with('mensaje', 'El pedido solicitado no existe o no tienes permiso para verlo')
                ->with('tipo', 'error');
        } catch (\Exception $e) {
            Log::error('Error en checkout.success: ' . $e->getMessage(), [
                'exception' => $e,
                'pedido_id' => $pedido_id,
                'user_id' => Auth::id()
            ]);
            return redirect()->route('landing')
                ->with('mensaje', 'Hubo un error al mostrar el detalle de tu pedido')
                ->with('tipo', 'error');
        }
    }

    /**
     * Manejar la cancelación del checkout
     */
    public function cancel($pedido_id)
    {
        try {
            Log::info('Iniciando checkout.cancel', ['pedido_id' => $pedido_id]);

            // Buscar el pedido y verificar que pertenezca al usuario autenticado
            $pedido = Pedido::where('usuario_id', Auth::id())
                ->where('pedido_id', $pedido_id)
                ->firstOrFail();

            // Si hay un pago pendiente, cancelarlo
            if ($pedido->pago && $pedido->pago->estado === 'pendiente') {
                $pedido->pago->update(['estado' => 'cancelado']);
            }

            // Cancelar las reservas de stock
            $this->reservaStockService->cancelarReservasPedido($pedido->pedido_id, Auth::id(), 'Cancelación de checkout');

            // Cambiar el estado del pedido a cancelado
            $pedido->update(['estado_id' => 5]); // Asumiendo que 5 es el estado "cancelado"

            Log::info('Checkout cancelado exitosamente', [
                'pedido_id' => $pedido->pedido_id,
                'usuario_id' => Auth::id()
            ]);

            return view('checkout.cancel', compact('pedido'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Intento de cancelar pedido no existente o no autorizado', [
                'pedido_id' => $pedido_id,
                'user_id' => Auth::id()
            ]);
            return redirect()->route('landing')
                ->with('mensaje', 'El pedido solicitado no existe o no tienes permiso para cancelarlo')
                ->with('tipo', 'error');
        } catch (\Exception $e) {
            Log::error('Error en checkout.cancel: ' . $e->getMessage(), [
                'exception' => $e,
                'pedido_id' => $pedido_id,
                'user_id' => Auth::id()
            ]);
            return redirect()->route('landing')
                ->with('mensaje', 'Hubo un error al cancelar el pedido. Por favor, contacta al soporte.')
                ->with('tipo', 'error');
        }
    }

    /**
     * Verificar stock disponible para productos en el carrito
     */
    public function verificarStock(Request $request)
    {
        try {
            $cart = $request->input('cart', []);
            $verificacion = $this->reservaStockService->verificarStockCarrito($cart);

            return response()->json([
                'success' => $verificacion['disponible'],
                'errores' => $verificacion['errores'],
                'productos_info' => $verificacion['productos_info']
            ]);
        } catch (\Exception $e) {
            Log::error('Error al verificar stock: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'errores' => ['Error al verificar stock'],
                'productos_info' => []
            ]);
        }
    }
}
