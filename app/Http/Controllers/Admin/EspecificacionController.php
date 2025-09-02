<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EspecificacionCategoria;
use App\Models\Categoria;

class EspecificacionController extends Controller
{
    /**
     * Mostrar el listado de especificaciones
     */
    public function index()
    {
        try {
            $especificaciones = EspecificacionCategoria::with('categoria')
                ->orderBy('categoria_id')
                ->orderBy('orden')
                ->get();

            return view('pages.admin.especificaciones.index', compact('especificaciones'));
        } catch (\Exception $e) {
            Log::error('Error al cargar especificaciones: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar las especificaciones');
        }
    }

    /**
     * Mostrar el formulario para crear una nueva especificación
     */
    public function create()
    {
        try {
            $categorias = Categoria::where('activo', true)
                ->orderBy('nombre')
                ->get();

            return view('pages.admin.especificaciones.create', compact('categorias'));
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de creación: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario');
        }
    }

    /**
     * Almacenar una nueva especificación
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'categoria_id' => 'required|exists:categorias,categoria_id',
                'nombre_campo' => 'required|string|max:100',
                'etiqueta' => 'required|string|max:100',
                'tipo_campo' => 'required|in:text,textarea,number,select,checkbox,radio,date,email,url',
                'opciones' => 'nullable|string|max:500',
                'unidad' => 'nullable|string|max:50',
                'descripcion' => 'nullable|string|max:500',
                'requerido' => 'boolean',
                'orden' => 'nullable|integer|min:0',
            ]);

            // Generar nombre_campo único para la categoría
            $nombreCampo = $request->nombre_campo;
            $categoriaId = $request->categoria_id;
            
            // Verificar si ya existe un campo con ese nombre en la categoría
            $existe = EspecificacionCategoria::where('categoria_id', $categoriaId)
                ->where('nombre_campo', $nombreCampo)
                ->exists();
                
            if ($existe) {
                return back()->withInput()->withErrors([
                    'nombre_campo' => 'Ya existe un campo con ese nombre en esta categoría'
                ]);
            }

            // Si no se especifica orden, usar el siguiente disponible
            if (!$request->orden) {
                $ultimoOrden = EspecificacionCategoria::where('categoria_id', $categoriaId)
                    ->max('orden');
                $request->merge(['orden' => ($ultimoOrden ?? 0) + 1]);
            }

            $especificacion = EspecificacionCategoria::create($request->all());

            Log::info('Especificación creada exitosamente', [
                'id' => $especificacion->especificacion_id,
                'categoria' => $especificacion->categoria->nombre ?? 'N/A',
                'campo' => $especificacion->nombre_campo
            ]);

            return redirect()->route('admin.especificaciones.index')
                ->with('success', 'Especificación creada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear especificación: ' . $e->getMessage());
            return back()->withInput()->withErrors([
                'error' => 'Error al crear la especificación: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mostrar una especificación específica
     */
    public function show($id)
    {
        try {
            $especificacion = EspecificacionCategoria::with('categoria')->findOrFail($id);
            
            return view('pages.admin.especificaciones.show', compact('especificacion'));
        } catch (\Exception $e) {
            Log::error('Error al cargar especificación: ' . $e->getMessage());
            return back()->with('error', 'Especificación no encontrada');
        }
    }

    /**
     * Mostrar el formulario para editar una especificación
     */
    public function edit($id)
    {
        try {
            $especificacion = EspecificacionCategoria::findOrFail($id);
            $categorias = Categoria::where('activo', true)
                ->orderBy('nombre')
                ->get();

            return view('pages.admin.especificaciones.edit', compact('especificacion', 'categorias'));
        } catch (\Exception $e) {
            Log::error('Error al cargar especificación para editar: ' . $e->getMessage());
            return back()->with('error', 'Especificación no encontrada');
        }
    }

    /**
     * Actualizar una especificación existente
     */
    public function update(Request $request, $id)
    {
        try {
            $especificacion = EspecificacionCategoria::findOrFail($id);

            $request->validate([
                'categoria_id' => 'required|exists:categorias,categoria_id',
                'nombre_campo' => 'required|string|max:100',
                'etiqueta' => 'required|string|max:100',
                'tipo_campo' => 'required|in:text,textarea,number,select,checkbox,radio,date,email,url',
                'opciones' => 'nullable|string|max:500',
                'unidad' => 'nullable|string|max:50',
                'descripcion' => 'nullable|string|max:500',
                'requerido' => 'boolean',
                'orden' => 'nullable|integer|min:0',
            ]);

            // Verificar si el nombre_campo ya existe en la misma categoría (excluyendo la actual)
            $existe = EspecificacionCategoria::where('categoria_id', $request->categoria_id)
                ->where('nombre_campo', $request->nombre_campo)
                ->where('especificacion_id', '!=', $id)
                ->exists();
                
            if ($existe) {
                return back()->withInput()->withErrors([
                    'nombre_campo' => 'Ya existe un campo con ese nombre en esta categoría'
                ]);
            }

            $especificacion->update($request->all());

            Log::info('Especificación actualizada exitosamente', [
                'id' => $especificacion->especificacion_id,
                'categoria' => $especificacion->categoria->nombre ?? 'N/A',
                'campo' => $especificacion->nombre_campo
            ]);

            return redirect()->route('admin.especificaciones.index')
                ->with('success', 'Especificación actualizada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar especificación: ' . $e->getMessage());
            return back()->withInput()->withErrors([
                'error' => 'Error al actualizar la especificación: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar una especificación
     */
    public function destroy($id)
    {
        try {
            $especificacion = EspecificacionCategoria::findOrFail($id);
            
            // Verificar si hay productos usando esta especificación
            $productosConEspecificacion = DB::table('especificaciones_producto')
                ->where('especificacion_id', $id)
                ->count();
                
            if ($productosConEspecificacion > 0) {
                return back()->with('error', 'No se puede eliminar esta especificación porque está siendo utilizada por productos');
            }

            $especificacion->delete();

            Log::info('Especificación eliminada exitosamente', [
                'id' => $id,
                'campo' => $especificacion->nombre_campo ?? 'N/A'
            ]);

            return redirect()->route('admin.especificaciones.index')
                ->with('eliminado', 'ok');

        } catch (\Exception $e) {
            Log::error('Error al eliminar especificación: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la especificación');
        }
    }

    /**
     * Obtener especificaciones por categoría (API)
     */
    public function getByCategoria($categoriaId)
    {
        try {
            $especificaciones = EspecificacionCategoria::where('categoria_id', $categoriaId)
                ->where('activo', true)
                ->orderBy('orden')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $especificaciones
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener especificaciones por categoría: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener especificaciones'
            ], 500);
        }
    }

    /**
     * Cambiar el estado activo/inactivo de una especificación
     */
    public function toggleEstado($id)
    {
        try {
            $especificacion = EspecificacionCategoria::findOrFail($id);
            $especificacion->activo = !$especificacion->activo;
            $especificacion->save();

            $estado = $especificacion->activo ? 'activada' : 'desactivada';

            Log::info('Estado de especificación cambiado', [
                'id' => $id,
                'estado' => $estado
            ]);

            return response()->json([
                'success' => true,
                'message' => "Especificación {$estado} exitosamente",
                'activo' => $especificacion->activo
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de especificación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado'
            ], 500);
        }
    }

    /**
     * Reordenar especificaciones
     */
    public function reordenar(Request $request)
    {
        try {
            $request->validate([
                'especificaciones' => 'required|array',
                'especificaciones.*.id' => 'required|exists:especificaciones_categoria,especificacion_id',
                'especificaciones.*.orden' => 'required|integer|min:0'
            ]);

            foreach ($request->especificaciones as $item) {
                EspecificacionCategoria::where('especificacion_id', $item['id'])
                    ->update(['orden' => $item['orden']]);
            }

            Log::info('Especificaciones reordenadas exitosamente');

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al reordenar especificaciones: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el orden'
            ], 500);
        }
    }
}
