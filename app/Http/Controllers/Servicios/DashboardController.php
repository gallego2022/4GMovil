<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Base\WebController;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;

class DashboardController extends WebController
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    public function index(Request $request)
    {
        $startTime = microtime(true);
        
        try {
            $data = $this->dashboardService->getDashboardData($request);
            
            if ($data['success']) {
                $loadTime = round((microtime(true) - $startTime) * 1000, 2);
                
                return View::make('pages.admin.index', [
                    'totalProductos' => $data['basicStats']['totalProductos'],
                    'totalCategorias' => $data['basicStats']['totalCategorias'],
                    'totalMarcas' => $data['basicStats']['totalMarcas'],
                    'total_variantes' => $data['basicStats']['total_variantes'],
                    'usuarios' => $data['basicStats']['usuarios'],
                    'ultimosProductos' => $data['recentProducts'],
                    'webhookStats' => $data['webhookStats'],
                    'pedidoStats' => $data['pedidoStats'],
                    'recentWebhooks' => $data['filteredWebhooks'],
                    'filters' => $data['filters'],
                    'loadTime' => $loadTime,
                    'cached_at' => $data['cached_at'] ?? null,
                    'is_cached' => isset($data['cached_at'])
                ]);
            }

            // En caso de error, mostrar vista con datos vacíos
            return $this->renderFallbackView();

        } catch (\Exception $e) {
            Log::error('Error en DashboardController@index: ' . $e->getMessage());
            return $this->renderFallbackView();
        }
    }

    /**
     * Renderizar vista de fallback
     */
    private function renderFallbackView()
    {
        return View::make('pages.admin.index', [
            'totalProductos' => 0,
            'totalCategorias' => 0,
            'totalMarcas' => 0,
            'total_variantes' => 0,
            'usuarios' => 0,
            'ultimosProductos' => Collection::make(),
            'webhookStats' => [],
            'pedidoStats' => [],
            'recentWebhooks' => Collection::make(),
            'filters' => [],
            'loadTime' => 0,
            'cached_at' => null,
            'is_cached' => false
        ]);
    }


}
