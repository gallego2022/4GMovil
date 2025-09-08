<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InventarioService;
use App\Models\Producto;
use App\Models\MovimientoInventarioVariante;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InventarioController extends Controller
{
    protected $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Dashboard de inventario
     */
    public function dashboard()
    {
        try {
            $data = $this->inventarioService->getDashboardData();
            
            return view('pages.admin.inventario.dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Error en dashboard de inventario', ['error' => $e->getMessage()]);
            
            // Datos de fallback
            $data = $this->inventarioService->getDashboardDataFallback();
            return view('pages.admin.inventario.dashboard', $data);
        }
    }

    /**
     * Lista de productos con alertas de stock
     */
    public function alertas()
    {
        try {
            $data = $this->inventarioService->getAlertasData();
            return view('pages.admin.inventario.alertas', $data);
        } catch (\Exception $e) {
            Log::error('Error al cargar alertas', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al cargar las alertas de inventario');
        }
    }

    /**
     * Movimientos de inventario
     */
    public function movimientos(Request $request)
    {
        try {
            $filtros = $this->getFiltrosMovimientos($request);
            $data = $this->inventarioService->getMovimientosData($filtros);
            
            return view('pages.admin.inventario.movimientos', $data);
        } catch (\Exception $e) {
            Log::error('Error al cargar movimientos', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al cargar los movimientos de inventario');
        }
    }

    /**
     * Registrar entrada de inventario
     */
    public function registrarEntrada(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,producto_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:100'
        ]);

        try {
            $success = $this->inventarioService->registrarEntrada(
                $validated['producto_id'],
                $validated['cantidad'],
                $validated['motivo'],
                Auth::id(),
                $validated['referencia'] ?? null
            );

            if ($success) {
                return redirect()->back()->with('success', 'Entrada de inventario registrada correctamente.');
            }

            return redirect()->back()->with('error', 'Error al registrar la entrada de inventario.');
        } catch (\Exception $e) {
            Log::error('Error al registrar entrada', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error interno del servidor');
        }
    }

    /**
     * Registrar salida de inventario
     */
    public function registrarSalida(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,producto_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'pedido_id' => 'nullable|exists:pedidos,pedido_id'
        ]);

        try {
            $success = $this->inventarioService->registrarSalida(
                $validated['producto_id'],
                $validated['cantidad'],
                $validated['motivo'],
                Auth::id(),
                $validated['pedido_id'] ?? null
            );

            if ($success) {
                return redirect()->back()->with('success', 'Salida de inventario registrada correctamente.');
            }

            return redirect()->back()->with('error', 'Error al registrar la salida de inventario. Verifique el stock disponible.');
        } catch (\Exception $e) {
            Log::error('Error al registrar salida', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error interno del servidor');
        }
    }

    /**
     * Ajustar stock de un producto
     */
    public function ajustarStock(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,producto_id',
            'nuevo_stock' => 'required|integer|min:0',
            'motivo' => 'required|string|max:255'
        ]);

        try {
            $success = $this->inventarioService->ajustarStock(
                $validated['producto_id'],
                $validated['nuevo_stock'],
                $validated['motivo'],
                Auth::id()
            );

            if ($success) {
                return redirect()->back()->with('success', 'Stock ajustado correctamente.');
            }

            return redirect()->back()->with('error', 'Error al ajustar el stock.');
        } catch (\Exception $e) {
            Log::error('Error al ajustar stock', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error interno del servidor');
        }
    }

    /**
     * Obtener filtros para movimientos
     */
    private function getFiltrosMovimientos(Request $request): array
    {
        return [
            'fecha_inicio' => $request->get('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : now()->subMonth(),
            'fecha_fin' => $request->get('fecha_fin') ? Carbon::parse($request->fecha_fin) : now(),
            'producto_id' => $request->get('producto_id'),
            'tipo' => $request->get('tipo'),
            'usuario_id' => $request->get('usuario_id')
        ];
    }
}
