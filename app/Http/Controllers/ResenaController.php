<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use App\Models\Usuario;
use App\Models\Producto;
use Illuminate\Http\Request;

class ResenaController extends Controller
{
    public function index()
    {
        $resenas = Resena::with(['usuario', 'producto'])->get();
        return view('resenas.index', compact('resenas'));
    }

    public function create()
    {
        $usuarios = Usuario::all();
        $productos = Producto::all();
        return view('resenas.create', compact('usuarios', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,usuario_id',
            'producto_id' => 'required|exists:productos,producto_id',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string',
        ]);

        Resena::create($request->all());

        return redirect()->route('resenas.index')->with('success', 'Reseña creada correctamente.');
    }

    public function show($id)
    {
        $resena = Resena::with(['usuario', 'producto'])->findOrFail($id);
        return view('resenas.show', compact('resena'));
    }

    public function edit($id)
    {
        $resena = Resena::findOrFail($id);
        $usuarios = Usuario::all();
        $productos = Producto::all();
        return view('resenas.edit', compact('resena', 'usuarios', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $resena = Resena::findOrFail($id);

        $request->validate([
            'usuario_id' => 'required|exists:usuarios,usuario_id',
            'producto_id' => 'required|exists:productos,producto_id',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string',
        ]);

        $resena->update($request->all());

        return redirect()->route('resenas.index')->with('success', 'Reseña actualizada correctamente.');
    }

    public function destroy($id)
    {
        $resena = Resena::findOrFail($id);
        $resena->delete();

        return redirect()->route('resenas.index')->with('success', 'Reseña eliminada correctamente.');
    }
}
