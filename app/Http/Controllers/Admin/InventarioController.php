<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\WebController;
use App\Models\Producto;
use App\Models\Usuario;
use App\Services\InventarioService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class InventarioController extends WebController
{
    protected $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Valor del inventario por categoría
     */
    public function valorPorCategoria(Request $request)
    {
        try {
            $valorTotal = $this->inventarioService->getValorTotalInventario();
            $valorPorCategoria = $this->inventarioService->getValorInventarioPorCategoria();

            return View::make('pages.admin.inventario.valor-por-categoria', [
                'valorTotal' => $valorTotal,
                'valorPorCategoria' => $valorPorCategoria,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en valor por categoría', ['error' => $e->getMessage()]);

            return Redirect::route('admin.inventario.dashboard')
                ->with('mensaje', 'Error al cargar Valor por Categoría')
                ->with('tipo', 'error');
        }
    }

    /**
     * Dashboard de inventario
     */
    public function dashboard()
    {
        try {
            $data = $this->inventarioService->getDashboardData();

            return View::make('pages.admin.inventario.dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Error en dashboard de inventario', ['error' => $e->getMessage()]);

            // Datos de fallback
            $data = $this->inventarioService->getDashboardDataFallback();

            return View::make('pages.admin.inventario.dashboard', $data);
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

            return View::make('pages.admin.inventario.movimientos', $data);
        } catch (\Exception $e) {
            Log::error('Error al cargar movimientos', ['error' => $e->getMessage()]);

            return Redirect::back()->with('mensaje', 'Error al cargar los movimientos de inventario')->with('tipo', 'error');
        }
    }

    /**
     * Registrar entrada de inventario
     */
    public function registrarEntrada(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:100',
            'tipo' => 'required|in:producto,variante',
        ]);

        try {
            $success = false;

            if ($validated['tipo'] === 'variante') {
                // Validar que la variante existe
                $request->validate([
                    'producto_id' => 'required|exists:variantes_producto,variante_id',
                ]);

                $success = $this->inventarioService->registrarEntradaVariante(
                    $validated['producto_id'],
                    $validated['cantidad'],
                    $validated['motivo'],
                    Auth::id(),
                    $validated['referencia'] ?? null
                );
            } else {
                // Validar que el producto existe
                $request->validate([
                    'producto_id' => 'required|exists:productos,producto_id',
                ]);

                $success = $this->inventarioService->registrarEntrada(
                    $validated['producto_id'],
                    $validated['cantidad'],
                    $validated['motivo'],
                    Auth::id(),
                    $validated['referencia'] ?? null
                );
            }

            if ($success) {
                if ($request->ajax()) {
                    return Response::json([
                        'success' => true,
                        'message' => 'Entrada de inventario registrada correctamente.',
                    ]);
                }

                return Redirect::back()->with('mensaje', 'Entrada de Inventario Registrada')->with('tipo', 'success');
            }

            if ($request->ajax()) {
                return Response::json([
                    'success' => false,
                    'message' => 'Error al registrar la entrada de inventario.',
                ], 400);
            }

            return Redirect::back()->with('mensaje', 'Error al registrar la entrada de inventario.')->with('tipo', 'error');
        } catch (\Exception $e) {
            Log::error('Error al registrar entrada', ['error' => $e->getMessage()]);
            if ($request->ajax()) {
                return Response::json([
                    'success' => false,
                    'message' => 'Error interno del servidor: '.$e->getMessage(),
                ], 500);
            }

            return Redirect::back()->with('mensaje', 'Error interno del servidor')->with('tipo', 'error');
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
            'pedido_id' => 'nullable|exists:pedidos,pedido_id',
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
                return Redirect::back()->with('mensaje', 'Salida de Inventario Registrada')->with('tipo', 'success');
            }

            return Redirect::back()->with('mensaje', 'Error al registrar la salida de inventario. Verifique el stock disponible.')->with('tipo', 'error');
        } catch (\Exception $e) {
            Log::error('Error al registrar salida', ['error' => $e->getMessage()]);

            return Redirect::back()->with('mensaje', 'Error interno del servidor')->with('tipo', 'error');
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
            'motivo' => 'required|string|max:255',
        ]);

        try {
            $success = $this->inventarioService->ajustarStock(
                $validated['producto_id'],
                $validated['nuevo_stock'],
                $validated['motivo'],
                Auth::id()
            );

            if ($success) {
                return Redirect::back()->with('mensaje', 'Stock Ajustado Correctamente')->with('tipo', 'success');
            }

            return Redirect::back()->with('mensaje', 'Error al ajustar el stock.')->with('tipo', 'error');
        } catch (\Exception $e) {
            Log::error('Error al ajustar stock', ['error' => $e->getMessage()]);

            return Redirect::back()->with('mensaje', 'Error interno del servidor')->with('tipo', 'error');
        }
    }

    /**
     * Generar reporte de inventario
     */
    public function reporte(Request $request)
    {
        try {
            $filtros = $this->getFiltrosReporte($request);
            $data = $this->inventarioService->getReporteData($filtros);

            return View::make('pages.admin.inventario.reporte', $data);
        } catch (\Exception $e) {
            Log::error('Error al generar reporte', ['error' => $e->getMessage()]);

            return Redirect::back()->with('mensaje', 'Error al generar el reporte de inventario')->with('tipo', 'error');
        }
    }

    /**
     * Exportar reporte de inventario
     */
    public function exportarReporte(Request $request)
    {
        try {
            $filtros = $this->getFiltrosReporte($request);
            $data = $this->inventarioService->getReporteData($filtros);

            // Solo generar CSV (Excel), para PDF se usa reportePDF() directamente
            return $this->generarReporteExcel($data);
        } catch (\Exception $e) {
            Log::error('Error al exportar reporte', ['error' => $e->getMessage()]);

            return Redirect::back()->with('mensaje', 'Error al exportar el reporte')->with('tipo', 'error');
        }
    }

    /**
     * Productos más vendidos
     */
    public function productosMasVendidos(Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : Carbon::now()->subMonth();
            $fechaFin = $request->get('fecha_fin') ? Carbon::parse($request->fecha_fin) : Carbon::now();

            $productos = $this->inventarioService->getProductosMasVendidos(20, $fechaInicio, $fechaFin);

            return View::make('pages.admin.inventario.productos-mas-vendidos', [
                'productos' => $productos,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar productos más vendidos', ['error' => $e->getMessage()]);

            return Redirect::back()->with('mensaje', 'Error al cargar los productos más vendidos')->with('tipo', 'error');
        }
    }

    /**
     * Generar reporte de inventario en PDF usando DomPDF
     */
    public function reportePDF(Request $request)
    {
        try {
            $filtros = $this->getFiltrosReporte($request);
            $data = $this->inventarioService->getReporteData($filtros);

            // Generar HTML de la vista
            $html = View::make('pages.admin.inventario.reporte-pdf', $data)->render();

            // Crear instancia de DomPDF
            $dompdf = new \Dompdf\Dompdf;
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Configurar el nombre del archivo
            $filename = 'reporte_inventario_'.date('Y-m-d_H-i-s').'.pdf';

            // Descargar el PDF
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar reporte PDF: '.$e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Redirect::back()->with('mensaje', 'Error al generar el reporte PDF')->with('tipo', 'error');
        }
    }

    /**
     * Obtener filtros para movimientos con validaciones
     */
    private function getFiltrosMovimientos(Request $request): array
    {
        // Validar fechas
        $fechaInicio = null;
        $fechaFin = null;

        if ($request->has('fecha_inicio') && $request->fecha_inicio) {
            try {
                $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
            } catch (\Exception $e) {
                Log::warning('Fecha de inicio inválida en filtros de movimientos', [
                    'fecha_inicio' => $request->fecha_inicio,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($request->has('fecha_fin') && $request->fecha_fin) {
            try {
                $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
            } catch (\Exception $e) {
                Log::warning('Fecha de fin inválida en filtros de movimientos', [
                    'fecha_fin' => $request->fecha_fin,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Validar que la fecha de inicio no sea mayor que la fecha de fin
        if ($fechaInicio && $fechaFin && $fechaInicio->gt($fechaFin)) {
            Log::warning('Fecha de inicio mayor que fecha de fin en filtros de movimientos', [
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin->format('Y-m-d'),
            ]);
            // Intercambiar las fechas si están invertidas
            $temp = $fechaInicio;
            $fechaInicio = $fechaFin;
            $fechaFin = $temp;
        }

        // Validar que el rango de fechas no sea mayor a 1 año
        if ($fechaInicio && $fechaFin) {
            $diferenciaDias = $fechaInicio->diffInDays($fechaFin);
            if ($diferenciaDias > 365) {
                Log::warning('Rango de fechas mayor a 1 año en filtros de movimientos', [
                    'diferencia_dias' => $diferenciaDias,
                ]);
                // Limitar a 1 año desde la fecha de inicio
                $fechaFin = $fechaInicio->copy()->addYear();
            }
        }

        // Establecer valores por defecto si no se proporcionaron fechas válidas
        if (! $fechaInicio) {
            $fechaInicio = Carbon::now()->subMonth()->startOfDay();
        }
        if (! $fechaFin) {
            $fechaFin = Carbon::now()->endOfDay();
        }

        // Validar producto_id
        $productoId = null;
        if ($request->has('producto_id') && $request->producto_id) {
            $productoId = (int) $request->producto_id;
            // Verificar que el producto existe
            if (! \App\Models\Producto::where('producto_id', $productoId)->exists()) {
                Log::warning('Producto no encontrado en filtros de movimientos', [
                    'producto_id' => $productoId,
                ]);
                $productoId = null;
            }
        }

        // Validar tipo de movimiento
        $tipo = null;
        if ($request->has('tipo') && $request->tipo) {
            $tiposValidos = ['entrada', 'salida', 'ajuste', 'devolucion'];
            if (in_array($request->tipo, $tiposValidos)) {
                $tipo = $request->tipo;
            } else {
                Log::warning('Tipo de movimiento inválido en filtros de movimientos', [
                    'tipo' => $request->tipo,
                ]);
            }
        }

        // Validar usuario_id
        $usuarioId = null;
        if ($request->has('usuario_id') && $request->usuario_id) {
            $usuarioId = (int) $request->usuario_id;
            // Verificar que el usuario existe
            if (! \App\Models\Usuario::where('usuario_id', $usuarioId)->exists()) {
                Log::warning('Usuario no encontrado en filtros de movimientos', [
                    'usuario_id' => $usuarioId,
                ]);
                $usuarioId = null;
            }
        }

        return [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'producto_id' => $productoId,
            'tipo' => $tipo,
            'usuario_id' => $usuarioId,
        ];
    }

    /**
     * Obtener filtros para reportes
     */
    private function getFiltrosReporte(Request $request): array
    {
        return [
            'fecha_inicio' => $request->get('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : Carbon::now()->subMonth(),
            'fecha_fin' => $request->get('fecha_fin') ? Carbon::parse($request->fecha_fin) : Carbon::now(),
            'categoria_id' => $request->get('categoria_id'),
            'marca_id' => $request->get('marca_id'),
            'tipo_reporte' => $request->get('tipo_reporte', 'general'),
            'incluir_variantes' => $request->boolean('incluir_variantes', true),
        ];
    }


    /**
     * Generar reporte en formato Excel (CSV)
     */
    private function generarReporteExcel(array $data)
    {
        $filename = 'reporte_inventario_'.date('Y-m-d_H-i-s').'.csv';

        // Generar contenido CSV
        $csv = $this->generarCSVReporte($data);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Generar CSV para reporte Excel
     */
    private function generarCSVReporte(array $data): string
    {
        $estadisticas = $data['estadisticas'] ?? [];
        $productos = $data['productos'] ?? Collection::make();
        $movimientos = $data['movimientos'] ?? Collection::make();

        // Encabezado del reporte
        $csv = "REPORTE DE INVENTARIO - 4GMOVIL\n";
        $csv .= "=====================================\n";
        $csv .= 'Generado el: '.Carbon::now()->format('d/m/Y H:i:s')."\n";

        if (isset($estadisticas['periodo'])) {
            $csv .= 'Período: '.$estadisticas['periodo']['inicio'].' - '.$estadisticas['periodo']['fin']."\n";
        }

        $csv .= "\n";
        $csv .= "ESTADÍSTICAS GENERALES\n";
        $csv .= "======================\n";
        $csv .= "Concepto,Valor\n";
        $csv .= 'Total de Productos,'.number_format($estadisticas['total_productos'] ?? 0)."\n";
        $csv .= 'Total de Variantes,'.number_format($estadisticas['total_variantes'] ?? 0)."\n";
        $csv .= 'Stock Total (unidades),'.number_format($estadisticas['stock_total'] ?? 0)."\n";
        $csv .= 'Valor Total del Inventario,$'.number_format($estadisticas['valor_inventario'] ?? 0, 0, ',', '.')."\n";
        $csv .= 'Productos con Stock Crítico,'.number_format($estadisticas['productos_stock_critico'] ?? 0)."\n";
        $csv .= 'Productos con Stock Bajo,'.number_format($estadisticas['productos_stock_bajo'] ?? 0)."\n";
        $csv .= 'Productos Sin Stock,'.number_format($estadisticas['productos_sin_stock'] ?? 0)."\n";
        $csv .= 'Movimientos de Entrada,'.number_format($estadisticas['movimientos_entrada'] ?? 0)."\n";
        $csv .= 'Movimientos de Salida,'.number_format($estadisticas['movimientos_salida'] ?? 0)."\n";

        $csv .= "\n";
        $csv .= "PRODUCTOS EN INVENTARIO\n";
        $csv .= "=======================\n";
        $csv .= "ID,Producto,Categoría,Marca,Stock,Precio Unitario,Valor Total\n";

        foreach ($productos as $producto) {
            $csv .= $producto->producto_id.',';
            $csv .= '"'.str_replace('"', '""', $producto->nombre_producto).'",';
            $csv .= '"'.str_replace('"', '""', $producto->categoria->nombre ?? 'Sin categoría').'",';
            $csv .= '"'.str_replace('"', '""', $producto->marca->nombre ?? 'Sin marca').'",';
            $csv .= number_format($producto->stock).',';
            $csv .= '$'.number_format($producto->precio, 0, ',', '.').',';
            $csv .= '$'.number_format($producto->stock * $producto->precio, 0, ',', '.')."\n";
        }

        if ($movimientos->count() > 0) {
            $csv .= "\n";
            $csv .= "MOVIMIENTOS RECIENTES\n";
            $csv .= "=====================\n";
            $csv .= "Fecha,Producto,Variante,Tipo,Cantidad,Motivo,Usuario\n";

            foreach ($movimientos->take(100) as $movimiento) {
                $csv .= $movimiento->fecha_movimiento ? $movimiento->fecha_movimiento->format('d/m/Y H:i') : 'Sin fecha'.',';
                $csv .= '"'.str_replace('"', '""', $movimiento->variante->producto->nombre_producto ?? 'N/A').'",';
                $csv .= '"'.str_replace('"', '""', $movimiento->variante->nombre ?? 'N/A').'",';
                $csv .= ucfirst($movimiento->tipo).',';
                $csv .= number_format($movimiento->cantidad).',';
                $csv .= '"'.str_replace('"', '""', $movimiento->motivo).'",';
                $csv .= '"'.str_replace('"', '""', $movimiento->usuario->nombre_usuario ?? 'Sistema').'"'."\n";
            }
        }

        $csv .= "\n";
        $csv .= "=====================================\n";
        $csv .= "Reporte generado automáticamente por el sistema 4GMovil\n";
        $csv .= "Para más información, contacte al administrador del sistema\n";

        return $csv;
    }

}
