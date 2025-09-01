<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\EstadoPedido;
use App\Mail\ConfirmacionPedido;
use App\Mail\PedidoCancelado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PedidoAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        try {
            $pedidos = Pedido::with(['usuario', 'estado', 'pago.metodoPago', 'direccion'])
                ->orderBy('fecha_pedido', 'desc')
                ->paginate(10);

            return view('pages.admin.pedidos.index', compact('pedidos'));
        } catch (\Exception $e) {
            Log::error('Error en PedidoAdminController@index: ' . $e->getMessage());
            return back()->with('error', 'Hubo un error al cargar los pedidos.');
        }
    }

    public function show($id)
    {
        try {
            $pedido = Pedido::with([
                'usuario', 
                'estado', 
                'pago.metodoPago', 
                'direccion',
                'detalles.producto'
            ])->findOrFail($id);

            return view('pages.admin.pedidos.show', compact('pedido'));
        } catch (\Exception $e) {
            Log::error('Error en PedidoAdminController@show: ' . $e->getMessage());
            return back()->with('error', 'Hubo un error al cargar el detalle del pedido.');
        }
    }

    public function updateEstado(Request $request, $id)
    {
        try {
            $request->validate([
                'estado_id' => 'required|exists:estados_pedido,estado_id'
            ]);

            $pedido = Pedido::with(['detalles.producto', 'usuario'])->findOrFail($id);
            $estadoAnterior = $pedido->estado_id;
            $nuevoEstado = (int) $request->estado_id;
            
            Log::info('Cambiando estado de pedido', [
                'pedido_id' => $pedido->pedido_id,
                'estado_anterior' => $estadoAnterior,
                'nuevo_estado' => $nuevoEstado
            ]);

            // Manejar stock reservado según el cambio de estado
            $this->manejarStockReservado($pedido, $estadoAnterior, $nuevoEstado);

            $pedido->estado_id = $nuevoEstado;
            $pedido->save();

            // Enviar correo según el tipo de cambio de estado
            $this->enviarCorreoSegunEstado($pedido, $estadoAnterior, $nuevoEstado);

            Log::info('Estado de pedido actualizado', [
                'pedido_id' => $pedido->pedido_id,
                'nuevo_estado' => $nuevoEstado
            ]);

            return back()->with('success', 'Estado del pedido actualizado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error en PedidoAdminController@updateEstado: ' . $e->getMessage(), [
                'pedido_id' => $id,
                'estado_id' => $request->estado_id ?? 'no_provisto'
            ]);
            return back()->with('error', 'Hubo un error al actualizar el estado del pedido.');
        }
    }

    /**
     * Enviar correo según el tipo de cambio de estado
     */
    private function enviarCorreoSegunEstado(Pedido $pedido, int $estadoAnterior, int $nuevoEstado): void
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
            $this->enviarCorreoCancelacion($pedido);
        }
        // Si pasa de confirmado a cancelado (cancelación post-confirmación)
        elseif (in_array($estadoAnterior, $estadosConfirmados) && in_array($nuevoEstado, $estadosCancelados)) {
            $this->enviarCorreoCancelacion($pedido);
        }
    }

    /**
     * Determinar si se debe enviar correo de confirmación
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
    private function enviarCorreoConfirmacion(Pedido $pedido): void
    {
        try {
            // Generar URL del pedido
            $pedidoUrl = route('pedidos.show', $pedido->pedido_id);
            
            // Enviar correo de confirmación
            Mail::to($pedido->usuario->correo_electronico)
                ->send(new ConfirmacionPedido($pedido->usuario, $pedido, $pedidoUrl));
            
            Log::info('Correo de confirmación enviado', [
                'pedido_id' => $pedido->pedido_id,
                'usuario_id' => $pedido->usuario_id,
                'email' => $pedido->usuario->correo_electronico
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error enviando correo de confirmación', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Enviar correo de cancelación del pedido
     */
    private function enviarCorreoCancelacion(Pedido $pedido): void
    {
        try {
            // Generar URL del pedido
            $pedidoUrl = route('pedidos.show', $pedido->pedido_id);
            
            // Enviar correo de cancelación
            Mail::to($pedido->usuario->correo_electronico)
                ->send(new PedidoCancelado($pedido->usuario, $pedido, $pedidoUrl));
            
            Log::info('Correo de cancelación enviado', [
                'pedido_id' => $pedido->pedido_id,
                'usuario_id' => $pedido->usuario_id,
                'email' => $pedido->usuario->correo_electronico
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error enviando correo de cancelación', [
                'pedido_id' => $pedido->pedido_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Manejar el stock reservado según el cambio de estado del pedido
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
            'nuevo_estado' => $nuevoEstado
        ]);

        foreach ($pedido->detalles as $detalle) {
            $producto = $detalle->producto;
            
            // Si el pedido se confirma (pasa de pendiente a confirmado/enviado/entregado)
            if (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosConfirmados)) {
                // Liberar stock reservado y registrar salida real
                $producto->liberarStockReservado(
                    $detalle->cantidad,
                    "Confirmación de pedido #{$pedido->pedido_id}",
                    \Illuminate\Support\Facades\Auth::id(),
                    $pedido->pedido_id
                );
                
                $producto->registrarSalida(
                    $detalle->cantidad,
                    "Venta confirmada - Pedido #{$pedido->pedido_id}",
                    \Illuminate\Support\Facades\Auth::id(),
                    $pedido->pedido_id
                );
                
                Log::info('Stock confirmado y registrada salida', [
                    'producto_id' => $producto->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'pedido_id' => $pedido->pedido_id
                ]);
            }
            // Si el pedido se cancela (pasa de pendiente a cancelado/rechazado)
            elseif (in_array($estadoAnterior, $estadosPendientes) && in_array($nuevoEstado, $estadosCancelados)) {
                // Solo liberar stock reservado sin registrar salida
                $producto->liberarStockReservado(
                    $detalle->cantidad,
                    "Cancelación de pedido #{$pedido->pedido_id}",
                    \Illuminate\Support\Facades\Auth::id(),
                    $pedido->pedido_id
                );
                
                Log::info('Stock liberado por cancelación', [
                    'producto_id' => $producto->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'pedido_id' => $pedido->pedido_id
                ]);
            }
            // Si el pedido se cancela después de estar confirmado
            elseif (in_array($estadoAnterior, $estadosConfirmados) && in_array($nuevoEstado, $estadosCancelados)) {
                // Registrar entrada para compensar la salida ya registrada
                $producto->registrarEntrada(
                    $detalle->cantidad,
                    "Devolución por cancelación - Pedido #{$pedido->pedido_id}",
                    \Illuminate\Support\Facades\Auth::id(),
                    "Pedido #{$pedido->pedido_id}"
                );
                
                Log::info('Stock devuelto por cancelación post-confirmación', [
                    'producto_id' => $producto->producto_id,
                    'cantidad' => $detalle->cantidad,
                    'pedido_id' => $pedido->pedido_id
                ]);
            }
        }
    }
} 