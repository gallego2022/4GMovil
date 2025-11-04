<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OptimizedStockAlertService;
use App\Models\Producto;
use App\Models\VarianteProducto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
    public function dashboard(Request $request)
    {
        $tipo = $request->get('tipo', 'criticos');
        $page = $request->get('page', 1);

        $alertas = $this->stockAlertService->getOptimizedStockAlerts($tipo, $page);

        // Si es una petición AJAX, retornar solo el contenido del tab
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('pages.admin.inventario.partials.tab-content', compact('alertas', 'tipo'))->render(),
                'tipo' => $tipo
            ]);
        }

        return view('pages.admin.inventario.alertas-optimizadas', compact('alertas', 'tipo'));
    }

    /**
     * Obtiene las variantes problemáticas de un producto específico (AJAX)
     */
    public function getVariantesProblematicas(Request $request): JsonResponse
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,producto_id',
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
                    'porcentaje' => $item['porcentaje'],
                ];
            }),
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
                'productos_criticos' => $alertas['productos_criticos_count'],
                'productos_stock_bajo' => $alertas['productos_stock_bajo_count'],
                'variantes_agotadas' => $alertas['variantes_agotadas_count'],
                'total_alertas' => $alertas['total_alertas'],
            ],
        ]);
    }

    /**
     * Obtiene todas las variantes de un producto (AJAX)
     */
    public function getVariantesProducto(Request $request): JsonResponse
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,producto_id',
        ]);

        $producto = Producto::with('variantes')->findOrFail($request->producto_id);

        return response()->json([
            'success' => true,
            'producto' => [
                'producto_id' => $producto->producto_id,
                'nombre' => $producto->nombre_producto,
                'tiene_variantes' => $producto->variantes->isNotEmpty(),
            ],
            'variantes' => $producto->variantes->map(function ($variante) {
                return [
                    'variante_id' => $variante->variante_id,
                    'nombre' => $variante->nombre,
                    'codigo_color' => $variante->codigo_color,
                    'stock_actual' => $variante->stock,
                    'stock_minimo' => $variante->producto->stock_minimo ?? 10,
                ];
            }),
        ]);
    }

    /**
     * Repone stock de un producto o variante
     */
    public function reponerStock(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|integer|exists:productos,producto_id',
            'variante_id' => 'nullable|integer|exists:variantes_producto,variante_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $producto = Producto::findOrFail($request->producto_id);
            $usuarioId = Auth::id();
            $cantidad = $request->cantidad;
            $motivo = $request->motivo ?? 'Reposición de stock desde alertas';

            if ($request->variante_id) {
                // Reponer variante específica
                $variante = VarianteProducto::findOrFail($request->variante_id);
                
                if ($variante->producto_id !== $producto->producto_id) {
                    throw new \Exception('La variante no pertenece al producto especificado');
                }

                $variante->registrarEntrada($cantidad, $motivo, $usuarioId, 'Reposición desde alertas');

                DB::commit();

                Session::flash('mensaje', "Stock repuesto exitosamente. Variante: {$variante->nombre}, Cantidad: {$cantidad}");
                Session::flash('tipo', 'success');
            } else {
                // Reponer producto directamente (solo si no tiene variantes)
                $producto->load('variantes');
                if ($producto->variantes->isNotEmpty()) {
                    throw new \Exception('Este producto tiene variantes. Por favor, seleccione una variante específica.');
                }

                // Usar el método del modelo para registrar entrada
                $producto->registrarEntrada($cantidad, $motivo, $usuarioId, 'Reposición desde alertas');

                DB::commit();

                Session::flash('mensaje', "Stock repuesto exitosamente. Cantidad: {$cantidad}");
                Session::flash('tipo', 'success');
            }

            // Determinar a dónde redirigir
            $tipo = $request->get('tipo_alerta');
            if ($tipo) {
                // Viene de alertas-optimizadas
                return redirect()->route('admin.inventario.alertas-optimizadas', ['tipo' => $tipo]);
            } else {
                // Viene del dashboard
                return redirect()->route('admin.inventario.dashboard');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            Session::flash('mensaje', 'Error al reponer stock: ' . $e->getMessage());
            Session::flash('tipo', 'error');
            
            // Determinar a dónde redirigir en caso de error
            $tipo = $request->get('tipo_alerta');
            if ($tipo) {
                return redirect()->route('admin.inventario.alertas-optimizadas', ['tipo' => $tipo]);
            } else {
                return redirect()->route('admin.inventario.dashboard');
            }
        }
    }
}
