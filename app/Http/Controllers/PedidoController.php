<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function historial()
    {
        $pedidos = Pedido::with(['detalles.producto', 'estado', 'pago.metodoPago'])
            ->where('usuario_id', Auth::id())
            ->orderBy('fecha_pedido', 'desc')
            ->paginate(10);

        return view('modules.cliente.pedidos.historial', compact('pedidos'));
    }

    public function detalle($id)
    {
        $pedido = Pedido::with(['detalles.producto', 'estado', 'pago.metodoPago', 'direccion'])
            ->where('usuario_id', Auth::id())
            ->findOrFail($id);

        // Asegurarnos de que la fecha de pago sea un objeto Carbon
        if ($pedido->pago && !($pedido->pago->fecha_pago instanceof \Carbon\Carbon)) {
            $pedido->pago->fecha_pago = \Carbon\Carbon::parse($pedido->pago->fecha_pago);
        }

        return view('modules.cliente.pedidos.detalle', compact('pedido'));
    }
}
