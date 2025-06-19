<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\EstadoPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

            $pedido = Pedido::findOrFail($id);
            $pedido->estado_id = $request->estado_id;
            $pedido->save();

            Log::info('Estado de pedido actualizado', [
                'pedido_id' => $id,
                'estado_id' => $request->estado_id,
                'usuario_id' => $pedido->usuario_id
            ]);

            return back()->with('success', 'Estado del pedido actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error en PedidoAdminController@updateEstado: ' . $e->getMessage());
            return back()->with('error', 'Hubo un error al actualizar el estado del pedido.');
        }
    }
} 