<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Mostrar la página de inicio con vista previa de productos
     */
    public function index()
    {
        try {
            // Obtener productos destacados (nuevos, con stock, activos)
            $productosDestacados = Producto::with(['categoria', 'marca', 'imagenes', 'resenas.usuario'])
                ->where('estado', 'nuevo')
                ->where('stock', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(12)
                ->get();

            // Obtener categorías principales
            $categorias = Categoria::withCount('productos')
                ->having('productos_count', '>', 0)
                ->orderBy('productos_count', 'desc')
                ->limit(6)
                ->get();

            // Obtener marcas principales
            $marcas = Marca::withCount('productos')
                ->having('productos_count', '>', 0)
                ->orderBy('productos_count', 'desc')
                ->limit(6)
                ->get();

                         // Obtener productos más vendidos (por ahora los más recientes)
             $productosPopulares = Producto::with(['categoria', 'marca', 'imagenes', 'resenas.usuario'])
                 ->where('estado', 'nuevo')
                 ->where('stock', '>', 0)
                 ->orderBy('created_at', 'desc')
                 ->limit(4)
                 ->get();

             // Variable $productos para compatibilidad con la vista existente
             $productos = Producto::with(['categoria', 'marca', 'imagenes', 'resenas.usuario'])
                 ->where('estado', 'nuevo')
                 ->where('stock', '>', 0)
                 ->orderBy('created_at', 'desc')
                 ->limit(12)
                 ->get();
                         return view('pages.landing.index', compact(
                         'productos', // Agregar esta variable para compatibilidad
                         'productosDestacados',
                         'categorias',
                         'marcas',
                         'productosPopulares'
                     ));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en LandingController@index: ' . $e->getMessage());
            
                  // En caso de error, mostrar vista con datos vacíos
                    return view('pages.landing.index', [
                        'productos' => collect(), // Agregar esta variable
                        'productosDestacados' => collect(),
                        'categorias' => collect(),
                        'marcas' => collect(),
                        'productosPopulares' => collect()
                    ]);
        }
    }

    /**
     * Mostrar todos los productos (catálogo completo) con filtros básicos
     */
    public function catalogo(Request $request)
    {
        try {
            $query = Producto::with(['categoria', 'marca', 'imagenes', 'resenas.usuario', 'variantes']);

            // Filtro por estado del producto
            if ($request->filled('estado')) {
                $estados = is_array($request->estado) ? $request->estado : [$request->estado];
                $query->whereIn('estado', $estados);
            }

            // Filtro por categoría
            if ($request->filled('categoria') && $request->categoria !== 'all') {
                $query->where('categoria_id', $request->categoria);
            }

            // Filtro por marca
            if ($request->filled('marca') && $request->marca !== 'all') {
                $query->where('marca_id', $request->marca);
            }

            // Filtro por rango de precios
            if ($request->filled('precio_min') && $request->precio_min > 0) {
                $query->where('precio', '>=', $request->precio_min);
            }

            if ($request->filled('precio_max') && $request->precio_max > 0) {
                $query->where('precio', '<=', $request->precio_max);
            }

            // Búsqueda por nombre del producto
            if ($request->filled('buscar')) {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('nombre_producto', 'LIKE', "%{$buscar}%")
                      ->orWhereHas('categoria', function($cat) use ($buscar) {
                          $cat->where('nombre_categoria', 'LIKE', "%{$buscar}%");
                      })
                      ->orWhereHas('marca', function($marca) use ($buscar) {
                          $marca->where('nombre_marca', 'LIKE', "%{$buscar}%");
                      });
                });
            }

            // Ordenamiento
            $orden = $request->get('orden', 'recommended');
            switch ($orden) {
                case 'price_low':
                    $query->orderBy('precio', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('precio', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'name':
                    $query->orderBy('nombre_producto', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            // Obtener productos paginados con parámetros de la URL
            $productos = $query->paginate(8)->appends($request->query());

            // Obtener datos para filtros
            $categorias = Categoria::withCount('productos')
                ->having('productos_count', '>', 0)
                ->orderBy('nombre')
                ->get();

            $marcas = Marca::withCount('productos')
                ->having('productos_count', '>', 0)
                ->orderBy('nombre')
                ->get();

            // Obtener rango de precios para el slider
            $precioMinimo = Producto::min('precio') ?? 100000;
            $precioMaximo = Producto::max('precio') ?? 5000000;

            return view('pages.landing.productos', compact(
                'productos',
                'categorias',
                'marcas',
                'precioMinimo',
                'precioMaximo'
            ));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en LandingController@catalogo: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el catálogo: ' . $e->getMessage());
        }
    }

    /**
     * Obtener productos filtrados vía AJAX o directo
     */
    public function productosFiltrados(Request $request)
    {
        try {
            // Log de debugging
            \Illuminate\Support\Facades\Log::info('ProductosFiltrados - Request recibido', [
                'all_data' => $request->all(),
                'especificaciones' => $request->get('especificaciones'),
                'categoria' => $request->get('categoria'),
                'marca' => $request->get('marca'),
                'estado' => $request->get('estado'),
                'buscar' => $request->get('buscar'),
                'orden' => $request->get('orden')
            ]);
            $query = Producto::with(['categoria', 'marca', 'imagenes', 'resenas.usuario', 'especificaciones.especificacionCategoria', 'variantes']);

            // Aplicar filtros
            if ($request->filled('categoria') && $request->categoria !== 'all') {
                $query->where('categoria_id', $request->categoria);
            }

            if ($request->filled('marca') && $request->marca !== 'all') {
                $query->where('marca_id', $request->marca);
            }

            if ($request->filled('estado') && !empty($request->estado)) {
                $estados = is_array($request->estado) ? $request->estado : [$request->estado];
                if (!empty($estados)) {
                    $query->whereIn('estado', $estados);
                }
            }

            if ($request->filled('precio_min') && $request->precio_min > 0) {
                $query->where('precio', '>=', $request->precio_min);
            }

            if ($request->filled('precio_max') && $request->precio_max > 0) {
                $query->where('precio', '<=', $request->precio_max);
            }

            if ($request->filled('buscar') && !empty($request->buscar)) {
                $query->where('nombre_producto', 'like', '%' . $request->buscar . '%');
            }

            // Filtros de especificaciones dinámicas
            if ($request->filled('especificaciones') && is_array($request->especificaciones)) {
                foreach ($request->especificaciones as $tipoEspecificacion => $valores) {
                    if (is_array($valores) && !empty($valores)) {
                        $query->whereHas('especificaciones.especificacionCategoria', function ($subQuery) use ($tipoEspecificacion, $valores) {
                            $subQuery->where('nombre_campo', $tipoEspecificacion)
                                    ->whereIn('valor', $valores);
                        });
                    }
                }
            }

            // Ordenamiento
            $orden = $request->get('orden', 'recommended');
            switch ($orden) {
                case 'price_low':
                    $query->orderBy('precio', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('precio', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'name':
                    $query->orderBy('nombre_producto', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            // Paginación con parámetros de la URL preservados
            $productos = $query->paginate(8)->appends($request->query());

            // Log de resultados
            \Illuminate\Support\Facades\Log::info('ProductosFiltrados - Resultados', [
                'total_productos' => $productos->total(),
                'productos_en_pagina' => $productos->count(),
                'pagina_actual' => $productos->currentPage(),
                'ultima_pagina' => $productos->lastPage()
            ]);

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax() || $request->wantsJson()) {
                try {
                    $html = view('components.productos-grid', compact('productos'))->render();
                    
                    return response()->json([
                        'success' => true,
                        'productos' => $productos,
                        'html' => $html
                    ]);
                } catch (\Exception $viewError) {
                    \Illuminate\Support\Facades\Log::error('Error al renderizar vista en productosFiltrados: ' . $viewError->getMessage());
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al generar la vista de productos: ' . $viewError->getMessage()
                    ], 500);
                }
            }

            // Obtener datos para filtros (solo para peticiones directas)
            $categorias = Categoria::withCount('productos')
                ->having('productos_count', '>', 0)
                ->orderBy('nombre')
                ->get();

            $marcas = Marca::withCount('productos')
                ->having('productos_count', '>', 0)
                ->orderBy('nombre')
                ->get();

            // Obtener rango de precios para el slider
            $precioMinimo = Producto::min('precio') ?? 100000;
            $precioMaximo = Producto::max('precio') ?? 5000000;

            // Si es una petición directa, devolver la vista completa
            return view('pages.landing.productos', compact(
                'productos',
                'categorias',
                'marcas',
                'precioMinimo',
                'precioMaximo'
            ));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en productosFiltrados: ' . $e->getMessage());
            
            // Si es AJAX, devolver error JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al filtrar productos: ' . $e->getMessage()
                ], 500);
            }
            
            // Si es directo, redirigir con error
            return back()->with('error', 'Error al filtrar productos: ' . $e->getMessage());
        }
    }
}
