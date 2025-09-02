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
            
            return $this->redirectSuccess('pedidos.show', 
                'Pedido procesado exitosamente', 
                ['id' => $result['pedido_id']]
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
            
            return $this->redirectSuccess('pedidos.show', 
                'Pedido confirmado exitosamente', 
                ['id' => $result['pedido_id']]
            );

        } catch (ValidationException $e) {
            return $this->handleValidationException($e, 'checkout.index');
        } catch (Exception $e) {
            return $this->handleException($e, 'checkout.index');
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
}
