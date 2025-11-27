<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\WebController;
use App\Models\Pedido;
use App\Services\Notification\PedidoNotificationService;
use App\Services\Inventory\ReservaStockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/**
 * Controlador de administración para gestión avanzada de pedidos.
 *
 * Administra listado, detalle, cambios de estado, notificaciones y
 * sincronización de stock asociado a los pedidos.
 */
class PedidoAdminController extends WebController
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Listado de pedidos (admin).
     *
     * @return ViewView|RedirectResponse Vista con paginación o redirección en error
     */
    public function index(): ViewView|RedirectResponse
    {
        try {
            $pedidos = Pedido::with(['usuario', 'estado', 'pago.metodoPago', 'direccion'])
                ->orderBy('fecha_pedido', 'desc')
                ->paginate(10);

            return View::make('pages.admin.pedidos.index', compact('pedidos'));
        } catch (\Exception $e) {
            Log::error('Error en PedidoAdminController@index: '.$e->getMessage());

            return Redirect::back()->with('error', 'Hubo un error al cargar los pedidos.');
        }
    }

    /**
     * Detalle de pedido (admin).
     *
     * @param int $id Pedido ID
     * @return ViewView|RedirectResponse Vista de detalle o redirección en error
     */
    public function show(int $id): ViewView|RedirectResponse
    {
        try {
            $pedido = Pedido::with([
                'usuario',
                'estado',
                'pago.metodoPago',
                'direccion',
                'detalles.producto',
            ])->findOrFail($id);

            return View::make('pages.admin.pedidos.show', compact('pedido'));
        } catch (\Exception $e) {
            Log::error('Error en PedidoAdminController@show: '.$e->getMessage());

            return Redirect::back()->with('error', 'Hubo un error al cargar el detalle del pedido.');
        }
    }

    /**
     * Actualizar estado del pedido (admin).
     *
     * @param Request $request Datos de estado
     * @param int $id Pedido ID
     * @return RedirectResponse Redirección con mensaje de estado
     */
    public function updateEstado(Request $request, int $id): RedirectResponse
    {
        try {
            $request->validate([
                'estado_id' => 'required|exists:estados_pedido,estado_id',
                'motivo_cancelacion' => 'exclude_unless:estado_id,3|required|string|min:5',
            ]);

            $pedido = Pedido::with(['detalles.producto', 'usuario', 'estado'])->findOrFail($id);
            $estadoAnterior = $pedido->estado_id;
            $nuevoEstado = (int) $request->estado_id;

            // Validar que el pedido no esté cancelado o confirmado
            if ($this->pedidoNoPermiteCambioEstado($pedido)) {
                $estadoActual = $pedido->estado->nombre ?? 'Desconocido';
                $mensaje = "No se puede cambiar el estado de un pedido que ya está {$estadoActual}";

                Log::warning('Intento de cambio de estado en pedido finalizado', [
                    'pedido_id' => $pedido->pedido_id,
                    'estado_actual' => $estadoActual,
                    'nuevo_estado' => $nuevoEstado,
                    'admin_id' => Auth::id(),
                ]);

                return Redirect::back()
                    ->with('mensaje', $mensaje)
                    ->with('tipo', 'warning');
            }

            Log::info('Cambiando estado de pedido', [
                'pedido_id' => $pedido->pedido_id,
                'estado_anterior' => $estadoAnterior,
                'nuevo_estado' => $nuevoEstado,
            ]);

            // Manejar stock reservado según el cambio de estado
            $this->manejarStockReservado($pedido, $estadoAnterior, $nuevoEstado);

            $pedido->estado_id = $nuevoEstado;
            $pedido->save();

            $this->enviarCorreoSegunEstado($pedido, $estadoAnterior, $nuevoEstado, $request->input('motivo_cancelacion'));

            Log::info('Estado de pedido actualizado', [
                'pedido_id' => $pedido->pedido_id,
                'nuevo_estado' => $nuevoEstado,
            ]);

            return Redirect::back()->with('mensaje', 'Estado del Pedido Actualizado')->with('tipo', 'success');

        } catch (\Exception $e) {
            Log::error('Error en PedidoAdminController@updateEstado: '.$e->getMessage(), [
                'pedido_id' => $id,
                'estado_id' => $request->estado_id ?? 'no_provisto',
            ]);

            return Redirect::back()->with('mensaje', 'Hubo un error al actualizar el estado del pedido.')->with('tipo', 'error');
        }
    }

    /**
     * Enviar correo según el tipo de cambio de estado
     */
    /**
     * Enviar correo según el tipo de cambio de estado.
     */
    private function enviarCorreoSegunEstado(Pedido $pedido, int $estadoAnterior, int $nuevoEstado, ?string $motivo = null): void
    {
        // Estados pendientes
        $estadosPendientes = [1]; // Pendiente

        // Estados que confirman la venta
        $estadosConfirmados = [2]; // Confirmado

        // Estados que cancelan la venta
        $estadosCancelados = [3]; // Cancelado

        // Si pasa de pendiente a confirmado/enviado/entregado
        if (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosConfirmados)) {
            $this->enviarCorreoConfirmacion($pedido);
        }
        // Si pasa de pendiente a cancelado/rechazado
        elseif (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosCancelados)) {
            $this->enviarCorreoCancelacion($pedido, $motivo);
        }
        // Si pasa de confirmado a cancelado (cancelación post-confirmación)
        elseif (in_array($estadoAnterior, $estadosConfirmados) && in_array($nuevoEstado, $estadosCancelados)) {
            $this->enviarCorreoCancelacion($pedido, $motivo);
        }
    }

    /**
     * Determinar si se debe enviar correo de confirmación
     */
    /**
     * Determinar si se debe enviar correo de confirmación.
     */
    private function debeEnviarCorreoConfirmacion(int $estadoAnterior, int $nuevoEstado): bool
    {
        // Estados pendientes
        $estadosPendientes = [1]; // Pendiente

        // Estados que confirman la venta
        $estadosConfirmados = [2]; // Confirmado

        // Solo enviar correo si pasa de pendiente a confirmado
        return in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosConfirmados);
    }

    /**
     * Enviar correo de confirmación del pedido
     */
    /**
     * Enviar correo de confirmación del pedido.
     */
    private function enviarCorreoConfirmacion(Pedido $pedido): void
    {
        try {
            $notificationService = new PedidoNotificationService;
            $notificationService->enviarCorreoConfirmacion($pedido);

            Log::info('Correo de confirmación enviado', [
                'pedido_id' => $pedido->pedido_id,
                'usuario_id' => $pedido->usuario_id,
                'email' => $pedido->usuario->correo_electronico,
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando correo de confirmación', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Enviar correo de cancelación del pedido
     */
    /**
     * Enviar correo de cancelación del pedido.
     */
    private function enviarCorreoCancelacion(Pedido $pedido, ?string $motivo = null): void
    {
        try {
            Mail::to($pedido->usuario->correo_electronico)
                ->send(new \App\Mail\PedidoCancelado($pedido->usuario, $pedido, $motivo));

            Log::info('Correo de cancelación enviado', [
                'pedido_id' => $pedido->pedido_id,
                'usuario_id' => $pedido->usuario_id,
                'email' => $pedido->usuario->correo_electronico,
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando correo de cancelación', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Manejar el stock reservado según el cambio de estado del pedido
     */
    /**
     * Manejar el stock reservado según el cambio de estado del pedido.
     */
    private function manejarStockReservado(Pedido $pedido, int $estadoAnterior, int $nuevoEstado): void
    {
        // Estados que confirman la venta (liberan stock reservado y registran salida)
        $estadosConfirmados = [2]; // Confirmado

        // Estados que cancelan la venta (liberan stock reservado sin registrar salida)
        $estadosCancelados = [3]; // Cancelado

        // Estados pendientes (mantienen stock reservado)
        $estadosPendientes = [1]; // Pendiente

        Log::info('Manejando stock reservado', [
            'pedido_id' => $pedido->pedido_id,
            'estado_anterior' => $estadoAnterior,
            'nuevo_estado' => $nuevoEstado,
        ]);

        // Si el pedido se cancela (pasa de pendiente a cancelado/rechazado), cancelar reservas de variantes primero
        if (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosCancelados)) {
            $tieneVariantes = $pedido->detalles->contains(function ($detalle) {
                return $detalle->variante_id !== null;
            });

            if ($tieneVariantes) {
                // Usar el servicio de reservas para cancelar todas las reservas activas del pedido
                $reservaStockService = new ReservaStockService;
                $reservaStockService->cancelarReservasPedido(
                    $pedido->pedido_id,
                    \Illuminate\Support\Facades\Auth::id(),
                    "Cancelación de pedido #{$pedido->pedido_id}"
                );

                Log::info('Reservas de variantes canceladas por cancelación de pedido', [
                    'pedido_id' => $pedido->pedido_id,
                ]);
            }
        }

        foreach ($pedido->detalles as $detalle) {
            $producto = $detalle->producto;

            // Si el pedido se confirma (pasa de pendiente a confirmado/enviado/entregado)
            if (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosConfirmados)) {
                // Verificar si el detalle tiene una variante específica
                if ($detalle->variante_id) {
                    // Registrar movimiento de confirmación de venta para la variante
                    $variante = $detalle->variante;
                    if ($variante) {
                        $variante->confirmarVenta(
                            $detalle->cantidad,
                            "Confirmación de pedido #{$pedido->pedido_id}",
                            \Illuminate\Support\Facades\Auth::id(),
                            "Pedido #{$pedido->pedido_id}"
                        );

                        Log::info('Venta de variante confirmada', [
                            'producto_id' => $producto->producto_id,
                            'variante_id' => $variante->variante_id,
                            'variante_nombre' => $variante->nombre,
                            'cantidad' => $detalle->cantidad,
                            'pedido_id' => $pedido->pedido_id,
                        ]);
                    }
                } else {
                    // Liberar stock reservado primero
                    if ($producto->stock_reservado > 0) {
                        $producto->liberarStockReservado(
                            $detalle->cantidad,
                            "Confirmación de pedido #{$pedido->pedido_id}",
                            \Illuminate\Support\Facades\Auth::id(),
                            $pedido->pedido_id
                        );
                    }
                    // Registrar movimiento de confirmación de venta para el producto
                    \App\Models\MovimientoInventario::create([
                        'producto_id' => $producto->producto_id,
                        'tipo_movimiento' => 'salida',
                        'cantidad' => $detalle->cantidad,
                        'stock_anterior' => $producto->stock,
                        'stock_nuevo' => $producto->stock, // No cambia el stock físico porque ya se reservó
                        'motivo' => "Confirmación de pedido #{$pedido->pedido_id}",
                        'usuario_id' => \Illuminate\Support\Facades\Auth::id(),
                        'pedido_id' => $pedido->pedido_id,
                        'costo_unitario' => $producto->costo_unitario,
                        'fecha_movimiento' => Carbon::now(),
                    ]);

                    Log::info('Venta de producto confirmada', [
                        'producto_id' => $producto->producto_id,
                        'cantidad' => $detalle->cantidad,
                        'pedido_id' => $pedido->pedido_id,
                    ]);
                }
            }
            // Si el pedido se cancela (pasa de pendiente a cancelado/rechazado)
            elseif (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosCancelados)) {
                // Para productos sin variantes, liberar stock reservado
                if (! $detalle->variante_id) {
                    // Liberar stock reservado del producto padre
                    if ($producto->stock_reservado > 0) {
                        $producto->liberarStockReservado(
                            $detalle->cantidad,
                            "Cancelación de pedido #{$pedido->pedido_id}",
                            \Illuminate\Support\Facades\Auth::id(),
                            $pedido->pedido_id
                        );
                    }

                    Log::info('Stock de producto liberado por cancelación', [
                        'producto_id' => $producto->producto_id,
                        'cantidad' => $detalle->cantidad,
                        'pedido_id' => $pedido->pedido_id,
                    ]);
                }
                // Las variantes ya se cancelaron antes del bucle
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

                        Log::info('Stock de variante devuelto por cancelación post-confirmación', [
                            'producto_id' => $producto->producto_id,
                            'variante_id' => $variante->variante_id,
                            'variante_nombre' => $variante->nombre,
                            'cantidad' => $detalle->cantidad,
                            'pedido_id' => $pedido->pedido_id,
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

                    Log::info('Stock de producto devuelto por cancelación post-confirmación', [
                        'producto_id' => $producto->producto_id,
                        'cantidad' => $detalle->cantidad,
                        'pedido_id' => $pedido->pedido_id,
                    ]);
                }
            }
        }
    }

    /**
     * Verificar si un pedido permite cambio de estado
     * Los pedidos cancelados o confirmados no permiten cambios
     */
    /**
     * Verificar si un pedido permite cambio de estado.
     * Los pedidos cancelados o confirmados no permiten cambios.
     */
    private function pedidoNoPermiteCambioEstado($pedido): bool
    {
        // Estados que no permiten cambios
        $estadosFinales = ['cancelado', 'confirmado', 'entregado'];

        $estadoActual = strtolower($pedido->estado->nombre ?? '');

        return in_array($estadoActual, $estadosFinales);
    }

}
