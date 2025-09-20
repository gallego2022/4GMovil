<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Pago;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Services\PedidoNotificationService;
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

            $pedido = Pedido::with(['usuario', 'detalles.producto', 'detalles.variante'])->findOrFail($request->pedido_id);

            // Verificar que el pedido pertenece al usuario autenticado
            if ($pedido->usuario_id !== Auth::id()) {
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

            // Buscar el pago existente para este pedido
            $pago = Pago::where('pedido_id', $pedido->pedido_id)
                        ->where('estado', 'pendiente')
                        ->first();

            if (!$pago) {
                return [
                    'success' => false,
                    'message' => 'No se encontró un pago pendiente para este pedido'
                ];
            }

            // Actualizar el pago existente con la referencia de Stripe
            $pago->update([
                'referencia_externa' => $paymentIntent->id
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

            $pedido = Pedido::with(['usuario', 'detalles.producto', 'detalles.variante'])->findOrFail($request->pedido_id);
            $pago = Pago::where('referencia_externa', $request->payment_intent_id)->first();

            if (!$pago) {
                return [
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ];
            }

            // Verificar que el pedido pertenece al usuario autenticado
            if ($pedido->usuario_id !== Auth::id()) {
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
                'type' => $event->type,
                'stripe_id' => $event->id,
                'data' => $event->data,
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
            $estadoAnterior = $pedido->estado_id;
            
            // Actualizar estado del pedido
            $pedido->update(['estado_id' => 2]); // Confirmado
            
            // Recargar el pedido para asegurar que tenemos los datos actualizados
            $pedido->refresh();

            // Actualizar estado del pago
            $pago->update([
                'estado' => 'completado', // Usar 'completado' en lugar de 'completed'
                'fecha_pago' => now()
            ]);
            
            Log::info('Estado del pedido actualizado en StripeService', [
                'pedido_id' => $pedido->pedido_id,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $pedido->estado_id,
                'estado_nombre' => $pedido->estado->nombre ?? 'No cargado'
            ]);

            // Manejar stock reservado (importante para variantes)
            Log::info('Llamando a manejarStockReservado desde StripeService', [
                'pedido_id' => $pedido->pedido_id,
                'estado_anterior' => $estadoAnterior,
                'nuevo_estado' => 2
            ]);
            
            try {
                $this->manejarStockReservado($pedido, $estadoAnterior, 2);
                Log::info('manejarStockReservado ejecutado exitosamente');
            } catch (\Exception $e) {
                Log::error('Error en manejarStockReservado', [
                    'pedido_id' => $pedido->pedido_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Intentar registrar movimientos directamente como fallback
                $this->registrarMovimientosFallback($pedido);
            }

            // Generar webhook local
            $this->generateLocalWebhook($pedido, $paymentIntent);

            // Enviar correo de confirmación del pedido
            $this->enviarCorreoConfirmacion($pedido);
            
            // Enviar correo específico de pago exitoso con Stripe
            $this->enviarCorreoPagoExitoso($pedido, $paymentIntent);

            Log::info('Pago procesado exitosamente', [
                'pedido_id' => $pedido->pedido_id,
                'pago_id' => $pago->pago_id,
                'payment_intent_id' => $paymentIntent->id
            ]);
        });
    }

    /**
     * Enviar correo de confirmación del pedido
     */
    private function enviarCorreoConfirmacion(Pedido $pedido): void
    {
        try {
            $notificationService = new PedidoNotificationService();
            $notificationService->enviarCorreoConfirmacion($pedido);
        } catch (\Exception $e) {
            Log::error('Error enviando correo de confirmación', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
            // No lanzar excepción para no afectar el flujo del pago
        }
    }

    /**
     * Manejar stock reservado según el cambio de estado del pedido
     */
    private function manejarStockReservado(Pedido $pedido, int $estadoAnterior, int $nuevoEstado): void
    {
        // Estados que confirman la venta (liberan stock reservado y registran salida)
        $estadosConfirmados = [2]; // Confirmado
        
        // Estados que cancelan la venta (liberan stock reservado sin registrar salida)
        $estadosCancelados = [3]; // Cancelado
        
        // Estados pendientes (mantienen stock reservado)
        $estadosPendientes = [1]; // Pendiente

        Log::info('Manejando stock reservado desde StripeService', [
            'pedido_id' => $pedido->pedido_id,
            'estado_anterior' => $estadoAnterior,
            'nuevo_estado' => $nuevoEstado
        ]);

        try {
            Log::info('Iniciando manejo de stock reservado', [
                'pedido_id' => $pedido->pedido_id,
                'total_detalles' => $pedido->detalles->count(),
                'detalles' => $pedido->detalles->map(function($detalle) {
                    return [
                        'detalle_id' => $detalle->detalle_id ?? 'N/A',
                        'producto_id' => $detalle->producto_id,
                        'variante_id' => $detalle->variante_id,
                        'cantidad' => $detalle->cantidad
                    ];
                })
            ]);

        foreach ($pedido->detalles as $detalle) {
            $producto = $detalle->producto;
            
            Log::info('Procesando detalle del pedido', [
                'detalle_id' => $detalle->detalle_id ?? 'N/A',
                'producto_id' => $detalle->producto_id,
                'variante_id' => $detalle->variante_id,
                'cantidad' => $detalle->cantidad,
                'producto_cargado' => $producto ? 'Sí' : 'No'
            ]);
            
            // Si el pedido se confirma (pasa de pendiente a confirmado/enviado/entregado)
            if (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosConfirmados)) {
                Log::info('Pedido se está confirmando - procesando stock', [
                    'pedido_id' => $pedido->pedido_id,
                    'detalle_id' => $detalle->detalle_id ?? 'N/A',
                    'producto_id' => $detalle->producto_id,
                    'variante_id' => $detalle->variante_id,
                    'cantidad' => $detalle->cantidad
                ]);
                
                // Verificar si el detalle tiene una variante específica
                if ($detalle->variante_id) {
                    // Registrar salida de stock para la variante (Stripe confirma automáticamente)
                    $variante = $detalle->variante;
                    if ($variante) {
                        Log::info('Registrando salida de stock para variante', [
                            'variante_id' => $variante->variante_id,
                            'cantidad' => $detalle->cantidad,
                            'pedido_id' => $pedido->pedido_id
                        ]);
                        
                        $resultado = $variante->registrarSalida(
                            $detalle->cantidad,
                            "Venta confirmada - Pedido #{$pedido->pedido_id} - Stripe",
                            \Illuminate\Support\Facades\Auth::id(),
                            "Pedido #{$pedido->pedido_id}"
                        );
                        
                        Log::info('Resultado de registrarSalida para variante', [
                            'variante_id' => $variante->variante_id,
                            'resultado' => $resultado ? 'Exitoso' : 'Falló'
                        ]);
                        
                        Log::info('Venta de variante confirmada desde Stripe', [
                            'producto_id' => $producto->producto_id,
                            'variante_id' => $variante->variante_id,
                            'variante_nombre' => $variante->nombre,
                            'cantidad' => $detalle->cantidad,
                            'pedido_id' => $pedido->pedido_id
                        ]);
                    }
                } else {
                    // Registrar salida de stock para el producto (Stripe confirma automáticamente)
                    Log::info('Registrando salida de stock para producto', [
                        'producto_id' => $producto->producto_id,
                        'cantidad' => $detalle->cantidad,
                        'pedido_id' => $pedido->pedido_id
                    ]);
                    
                    $resultado = $producto->registrarSalida(
                        $detalle->cantidad,
                        "Venta confirmada - Pedido #{$pedido->pedido_id} - Stripe",
                        \Illuminate\Support\Facades\Auth::id(),
                        $pedido->pedido_id
                    );
                    
                    Log::info('Resultado de registrarSalida para producto', [
                        'producto_id' => $producto->producto_id,
                        'resultado' => $resultado ? 'Exitoso' : 'Falló'
                    ]);
                    
                    Log::info('Venta de producto confirmada desde Stripe', [
                        'producto_id' => $producto->producto_id,
                        'cantidad' => $detalle->cantidad,
                        'pedido_id' => $pedido->pedido_id
                    ]);
                }
            }
            // Si el pedido se cancela (pasa de pendiente a cancelado/rechazado)
            elseif (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosCancelados)) {
                // Verificar si el detalle tiene una variante específica
                if ($detalle->variante_id) {
                    // Liberar stock reservado de la variante
                    $variante = $detalle->variante;
                    if ($variante) {
                        $variante->liberarReserva(
                            $detalle->cantidad,
                            "Cancelación de pedido #{$pedido->pedido_id}",
                            \Illuminate\Support\Facades\Auth::id(),
                            "Pedido #{$pedido->pedido_id}"
                        );
                        
                        Log::info('Stock de variante liberado por cancelación desde Stripe', [
                            'producto_id' => $producto->producto_id,
                            'variante_id' => $variante->variante_id,
                            'variante_nombre' => $variante->nombre,
                            'cantidad' => $detalle->cantidad,
                            'pedido_id' => $pedido->pedido_id
                        ]);
                    }
                } else {
                    // Liberar stock reservado del producto padre
                    $producto->liberarStockReservado(
                        $detalle->cantidad,
                        "Cancelación de pedido #{$pedido->pedido_id}",
                        \Illuminate\Support\Facades\Auth::id(),
                        $pedido->pedido_id
                    );
                    
                    Log::info('Stock de producto liberado por cancelación desde Stripe', [
                        'producto_id' => $producto->producto_id,
                        'cantidad' => $detalle->cantidad,
                        'pedido_id' => $pedido->pedido_id
                    ]);
                }
            }
            // Si el pedido se cancela después de estar confirmado
            elseif (in_array($estadoAnterior, $estadosConfirmados) && in_array($nuevoEstado, $estadosCancelados)) {
                // Verificar si el detalle tiene una variante específica
                if ($detalle->variante_id) {
                    // Registrar entrada de la variante para compensar la salida ya registrada
                    $variante = $detalle->variante;
                    if ($variante) {
                        $variante->registrarEntrada(
                            $detalle->cantidad,
                            "Devolución por cancelación - Pedido #{$pedido->pedido_id}",
                            \Illuminate\Support\Facades\Auth::id(),
                            "Pedido #{$pedido->pedido_id}"
                        );
                        
                        Log::info('Stock de variante devuelto por cancelación post-confirmación desde Stripe', [
                            'producto_id' => $producto->producto_id,
                            'variante_id' => $variante->variante_id,
                            'variante_nombre' => $variante->nombre,
                            'cantidad' => $detalle->cantidad,
                            'pedido_id' => $pedido->pedido_id
                        ]);
                    }
                } else {
                    // Registrar entrada del producto padre para compensar la salida ya registrada
                    $producto->registrarEntrada(
                        $detalle->cantidad,
                        "Devolución por cancelación - Pedido #{$pedido->pedido_id}",
                        \Illuminate\Support\Facades\Auth::id(),
                        "Pedido #{$pedido->pedido_id}"
                    );
                    
                    Log::info('Stock de producto devuelto por cancelación post-confirmación desde Stripe', [
                        'producto_id' => $producto->producto_id,
                        'cantidad' => $detalle->cantidad,
                        'pedido_id' => $pedido->pedido_id
                    ]);
                }
            }
        }
        
        } catch (\Exception $e) {
            Log::error('Error en manejarStockReservado desde StripeService', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Método de respaldo para registrar movimientos de inventario
     */
    private function registrarMovimientosFallback(Pedido $pedido): void
    {
        try {
            Log::info('Ejecutando fallback para registrar movimientos de inventario', [
                'pedido_id' => $pedido->pedido_id
            ]);

            foreach ($pedido->detalles as $detalle) {
                $producto = $detalle->producto;
                
                if ($detalle->variante_id) {
                    // Registrar salida para variante
                    $variante = $detalle->variante;
                    if ($variante) {
                        Log::info('Fallback: Registrando salida para variante', [
                            'variante_id' => $variante->variante_id,
                            'cantidad' => $detalle->cantidad
                        ]);
                        
                        $variante->registrarSalida(
                            $detalle->cantidad,
                            "Venta confirmada - Pedido #{$pedido->pedido_id} - Stripe (Fallback)",
                            \Illuminate\Support\Facades\Auth::id(),
                            "Pedido #{$pedido->pedido_id}"
                        );
                    }
                } else {
                    // Registrar salida para producto
                    Log::info('Fallback: Registrando salida para producto', [
                        'producto_id' => $producto->producto_id,
                        'cantidad' => $detalle->cantidad
                    ]);
                    
                    $producto->registrarSalida(
                        $detalle->cantidad,
                        "Venta confirmada - Pedido #{$pedido->pedido_id} - Stripe (Fallback)",
                        \Illuminate\Support\Facades\Auth::id(),
                        $pedido->pedido_id
                    );
                }
            }
            
            Log::info('Fallback ejecutado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error en fallback de movimientos de inventario', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Enviar correo específico de pago exitoso con Stripe
     */
    private function enviarCorreoPagoExitoso(Pedido $pedido, $paymentIntent): void
    {
        try {
            // Cargar la relación del usuario si no está cargada
            if (!$pedido->relationLoaded('usuario')) {
                $pedido->load('usuario');
            }

            // Verificar que el pedido tenga usuario
            if (!$pedido->usuario) {
                Log::warning('Pedido sin usuario para enviar correo de pago exitoso', [
                    'pedido_id' => $pedido->pedido_id
                ]);
                return;
            }

            // Enviar correo específico de pago exitoso con Stripe
            \Illuminate\Support\Facades\Mail::to($pedido->usuario->correo_electronico)
                ->send(new \App\Mail\StripePagoExitoso($pedido->usuario, $pedido, $paymentIntent));
            
            Log::info('Correo de pago exitoso con Stripe enviado', [
                'pedido_id' => $pedido->pedido_id,
                'usuario_id' => $pedido->usuario_id,
                'email' => $pedido->usuario->correo_electronico,
                'payment_intent_id' => $paymentIntent->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error enviando correo de pago exitoso con Stripe', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
            // No lanzar excepción para no afectar el flujo del pago
        }
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

            $pago = Pago::where('referencia_externa', $paymentIntent->id)->first();
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
                'type' => 'payment_intent.succeeded',
                'stripe_id' => 'local_' . time(),
                'livemode' => false, // Webhook local siempre es false
                'pedido_id' => $pedido->pedido_id,
                'data' => [
                    'pedido_id' => $pedido->pedido_id,
                    'payment_intent_id' => $paymentIntent->id,
                    'monto' => $paymentIntent->amount / 100
                ],
                'status' => 'processed',
                'processed' => true,
                'processed_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar webhook local: ' . $e->getMessage());
        }
    }
}
