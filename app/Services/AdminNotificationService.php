<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\Pedido;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NotificacionPedidoNuevo;

class AdminNotificationService
{
    /**
     * Enviar notificación a todos los administradores sobre un nuevo pedido
     */
    public function notificarPedidoNuevo(Pedido $pedido, string $metodoPago): bool
    {
        try {
            // Cargar la relación del usuario si no está cargada
            if (!$pedido->relationLoaded('usuario')) {
                $pedido->load('usuario');
            }

            // Verificar que el pedido tenga usuario
            if (!$pedido->usuario) {
                Log::warning('Pedido sin usuario para notificar a admin', [
                    'pedido_id' => $pedido->pedido_id
                ]);
                return false;
            }

            // Obtener todos los administradores
            $admins = $this->obtenerAdministradores();
            
            if ($admins->isEmpty()) {
                Log::warning('No hay administradores para notificar', [
                    'pedido_id' => $pedido->pedido_id
                ]);
                return false;
            }

            // Generar URL del panel de administración
            $adminUrl = route('admin.pedidos.show', $pedido->pedido_id);

            // Enviar notificación a cada administrador
            $enviados = 0;
            foreach ($admins as $admin) {
                if ($this->enviarNotificacionAdmin($admin, $pedido, $metodoPago, $adminUrl)) {
                    $enviados++;
                }
            }

            Log::info('Notificaciones de pedido nuevo enviadas a administradores', [
                'pedido_id' => $pedido->pedido_id,
                'metodo_pago' => $metodoPago,
                'admins_notificados' => $enviados,
                'total_admins' => $admins->count()
            ]);

            return $enviados > 0;

        } catch (\Exception $e) {
            Log::error('Error enviando notificaciones de pedido nuevo a administradores', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Obtener todos los usuarios administradores
     */
    private function obtenerAdministradores()
    {
        try {
            // Buscar usuarios con rol de administrador
            // Solo usar campos que existen en la tabla
            return Usuario::where('rol', 'admin')
                         ->where('estado', 1) // Solo usuarios activos
                         ->get();
        } catch (\Exception $e) {
            Log::error('Error obteniendo administradores: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Enviar notificación individual a un administrador
     */
    private function enviarNotificacionAdmin(Usuario $admin, Pedido $pedido, string $metodoPago, string $adminUrl): bool
    {
        try {
            // Verificar que el admin tenga email válido
            if (empty($admin->correo_electronico)) {
                Log::warning('Admin sin email válido', [
                    'admin_id' => $admin->usuario_id,
                    'pedido_id' => $pedido->pedido_id
                ]);
                return false;
            }

            // Enviar correo de notificación
            Mail::to($admin->correo_electronico)
                ->send(new NotificacionPedidoNuevo($pedido, $pedido->usuario, $metodoPago, $adminUrl));

            Log::info('Notificación de pedido nuevo enviada a admin', [
                'admin_id' => $admin->usuario_id,
                'admin_email' => $admin->correo_electronico,
                'pedido_id' => $pedido->pedido_id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error enviando notificación a admin', [
                'admin_id' => $admin->usuario_id,
                'admin_email' => $admin->correo_electronico ?? 'sin email',
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar si se debe enviar notificación
     * Útil para validaciones antes de enviar
     */
    public function debeNotificarPedidoNuevo(Pedido $pedido): bool
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

            // Verificar que haya administradores disponibles
            $admins = $this->obtenerAdministradores();
            return $admins->isNotEmpty();

        } catch (\Exception $e) {
            Log::error('Error al verificar si se debe notificar pedido nuevo', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener estadísticas de notificaciones enviadas
     */
    public function obtenerEstadisticas(): array
    {
        try {
            $admins = $this->obtenerAdministradores();
            
            return [
                'total_admins' => $admins->count(),
                'admins_con_email' => $admins->whereNotNull('correo_electronico')->count(),
                'admins_sin_email' => $admins->whereNull('correo_electronico')->count(),
                'fecha_consulta' => now()->format('Y-m-d H:i:s')
            ];

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de notificaciones: ' . $e->getMessage());
            return [
                'total_admins' => 0,
                'admins_con_email' => 0,
                'admins_sin_email' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}
