<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InventarioService;
use App\Models\Producto;
use App\Models\MovimientoInventario;
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
            'producto_id' => 'required',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:100',
            'tipo' => 'required|in:producto,variante'
        ]);

        try {
            $success = false;
            
            if ($validated['tipo'] === 'variante') {
                // Validar que la variante existe
                $request->validate([
                    'producto_id' => 'required|exists:variantes_producto,variante_id'
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
                    'producto_id' => 'required|exists:productos,producto_id'
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
                    return response()->json([
                        'success' => true,
                        'message' => 'Entrada de inventario registrada correctamente.'
                    ]);
                }
                return redirect()->back()->with('success', 'Entrada de inventario registrada correctamente.');
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar la entrada de inventario.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Error al registrar la entrada de inventario.');
        } catch (\Exception $e) {
            Log::error('Error al registrar entrada', ['error' => $e->getMessage()]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno del servidor: ' . $e->getMessage()
                ], 500);
            }
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
     * Generar reporte de inventario
     */
    public function reporte(Request $request)
    {
        try {
            $filtros = $this->getFiltrosReporte($request);
            $data = $this->inventarioService->getReporteData($filtros);
            
            return view('pages.admin.inventario.reporte', $data);
        } catch (\Exception $e) {
            Log::error('Error al generar reporte', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al generar el reporte de inventario');
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
            
            // Generar archivo Excel o PDF según el formato solicitado
            $formato = $request->get('formato', 'excel');
            
            if ($formato === 'pdf') {
                return $this->generarReportePDF($data);
            } else {
                return $this->generarReporteExcel($data);
            }
        } catch (\Exception $e) {
            Log::error('Error al exportar reporte', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al exportar el reporte');
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
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        if ($request->has('fecha_fin') && $request->fecha_fin) {
            try {
                $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
            } catch (\Exception $e) {
                Log::warning('Fecha de fin inválida en filtros de movimientos', [
                    'fecha_fin' => $request->fecha_fin,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Validar que la fecha de inicio no sea mayor que la fecha de fin
        if ($fechaInicio && $fechaFin && $fechaInicio->gt($fechaFin)) {
            Log::warning('Fecha de inicio mayor que fecha de fin en filtros de movimientos', [
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin->format('Y-m-d')
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
                    'diferencia_dias' => $diferenciaDias
                ]);
                // Limitar a 1 año desde la fecha de inicio
                $fechaFin = $fechaInicio->copy()->addYear();
            }
        }
        
        // Establecer valores por defecto si no se proporcionaron fechas válidas
        if (!$fechaInicio) {
            $fechaInicio = now()->subMonth()->startOfDay();
        }
        if (!$fechaFin) {
            $fechaFin = now()->endOfDay();
        }
        
        // Validar producto_id
        $productoId = null;
        if ($request->has('producto_id') && $request->producto_id) {
            $productoId = (int) $request->producto_id;
            // Verificar que el producto existe
            if (!\App\Models\Producto::where('producto_id', $productoId)->exists()) {
                Log::warning('Producto no encontrado en filtros de movimientos', [
                    'producto_id' => $productoId
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
                    'tipo' => $request->tipo
                ]);
            }
        }
        
        // Validar usuario_id
        $usuarioId = null;
        if ($request->has('usuario_id') && $request->usuario_id) {
            $usuarioId = (int) $request->usuario_id;
            // Verificar que el usuario existe
            if (!\App\Models\Usuario::where('usuario_id', $usuarioId)->exists()) {
                Log::warning('Usuario no encontrado en filtros de movimientos', [
                    'usuario_id' => $usuarioId
                ]);
                $usuarioId = null;
            }
        }
        
        return [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'producto_id' => $productoId,
            'tipo' => $tipo,
            'usuario_id' => $usuarioId
        ];
    }

    /**
     * Obtener filtros para reportes
     */
    private function getFiltrosReporte(Request $request): array
    {
        return [
            'fecha_inicio' => $request->get('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : now()->subMonth(),
            'fecha_fin' => $request->get('fecha_fin') ? Carbon::parse($request->fecha_fin) : now(),
            'categoria_id' => $request->get('categoria_id'),
            'marca_id' => $request->get('marca_id'),
            'tipo_reporte' => $request->get('tipo_reporte', 'general'),
            'incluir_variantes' => $request->boolean('incluir_variantes', true)
        ];
    }

    /**
     * Generar reporte en formato PDF
     */
    private function generarReportePDF(array $data)
    {
        $filename = 'reporte_inventario_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Preparar datos para la vista
        $reporte = $this->prepararDatosParaVista($data);
        $secciones = ['resumen', 'alertas', 'productos', 'categorias']; // Secciones por defecto
        
        // Generar PDF usando la vista existente
        $html = view('pages.admin.inventario.pdf.reporte', compact('reporte', 'secciones'))->render();
        
        // Generar HTML optimizado para impresión (más confiable)
        $htmlOptimizado = $this->optimizarHTMLParaImpresion($html);
        
        return response($htmlOptimizado, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . str_replace('.pdf', '.html', $filename) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Generar reporte en formato Excel (CSV)
     */
    private function generarReporteExcel(array $data)
    {
        $filename = 'reporte_inventario_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Generar contenido CSV
        $csv = $this->generarCSVReporte($data);
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Generar HTML para reporte PDF
     */
    private function generarHTMLReporte(array $data): string
    {
        $estadisticas = $data['estadisticas'] ?? [];
        $productos = $data['productos'] ?? collect();
        $movimientos = $data['movimientos'] ?? collect();
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario - 4GMovil</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .stats { display: flex; justify-content: space-around; margin: 20px 0; }
        .stat-box { border: 1px solid #ddd; padding: 15px; text-align: center; width: 200px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Inventario - 4GMovil</h1>
        <p>Generado el: ' . now()->format('d/m/Y H:i:s') . '</p>';
        
        if (isset($estadisticas['periodo'])) {
            $html .= '<p>Período: ' . $estadisticas['periodo']['inicio'] . ' - ' . $estadisticas['periodo']['fin'] . '</p>';
        }
        
        $html .= '</div>
    
    <div class="stats">
        <div class="stat-box">
            <h3>Total Productos</h3>
            <p>' . ($estadisticas['total_productos'] ?? 0) . '</p>
        </div>
        <div class="stat-box">
            <h3>Stock Total</h3>
            <p>' . number_format($estadisticas['stock_total'] ?? 0) . '</p>
        </div>
        <div class="stat-box">
            <h3>Valor Inventario</h3>
            <p>$' . number_format($estadisticas['valor_inventario'] ?? 0, 0, ',', '.') . '</p>
        </div>
        <div class="stat-box">
            <h3>Stock Bajo</h3>
            <p>' . ($estadisticas['productos_stock_bajo'] ?? 0) . '</p>
        </div>
    </div>
    
    <h2>Productos en Inventario</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Stock</th>
                <th>Precio</th>
                <th>Valor Total</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($productos as $producto) {
            $html .= '<tr>
                <td>' . htmlspecialchars($producto->nombre_producto) . '</td>
                <td>' . htmlspecialchars($producto->categoria->nombre ?? 'Sin categoría') . '</td>
                <td>' . htmlspecialchars($producto->marca->nombre ?? 'Sin marca') . '</td>
                <td>' . $producto->stock . '</td>
                <td>$' . number_format($producto->precio, 0, ',', '.') . '</td>
                <td>$' . number_format($producto->stock * $producto->precio, 0, ',', '.') . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
    </table>
    
    <h2>Movimientos Recientes</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Variante</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($movimientos->take(50) as $movimiento) {
            $html .= '<tr>
                <td>' . ($movimiento->fecha_movimiento ? $movimiento->fecha_movimiento->format('d/m/Y H:i') : '') . '</td>
                <td>' . htmlspecialchars($movimiento->variante->producto->nombre_producto ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($movimiento->variante->nombre ?? 'N/A') . '</td>
                <td>' . ucfirst($movimiento->tipo_movimiento ?? $movimiento->tipo ?? '') . '</td>
                <td>' . $movimiento->cantidad . '</td>
                <td>' . htmlspecialchars($movimiento->motivo) . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
    </table>
    
    <div class="footer">
        <p>Reporte generado automáticamente por el sistema 4GMovil</p>
    </div>
</body>
</html>';
        
        return $html;
    }

    /**
     * Generar CSV para reporte Excel
     */
    private function generarCSVReporte(array $data): string
    {
        $estadisticas = $data['estadisticas'] ?? [];
        $productos = $data['productos'] ?? collect();
        $movimientos = $data['movimientos'] ?? collect();
        
        // Encabezado del reporte
        $csv = "REPORTE DE INVENTARIO - 4GMOVIL\n";
        $csv .= "=====================================\n";
        $csv .= "Generado el: " . now()->format('d/m/Y H:i:s') . "\n";
        
        if (isset($estadisticas['periodo'])) {
            $csv .= "Período: " . $estadisticas['periodo']['inicio'] . " - " . $estadisticas['periodo']['fin'] . "\n";
        }
        
        $csv .= "\n";
        $csv .= "ESTADÍSTICAS GENERALES\n";
        $csv .= "======================\n";
        $csv .= "Concepto,Valor\n";
        $csv .= "Total de Productos," . number_format($estadisticas['total_productos'] ?? 0) . "\n";
        $csv .= "Total de Variantes," . number_format($estadisticas['total_variantes'] ?? 0) . "\n";
        $csv .= "Stock Total (unidades)," . number_format($estadisticas['stock_total'] ?? 0) . "\n";
        $csv .= "Valor Total del Inventario,$" . number_format($estadisticas['valor_inventario'] ?? 0, 0, ',', '.') . "\n";
        $csv .= "Productos con Stock Bajo," . number_format($estadisticas['productos_stock_bajo'] ?? 0) . "\n";
        $csv .= "Productos Sin Stock," . number_format($estadisticas['productos_sin_stock'] ?? 0) . "\n";
        $csv .= "Movimientos de Entrada," . number_format($estadisticas['movimientos_entrada'] ?? 0) . "\n";
        $csv .= "Movimientos de Salida," . number_format($estadisticas['movimientos_salida'] ?? 0) . "\n";
        
        $csv .= "\n";
        $csv .= "PRODUCTOS EN INVENTARIO\n";
        $csv .= "=======================\n";
        $csv .= "ID,Producto,Categoría,Marca,Stock,Precio Unitario,Valor Total\n";
        
        foreach ($productos as $producto) {
            $csv .= $producto->producto_id . ',';
            $csv .= '"' . str_replace('"', '""', $producto->nombre_producto) . '",';
            $csv .= '"' . str_replace('"', '""', $producto->categoria->nombre ?? 'Sin categoría') . '",';
            $csv .= '"' . str_replace('"', '""', $producto->marca->nombre ?? 'Sin marca') . '",';
            $csv .= number_format($producto->stock) . ',';
            $csv .= '$' . number_format($producto->precio, 0, ',', '.') . ',';
            $csv .= '$' . number_format($producto->stock * $producto->precio, 0, ',', '.') . "\n";
        }
        
        if ($movimientos->count() > 0) {
            $csv .= "\n";
            $csv .= "MOVIMIENTOS RECIENTES\n";
            $csv .= "=====================\n";
            $csv .= "Fecha,Producto,Variante,Tipo,Cantidad,Motivo,Usuario\n";
            
            foreach ($movimientos->take(100) as $movimiento) {
                $csv .= $movimiento->fecha_movimiento ? $movimiento->fecha_movimiento->format('d/m/Y H:i') : 'Sin fecha' . ',';
                $csv .= '"' . str_replace('"', '""', $movimiento->variante->producto->nombre_producto ?? 'N/A') . '",';
                $csv .= '"' . str_replace('"', '""', $movimiento->variante->nombre ?? 'N/A') . '",';
                $csv .= ucfirst($movimiento->tipo) . ',';
                $csv .= number_format($movimiento->cantidad) . ',';
                $csv .= '"' . str_replace('"', '""', $movimiento->motivo) . '",';
                $csv .= '"' . str_replace('"', '""', $movimiento->usuario->name ?? 'Sistema') . '"' . "\n";
            }
        }
        
        $csv .= "\n";
        $csv .= "=====================================\n";
        $csv .= "Reporte generado automáticamente por el sistema 4GMovil\n";
        $csv .= "Para más información, contacte al administrador del sistema\n";
        
        return $csv;
    }

    /**
     * Generar PDF usando DomPDF
     */
    private function generarPDFConDomPDF(string $html, string $filename)
    {
        try {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar PDF con DomPDF', ['error' => $e->getMessage()]);
            
            // Fallback a HTML
            return response($html, 200, [
                'Content-Type' => 'text/html; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . str_replace('.pdf', '.html', $filename) . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        }
    }


    /**
     * Preparar datos para la vista de PDF
     */
    private function prepararDatosParaVista(array $data): array
    {
        $estadisticas = $data['estadisticas'] ?? [];
        $productos = $data['productos'] ?? collect();
        $movimientos = $data['movimientos'] ?? collect();
        
        // Preparar resumen general
        $resumenGeneral = [
            'total_productos' => $estadisticas['total_productos'] ?? 0,
            'productos_activos' => $estadisticas['total_productos'] ?? 0, // Asumimos que todos están activos
            'valor_total_inventario' => $estadisticas['valor_inventario'] ?? 0,
            'stock_total' => $estadisticas['stock_total'] ?? 0,
        ];
        
        // Preparar alertas
        $alertas = [
            'stock_critico' => $estadisticas['productos_stock_bajo'] ?? 0,
            'stock_bajo' => $estadisticas['productos_stock_bajo'] ?? 0,
            'sin_stock' => $estadisticas['productos_sin_stock'] ?? 0,
            'stock_excesivo' => 0, // No tenemos esta métrica en los datos actuales
        ];
        
        // Preparar productos con stock bajo
        $productosStockBajo = $productos->filter(function ($producto) {
            return $producto->stock <= 10; // Consideramos stock bajo si es <= 10
        })->values();
        
        // Preparar valor por categoría
        $valorPorCategoria = $productos->groupBy('categoria_id')->map(function ($productosCategoria) {
            $categoria = $productosCategoria->first()->categoria;
            $stockTotal = $productosCategoria->sum('stock');
            $valorTotal = $productosCategoria->sum(function ($producto) {
                return $producto->stock * $producto->precio;
            });
            
            return [
                'categoria' => $categoria,
                'productos_count' => $productosCategoria->count(),
                'stock_total' => $stockTotal,
                'valor_total' => $valorTotal,
            ];
        })->values();
        
        return [
            'resumen_general' => $resumenGeneral,
            'alertas' => $alertas,
            'productos_stock_bajo' => $productosStockBajo,
            'valor_por_categoria' => $valorPorCategoria,
        ];
    }

    /**
     * Optimizar HTML para impresión (solución confiable)
     */
    private function optimizarHTMLParaImpresion(string $html): string
    {
        // Agregar estilos CSS optimizados para impresión y PDF
        $estilosImpresion = '
        <style>
            @media print {
                body { 
                    margin: 0; 
                    font-size: 12px;
                    line-height: 1.4;
                }
                .page-break { page-break-before: always; }
                .no-print { display: none; }
                table { page-break-inside: avoid; }
                .summary-grid { page-break-inside: avoid; }
            }
            @page {
                margin: 1.5cm;
                size: A4;
            }
            body {
                font-family: Arial, sans-serif;
                color: #333;
                background: white;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            .header h1 {
                margin: 0;
                font-size: 18px;
                color: #333;
            }
            .section {
                margin-bottom: 20px;
            }
            .section h2 {
                font-size: 14px;
                color: #333;
                border-bottom: 1px solid #ccc;
                padding-bottom: 5px;
                margin-bottom: 10px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
                font-size: 10px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 4px;
                text-align: left;
            }
            th {
                background-color: #f5f5f5;
                font-weight: bold;
                font-size: 9px;
            }
            .summary-grid {
                display: table;
                width: 100%;
                margin-bottom: 15px;
            }
            .summary-item {
                display: table-cell;
                width: 25%;
                padding: 8px;
                border: 1px solid #ddd;
                text-align: center;
                vertical-align: top;
            }
            .summary-item h3 {
                margin: 0 0 5px 0;
                font-size: 10px;
                color: #666;
            }
            .summary-item p {
                margin: 0;
                font-size: 12px;
                font-weight: bold;
                color: #333;
            }
            .footer {
                margin-top: 20px;
                text-align: center;
                font-size: 8px;
                color: #666;
                border-top: 1px solid #ccc;
                padding-top: 10px;
            }
        </style>';
        
        // Insertar estilos en el head
        $html = str_replace('</head>', $estilosImpresion . '</head>', $html);
        
        // Agregar script mejorado para impresión
        $scriptImpresion = '
        <script>
            window.onload = function() {
                // Mostrar instrucciones claras
                const mensaje = "REPORTE DE INVENTARIO LISTO PARA IMPRIMIR\\n\\n" +
                              "Para convertir a PDF:\\n" +
                              "1. Presione Ctrl+P (o Cmd+P en Mac)\\n" +
                              "2. Seleccione \\"Guardar como PDF\\" como destino\\n" +
                              "3. Ajuste la configuración si es necesario\\n" +
                              "4. Haga clic en \\"Guardar\\"\\n\\n" +
                              "¿Desea abrir el diálogo de impresión ahora?";
                
                if (confirm(mensaje)) {
                    window.print();
                }
            }
            
            // También permitir impresión con botón
            document.addEventListener("keydown", function(e) {
                if (e.ctrlKey && e.key === "p") {
                    e.preventDefault();
                    window.print();
                }
            });
        </script>
        
        <!-- Botón de impresión visible -->
        <div style="position: fixed; top: 10px; right: 10px; z-index: 1000; background: #007bff; color: white; padding: 10px; border-radius: 5px; cursor: pointer; font-size: 12px;" onclick="window.print()">
            🖨️ Imprimir/Guardar PDF
        </div>';
        
        $html = str_replace('</body>', $scriptImpresion . '</body>', $html);
        
        return $html;
    }

    /**
     * Obtener filtros para variantes
     */
    private function getFiltrosVariantes(Request $request): array
    {
        return [
            'producto_id' => $request->get('producto_id'),
            'estado_stock' => $request->get('estado_stock'),
            'disponible' => $request->get('disponible')
        ];
    }
}
