<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\WebController;
use App\Models\Resena;
use App\Models\Producto;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ResenaController extends WebController
{
    /**
     * Mostrar todas las reseñas en el dashboard admin
     */
    public function index(Request $request)
    {
        try {
            $query = Resena::with(['usuario', 'producto'])
                ->orderBy('created_at', 'desc');

            // Filtros
            if ($request->filled('producto_id')) {
                $query->where('producto_id', $request->producto_id);
            }

            if ($request->filled('calificacion')) {
                $query->where('calificacion', $request->calificacion);
            }

            if ($request->filled('activa')) {
                $query->where('activa', $request->activa === '1');
            }

            if ($request->filled('verificada')) {
                $query->where('verificada', $request->verificada === '1');
            }

            // Búsqueda por comentario
            if ($request->filled('search')) {
                $query->where('comentario', 'like', '%' . $request->search . '%');
            }

            $resenas = $query->paginate(15);

            // Estadísticas
            $stats = [
                'total' => Resena::count(),
                'activas' => Resena::where('activa', true)->count(),
                'verificadas' => Resena::where('verificada', true)->count(),
                'promedio' => Resena::where('activa', true)->avg('calificacion'),
                'por_calificacion' => Resena::where('activa', true)
                    ->select('calificacion', DB::raw('count(*) as total'))
                    ->groupBy('calificacion')
                    ->orderBy('calificacion', 'desc')
                    ->get(),
            ];

            // Productos para filtro
            $productos = Producto::select('producto_id', 'nombre_producto')
                ->orderBy('nombre_producto')
                ->get();

            return View::make('pages.admin.resenas.index', compact('resenas', 'stats', 'productos'));
        } catch (\Exception $e) {
            Log::error('Error en ResenaController@index: ' . $e->getMessage());
            return Redirect::back()->with('mensaje', 'Error al cargar las reseñas.')->with('tipo', 'error');
        }
    }

    /**
     * Mostrar detalles de una reseña
     */
    public function show($id)
    {
        try {
            $resena = Resena::with(['usuario', 'producto'])->findOrFail($id);

            return View::make('pages.admin.resenas.show', compact('resena'));
        } catch (\Exception $e) {
            Log::error('Error en ResenaController@show: ' . $e->getMessage());
            return Redirect::route('admin.resenas.index')->with('mensaje', 'Reseña no encontrada.')->with('tipo', 'error');
        }
    }

    /**
     * Toggle estado activo/inactivo de una reseña
     */
    public function toggleActiva($id)
    {
        try {
            $resena = Resena::findOrFail($id);
            $resena->activa = !$resena->activa;
            $resena->save();

            $mensaje = $resena->activa ? 'Reseña activada exitosamente.' : 'Reseña desactivada exitosamente.';

            return Redirect::back()->with('mensaje', $mensaje)->with('tipo', 'success');
        } catch (\Exception $e) {
            Log::error('Error en ResenaController@toggleActiva: ' . $e->getMessage());
            return Redirect::back()->with('mensaje', 'Error al actualizar el estado de la reseña.')->with('tipo', 'error');
        }
    }

    /**
     * Marcar reseña como verificada
     */
    public function verificar($id)
    {
        try {
            $resena = Resena::findOrFail($id);
            $resena->verificada = true;
            $resena->save();

            return Redirect::back()->with('mensaje', 'Reseña verificada exitosamente.')->with('tipo', 'success');
        } catch (\Exception $e) {
            Log::error('Error en ResenaController@verificar: ' . $e->getMessage());
            return Redirect::back()->with('mensaje', 'Error al verificar la reseña.')->with('tipo', 'error');
        }
    }

    /**
     * Eliminar reseña
     */
    public function destroy($id)
    {
        try {
            $resena = Resena::findOrFail($id);
            $resena->delete();

            return Redirect::route('admin.resenas.index')->with('mensaje', 'Reseña eliminada exitosamente.')->with('tipo', 'success');
        } catch (\Exception $e) {
            Log::error('Error en ResenaController@destroy: ' . $e->getMessage());
            return Redirect::back()->with('mensaje', 'Error al eliminar la reseña.')->with('tipo', 'error');
        }
    }
}

