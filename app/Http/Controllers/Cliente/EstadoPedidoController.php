<?php

namespace App\Http\Controllers\Cliente;

use App\Models\EstadoPedido;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\WebController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class EstadoPedidoController extends WebController
{
    public function index()
    {
        $estados = EstadoPedido::all();
        return View::make('estados_pedido.index', compact('estados'));
    }

    public function create()
    {
        return View::make('estados_pedido.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        EstadoPedido::create($request->all());

        return Redirect::route('estados_pedido.index')->with('success', 'Estado de pedido creado correctamente.');
    }

    public function show($id)
    {
        $estado = EstadoPedido::findOrFail($id);
        return View::make('estados_pedido.show', compact('estado'));
    }

    public function edit($id)
    {
        $estado = EstadoPedido::findOrFail($id);
        return View::make('estados_pedido.edit', compact('estado'));
    }

    public function update(Request $request, $id)
    {
        $estado = EstadoPedido::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $estado->update($request->all());

        return Redirect::route('estados_pedido.index')->with('success', 'Estado de pedido actualizado correctamente.');
    }

    public function destroy($id)
    {
        $estado = EstadoPedido::findOrFail($id);
        $estado->delete();

        return Redirect::route('estados_pedido.index')->with('success', 'Estado de pedido eliminado correctamente.');
    }
}
