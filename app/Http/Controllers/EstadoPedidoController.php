<?php

namespace App\Http\Controllers;

use App\Models\EstadoPedido;
use Illuminate\Http\Request;

class EstadoPedidoController extends Controller
{
    public function index()
    {
        $estados = EstadoPedido::all();
        return view('estados_pedido.index', compact('estados'));
    }

    public function create()
    {
        return view('estados_pedido.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        EstadoPedido::create($request->all());

        return redirect()->route('estados_pedido.index')->with('success', 'Estado de pedido creado correctamente.');
    }

    public function show($id)
    {
        $estado = EstadoPedido::findOrFail($id);
        return view('estados_pedido.show', compact('estado'));
    }

    public function edit($id)
    {
        $estado = EstadoPedido::findOrFail($id);
        return view('estados_pedido.edit', compact('estado'));
    }

    public function update(Request $request, $id)
    {
        $estado = EstadoPedido::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $estado->update($request->all());

        return redirect()->route('estados_pedido.index')->with('success', 'Estado de pedido actualizado correctamente.');
    }

    public function destroy($id)
    {
        $estado = EstadoPedido::findOrFail($id);
        $estado->delete();

        return redirect()->route('estados_pedido.index')->with('success', 'Estado de pedido eliminado correctamente.');
    }
}
