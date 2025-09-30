<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pago;
use App\Models\Pedido;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\WebController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class PagoController extends WebController
{
    public function index()
    {
        $pagos = Pago::with(['pedido', 'metodo'])->get();
        return View::make('pagos.index', compact('pagos'));
    }

    public function create()
    {
        $pedidos = Pedido::all();
        $metodos = MetodoPago::all();
        return View::make('pagos.create', compact('pedidos', 'metodos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,pedido_id',
            'monto' => 'required|numeric|min:0',
            'metodo_id' => 'required|exists:metodos_pago,metodo_id',
            'fecha_pago' => 'required|date',
        ]);

        Pago::create($request->all());

        return Redirect::route('pagos.index')->with('success', 'Pago registrado correctamente.');
    }

    public function show($id)
    {
        $pago = Pago::with(['pedido', 'metodo'])->findOrFail($id);
        return View::make('pagos.show', compact('pago'));
    }

    public function edit($id)
    {
        $pago = Pago::findOrFail($id);
        $pedidos = Pedido::all();
        $metodos = MetodoPago::all();

        return View::make('pagos.edit', compact('pago', 'pedidos', 'metodos'));
    }

    public function update(Request $request, $id)
    {
        $pago = Pago::findOrFail($id);

        $request->validate([
            'pedido_id' => 'required|exists:pedidos,pedido_id',
            'monto' => 'required|numeric|min:0',
            'metodo_id' => 'required|exists:metodos_pago,metodo_id',
            'fecha_pago' => 'required|date',
        ]);

        $pago->update($request->all());

        return Redirect::route('pagos.index')->with('success', 'Pago actualizado correctamente.');
    }

    public function destroy($id)
    {
        $pago = Pago::findOrFail($id);
        $pago->delete();

        return Redirect::route('pagos.index')->with('success', 'Pago eliminado correctamente.');
    }
}
