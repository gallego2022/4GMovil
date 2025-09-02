<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Pago;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Crear Payment Intent
     */
    public function createPaymentIntent(Request $request): array
    {
        try {
            $request->validate([
                'pedido_id' => 'required|exists:pedidos,pedido_id',
                'amount' => 'required|integer|min:100'
            ]);

            $pedido = Pedido::with(['usuario', 'detalles.producto'])->findOrFail($request->pedido_id);

            // Verificar que el pedido pertenece al usuario autenticado
            if ($pedido->usuario_id !== auth()->id()) {
                return [
                    'success' => false,
                    'message' => 'No tienes permisos para procesar este pedido'
                ];
            }

            // Verificar que el pedido esté pendiente
            if ($pedido->estado_id !== 1) {
                return [
                    'success' => false,
                    'message' => 'El pedido no está en estado pendiente'
                ];
            }

            // Crear Payment Intent en Stripe
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount,
                'currency' => 'cop',
                'metadata' => [
                    'pedido_id' => $pedido->pedido_id,
                    'usuario_id' => $pedido->usuario_id
                ]
            ]);

            // Registrar el Payment Intent en la base de datos
            $pago = Pago::create([
                'pedido_id' => $pedido->pedido_id,
                'metodo_pago_id' => 1, // Stripe
                'monto' => $request->amount / 100, // Convertir de centavos a pesos
                'estado' => 'pending',
                'stripe_payment_intent_id' => $paymentIntent->id,
                'fecha_pago' => now()
            ]);

            Log::info('Payment Intent creado exitosamente', [
                'pedido_id' => $pedido->pedido_id,
                'payment_intent_id' => $paymentIntent->id,
                'monto' => $request->amount
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'pago_id' => $pago->pago_id
            ];

        } catch (ApiErrorException $e) {
            Log::error('Error de Stripe al crear Payment Intent: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Error al crear Payment Intent: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }

    /**
     * Confirmar pago
     */
    public function confirmPayment(Request $request): array
    {
        try {
            $request->validate([
                'payment_intent_id' => 'required|string',
                'pedido_id' => 'required|exists:pedidos,pedido_id'
            ]);

            $pedido = Pedido::with(['usuario', 'detalles.producto'])->findOrFail($request->pedido_id);
            $pago = Pago::where('stripe_payment_intent_id', $request->payment_intent_id)->first();

            if (!$pago) {
                return [
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ];
            }

            // Verificar que el pedido pertenece al usuario autenticado
            if ($pedido->usuario_id !== auth()->id()) {
                return [
                    'success' => false,
                    'message' => 'No tienes permisos para procesar este pedido'
                ];
            }

            // Obtener Payment Intent de Stripe
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                // Pago exitoso
                $this->handleSuccessfulPayment($pedido, $pago, $paymentIntent);
                
                return [
                    'success' => true,
                    'message' => 'Pago procesado exitosamente',
                    'pedido_id' => $pedido->pedido_id
                ];
            } elseif ($paymentIntent->status === 'requires_payment_method') {
                // Pago requiere método de pago
                return [
                    'success' => false,
                    'message' => 'El pago requiere un método de pago válido'
                ];
            } else {
                // Otro estado
                return [
                    'success' => false,
                    'message' => 'Estado de pago no válido: ' . $paymentIntent->status
                ];
            }

        } catch (ApiErrorException $e) {
            Log::error('Error de Stripe al confirmar pago: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al confirmar el pago: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Error al confirmar pago: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }

    /**
     * Procesar webhook de Stripe
     */
    public function processWebhook(Request $request): array
    {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            $endpointSecret = config('services.stripe.webhook_secret');

            $event = null;

            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } catch (\Exception $e) {
                Log::error('Error al verificar webhook de Stripe: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Error al verificar webhook'
                ];
            }

            // Registrar evento en la base de datos
            $webhookEvent = WebhookEvent::create([
                'event_type' => $event->type,
                'stripe_event_id' => $event->id,
                'data' => json_encode($event->data),
                'status' => 'pending'
            ]);

            // Procesar evento según su tipo
            $result = $this->handleWebhookEvent($event, $webhookEvent);

            if ($result['success']) {
                $webhookEvent->update(['status' => 'processed']);
            } else {
                $webhookEvent->update(['status' => 'failed']);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Error al procesar webhook: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }

    /**
     * Manejar eventos de webhook
     */
    private function handleWebhookEvent($event, WebhookEvent $webhookEvent): array
    {
        try {
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentSucceeded($event->data->object);

                case 'payment_intent.payment_failed':
                    return $this->handlePaymentFailed($event->data->object);

                case 'payment_intent.canceled':
                    return $this->handlePaymentCanceled($event->data->object);

                case 'charge.succeeded':
                    return $this->handleChargeSucceeded($event->data->object);

                case 'charge.failed':
                    return $this->handleChargeFailed($event->data->object);

                default:
                    Log::info('Evento de webhook no manejado: ' . $event->type);
                    return [
                        'success' => true,
                        'message' => 'Evento procesado (no requiere acción)'
                    ];
            }
        } catch (\Exception $e) {
            Log::error('Error al manejar evento de webhook: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar evento'
            ];
        }
    }

    /**
     * Manejar pago exitoso
     */
    private function handleSuccessfulPayment(Pedido $pedido, Pago $pago, $paymentIntent): void
    {
        DB::transaction(function () use ($pedido, $pago, $paymentIntent) {
            // Actualizar estado del pedido
            $pedido->update(['estado_id' => 2]); // Confirmado

            // Actualizar estado del pago
            $pago->update([
                'estado' => 'completed',
                'fecha_pago' => now()
            ]);

            // Generar webhook local
            $this->generateLocalWebhook($pedido, $paymentIntent);

            Log::info('Pago procesado exitosamente', [
                'pedido_id' => $pedido->pedido_id,
                'pago_id' => $pago->pago_id,
                'payment_intent_id' => $paymentIntent->id
            ]);
        });
    }

    /**
     * Extraer ID del pedido del evento
     */
    private function extractPedidoId($event): ?int
    {
        if (isset($event->metadata->pedido_id)) {
            return (int) $event->metadata->pedido_id;
        }

        if (isset($event->description)) {
            // Intentar extraer de la descripción
            if (preg_match('/Pedido #(\d+)/', $event->description, $matches)) {
                return (int) $matches[1];
            }
        }

        return null;
    }

    /**
     * Manejar pago exitoso desde webhook
     */
    private function handlePaymentSucceeded($paymentIntent): array
    {
        try {
            $pedidoId = $this->extractPedidoId($paymentIntent);
            
            if (!$pedidoId) {
                Log::warning('No se pudo extraer pedido_id del Payment Intent', [
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return [
                    'success' => false,
                    'message' => 'No se pudo identificar el pedido'
                ];
            }

            $pedido = Pedido::find($pedidoId);
            if (!$pedido) {
                Log::warning('Pedido no encontrado en webhook', [
                    'pedido_id' => $pedidoId,
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return [
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ];
            }

            $pago = Pago::where('stripe_payment_intent_id', $paymentIntent->id)->first();
            if (!$pago) {
                Log::warning('Pago no encontrado en webhook', [
                    'pedido_id' => $pedidoId,
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return [
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ];
            }

            $this->handleSuccessfulPayment($pedido, $pago, $paymentIntent);

            return [
                'success' => true,
                'message' => 'Pago procesado exitosamente desde webhook'
            ];

        } catch (\Exception $e) {
            Log::error('Error al manejar pago exitoso: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar pago exitoso'
            ];
        }
    }

    /**
     * Manejar pago fallido
     */
    private function handlePaymentFailed($paymentIntent): array
    {
        try {
            $pedidoId = $this->extractPedidoId($paymentIntent);
            
            if ($pedidoId) {
                $pedido = Pedido::find($pedidoId);
                if ($pedido) {
                    $pedido->update(['estado_id' => 3]); // Cancelado
                    
                    $pago = Pago::where('stripe_payment_intent_id', $paymentIntent->id)->first();
                    if ($pago) {
                        $pago->update(['estado' => 'failed']);
                    }
                }
            }

            Log::info('Pago fallido procesado', [
                'payment_intent_id' => $paymentIntent->id,
                'pedido_id' => $pedidoId
            ]);

            return [
                'success' => true,
                'message' => 'Pago fallido procesado'
            ];

        } catch (\Exception $e) {
            Log::error('Error al manejar pago fallido: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar pago fallido'
            ];
        }
    }

    /**
     * Manejar pago cancelado
     */
    private function handlePaymentCanceled($paymentIntent): array
    {
        try {
            $pedidoId = $this->extractPedidoId($paymentIntent);
            
            if ($pedidoId) {
                $pedido = Pedido::find($pedidoId);
                if ($pedido) {
                    $pedido->update(['estado_id' => 3]); // Cancelado
                    
                    $pago = Pago::where('stripe_payment_intent_id', $paymentIntent->id)->first();
                    if ($pago) {
                        $pago->update(['estado' => 'canceled']);
                    }
                }
            }

            Log::info('Pago cancelado procesado', [
                'payment_intent_id' => $paymentIntent->id,
                'pedido_id' => $pedidoId
            ]);

            return [
                'success' => true,
                'message' => 'Pago cancelado procesado'
            ];

        } catch (\Exception $e) {
            Log::error('Error al manejar pago cancelado: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar pago cancelado'
            ];
        }
    }

    /**
     * Manejar cargo exitoso
     */
    private function handleChargeSucceeded($charge): array
    {
        try {
            Log::info('Cargo exitoso procesado', [
                'charge_id' => $charge->id,
                'amount' => $charge->amount
            ]);

            return [
                'success' => true,
                'message' => 'Cargo exitoso procesado'
            ];

        } catch (\Exception $e) {
            Log::error('Error al manejar cargo exitoso: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar cargo exitoso'
            ];
        }
    }

    /**
     * Manejar cargo fallido
     */
    private function handleChargeFailed($charge): array
    {
        try {
            Log::info('Cargo fallido procesado', [
                'charge_id' => $charge->id,
                'failure_message' => $charge->failure_message ?? 'Sin mensaje de error'
            ]);

            return [
                'success' => true,
                'message' => 'Cargo fallido procesado'
            ];

        } catch (\Exception $e) {
            Log::error('Error al manejar cargo fallido: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al procesar cargo fallido'
            ];
        }
    }

    /**
     * Generar webhook local
     */
    private function generateLocalWebhook(Pedido $pedido, $paymentIntent): void
    {
        try {
            WebhookEvent::create([
                'event_type' => 'payment_intent.succeeded',
                'stripe_event_id' => 'local_' . time(),
                'pedido_id' => $pedido->pedido_id,
                'data' => json_encode([
                    'pedido_id' => $pedido->pedido_id,
                    'payment_intent_id' => $paymentIntent->id,
                    'monto' => $paymentIntent->amount / 100
                ]),
                'status' => 'processed'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar webhook local: ' . $e->getMessage());
        }
    }
}
