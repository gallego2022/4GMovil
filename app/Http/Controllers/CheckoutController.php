<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Direccion;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
            }

            // Obtener las direcciones del usuario
            $direcciones = Direccion::where('usuario_id', Auth::id())->get();
            Log::info('Direcciones del usuario obtenidas', ['count' => $direcciones->count()]);
            
            // Obtener los métodos de pago disponibles
            $metodosPago = MetodoPago::all();
            Log::info('Métodos de pago obtenidos', ['count' => $metodosPago->count()]);

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

            // Validar productos y calcular el total
            $total = 0;
            $productosValidados = [];
            
            foreach ($cart as $item) {
                $producto = Producto::findOrFail($item['id']);
                $total += $item['price'] * $item['quantity'];
                $productosValidados[] = [
                    'producto' => $producto,
                    'cantidad' => $item['quantity'],
                    'precio' => $item['price']
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

            // Crear los detalles del pedido
            foreach ($productosValidados as $item) {
                DetallePedido::create([
                    'pedido_id' => $pedido->pedido_id,
                    'producto_id' => $item['producto']->producto_id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['precio'] * $item['cantidad']
                ]);
            }

            Log::info('Detalles del pedido creados');

            // Registrar el pago
            Pago::create([
                'pedido_id' => $pedido->pedido_id,
                'metodo_id' => $request->metodo_pago_id,
                'monto' => $total,
                'fecha_pago' => now()
            ]);

            Log::info('Pago registrado');

            DB::commit();
            Log::info('Transacción DB completada');

            // Limpiar el carrito de la sesión
            session()->forget('cart');
            Log::info('Carrito limpiado de la sesión');

            return redirect()->route('checkout.success', ['pedido' => $pedido->pedido_id])
                ->with('mensaje', '¡Tu pedido ha sido procesado exitosamente!')
                ->with('tipo', 'success');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en checkout.process', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('mensaje', 'Por favor, verifica los campos requeridos')
                ->with('tipo', 'error');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Error en checkout.process - Modelo no encontrado: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id()
            ]);
            return redirect()->route('checkout.index')
                ->with('mensaje', 'Uno o más productos ya no están disponibles')
                ->with('tipo', 'error');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en checkout.process: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'request' => $request->all()
            ]);
            return redirect()->route('checkout.index')
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
                'usuario_id' => $pedido->usuario_id
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
}
