<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OptimizedStockAlertService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OptimizedStockAlertController extends Controller
{
    protected $stockAlertService;

    public function __construct(OptimizedStockAlertService $stockAlertService)
    {
        $this->stockAlertService = $stockAlertService;
    }

    /**
     * Muestra el dashboard de alertas optimizado
     */
    public function dashboard()
    {
        $alertas = $this->stockAlertService->getOptimizedStockAlerts();
        
        return view('pages.admin.inventario.alertas-optimizadas', compact('alertas'));
    }

    /**
     * Obtiene las variantes problemáticas de un producto específico (AJAX)
     */
    public function getVariantesProblematicas(Request $request): JsonResponse
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,producto_id'
        ]);

        $variantes = $this->stockAlertService->getVariantesProblematicas($request->producto_id);

        return response()->json([
            'success' => true,
            'variantes' => $variantes->map(function ($item) {
                return [
                    'variante_id' => $item['variante']->variante_id,
                    'nombre' => $item['variante']->nombre,
                    'codigo_color' => $item['variante']->codigo_color,
                    'precio_adicional' => $item['variante']->precio_adicional,
                    'tipo_alerta' => $item['tipo_alerta'],
                    'stock_actual' => $item['stock_actual'],
                    'stock_minimo' => $item['stock_minimo'],
                    'porcentaje' => $item['porcentaje']
                ];
            })
        ]);
    }

    /**
     * Obtiene estadísticas de alertas (AJAX)
     */
    public function getEstadisticas(): JsonResponse
    {
        $alertas = $this->stockAlertService->getOptimizedStockAlerts();
        
        return response()->json([
            'success' => true,
            'estadisticas' => [
                'productos_criticos' => $alertas['productos_criticos']->count(),
                'productos_stock_bajo' => $alertas['productos_stock_bajo']->count(),
                'variantes_agotadas' => $alertas['variantes_agotadas']->count(),
                'total_alertas' => $alertas['total_alertas']
            ]
        ]);
    }
}
