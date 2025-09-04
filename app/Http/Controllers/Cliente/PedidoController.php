<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Services\Business\PedidoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class PedidoController extends Controller
{
    protected $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    /**
     * Muestra el historial de pedidos del usuario autenticado
     */
    public function historial(Request $request)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $filters = [
                'per_page' => $request->get('per_page', 15),
                'estado_id' => $request->get('estado_id'),
                'fecha_desde' => $request->get('fecha_desde'),
                'fecha_hasta' => $request->get('fecha_hasta'),
            ];

            $result = $this->pedidoService->getUserOrders($filters);
            
            return view('modules.cliente.pedidos.historial', [
                'pedidos' => $result['data']
            ]);

        } catch (Exception $e) {
            // Log del error
            Log::error('Error al cargar historial de pedidos: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Error al cargar el historial de pedidos. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Muestra el detalle de un pedido específico
     */
    public function detalle($pedidoId)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            // Obtener el pedido y verificar que pertenezca al usuario
            $pedido = \App\Models\Pedido::where('pedido_id', $pedidoId)
                ->where('usuario_id', Auth::id())
                ->with(['detalles.producto', 'detalles.variante', 'estado', 'direccion', 'pago.metodoPago'])
                ->firstOrFail();

            return view('modules.cliente.pedidos.detalle', [
                'pedido' => $pedido
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('pedidos.historial')->with('error', 'Pedido no encontrado.');
        } catch (Exception $e) {
            Log::error('Error al cargar detalle de pedido: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'pedido_id' => $pedidoId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error al cargar el detalle del pedido. Por favor, inténtalo de nuevo.');
        }
    }
}
