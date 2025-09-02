<?php

namespace App\Http\Controllers\Admin;

use App\Services\StockSincronizacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class StockSincronizacionController extends Controller
{
    protected $stockSincronizacionService;

    public function __construct(StockSincronizacionService $stockSincronizacionService)
    {
        $this->stockSincronizacionService = $stockSincronizacionService;
    }

    /**
     * Mostrar dashboard de sincronización de stock
     */
    public function dashboard()
    {
        $reporte = $this->stockSincronizacionService->obtenerReporteSincronizacion();
        $integridad = $this->stockSincronizacionService->verificarIntegridadStock();

        return view('admin.stock-sincronizacion.dashboard', compact('reporte', 'integridad'));
    }

    /**
     * Sincronizar stock de un producto específico
     */
    public function sincronizarProducto(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,producto_id'
        ]);

        try {
            $resultado = $this->stockSincronizacionService->sincronizarProducto($request->producto_id);
            
            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock sincronizado correctamente',
                    'data' => $resultado
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al sincronizar stock',
                    'error' => $resultado['error']
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error en sincronización de stock', [
                'producto_id' => $request->producto_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sincronizar stock de todos los productos
     */
    public function sincronizarTodos(Request $request)
    {
        try {
            $resultado = $this->stockSincronizacionService->sincronizarTodosLosProductos();
            
            return response()->json([
                'success' => true,
                'message' => 'Sincronización completada',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            Log::error('Error en sincronización masiva de stock', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener reporte de sincronización
     */
    public function obtenerReporte()
    {
        try {
            $reporte = $this->stockSincronizacionService->obtenerReporteSincronizacion();
            
            return response()->json([
                'success' => true,
                'data' => $reporte
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener reporte de sincronización', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener reporte',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar integridad del stock
     */
    public function verificarIntegridad()
    {
        try {
            $integridad = $this->stockSincronizacionService->verificarIntegridadStock();
            
            return response()->json([
                'success' => true,
                'data' => $integridad
            ]);
        } catch (\Exception $e) {
            Log::error('Error al verificar integridad del stock', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar integridad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Corregir automáticamente problemas de sincronización
     */
    public function corregirAutomaticamente()
    {
        try {
            $resultado = $this->stockSincronizacionService->corregirSincronizacion();
            
            return response()->json([
                'success' => true,
                'message' => 'Corrección automática completada',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            Log::error('Error en corrección automática de stock', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en corrección automática',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar vista de reporte detallado
     */
    public function reporteDetallado()
    {
        $reporte = $this->stockSincronizacionService->obtenerReporteSincronizacion();
        $integridad = $this->stockSincronizacionService->verificarIntegridadStock();

        return view('admin.stock-sincronizacion.reporte-detallado', compact('reporte', 'integridad'));
    }
}
