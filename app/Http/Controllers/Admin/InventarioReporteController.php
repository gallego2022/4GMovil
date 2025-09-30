<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\WebController;
use App\Services\InventarioService;
use App\Models\Producto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class InventarioReporteController extends WebController
{
    protected $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Reporte general de inventario
     */
    public function general(Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : Carbon::now()->subMonth();
            $fechaFin = $request->get('fecha_fin') ? Carbon::parse($request->fecha_fin) : Carbon::now();
            
            $data = [
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'resumen' => $this->inventarioService->getResumenInventario($fechaInicio, $fechaFin),
                'productos' => Producto::activos()->orderBy('nombre_producto')->get()
            ];

            if ($request->wantsJson()) {
                return Response::json($data);
            }

            return View::make('pages.admin.inventario.reportes.general', $data);
        } catch (\Exception $e) {
            Log::error('Error en reporte general de inventario', ['error' => $e->getMessage()]);
            
            if ($request->wantsJson()) {
                return Response::json(['error' => 'Error al generar reporte'], 500);
            }

            return Redirect::back()->with('error', 'Error al generar el reporte');
        }
    }

    /**
     * Reporte de valor de inventario
     */
    public function valorInventario(Request $request)
    {
        try {
            $data = [
                'valorTotal' => $this->inventarioService->getValorTotalInventario(),
                'valorPorCategoria' => $this->inventarioService->getValorInventarioPorCategoria(),
                'valorPorMarca' => $this->inventarioService->getValorInventarioPorMarca(),
                'productos' => Producto::activos()->with(['categoria', 'marca'])->get()
            ];

            if ($request->wantsJson()) {
                return Response::json($data);
            }

            return View::make('pages.admin.inventario.reportes.valor', $data);
        } catch (\Exception $e) {
            Log::error('Error en reporte de valor de inventario', ['error' => $e->getMessage()]);
            
            if ($request->wantsJson()) {
                return Response::json(['error' => 'Error al generar reporte'], 500);
            }

            return Redirect::back()->with('error', 'Error al generar el reporte');
        }
    }

    /**
     * Reporte de rotación de inventario
     */
    public function rotacionInventario(Request $request)
    {
        try {
            $periodo = $request->get('periodo', '30'); // días por defecto
            
            $data = [
                'periodo' => $periodo,
                'rotacion' => $this->inventarioService->getRotacionInventario($periodo),
                'productosLentos' => $this->inventarioService->getProductosRotacionLenta($periodo),
                'productosRapidos' => $this->inventarioService->getProductosRotacionRapida($periodo)
            ];

            if ($request->wantsJson()) {
                return Response::json($data);
            }

            return View::make('pages.admin.inventario.reportes.rotacion', $data);
        } catch (\Exception $e) {
            Log::error('Error en reporte de rotación de inventario', ['error' => $e->getMessage()]);
            
            if ($request->wantsJson()) {
                return Response::json(['error' => 'Error al generar reporte'], 500);
            }

            return Redirect::back()->with('error', 'Error al generar el reporte');
        }
    }

    /**
     * Reporte de alertas de inventario
     */
    public function alertas(Request $request)
    {
        try {
            $data = [
                'alertas' => $this->inventarioService->getAlertasInventarioCompletas(),
                'productosStockBajo' => $this->inventarioService->getProductosStockBajo(),
                'productosStockCritico' => $this->inventarioService->getProductosStockCritico(),
                'productosSinStock' => $this->inventarioService->getProductosSinStock(),
                'productosStockExcesivo' => $this->inventarioService->getProductosStockExcesivo(),
                'variantesStockBajo' => $this->inventarioService->getVariantesStockBajo(),
                'variantesSinStock' => $this->inventarioService->getVariantesSinStock()
            ];

            if ($request->wantsJson()) {
                return Response::json($data);
            }

            return View::make('pages.admin.inventario.reportes.alertas', $data);
        } catch (\Exception $e) {
            Log::error('Error en reporte de alertas de inventario', ['error' => $e->getMessage()]);
            
            if ($request->wantsJson()) {
                return Response::json(['error' => 'Error al generar reporte'], 500);
            }

            return Redirect::back()->with('error', 'Error al generar el reporte');
        }
    }

    /**
     * Exportar reporte a Excel/CSV
     */
    public function exportar(Request $request)
    {
        try {
            $tipo = $request->get('tipo', 'general');
            $formato = $request->get('formato', 'excel');
            
            $data = $this->getDataForExport($tipo, $request);
            
            // Aquí se implementaría la lógica de exportación
            // Por ahora retornamos JSON como ejemplo
            return Response::json([
                'success' => true,
                'message' => 'Reporte exportado correctamente',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error al exportar reporte', ['error' => $e->getMessage()]);
            return Response::json(['error' => 'Error al exportar el reporte'], 500);
        }
    }

    /**
     * Obtener datos para exportación
     */
    private function getDataForExport(string $tipo, Request $request): array
    {
        switch ($tipo) {
            case 'general':
                $fechaInicio = $request->get('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : Carbon::now()->subMonth();
                $fechaFin = $request->get('fecha_fin') ? Carbon::parse($request->fecha_fin) : Carbon::now();
                return $this->inventarioService->getResumenInventario($fechaInicio, $fechaFin);
                
            case 'valor':
                return [
                    'valor_total' => $this->inventarioService->getValorTotalInventario(),
                    'por_categoria' => $this->inventarioService->getValorInventarioPorCategoria(),
                    'por_marca' => $this->inventarioService->getValorInventarioPorMarca()
                ];
                
            case 'alertas':
                return [
                    'alertas' => $this->inventarioService->getAlertasInventarioCompletas(),
                    'productos_problema' => $this->inventarioService->getProductosStockBajo()->merge(
                        $this->inventarioService->getProductosStockCritico()
                    )
                ];
                
            default:
                return [];
        }
    }
}
