<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Base\WebController;
use App\Services\Business\PedidoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use App\Models\Pedido;
use Exception;

class PedidoController extends WebController
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
                return Redirect::route('login');
            }

            $filters = [
                'per_page' => $request->get('per_page', 15),
                'estado_id' => $request->get('estado_id'),
                'fecha_desde' => $request->get('fecha_desde'),
                'fecha_hasta' => $request->get('fecha_hasta'),
            ];

            $result = $this->pedidoService->getUserOrders($filters);
            
            return View::make('modules.cliente.pedidos.historial', [
                'pedidos' => $result['data']
            ]);

        } catch (Exception $e) {
            // Log del error
            Log::error('Error al cargar historial de pedidos: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::back()->with('error', 'Error al cargar el historial de pedidos. Por favor, inténtalo de nuevo.');
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
                return Redirect::route('login');
            }

            // Obtener el pedido y verificar que pertenezca al usuario
            $pedido = Pedido::where('pedido_id', $pedidoId)
                ->where('usuario_id', Auth::id())
                ->with(['detalles.producto', 'detalles.variante', 'estado', 'direccion', 'pago.metodoPago'])
                ->firstOrFail();

            return View::make('modules.cliente.pedidos.detalle', [
                'pedido' => $pedido
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Redirect::route('pedidos.historial')->with('error', 'Pedido no encontrado.');
        } catch (Exception $e) {
            Log::error('Error al cargar detalle de pedido: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'pedido_id' => $pedidoId,
                'error' => $e->getMessage()
            ]);

            return Redirect::back()->with('error', 'Error al cargar el detalle del pedido. Por favor, inténtalo de nuevo.');
        }
    }
}
