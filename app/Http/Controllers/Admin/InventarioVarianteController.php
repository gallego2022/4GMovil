<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\WebController;
use App\Services\InventarioService;
use App\Models\VarianteProducto;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class InventarioVarianteController extends WebController
{
    protected $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Registrar entrada de stock de variante
     */
    public function registrarEntrada(Request $request)
    {
        $validated = $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:100'
        ]);

        try {
            $success = $this->inventarioService->registrarEntradaVariante(
                $validated['variante_id'],
                $validated['cantidad'],
                $validated['motivo'],
                Auth::id(),
                $validated['referencia'] ?? null
            );

            if ($success) {
                return Redirect::back()->with('success', 'Entrada de stock registrada correctamente');
            }

            return Redirect::back()->with('error', 'Error al registrar la entrada de stock');
        } catch (\Exception $e) {
            Log::error('Error al registrar entrada de variante', ['error' => $e->getMessage()]);
            return Redirect::back()->with('error', 'Error interno del servidor');
        }
    }

    /**
     * Registrar salida de stock de variante
     */
    public function registrarSalida(Request $request)
    {
        $validated = $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'pedido_id' => 'nullable|exists:pedidos,pedido_id'
        ]);

        try {
            $success = $this->inventarioService->registrarSalidaVariante(
                $validated['variante_id'],
                $validated['cantidad'],
                $validated['motivo'],
                Auth::id(),
                $validated['pedido_id'] ?? null
            );

            if ($success) {
                return Redirect::back()->with('success', 'Salida de stock registrada correctamente');
            }

            return Redirect::back()->with('error', 'Error al registrar la salida de stock');
        } catch (\Exception $e) {
            Log::error('Error al registrar salida de variante', ['error' => $e->getMessage()]);
            return Redirect::back()->with('error', 'Error interno del servidor');
        }
    }

    /**
     * Ajustar stock de variante
     */
    public function ajustarStock(Request $request)
    {
        $validated = $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'nuevo_stock' => 'required|integer|min:0',
            'motivo' => 'required|string|max:255'
        ]);

        try {
            $resultado = $this->inventarioService->ajustarStockVariante(
                $validated['variante_id'],
                $validated['nuevo_stock'],
                $validated['motivo'],
                Auth::id()
            );

            if ($resultado) {
                return Redirect::back()->with('success', 'Stock ajustado correctamente');
            }

            return Redirect::back()->with('error', 'Error al ajustar el stock');
        } catch (\Exception $e) {
            Log::error('Error al ajustar stock de variante', ['error' => $e->getMessage()]);
            return Redirect::back()->with('error', 'Error interno del servidor');
        }
    }

    /**
     * Reporte de variantes
     */
    public function reporte(Request $request)
    {
        try {
            $productoId = $request->get('producto_id');
            $reporte = $this->inventarioService->getReporteInventarioVariantes($productoId);

            if ($request->wantsJson()) {
                return Response::json($reporte);
            }

            return View::make('pages.admin.inventario.variantes.reporte', compact('reporte'));
        } catch (\Exception $e) {
            Log::error('Error en reporte de variantes', ['error' => $e->getMessage()]);
            
            if ($request->wantsJson()) {
                return Response::json(['error' => 'Error al generar reporte'], 500);
            }

            return Redirect::back()->with('error', 'Error al generar el reporte');
        }
    }

    /**
     * Movimientos de variantes
     */
    public function movimientos(Request $request)
    {
        try {
            $filtros = $this->getFiltrosMovimientos($request);
            $data = $this->getMovimientosVariantesData($filtros);

            return View::make('pages.admin.inventario.variantes.movimientos', $data);
        } catch (\Exception $e) {
            Log::error('Error en movimientos de variantes', ['error' => $e->getMessage()]);
            return Redirect::back()->with('error', 'Error al cargar los movimientos');
        }
    }

    /**
     * Obtener filtros para movimientos de variantes
     */
    private function getFiltrosMovimientos(Request $request): array
    {
        return [
            'fecha_inicio' => $request->get('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : Carbon::now()->subMonth(),
            'fecha_fin' => $request->get('fecha_fin') ? Carbon::parse($request->fecha_fin) : Carbon::now(),
            'variante_id' => $request->get('variante_id'),
            'tipo_movimiento' => $request->get('tipo_movimiento'),
            'producto_id' => $request->get('producto_id')
        ];
    }

    /**
     * Obtener datos de movimientos de variantes
     */
    private function getMovimientosVariantesData(array $filtros): array
    {
        $fechaInicio = $filtros['fecha_inicio'];
        $fechaFin = $filtros['fecha_fin'];
        $varianteId = $filtros['variante_id'];
        $tipoMovimiento = $filtros['tipo_movimiento'];
        $productoId = $filtros['producto_id'];

        $query = MovimientoInventario::with(['variante.producto', 'usuario'])
            ->whereNotNull('variante_id')
            ->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);

        if ($varianteId) {
            $query->where('variante_id', $varianteId);
        }

        if ($tipoMovimiento) {
            $query->where('tipo_movimiento', $tipoMovimiento);
        }

        if ($productoId) {
            $query->whereHas('variante', function($q) use ($productoId) {
                $q->where('producto_id', $productoId);
            });
        }

        $movimientos = $query->orderBy('fecha_movimiento', 'desc')->paginate(20);

        return [
            'movimientos' => $movimientos,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'varianteId' => $varianteId,
            'tipoMovimiento' => $tipoMovimiento,
            'productoId' => $productoId
        ];
    }
}
