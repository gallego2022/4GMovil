<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Base\WebController;
use App\Services\Business\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use Exception;

class CheckoutController extends WebController
{
    protected $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->middleware('auth');
        $this->checkoutService = $checkoutService;
    }

    /**
     * Muestra la página de checkout
     */
    public function index(Request $request)
    {
        try {
            $result = $this->checkoutService->prepareCheckout($request);
            
            return view('checkout.index', [
                'cart' => $result['cart'],
                'direcciones' => $result['direcciones'],
                'metodosPago' => $result['metodosPago']
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
     * Procesa el checkout
     */
    public function process(Request $request)
    {
        try {
            $result = $this->checkoutService->processCheckout($request);
            
            // NO limpiar el carrito aquí - se limpiará en la página de éxito
            // Session::forget('cart');
            
            return $this->redirectSuccess('checkout.success', 
                'Pedido procesado exitosamente', 
                ['pedido' => $result['pedido_id']]
            );

        } catch (ValidationException $e) {
            return $this->handleValidationException($e, 'checkout.index');
        } catch (Exception $e) {
            return $this->handleException($e, 'checkout.index');
        }
    }

    /**
     * Muestra el resumen del checkout
     */
    public function summary(Request $request)
    {
        try {
            $cart = session('cart', []);
            
            if (empty($cart)) {
                return $this->redirectError('landing', 'Tu carrito está vacío');
            }

            $summary = $this->checkoutService->getCheckoutSummary($cart);
            
            return view('checkout.summary', $summary);

        } catch (Exception $e) {
            return $this->handleException($e, 'landing');
        }
    }

    /**
     * Confirma el pedido
     */
    public function confirm(Request $request)
    {
        try {
            $this->requireAuth();
            
            $result = $this->checkoutService->processCheckout($request);
            
            // NO limpiar el carrito aquí - se limpiará en la página de éxito
            // Session::forget('cart');
            
            return $this->redirectSuccess('checkout.success', 
                'Pedido confirmado exitosamente', 
                ['pedido' => $result['pedido_id']]
            );

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
            Session::forget('cart');
            
            $pedido = \App\Models\Pedido::findOrFail($pedidoId);
            
            return view('checkout.success', [
                'pedido' => $pedido
            ]);
            
        } catch (Exception $e) {
            return $this->handleException($e, 'landing');
        }
    }

    /**
     * Cancela el checkout
     */
    public function cancel()
    {
        try {
            Session::forget('cart');
            
            return $this->redirectInfo('landing', 'Checkout cancelado');
            
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
                return response()->json([
                    'success' => false,
                    'message' => 'El carrito está vacío'
                ]);
            }

            $resultado = $this->checkoutService->verificarStock($cart);
            
            return response()->json([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar stock: ' . $e->getMessage()
            ], 500);
        }
    }
}
