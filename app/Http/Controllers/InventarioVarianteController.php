<?php

namespace App\Http\Controllers;

use App\Services\InventarioVarianteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InventarioVarianteController extends Controller
{
    protected $inventarioService;

    public function __construct(InventarioVarianteService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
    }

    /**
     * Mostrar reporte de inventario de variantes
     */
    public function reporte(Request $request)
    {
        try {
            $productoId = $request->get('producto_id');
            $resultado = $this->inventarioService->obtenerReporteInventario($productoId);

            if ($resultado['success']) {
                return response()->json($resultado['data']);
            }

            return response()->json(['error' => $resultado['message']], 400);

        } catch (\Exception $e) {
            Log::error('Error en reporte de inventario', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Registrar entrada de stock
     */
    public function registrarEntrada(Request $request)
    {
        $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:255'
        ]);

        try {
            $resultado = $this->inventarioService->registrarEntrada(
                $request->variante_id,
                $request->cantidad,
                $request->motivo,
                Auth::id(),
                $request->referencia
            );

            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $resultado['message'],
                    'data' => [
                        'stock_anterior' => $resultado['stock_anterior'],
                        'stock_nuevo' => $resultado['stock_nuevo']
                    ]
                ]);
            }

            return response()->json(['error' => $resultado['message']], 400);

        } catch (\Exception $e) {
            Log::error('Error al registrar entrada', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Registrar salida de stock
     */
    public function registrarSalida(Request $request)
    {
        $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:255'
        ]);

        try {
            $resultado = $this->inventarioService->registrarSalida(
                $request->variante_id,
                $request->cantidad,
                $request->motivo,
                Auth::id(),
                $request->referencia
            );

            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $resultado['message'],
                    'data' => [
                        'stock_anterior' => $resultado['stock_anterior'],
                        'stock_nuevo' => $resultado['stock_nuevo']
                    ]
                ]);
            }

            return response()->json(['error' => $resultado['message']], 400);

        } catch (\Exception $e) {
            Log::error('Error al registrar salida', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Ajustar stock de una variante
     */
    public function ajustarStock(Request $request)
    {
        $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'nuevo_stock' => 'required|integer|min:0',
            'motivo' => 'required|string|max:255'
        ]);

        try {
            $resultado = $this->inventarioService->ajustarStock(
                $request->variante_id,
                $request->nuevo_stock,
                $request->motivo,
                Auth::id()
            );

            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $resultado['message']
                ]);
            }

            return response()->json(['error' => $resultado['message']], 400);

        } catch (\Exception $e) {
            Log::error('Error al ajustar stock', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener variantes que necesitan reposición
     */
    public function variantesNecesitanReposicion()
    {
        try {
            $resultado = $this->inventarioService->obtenerVariantesNecesitanReposicion();

            if ($resultado['success']) {
                return response()->json($resultado['data']);
            }

            return response()->json(['error' => $resultado['message']], 400);

        } catch (\Exception $e) {
            Log::error('Error al obtener variantes que necesitan reposición', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }
}
