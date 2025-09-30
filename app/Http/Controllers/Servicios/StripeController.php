<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Base\WebController;
use App\Services\StripeService;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

class StripeController extends WebController
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Mostrar el formulario de pago con Stripe
     */
    public function showPaymentForm($pedidoId)
    {
        try {
            $pedido = Pedido::with(['detalles.producto.imagenes', 'usuario'])->find($pedidoId);
            
            if (!$pedido) {
                Log::error('Pedido no encontrado', ['pedido_id' => $pedidoId]);
                return $this->redirectError('checkout.index', 'Pedido no encontrado.');
            }

            // Verificar que el pedido pertenece al usuario autenticado
            if ($pedido->usuario_id !== Auth::id()) {
                Log::warning('Usuario no autorizado para acceder al pedido', [
                    'pedido_id' => $pedido->pedido_id,
                    'usuario_id' => $pedido->usuario_id,
                    'auth_user_id' => Auth::id()
                ]);
                return $this->redirectError('checkout.index', 'No tienes permiso para acceder a este pedido.');
            }

            // Verificar que el pedido no esté ya pagado
            if ($pedido->estado_id == 2) {
                Log::info('Pedido ya pagado', ['pedido_id' => $pedido->pedido_id]);
                return Redirect::route('checkout.success', $pedido)
                    ->with('info', 'Este pedido ya ha sido pagado.');
            }

            Log::info('Mostrando formulario de pago Stripe', ['pedido_id' => $pedido->pedido_id]);

            return View::make('checkout.stripe-payment', compact('pedido'));

        } catch (\Exception $e) {
            Log::error('Error al mostrar formulario de pago: ' . $e->getMessage());
            return $this->redirectError('checkout.index', 'Error al cargar el formulario de pago.');
        }
    }

    /**
     * Crear un intent de pago para un pedido
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $result = $this->stripeService->createPaymentIntent($request);

            if ($result['success']) {
                return Response::json([
                    'clientSecret' => $result['client_secret'],
                    'paymentIntentId' => $result['payment_intent_id'],
                ]);
            }

            return Response::json(['error' => $result['message']], 400);

        } catch (\Exception $e) {
            Log::error('Error al crear PaymentIntent: ' . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Confirmar un pago exitoso
     */
    public function confirmPayment(Request $request)
    {
        try {
            $result = $this->stripeService->confirmPayment($request);

            if ($result['success']) {
                return Response::json([
                    'success' => true,
                    'message' => $result['message'],
                    'pedido_id' => $result['pedido_id']
                ]);
            }

            return Response::json(['error' => $result['message']], 400);

        } catch (\Exception $e) {
            Log::error('Error al confirmar pago: ' . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Webhook para recibir eventos de Stripe
     */
    public function webhook(Request $request)
    {
        try {
            $result = $this->stripeService->processWebhook($request);

            if ($result['success']) {
                return Response::json(['status' => 'success']);
            }

            return Response::json(['error' => $result['message']], 400);

        } catch (\Exception $e) {
            Log::error('Error al procesar webhook: ' . $e->getMessage());
            return Response::json(['error' => 'Error interno del servidor'], 500);
        }
    }
}
