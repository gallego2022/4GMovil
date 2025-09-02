<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LandingService
{
    /**
     * Obtener datos para la página de inicio
     */
    public function getHomePageData(): array
    {
        try {
            // Productos destacados (nuevos, con stock, activos)
            $productosDestacados = Producto::with(['categoria', 'marca', 'imagenes', 'resenas.usuario'])
                ->where('estado', 'nuevo')
                ->where('stock', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(12)
                ->get();

            // Categorías principales
            $categorias = Categoria::withCount('productos')
                ->having('productos_count', '>', 0)
                ->orderBy('productos_count', 'desc')
                ->limit(6)
                ->get();

            // Marcas principales
            $marcas = Marca::withCount('productos')
                ->having('productos_count', '>', 0)
                ->orderBy('productos_count', 'desc')
                ->limit(6)
                ->get();

            // Productos populares
            $productosPopulares = Producto::with(['categoria', 'marca', 'imagenes', 'resenas.usuario'])
                ->where('estado', 'nuevo')
                ->where('stock', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();

            // Variable $productos para compatibilidad con la vista existente
            $productos = $productosDestacados;

            return [
                'productos' => $productos,
                'productosDestacados' => $productosDestacados,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'productosPopulares' => $productosPopulares
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener datos de la página de inicio: ' . $e->getMessage());
            
            // En caso de error, retornar datos vacíos
            return [
                'productos' => collect(),
                'productosDestacados' => collect(),
                'categorias' => collect(),
                'marcas' => collect(),
                'productosPopulares' => collect()
            ];
        }
    }

    /**
     * Obtener datos para el catálogo completo
     */
    public function getCatalogData(Request $request): array
    {
        try {
            $query = Producto::with(['categoria', 'marca', 'imagenes', 'resenas.usuario', 'variantes']);

            // Aplicar filtros
            $this->applyCatalogFilters($query, $request);

            // Aplicar ordenamiento
            $this->applyCatalogSorting($query, $request);

            // Obtener productos paginados
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

            return [
                'productos' => $productos,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'precioMinimo' => $precioMinimo,
                'precioMaximo' => $precioMaximo
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener datos del catálogo: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener productos filtrados
     */
    public function getFilteredProducts(Request $request): array
    {
        try {
            Log::info('ProductosFiltrados - Request recibido', [
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
            $this->applyAdvancedFilters($query, $request);

            // Aplicar ordenamiento
            $this->applyCatalogSorting($query, $request);

            // Paginación con parámetros de la URL preservados
            $productos = $query->paginate(8)->appends($request->query());

            Log::info('ProductosFiltrados - Resultados', [
                'total_productos' => $productos->total(),
                'productos_en_pagina' => $productos->count(),
                'pagina_actual' => $productos->currentPage(),
                'ultima_pagina' => $productos->lastPage()
            ]);

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

            return [
                'productos' => $productos,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'precioMinimo' => $precioMinimo,
                'precioMaximo' => $precioMaximo
            ];

        } catch (\Exception $e) {
            Log::error('Error al obtener productos filtrados: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Aplicar filtros básicos del catálogo
     */
    private function applyCatalogFilters($query, Request $request): void
    {
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
    }

    /**
     * Aplicar filtros avanzados
     */
    private function applyAdvancedFilters($query, Request $request): void
    {
        // Filtros básicos
        $this->applyCatalogFilters($query, $request);

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
    }

    /**
     * Aplicar ordenamiento
     */
    private function applyCatalogSorting($query, Request $request): void
    {
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
    }

    /**
     * Generar HTML de productos para AJAX
     */
    public function generateProductsHtml($productos): string
    {
        try {
            return view('components.productos-grid', compact('productos'))->render();
        } catch (\Exception $e) {
            Log::error('Error al generar HTML de productos: ' . $e->getMessage());
            throw $e;
        }
    }
}
