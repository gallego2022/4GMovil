<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::all();
        return view('pages.admin.marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('pages.admin.marcas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_marca' => 'required|string|max:255|unique:marcas,nombre_marca',
        ]);

       Marca::create($request->only('nombre_marca'));


        return redirect()->route('marcas.index')->with('success', 'Marca creada correctamente.');
    }

    public function edit($id)
    {
        $marca = Marca::findOrFail($id);
        return view('pages.admin.marcas.edit', compact('marca'));
    }

    public function update(Request $request, $id)
    {
        $marca = Marca::findOrFail($id);

        $request->validate([
            'nombre_marca' => 'required|string|max:255|unique:marcas,nombre_marca,' . $id . ',marca_id',
        ]);

        $marca->update([
            'nombre_marca' => $request->nombre_marca,
        ]);

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy($id)
    {
        $marca = Marca::findOrFail($id);
        $marca->delete();

        return redirect()->route('marcas.index')->with('success', 'Marca eliminada correctamente.');
    }
}
