<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Base\WebController;
use App\Services\LandingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;

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
            $this->applyLocalization();
            $data = $this->landingService->getHomePageData();
            
            return View::make('pages.landing.index', $data);

        } catch (\Exception $e) {
            Log::error('Error en LandingController@index: ' . $e->getMessage());
            
            // En caso de error, mostrar vista con datos vacíos
            return View::make('pages.landing.index', [
                'productos' => Collection::make(),
                'productosDestacados' => Collection::make(),
                'categorias' => Collection::make(),
                'marcas' => Collection::make(),
                'productosPopulares' => Collection::make()
            ]);
        }
    }

    /**
     * Mostrar todos los productos (catálogo completo) con filtros básicos
     */
    public function catalogo(Request $request)
    {
        try {
            $this->applyLocalization();
            $data = $this->landingService->getCatalogData($request);
            
            return View::make('pages.landing.productos', $data);

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
                    
                    return Response::json([
                        'success' => true,
                        'productos' => $data['productos'],
                        'html' => $html
                    ]);
                } catch (\Exception $viewError) {
                    Log::error('Error al renderizar vista en productosFiltrados: ' . $viewError->getMessage());
                    
                    return Response::json([
                        'success' => false,
                        'message' => 'Error al generar la vista de productos: ' . $viewError->getMessage()
                    ], 500);
                }
            }

            // Si es una petición directa, devolver la vista completa
            return View::make('pages.landing.productos', $data);

        } catch (\Exception $e) {
            Log::error('Error en productosFiltrados: ' . $e->getMessage());
            
            // Si es AJAX, devolver error JSON
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => 'Error al filtrar productos: ' . $e->getMessage()
                ], 500);
            }
            
            // Si es directo, redirigir con error
            return $this->backError('Error al filtrar productos: ' . $e->getMessage());
        }
    }
}
