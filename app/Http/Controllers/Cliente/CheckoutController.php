<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Base\WebController;
use App\Models\Pedido;
use App\Services\Orders\CheckoutService;
use App\Services\Orders\PedidoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;

class CheckoutController extends WebController
{
    protected $checkoutService;
    protected $pedidoService;

    public function __construct(CheckoutService $checkoutService, PedidoService $pedidoService)
    {
        $this->middleware('auth');
        $this->checkoutService = $checkoutService;
        $this->pedidoService = $pedidoService;
    }

    /**
     * Muestra la página de checkout
     */
    public function index(Request $request)
    {
        try {
            $result = $this->checkoutService->prepareCheckout($request);

            return View::make('checkout.index', [
                'cart' => $result['cart'],
                'direcciones' => $result['direcciones'],
                'metodosPago' => $result['metodosPago'],
            ]);

        } catch (Exception $e) {
            // Si el error es "carrito vacío", redirigir al landing con mensaje específico
            if (str_contains($e->getMessage(), 'carrito está vacío')) {
                return $this->redirectError('landing', 'Tu carrito está vacío. Agrega productos antes de proceder al checkout.');
            }

            return $this->handleException($e, 'landing');
        }
    }
    /**
     * Procesa el checkout y redirige a Stripe si es necesario
     */
    public function process(Request $request)
    {
        try {
            $result = $this->checkoutService->processCheckout($request);

            if (isset($result['redirect_to_stripe']) && $result['redirect_to_stripe']) {
                return Redirect::route('stripe.payment', $result['pedido_id'])
                    ->with('info', 'Pedido creado. Completa el pago con Stripe.');
            }
            return $this->redirectError('checkout.index', 'Solo se acepta pago con Stripe');

        } catch (ValidationException $e) {
            return $this->handleValidationException($e, 'checkout.index');
        } catch (Exception $e) {
            return $this->handleException($e, 'checkout.index');
        }
    }



    /**
     * Página de éxito del checkout
     */
    public function success($pedidoId)
    {
        try {
            // Limpiar el carrito después de confirmar que el usuario llegó a la página de éxito
            Session::forget('checkout_cart');
            Session::forget('cart');

            // Si el usuario está autenticado, limpiar también el carrito de la BD
            if (Auth::check()) {
                $carrito = \App\Models\Carrito::where('usuario_id', Auth::id())->first();
                if ($carrito) {
                    $carrito->items()->delete();
                }
            }

            $pedido = Pedido::with('estado')->findOrFail($pedidoId);

            return View::make('checkout.success', [
                'pedido' => $pedido,
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'landing');
        }
    }

    public function cancel(Pedido $pedido)
    {
        try {
            Session::forget('checkout_cart');
            $this->pedidoService->cancelOrder($pedido->pedido_id, request());
            $pedido->load('usuario');
            Mail::to($pedido->usuario->correo_electronico)
                ->send(new \App\Mail\StripePagoFallido($pedido->usuario, $pedido, request()->get('payment_intent_id')));
            Mail::to($pedido->usuario->correo_electronico)
                ->send(new \App\Mail\PedidoCancelado($pedido->usuario, $pedido, 'Cancelación iniciada por el usuario en checkout'));
            return Redirect::route('checkout.index')
                ->with('cancellation_success', 'Pedido cancelado. Puedes volver a intentar el pago cuando quieras.');

        } catch (Exception $e) {
            return $this->handleException($e, 'landing');
        }
    }

    /**
     * Verifica el stock disponible para el carrito
     */
    public function verificarStock(Request $request)
    {
        try {
            $cartInput = $request->input('cart');

            // Manejar tanto strings JSON como arrays directamente
            if (is_string($cartInput)) {
                $cart = json_decode($cartInput, true) ?? [];
            } elseif (is_array($cartInput)) {
                $cart = $cartInput;
            } else {
                $cart = [];
            }

            if (empty($cart)) {
                return Response::json([
                    'success' => false,
                    'message' => 'El carrito está vacío',
                ]);
            }

            $resultado = $this->checkoutService->verificarStock($cart);

            return Response::json([
                'success' => true,
                'data' => $resultado,
            ]);

        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Error al verificar stock: '.$e->getMessage(),
            ], 500);
        }
    }

}
