<?php

namespace App\Http\Controllers\Admin;

use App\Models\Marca;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\WebController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class MarcaController extends WebController
{
    public function index()
    {
        $marcas = Marca::all();
        return View::make('pages.admin.marcas.index', compact('marcas'));
    }

    public function create()
    {
        return View::make('pages.admin.marcas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre',
        ]);

       Marca::create($request->only('nombre'));


        return Redirect::route('marcas.index')->with('mensaje', 'Marca Creada')->with('tipo', 'success');
    }

    public function edit($id)
    {
        $marca = Marca::findOrFail($id);
        return View::make('pages.admin.marcas.edit', compact('marca'));
    }

    public function update(Request $request, $id)
    {
        $marca = Marca::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre,' . $id . ',marca_id',
        ]);

        $marca->update([
            'nombre' => $request->nombre,
        ]);

        return Redirect::route('marcas.index')->with('mensaje', 'Marca Actualizada')->with('tipo', 'success');
    }

    public function destroy($id)
    {
        $marca = Marca::findOrFail($id);
        $marca->delete();

        return Redirect::route('marcas.index')->with('mensaje', 'Marca Eliminada')->with('tipo', 'success');
    }
}
