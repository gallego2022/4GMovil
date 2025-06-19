<?php

namespace App\Http\Controllers;

use App\Models\ImagenProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImagenProductoController extends Controller
{
    // Mostrar todas las imágenes de un producto específico
    public function index($producto_id)
    {
        $producto = Producto::findOrFail($producto_id);
        $imagenes = $producto->imagenes;  // Relación hasMany en el modelo Producto

        return view('imagenes.index', compact('producto', 'imagenes'));
    }

    // Mostrar formulario para subir imagen nueva
    public function create($producto_id)
    {
        $producto = Producto::findOrFail($producto_id);
        return view('imagenes.create', compact('producto'));
    }

    // Guardar imagen nueva
    public function store(Request $request, $producto_id)
    {
        $request->validate([
            'ruta_imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $producto = Producto::findOrFail($producto_id);

        $ruta = $request->file('ruta_imagen')->store('productos', 'public');

        $imagen = new ImagenProducto();
        $imagen->producto_id = $producto->producto_id;
        $imagen->ruta_imagen = $ruta;
        $imagen->save();

        return redirect()->route('imagenes.index', $producto_id)
                         ->with('success', 'Imagen agregada correctamente.');
    }

    // Eliminar imagen
    public function destroy($id)
    {
        $imagen = ImagenProducto::findOrFail($id);

        // Eliminar archivo físico solo si existe
        if ($imagen->ruta_imagen && Storage::disk('public')->exists($imagen->ruta_imagen)) {
            Storage::disk('public')->delete($imagen->ruta_imagen);
        }

        $imagen->delete();

        return back()->with('success', 'Imagen eliminada correctamente.');
    }
}
