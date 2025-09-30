<?php

namespace App\Http\Controllers\Cliente;

use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\WebController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class DetallePedidoController extends WebController
{
    // Mostrar todos los detalles de un pedido específico
    public function index($pedido_id)
    {
        $pedido = Pedido::findOrFail($pedido_id);
        $detalles = $pedido->detalles; // Relación hasMany en Pedido

        return View::make('detalles_pedido.index', compact('pedido', 'detalles'));
    }

    // Mostrar formulario para crear un detalle de pedido nuevo
    public function create($pedido_id)
    {
        $pedido = Pedido::findOrFail($pedido_id);
        $productos = Producto::all();

        return View::make('detalles_pedido.create', compact('pedido', 'productos'));
    }

    // Guardar un nuevo detalle de pedido
    public function store(Request $request, $pedido_id)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,producto_id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $detalle = new DetallePedido();
        $detalle->pedido_id = $pedido_id;
        $detalle->producto_id = $request->producto_id;
        $detalle->cantidad = $request->cantidad;
        $detalle->precio_unitario = $request->precio_unitario;
        $detalle->save();

        return Redirect::route('detalles_pedido.index', $pedido_id)->with('success', 'Detalle de pedido agregado correctamente.');
    }

    // Mostrar formulario para editar un detalle existente
    public function edit($id)
    {
        $detalle = DetallePedido::findOrFail($id);
        $pedido = $detalle->pedido;
        $productos = Producto::all();

        return View::make('detalles_pedido.edit', compact('detalle', 'pedido', 'productos'));
    }

    // Actualizar detalle de pedido
    public function update(Request $request, $id)
    {
        $detalle = DetallePedido::findOrFail($id);

        $request->validate([
            'producto_id' => 'required|exists:productos,producto_id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $detalle->producto_id = $request->producto_id;
        $detalle->cantidad = $request->cantidad;
        $detalle->precio_unitario = $request->precio_unitario;
        $detalle->save();

        return Redirect::route('detalles_pedido.index', $detalle->pedido_id)->with('success', 'Detalle de pedido actualizado correctamente.');
    }

    // Eliminar un detalle de pedido
    public function destroy($id)
    {
        $detalle = DetallePedido::findOrFail($id);
        $pedido_id = $detalle->pedido_id;
        $detalle->delete();

        return Redirect::route('detalles_pedido.index', $pedido_id)->with('success', 'Detalle de pedido eliminado correctamente.');
    }
}
