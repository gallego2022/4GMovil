<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Base\WebController;
use App\Services\LandingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LandingController extends WebController
{
    protected $landingService;

    public function __construct(LandingService $landingService)
    {
        $this->landingService = $landingService;
    }

    /**
     * Mostrar la página de inicio con vista previa de productos
     */
    public function index()
    {
        try {
            $data = $this->landingService->getHomePageData();
            
            return view('pages.landing.index', $data);

        } catch (\Exception $e) {
            Log::error('Error en LandingController@index: ' . $e->getMessage());
            
            // En caso de error, mostrar vista con datos vacíos
            return view('pages.landing.index', [
                'productos' => collect(),
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
            $data = $this->landingService->getCatalogData($request);
            
            return view('pages.landing.productos', $data);

        } catch (\Exception $e) {
            Log::error('Error en LandingController@catalogo: ' . $e->getMessage());
            return $this->backError('Error al cargar el catálogo: ' . $e->getMessage());
        }
    }

    /**
     * Obtener productos filtrados vía AJAX o directo
     */
    public function productosFiltrados(Request $request)
    {
        try {
            $data = $this->landingService->getFilteredProducts($request);
            
            // Si es una petición AJAX, devolver JSON
            if ($request->ajax() || $request->wantsJson()) {
                try {
                    $html = $this->landingService->generateProductsHtml($data['productos']);
                    
                    return response()->json([
                        'success' => true,
                        'productos' => $data['productos'],
                        'html' => $html
                    ]);
                } catch (\Exception $viewError) {
                    Log::error('Error al renderizar vista en productosFiltrados: ' . $viewError->getMessage());
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al generar la vista de productos: ' . $viewError->getMessage()
                    ], 500);
                }
            }

            // Si es una petición directa, devolver la vista completa
            return view('pages.landing.productos', $data);

        } catch (\Exception $e) {
            Log::error('Error en productosFiltrados: ' . $e->getMessage());
            
            // Si es AJAX, devolver error JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al filtrar productos: ' . $e->getMessage()
                ], 500);
            }
            
            // Si es directo, redirigir con error
            return $this->backError('Error al filtrar productos: ' . $e->getMessage());
        }
    }
}
