<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Pago; // Added this import
use App\Mail\StripePagoExitoso;
use App\Mail\StripePagoFallido;
use App\Mail\PedidoCancelado;
use Illuminate\Support\Facades\Mail;
use App\Models\WebhookEvent; // Added this import

class StripeController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Mostrar el formulario de pago con Stripe
     */
    public function showPaymentForm($pedidoId)
    {
        // Buscar el pedido manualmente para debuggear
        $pedido = Pedido::find($pedidoId);
        
        if (!$pedido) {
            Log::error('Pedido no encontrado', ['pedido_id' => $pedidoId]);
            return redirect()->route('checkout.index')
                ->with('error', 'Pedido no encontrado.');
        }

        Log::info('Accediendo a showPaymentForm', [
            'pedido_id' => $pedido->pedido_id,
            'usuario_id' => $pedido->usuario_id,
            'auth_user_id' => Auth::id(),
            'estado_id' => $pedido->estado_id
        ]);

        // Verificar que el pedido pertenece al usuario autenticado
        if ($pedido->usuario_id !== Auth::id()) {
            Log::warning('Usuario no autorizado para acceder al pedido', [
                'pedido_id' => $pedido->pedido_id,
                'usuario_id' => $pedido->usuario_id,
                'auth_user_id' => Auth::id()
            ]);
            return redirect()->route('checkout.index')
                ->with('error', 'No tienes permiso para acceder a este pedido.');
        }

        // Verificar que el pedido no esté ya pagado
        if ($pedido->estado_id == 2) {
            Log::info('Pedido ya pagado', ['pedido_id' => $pedido->pedido_id]);
            return redirect()->route('checkout.success', $pedido)
                ->with('info', 'Este pedido ya ha sido pagado.');
        }

        // Cargar las relaciones necesarias
        $pedido->load(['detalles.producto.imagenes', 'usuario']);

        Log::info('Mostrando formulario de pago Stripe', ['pedido_id' => $pedido->pedido_id]);

        return view('checkout.stripe-payment', compact('pedido'));
    }

    /**
     * Crear un intent de pago para un pedido
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $request->validate([
                'pedido_id' => 'required|exists:pedidos,pedido_id',
                'amount' => 'required|numeric|min:100', // Mínimo $1.00
            ]);

            $pedido = Pedido::findOrFail($request->pedido_id);
            
            // Verificar que el pedido pertenece al usuario autenticado
            if ($pedido->usuario_id !== Auth::id()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            // Crear el PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount, // En centavos
                'currency' => 'cop', // Pesos colombianos
                'metadata' => [
                    'pedido_id' => $pedido->pedido_id,
                    'usuario_id' => Auth::id(),
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear PaymentIntent: ' . $e->getMessage());
            return response()->json(['error' => 'Error al procesar el pago'], 500);
        }
    }

    /**
     * Confirmar un pago exitoso
     */
    public function confirmPayment(Request $request)
    {
        try {
            $request->validate([
                'payment_intent_id' => 'required|string',
                'pedido_id' => 'required|exists:pedidos,pedido_id',
            ]);

            $pedido = Pedido::findOrFail($request->pedido_id);
            
            // Verificar que el pedido pertenece al usuario autenticado
            if ($pedido->usuario_id !== Auth::id()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }

            // Obtener el PaymentIntent de Stripe
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                // NO crear el registro de pago aquí
                // Generar webhook local automáticamente
                
                Log::info("Pago confirmado localmente para pedido {$pedido->pedido_id}, generando webhook local");

                // Generar webhook local automáticamente
                $this->generateLocalWebhook($pedido, $paymentIntent);

                return response()->json([
                    'success' => true,
                    'message' => 'Pago procesado exitosamente. Webhook generado localmente.',
                    'pedido_id' => $pedido->pedido_id,
                    'webhook_generated' => true,
                ]);
            } else {
                return response()->json(['error' => 'El pago no fue exitoso'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Error al confirmar pago: ' . $e->getMessage());
            return response()->json(['error' => 'Error al confirmar el pago'], 500);
        }
    }

    /**
     * Webhook para recibir eventos de Stripe
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook.secret');

        try {
            // Si estamos en modo local y no hay secret configurado, saltar verificación
            if (app()->environment('local') && !$endpointSecret) {
                $event = json_decode($payload);
                if (!$event) {
                    return response()->json(['error' => 'Invalid JSON payload'], 400);
                }
                Log::info('Procesando webhook sin verificación de firma (modo local)');
            } else {
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $sigHeader, $endpointSecret
                );
            }
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid signature: ' . $e->getMessage());
            
            // En modo local, intentar procesar sin verificación
            if (app()->environment('local')) {
                try {
                    $event = json_decode($payload);
                    if (!$event) {
                        return response()->json(['error' => 'Invalid JSON payload'], 400);
                    }
                    Log::info('Procesando webhook sin verificación de firma (modo local)');
                } catch (\Exception $jsonError) {
                    return response()->json(['error' => 'Invalid signature and JSON'], 400);
                }
            } else {
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        // Guardar el evento en la base de datos
        try {
            $webhookEvent = WebhookEvent::create([
                'stripe_event_id' => $event->id ?? 'local_' . uniqid(),
                'event_type' => $event->type,
                'pedido_id' => $this->extractPedidoId($event),
                'payload' => is_array($event) ? $event : (array) $event,
                'status' => 'pending',
                'attempts' => 1,
                'last_attempt_at' => now(),
            ]);
            
            Log::info("Webhook event saved: {$event->type} - ID: {$webhookEvent->id}");
        } catch (\Exception $e) {
            Log::error("Error saving webhook event: " . $e->getMessage());
        }

        // Manejar el evento
        try {
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;
                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;
                case 'payment_intent.canceled':
                    $this->handlePaymentCanceled($event->data->object);
                    break;
                case 'charge.succeeded':
                    $this->handleChargeSucceeded($event->data->object);
                    break;
                case 'charge.failed':
                    $this->handleChargeFailed($event->data->object);
                    break;
                default:
                    Log::info('Evento no manejado: ' . $event->type);
            }

            // Marcar el evento como procesado
            if (isset($webhookEvent)) {
                $webhookEvent->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Error processing webhook event: " . $e->getMessage());
            
            // Marcar el evento como fallido
            if (isset($webhookEvent)) {
                $webhookEvent->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
            
            return response()->json(['error' => 'Processing failed'], 500);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Extraer el ID del pedido del evento
     */
    private function extractPedidoId($event)
    {
        $pedidoId = null;
        
        switch ($event->type) {
            case 'payment_intent.succeeded':
            case 'payment_intent.payment_failed':
            case 'payment_intent.canceled':
                $pedidoId = $event->data->object->metadata->pedido_id ?? null;
                break;
            case 'charge.succeeded':
            case 'charge.failed':
                $pedidoId = $event->data->object->metadata->pedido_id ?? null;
                break;
        }
        
        return $pedidoId;
    }

    /**
     * Manejar pago exitoso
     */
    private function handlePaymentSucceeded($paymentIntent)
    {
        $pedidoId = $paymentIntent->metadata->pedido_id ?? null;
        
        if ($pedidoId) {
            $pedido = Pedido::find($pedidoId);
            if ($pedido) {
                // NO cambiar el estado del pedido, mantenerlo como "Pendiente"
                // Solo actualizar el estado del pago
                
                // Crear o actualizar registro de pago
                Pago::updateOrCreate(
                    ['pedido_id' => $pedidoId],
                    [
                        'metodo_id' => 1, // Stripe
                        'monto' => $paymentIntent->amount / 100,
                        'fecha_pago' => now(),
                        'estado' => 'completado',
                        'referencia' => $paymentIntent->id,
                    ]
                );
                
                // Enviar email de confirmación usando Mailable
                try {
                    Mail::to($pedido->usuario->correo_electronico)
                        ->send(new StripePagoExitoso($pedido->usuario, $pedido, $paymentIntent));
                    Log::info("Email de confirmación enviado para pedido {$pedidoId}");
                } catch (\Exception $e) {
                    Log::error("Error enviando email de confirmación: " . $e->getMessage());
                }
                
                Log::info("Pago completado para pedido {$pedidoId} via webhook (estado del pedido mantenido como pendiente)");
            }
        }
    }

    /**
     * Manejar pago fallido
     */
    private function handlePaymentFailed($paymentIntent)
    {
        $pedidoId = $paymentIntent->metadata->pedido_id ?? null;
        
        if ($pedidoId) {
            $pedido = Pedido::find($pedidoId);
            if ($pedido) {
                // NO cambiar el estado del pedido, mantenerlo como "Pendiente"
                // Solo actualizar el estado del pago
                
                // Actualizar registro de pago si existe
                $pago = Pago::where('pedido_id', $pedidoId)->first();
                if ($pago) {
                    $pago->estado = 'fallido';
                    $pago->save();
                }
                
                // Enviar email de pago fallido usando Mailable
                try {
                    Mail::to($pedido->usuario->correo_electronico)
                        ->send(new StripePagoFallido($pedido->usuario, $pedido, $paymentIntent));
                    Log::info("Email de pago fallido enviado para pedido {$pedidoId}");
                } catch (\Exception $e) {
                    Log::error("Error enviando email de pago fallido: " . $e->getMessage());
                }
                
                Log::info("Pago fallido para pedido {$pedidoId} via webhook (estado del pedido mantenido como pendiente)");
            }
        }
    }

    /**
     * Manejar pago cancelado
     */
    private function handlePaymentCanceled($paymentIntent)
    {
        $pedidoId = $paymentIntent->metadata->pedido_id ?? null;
        
        if ($pedidoId) {
            $pedido = Pedido::find($pedidoId);
            if ($pedido) {
                // NO cambiar el estado del pedido, mantenerlo como "Pendiente"
                // Solo actualizar el estado del pago
                
                // Actualizar registro de pago si existe
                $pago = Pago::where('pedido_id', $pedidoId)->first();
                if ($pago) {
                    $pago->estado = 'cancelado';
                    $pago->save();
                }
                
                // Enviar email de pedido cancelado usando Mailable
                try {
                    $motivo = 'Pago cancelado por el usuario o por Stripe';
                    Mail::to($pedido->usuario->correo_electronico)
                        ->send(new PedidoCancelado($pedido->usuario, $pedido, $motivo));
                    Log::info("Email de pedido cancelado enviado para pedido {$pedidoId}");
                } catch (\Exception $e) {
                    Log::error("Error enviando email de pedido cancelado: " . $e->getMessage());
                }
                
                Log::info("Pago cancelado para pedido {$pedidoId} via webhook (estado del pedido mantenido como pendiente)");
            }
        }
    }

    /**
     * Manejar cargo exitoso
     */
    private function handleChargeSucceeded($charge)
    {
        $paymentIntentId = $charge->payment_intent;
        if ($paymentIntentId) {
            try {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
                $pedidoId = $paymentIntent->metadata->pedido_id ?? null;
                
                if ($pedidoId) {
                    Log::info("Cargo exitoso para pedido {$pedidoId}");
                }
            } catch (\Exception $e) {
                Log::error('Error al procesar charge.succeeded: ' . $e->getMessage());
            }
        }
    }

    /**
     * Manejar cargo fallido
     */
    private function handleChargeFailed($charge)
    {
        $paymentIntentId = $charge->payment_intent;
        if ($paymentIntentId) {
            try {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
                $pedidoId = $paymentIntent->metadata->pedido_id ?? null;
                
                if ($pedidoId) {
                    $pedido = Pedido::find($pedidoId);
                    if ($pedido) {
                        // NO cambiar el estado del pedido, mantenerlo como "Pendiente"
                        // Solo registrar el evento del cargo fallido
                        
                        Log::info("Cargo fallido para pedido {$pedidoId} via webhook (estado del pedido mantenido como pendiente)");
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error al procesar charge.failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Generar webhook local automáticamente
     */
    private function generateLocalWebhook($pedido, $paymentIntent)
    {
        try {
            // Crear el evento del webhook directamente
            $webhookEvent = WebhookEvent::create([
                'stripe_event_id' => 'local_' . uniqid(),
                'event_type' => 'payment_intent.succeeded',
                'pedido_id' => $pedido->pedido_id,
                'payload' => [
                    'type' => 'payment_intent.succeeded',
                    'data' => [
                        'object' => [
                            'id' => $paymentIntent->id,
                            'status' => 'succeeded',
                            'amount' => $paymentIntent->amount,
                            'currency' => $paymentIntent->currency,
                            'metadata' => [
                                'pedido_id' => $pedido->pedido_id,
                                'usuario_id' => $pedido->usuario_id,
                            ]
                        ]
                    ]
                ],
                'status' => 'pending',
                'attempts' => 1,
                'last_attempt_at' => now(),
            ]);

            Log::info("Webhook local generado para pedido {$pedido->pedido_id}: {$webhookEvent->id}");

            // Procesar el webhook inmediatamente
            $this->handlePaymentSucceeded($paymentIntent);

            // Marcar el evento como procesado
            $webhookEvent->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            Log::info("Webhook local procesado exitosamente para pedido {$pedido->pedido_id}");

        } catch (\Exception $e) {
            Log::error("Error generando webhook local: " . $e->getMessage());
        }
    }
}
