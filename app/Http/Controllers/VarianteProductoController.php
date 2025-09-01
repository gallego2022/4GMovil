<?php

namespace App\Http\Controllers;

use App\Models\VarianteProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VarianteProductoController extends Controller
{
    /**
     * Mostrar las variantes de un producto
     */
    public function index($productoId)
    {
        $producto = Producto::findOrFail($productoId);
        $variantes = $producto->variantes()->orderBy('orden')->get();
        
        return view('admin.variantes.index', compact('producto', 'variantes'));
    }

    /**
     * Mostrar formulario para crear nueva variante
     */
    public function create($productoId)
    {
        $producto = Producto::findOrFail($productoId);
        return view('admin.variantes.create', compact('producto'));
    }

    /**
     * Guardar nueva variante
     */
    public function store(Request $request, $productoId)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo_color' => 'nullable|string|max:7',
            'stock' => 'required|integer|min:0',
            'disponible' => 'boolean',
            'precio_adicional' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'orden' => 'nullable|integer|min:0'
        ]);

        try {
            VarianteProducto::create([
                'producto_id' => $productoId,
                'nombre' => $request->nombre,
                'codigo_color' => $request->codigo_color,
                'stock' => $request->stock,
                'disponible' => $request->has('disponible'),
                'precio_adicional' => $request->precio_adicional ?? 0,
                'descripcion' => $request->descripcion,
                'orden' => $request->orden ?? 0
            ]);

            return redirect()->route('admin.variantes.index', $productoId)
                ->with('success', 'Variante creada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear variante', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Error al crear la variante');
        }
    }

    /**
     * Mostrar formulario para editar variante
     */
    public function edit($productoId, $varianteId)
    {
        $producto = Producto::findOrFail($productoId);
        $variante = VarianteProducto::where('producto_id', $productoId)
            ->where('variante_id', $varianteId)
            ->firstOrFail();
        
        return view('admin.variantes.edit', compact('producto', 'variante'));
    }

    /**
     * Actualizar variante
     */
    public function update(Request $request, $productoId, $varianteId)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo_color' => 'nullable|string|max:7',
            'stock' => 'required|integer|min:0',
            'disponible' => 'boolean',
            'precio_adicional' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string',
            'orden' => 'nullable|integer|min:0'
        ]);

        try {
            $variante = VarianteProducto::where('producto_id', $productoId)
                ->where('variante_id', $varianteId)
                ->firstOrFail();

            $variante->update([
                'nombre' => $request->nombre,
                'codigo_color' => $request->codigo_color,
                'stock' => $request->stock,
                'disponible' => $request->has('disponible'),
                'precio_adicional' => $request->precio_adicional ?? 0,
                'descripcion' => $request->descripcion,
                'orden' => $request->orden ?? 0
            ]);

            return redirect()->route('admin.variantes.index', $productoId)
                ->with('success', 'Variante actualizada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar variante', [
                'producto_id' => $productoId,
                'variante_id' => $varianteId,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Error al actualizar la variante');
        }
    }

    /**
     * Eliminar variante
     */
    public function destroy($productoId, $varianteId)
    {
        try {
            $variante = VarianteProducto::where('producto_id', $productoId)
                ->where('variante_id', $varianteId)
                ->firstOrFail();

            $variante->delete();

            return redirect()->route('admin.variantes.index', $productoId)
                ->with('success', 'Variante eliminada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar variante', [
                'producto_id' => $productoId,
                'variante_id' => $varianteId,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Error al eliminar la variante');
        }
    }
}
