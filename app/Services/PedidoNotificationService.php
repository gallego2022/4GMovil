<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Pago;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ConfirmacionPedido;
use App\Services\AdminNotificationService;

class PedidoNotificationService
{
    /**
     * Enviar correo de confirmación del pedido
     * Este método se puede llamar desde cualquier parte del sistema
     */
    public function enviarCorreoConfirmacion(Pedido $pedido): bool
    {
        try {
            // Cargar la relación del usuario si no está cargada
            if (!$pedido->relationLoaded('usuario')) {
                $pedido->load('usuario');
            }

            // Verificar que el pedido tenga usuario
            if (!$pedido->usuario) {
                Log::warning('Pedido sin usuario para enviar correo', [
                    'pedido_id' => $pedido->pedido_id
                ]);
                return false;
            }

            // Generar URL del pedido
            $pedidoUrl = route('pedidos.show', $pedido->pedido_id);
            
            // Enviar correo de confirmación al cliente
            Mail::to($pedido->usuario->correo_electronico)
                ->send(new ConfirmacionPedido($pedido->usuario, $pedido, $pedidoUrl));
            
            // Enviar notificación a administradores
            $this->notificarAdministradores($pedido);
            
            Log::info('Correo de confirmación enviado exitosamente', [
                'pedido_id' => $pedido->pedido_id,
                'usuario_id' => $pedido->usuario_id,
                'email' => $pedido->usuario->correo_electronico,
                'metodo_pago' => $this->getMetodoPagoNombre($pedido)
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error enviando correo de confirmación', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Enviar correo de confirmación cuando se confirma un pedido
     * Este método se llama cuando el estado del pedido cambia a confirmado
     */
    public function confirmarPedido(Pedido $pedido): bool
    {
        try {
            // Verificar que el pedido esté confirmado
            if ($pedido->estado_id != 2) { // 2 = Confirmado
                Log::info('Pedido no confirmado, no se envía correo', [
                    'pedido_id' => $pedido->pedido_id,
                    'estado_id' => $pedido->estado_id
                ]);
                return false;
            }

            // Verificar que no se haya enviado ya el correo
            if ($this->yaSeEnvioCorreo($pedido)) {
                Log::info('Correo ya enviado para este pedido', [
                    'pedido_id' => $pedido->pedido_id
                ]);
                return true;
            }

            // Enviar correo
            $enviado = $this->enviarCorreoConfirmacion($pedido);
            
            if ($enviado) {
                // Marcar que se envió el correo (opcional, para evitar duplicados)
                $this->marcarCorreoEnviado($pedido);
            }

            return $enviado;

        } catch (\Exception $e) {
            Log::error('Error al confirmar pedido y enviar correo', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enviar correo de confirmación para métodos de pago no-Stripe
     * Este método se llama cuando se confirma un pedido con efectivo, transferencia, etc.
     * NO se debe usar para Stripe
     */
    public function confirmarPedidoMetodoNoStripe(Pedido $pedido, string $metodoPago): bool
    {
        try {
            // Verificar que NO sea Stripe
            if (strtolower($metodoPago) === 'stripe') {
                Log::warning('No se debe usar confirmarPedidoMetodoNoStripe para Stripe', [
                    'pedido_id' => $pedido->pedido_id,
                    'metodo_pago' => $metodoPago
                ]);
                return false;
            }

            Log::info('Confirmando pedido con método de pago no-Stripe', [
                'pedido_id' => $pedido->pedido_id,
                'metodo_pago' => $metodoPago,
                'estado_id' => $pedido->estado_id
            ]);

            // Verificar que el pedido esté confirmado
            if ($pedido->estado_id != 2) {
                Log::warning('Pedido no confirmado para método no-Stripe', [
                    'pedido_id' => $pedido->pedido_id,
                    'estado_id' => $pedido->estado_id
                ]);
                return false;
            }

            // Enviar correo de confirmación
            return $this->enviarCorreoConfirmacion($pedido);

        } catch (\Exception $e) {
            Log::error('Error al confirmar pedido método no-Stripe', [
                'pedido_id' => $pedido->pedido_id,
                'metodo_pago' => $metodoPago,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener el nombre del método de pago
     */
    private function getMetodoPagoNombre(Pedido $pedido): string
    {
        try {
            if ($pedido->pago && $pedido->pago->metodoPago) {
                return $pedido->pago->metodoPago->nombre;
            }
            return 'No especificado';
        } catch (\Exception $e) {
            return 'No especificado';
        }
    }

    /**
     * Verificar si ya se envió el correo para este pedido
     * Esto evita duplicados
     */
    private function yaSeEnvioCorreo(Pedido $pedido): bool
    {
        // Por ahora, siempre permitimos reenvío
        // En el futuro se puede implementar un sistema de tracking
        return false;
    }

    /**
     * Marcar que se envió el correo
     * Esto se puede usar para evitar duplicados
     */
    private function marcarCorreoEnviado(Pedido $pedido): void
    {
        // Por ahora no implementamos tracking
        // En el futuro se puede agregar una tabla de notificaciones enviadas
        Log::info('Correo marcado como enviado', [
            'pedido_id' => $pedido->pedido_id,
            'timestamp' => now()
        ]);
    }

    /**
     * Enviar correo de confirmación para pedidos con pago pendiente
     * Útil para métodos como efectivo o transferencia que requieren confirmación manual
     */
    public function confirmarPedidoPagoPendiente(Pedido $pedido): bool
    {
        try {
            // Verificar que el pedido tenga pago pendiente
            if (!$pedido->pago || $pedido->pago->estado !== 'pendiente') {
                Log::info('Pedido sin pago pendiente', [
                    'pedido_id' => $pedido->pedido_id,
                    'estado_pago' => $pedido->pago->estado ?? 'sin pago'
                ]);
                return false;
            }

            // Verificar que el pedido esté confirmado
            if ($pedido->estado_id != 2) {
                Log::info('Pedido no confirmado para pago pendiente', [
                    'pedido_id' => $pedido->pedido_id,
                    'estado_id' => $pedido->estado_id
                ]);
                return false;
            }

            // Enviar correo de confirmación
            return $this->enviarCorreoConfirmacion($pedido);

        } catch (\Exception $e) {
            Log::error('Error al confirmar pedido con pago pendiente', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Notificar a administradores sobre nuevo pedido
     */
    private function notificarAdministradores(Pedido $pedido): void
    {
        try {
            $adminService = new AdminNotificationService();
            $metodoPago = $this->getMetodoPagoNombre($pedido);
            
            $adminService->notificarPedidoNuevo($pedido, $metodoPago);
            
            Log::info('Notificación a administradores enviada', [
                'pedido_id' => $pedido->pedido_id,
                'metodo_pago' => $metodoPago
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error notificando a administradores', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
            // No lanzar excepción para no afectar el flujo principal
        }
    }

    /**
     * Método público para verificar si se debe enviar correo
     * Útil para validaciones antes de enviar
     */
    public function debeEnviarCorreo(Pedido $pedido): bool
    {
        try {
            // Verificar que el pedido esté confirmado
            if ($pedido->estado_id != 2) {
                return false;
            }

            // Verificar que tenga usuario
            if (!$pedido->usuario) {
                return false;
            }

            // Verificar que tenga email válido
            if (empty($pedido->usuario->correo_electronico)) {
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error al verificar si se debe enviar correo', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
