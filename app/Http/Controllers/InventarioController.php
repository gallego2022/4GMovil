<?php

namespace App\Http\Controllers;

use App\Services\InventarioService;
use App\Models\Producto;
use App\Models\MovimientoInventarioVariante;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    protected $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
    }

    /**
     * Dashboard de inventario
     */
    public function dashboard()
    {
        // Verificación temporal simplificada
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
        }

        // Verificar si el usuario es admin (temporal)
        $user = Auth::user();
        if (!$user || $user->rol !== 'admin') {
            return redirect()->back()->with('error', 'No tienes permisos de administrador.');
        }

        try {
            $alertas = $this->inventarioService->getAlertasInventarioCompletas();
            $productosStockBajo = $this->inventarioService->getProductosStockBajo();
            $productosStockCritico = $this->inventarioService->getProductosStockCritico();
            $valorTotal = $this->inventarioService->getValorTotalInventario();
            
            // Datos de variantes
            $variantesStockBajo = $this->inventarioService->getVariantesStockBajo();
            $variantesSinStock = $this->inventarioService->getVariantesSinStock();
            $reporteVariantes = $this->inventarioService->getReporteInventarioVariantes();

            return view('pages.admin.inventario.dashboard', compact(
                'alertas',
                'productosStockBajo',
                'productosStockCritico',
                'valorTotal',
                'variantesStockBajo',
                'variantesSinStock',
                'reporteVariantes'
            ));
        } catch (\Exception $e) {
            // Si hay error, mostrar datos de prueba
            $alertas = [
                'stock_critico' => 0,
                'stock_bajo' => 0,
                'sin_stock' => 0,
                'stock_excesivo' => 0,
                'necesita_reabastecimiento' => 0,
                'stock_reservado_alto' => 0,
                'productos_inactivos' => 0
            ];
            $productosStockBajo = collect([]);
            $productosStockCritico = collect([]);
            $valorTotal = 0;

            return view('pages.admin.inventario.dashboard', compact(
                'alertas',
                'productosStockBajo',
                'productosStockCritico',
                'valorTotal'
            ));
        }
    }

    /**
     * Lista de productos con alertas de stock
     */
    public function alertas()
    {
        $productosStockBajo = $this->inventarioService->getProductosStockBajo();
        $productosStockCritico = $this->inventarioService->getProductosStockCritico();
        $productosSinStock = $this->inventarioService->getProductosSinStock();
        $productosStockExcesivo = $this->inventarioService->getProductosStockExcesivo();

        return view('pages.admin.inventario.alertas', compact(
            'productosStockBajo',
            'productosStockCritico',
            'productosSinStock',
            'productosStockExcesivo'
        ));
    }

    /**
     * Movimientos de inventario
     */
    public function movimientos(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : now()->subMonth();
        $fechaFin = $request->get('fecha_fin') ? Carbon::parse($request->fecha_fin) : now();
        $productoId = $request->get('producto_id');

        // Inicializar variables
        $producto = null;
        $resumen = null;

        if ($productoId) {
            $movimientos = $this->inventarioService->getMovimientosProducto($productoId, $fechaInicio, $fechaFin);
            $producto = Producto::findOrFail($productoId);
        } else {
            $reporte = $this->inventarioService->getReporteMovimientos($fechaInicio, $fechaFin);
            $movimientos = $reporte['movimientos'];
            $resumen = $reporte['resumen'];
        }

        $productos = Producto::activos()->orderBy('nombre_producto')->get();

        return view('pages.admin.inventario.movimientos', compact(
            'movimientos',
            'productos',
            'fechaInicio',
            'fechaFin',
            'productoId',
            'producto',
            'resumen'
        ));
    }

    /**
     * Registrar entrada de inventario
     */
    public function registrarEntrada(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,producto_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:100'
        ]);

        $success = $this->inventarioService->registrarEntrada(
            $request->producto_id,
            $request->cantidad,
            $request->motivo,
            Auth::user()->usuario_id,
            $request->referencia
        );

        if ($success) {
            return redirect()->back()->with('success', 'Entrada de inventario registrada correctamente.');
        }

        return redirect()->back()->with('error', 'Error al registrar la entrada de inventario.');
    }

    /**
     * Registrar salida de inventario
     */
    public function registrarSalida(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,producto_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'pedido_id' => 'nullable|exists:pedidos,pedido_id'
        ]);

        $success = $this->inventarioService->registrarSalida(
            $request->producto_id,
            $request->cantidad,
            $request->motivo,
            Auth::user()->usuario_id,
            $request->pedido_id
        );

        if ($success) {
            return redirect()->back()->with('success', 'Salida de inventario registrada correctamente.');
        }

        return redirect()->back()->with('error', 'Error al registrar la salida de inventario. Verifique el stock disponible.');
    }

    /**
     * Ajustar stock de un producto
     */
    public function ajustarStock(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,producto_id',
            'nuevo_stock' => 'required|integer|min:0',
            'motivo' => 'required|string|max:255'
        ]);

        $success = $this->inventarioService->ajustarStock(
            $request->producto_id,
            $request->nuevo_stock,
            $request->motivo,
            Auth::user()->usuario_id
        );

        if ($success) {
            return redirect()->back()->with('success', 'Stock ajustado correctamente.');
        }

        return redirect()->back()->with('error', 'Error al ajustar el stock.');
    }

    /**
     * Reporte de inventario
     */
    public function reporte(Request $request)
    {
        $reporte = $this->inventarioService->generarReporteInventario();

        return view('pages.admin.inventario.reporte', compact('reporte'));
    }

    /**
     * Productos más vendidos
     */
    public function productosMasVendidos(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : now()->subMonth();
        $fechaFin = $request->get('fecha_fin') ? Carbon::parse($request->fecha_fin) : now();

        $productos = $this->inventarioService->getProductosMasVendidos(20, $fechaInicio, $fechaFin);

        return view('pages.admin.inventario.productos-mas-vendidos', compact('productos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Valor del inventario por categoría
     */
    public function valorPorCategoria()
    {
        $valorPorCategoria = $this->inventarioService->getValorInventarioPorCategoria();
        $valorTotal = $this->inventarioService->getValorTotalInventario();

        return view('pages.admin.inventario.valor-por-categoria', compact('valorPorCategoria', 'valorTotal'));
    }

    /**
     * Exportar reporte de inventario
     */
    public function exportarReporte(Request $request)
    {
        $reporte = $this->inventarioService->generarReporteInventario();

        // Aquí puedes implementar la exportación a PDF o Excel
        // Por ahora retornamos una vista con los datos

        return view('pages.admin.inventario.exportar-reporte', compact('reporte'));
    }

    /**
     * Exportar reporte a PDF
     */
    public function exportarPDF(Request $request)
    {
        $request->validate([
            'secciones' => 'required|array|min:1',
            'secciones.*' => 'in:resumen,alertas,productos,categorias'
        ]);

        try {
            $reporte = $this->inventarioService->generarReporteInventario();
            $secciones = $request->secciones;
            $fecha = now()->format('Y-m-d_H-i-s');

            // Generar contenido HTML para PDF
            $html = view('pages.admin.inventario.pdf.reporte', compact('reporte', 'secciones'))->render();

            // Devolver HTML que será convertido a PDF con JavaScript
            return response()->json([
                'success' => true,
                'html' => $html,
                'filename' => "reporte_inventario_{$fecha}.pdf"
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportarExcel(Request $request)
    {
        $request->validate([
            'secciones' => 'required|array|min:1',
            'secciones.*' => 'in:resumen,alertas,productos,categorias'
        ]);

        try {
            $reporte = $this->inventarioService->generarReporteInventario();
            $secciones = $request->secciones;
            $fecha = now()->format('Y-m-d_H-i-s');

            // Crear contenido CSV simple
            $csvContent = $this->generarCSV($reporte, $secciones);

            $filename = "reporte_inventario_{$fecha}.csv";

            return response($csvContent)
                ->header('Content-Type', 'text/csv; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            Log::error('Error al generar CSV: ' . $e->getMessage());
            return response()->json(['error' => 'Error al generar CSV: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Generar contenido CSV (método original mantenido para compatibilidad)
     */
    private function generarCSV($reporte, $secciones)
    {
        $csv = [];

        // Encabezado mejorado
        $csv[] = ['═══════════════════════════════════════════════════════════════════════════════'];
        $csv[] = ['                           REPORTE DE INVENTARIO - 4GMovil'];
        $csv[] = ['═══════════════════════════════════════════════════════════════════════════════'];
        $csv[] = ['Fecha de Generación: ' . now()->format('d/m/Y H:i:s')];
        $csv[] = ['Generado por: ' . (Auth::user()->nombre ?? 'Administrador')];
        $csv[] = ['═══════════════════════════════════════════════════════════════════════════════'];
        $csv[] = [];

        foreach ($secciones as $seccion) {
            switch ($seccion) {
                case 'resumen':
                    $csv = array_merge($csv, $this->generarResumenCSV($reporte));
                    break;
                case 'alertas':
                    $csv = array_merge($csv, $this->generarAlertasCSV($reporte));
                    break;
                case 'productos':
                    $csv = array_merge($csv, $this->generarProductosCSV($reporte));
                    break;
                case 'categorias':
                    $csv = array_merge($csv, $this->generarCategoriasCSV($reporte));
                    break;
            }
            $csv[] = []; // Línea en blanco entre secciones
        }

        // Pie del reporte
        $csv[] = ['═══════════════════════════════════════════════════════════════════════════════'];
        $csv[] = ['Fin del Reporte - 4GMovil'];
        $csv[] = ['═══════════════════════════════════════════════════════════════════════════════'];

        // Convertir a string CSV
        $output = fopen('php://temp', 'r+');
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvString = stream_get_contents($output);
        fclose($output);

        return $csvString;
    }



    /**
     * Generar resumen CSV (método original)
     */
    private function generarResumenCSV($reporte)
    {
        return [
            ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'],
            ['                                    RESUMEN GENERAL DEL INVENTARIO'],
            ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'],
            [''],
            ['INFORMACIÓN GENERAL:'],
            ['• Total de Productos:', $reporte['resumen_general']['total_productos'] ?? 0],
            ['• Productos Activos', $reporte['resumen_general']['productos_activos'] ?? 0],
            ['• Productos con Stock Bajo', $reporte['resumen_general']['productos_stock_bajo'] ?? 0],
            ['• Productos Sin Stock', $reporte['resumen_general']['productos_sin_stock'] ?? 0],
            [''],
            ['INVENTARIO:'],
            ['• Stock Total en Inventario', $reporte['resumen_general']['stock_total'] ?? 0],
            ['• Valor Total del Inventario', '$' . number_format($reporte['resumen_general']['valor_total_inventario'] ?? 0, 2)],
            [''],
            ['ESTADÍSTICAS ADICIONALES:'],
            ['• Promedio de Stock por Producto', $reporte['resumen_general']['total_productos'] > 0 ? round(($reporte['resumen_general']['stock_total'] ?? 0) / $reporte['resumen_general']['total_productos'], 2) : 0],
            ['• Valor Promedio por Producto', $reporte['resumen_general']['total_productos'] > 0 ? '$' . number_format(($reporte['resumen_general']['valor_total_inventario'] ?? 0) / $reporte['resumen_general']['total_productos'], 2) : '$0.00'],
            ['• Porcentaje de Productos Activos', $reporte['resumen_general']['total_productos'] > 0 ? round((($reporte['resumen_general']['productos_activos'] ?? 0) / $reporte['resumen_general']['total_productos']) * 100, 1) . '%' : '0%'],
            ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━']
        ];
    }



    /**
     * Generar alertas CSV (método original)
     */
    private function generarAlertasCSV($reporte)
    {
        return [
            ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'],
            ['                                    ALERTAS Y NOTIFICACIONES DEL INVENTARIO'],
            ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'],
            [''],
            ['RESUMEN DE ALERTAS:'],
            ['• Productos con Stock Crítico', $reporte['alertas']['stock_critico'] ?? 0],
            ['• Productos con Stock Bajo', $reporte['alertas']['stock_bajo'] ?? 0],
            ['• Productos Sin Stock', $reporte['alertas']['sin_stock'] ?? 0],
            ['• Productos con Stock Excesivo', $reporte['alertas']['stock_excesivo'] ?? 0],
            [''],
            ['TOTAL DE ALERTAS:', ($reporte['alertas']['stock_critico'] ?? 0) + ($reporte['alertas']['stock_bajo'] ?? 0) + ($reporte['alertas']['sin_stock'] ?? 0) + ($reporte['alertas']['stock_excesivo'] ?? 0)],
            [''],
            ['DEFINICIONES:'],
            ['• Stock Crítico: Productos con stock menor al 20% del mínimo requerido'],
            ['• Stock Bajo: Productos con stock entre 20% y 50% del mínimo requerido'],
            ['• Sin Stock: Productos con stock igual a 0'],
            ['• Stock Excesivo: Productos con stock muy por encima del mínimo requerido'],
            ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━']
        ];
    }



    /**
     * Generar productos CSV (método original)
     */
    private function generarProductosCSV($reporte)
    {
        $rows = [
            ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'],
            ['                                    PRODUCTOS CON STOCK BAJO'],
            ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'],
            [''],
            ['INFORMACIÓN DE LA TABLA:'],
            ['• Esta sección muestra todos los productos que requieren atención inmediata'],
            ['• Se incluyen productos con stock bajo, crítico o sin stock'],
            ['• Los datos están ordenados por prioridad de atención'],
            [''],
            ['DETALLE DE PRODUCTOS:'],
            ['ID', 'Nombre del Producto', 'Stock Actual', 'Stock Mínimo', 'Precio Unitario', 'Categoría', 'Estado', 'Valor en Inventario']
        ];

        $totalValorInventario = 0;
        $productosSinStock = 0;
        $productosStockBajo = 0;
        $productosStockCritico = 0;

        foreach ($reporte['productos_stock_bajo'] as $producto) {
            $estado = $producto->stock == 0 ? 'SIN STOCK' : ($producto->stock < ($producto->stock_minimo * 0.2) ? 'CRÍTICO' : ($producto->stock < $producto->stock_minimo ? 'BAJO' : 'NORMAL'));

            $valorInventario = $producto->stock * $producto->precio;
            $totalValorInventario += $valorInventario;

            // Contar por estado
            if ($producto->stock == 0) $productosSinStock++;
            elseif ($producto->stock < ($producto->stock_minimo * 0.2)) $productosStockCritico++;
            elseif ($producto->stock < $producto->stock_minimo) $productosStockBajo++;

            $rows[] = [
                $producto->producto_id,
                $producto->nombre_producto,
                $producto->stock,
                $producto->stock_minimo,
                '$' . number_format($producto->precio, 2),
                $producto->categoria->nombre_categoria ?? 'Sin categoría',
                $estado,
                '$' . number_format($valorInventario, 2)
            ];
        }

        // Agregar resumen estadístico
        $rows[] = [''];
        $rows[] = ['RESUMEN ESTADÍSTICO:'];
        $rows[] = ['• Total de Productos Analizados', count($reporte['productos_stock_bajo'])];
        $rows[] = ['• Productos Sin Stock', $productosSinStock];
        $rows[] = ['• Productos con Stock Crítico', $productosStockCritico];
        $rows[] = ['• Productos con Stock Bajo', $productosStockBajo];
        $rows[] = ['• Productos con Stock Normal', count($reporte['productos_stock_bajo']) - $productosSinStock - $productosStockCritico - $productosStockBajo];
        $rows[] = ['• Valor Total en Inventario', '$' . number_format($totalValorInventario, 2)];
        $rows[] = ['• Valor Promedio por Producto', count($reporte['productos_stock_bajo']) > 0 ? '$' . number_format($totalValorInventario / count($reporte['productos_stock_bajo']), 2) : '$0.00'];
        $rows[] = ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'];

        return $rows;
    }



    /**
     * Generar categorías CSV (método original)
     */
    private function generarCategoriasCSV($reporte)
    {
        $rows = [
            ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'],
            ['                                    ANÁLISIS DE VALOR POR CATEGORÍA'],
            ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'],
            [''],
            ['INFORMACIÓN DEL ANÁLISIS:'],
            ['• Este análisis muestra la distribución del valor del inventario por categoría'],
            ['• Se calculan porcentajes y promedios para cada categoría'],
            ['• Los datos ayudan a identificar las categorías más valiosas'],
            [''],
            ['RESUMEN GENERAL:'],
            ['• Total de Categorías Analizadas', count($reporte['valor_por_categoria'])],
            ['• Valor Total del Inventario', '$' . number_format($reporte['resumen_general']['valor_total_inventario'] ?? 0, 2)],
            [''],
            ['DETALLE POR CATEGORÍA:'],
            ['Categoría', 'Cantidad de Productos', 'Stock Total', 'Valor Total', 'Porcentaje del Total', 'Valor Promedio por Producto']
        ];

        $valorTotal = $reporte['resumen_general']['valor_total_inventario'] ?? 0;
        $totalProductos = 0;
        $categoriaMaxValor = null;
        $categoriaMaxProductos = null;
        $maxValor = 0;
        $maxProductos = 0;

        foreach ($reporte['valor_por_categoria'] as $item) {
            $porcentaje = $valorTotal > 0 ? ($item['valor_total'] / $valorTotal) * 100 : 0;
            $valorPromedio = $item['productos_count'] > 0 ? $item['valor_total'] / $item['productos_count'] : 0;
            $totalProductos += $item['productos_count'];

            // Encontrar categorías destacadas
            if ($item['valor_total'] > $maxValor) {
                $maxValor = $item['valor_total'];
                $categoriaMaxValor = $item['categoria']->nombre_categoria ?? 'Sin categoría';
            }

            if ($item['productos_count'] > $maxProductos) {
                $maxProductos = $item['productos_count'];
                $categoriaMaxProductos = $item['categoria']->nombre_categoria ?? 'Sin categoría';
            }

            $rows[] = [
                $item['categoria']->nombre_categoria ?? 'Sin categoría',
                $item['productos_count'] ?? 0,
                $item['stock_total'] ?? 0,
                '$' . number_format($item['valor_total'], 2),
                number_format($porcentaje, 1) . '%',
                '$' . number_format($valorPromedio, 2)
            ];
        }

        // Agregar análisis adicional
        $rows[] = [''];
        $rows[] = ['ANÁLISIS ADICIONAL:'];
        $rows[] = ['• Categoría con Mayor Valor', $categoriaMaxValor ?? 'N/A'];
        $rows[] = ['• Categoría con Más Productos', $categoriaMaxProductos ?? 'N/A'];
        $rows[] = ['• Total de Productos en Todas las Categorías', $totalProductos];
        $rows[] = ['• Valor Promedio por Categoría', count($reporte['valor_por_categoria']) > 0 ? '$' . number_format($valorTotal / count($reporte['valor_por_categoria']), 2) : '$0.00'];
        $rows[] = ['• Productos Promedio por Categoría', count($reporte['valor_por_categoria']) > 0 ? round($totalProductos / count($reporte['valor_por_categoria']), 1) : 0];
        $rows[] = ['━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'];

        return $rows;
    }

    // ==================== MÉTODOS PARA VARIANTES ====================

    /**
     * Dashboard de variantes
     */
    public function dashboardVariantes()
    {
        try {
            $variantesStockBajo = $this->inventarioService->getVariantesStockBajo();
            $variantesSinStock = $this->inventarioService->getVariantesSinStock();
            $variantesNecesitanReposicion = $this->inventarioService->getVariantesNecesitanReposicion();
            $reporteVariantes = $this->inventarioService->getReporteInventarioVariantes();

            return view('pages.admin.inventario.variantes.dashboard', compact(
                'variantesStockBajo',
                'variantesSinStock',
                'variantesNecesitanReposicion',
                'reporteVariantes'
            ));
        } catch (\Exception $e) {
            Log::error('Error en dashboard de variantes', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al cargar el dashboard de variantes');
        }
    }

    /**
     * Registrar entrada de stock para variante
     */
    public function registrarEntradaVariante(Request $request)
    {
        $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:255'
        ]);

        try {
            $resultado = $this->inventarioService->registrarEntradaVariante(
                $request->variante_id,
                $request->cantidad,
                $request->motivo,
                Auth::id(),
                $request->referencia
            );

            if ($resultado) {
                return redirect()->back()->with('success', 'Entrada de stock registrada correctamente');
            }

            return redirect()->back()->with('error', 'Error al registrar la entrada de stock');
        } catch (\Exception $e) {
            Log::error('Error al registrar entrada de variante', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error interno del servidor');
        }
    }

    /**
     * Registrar salida de stock para variante
     */
    public function registrarSalidaVariante(Request $request)
    {
        $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255',
            'referencia' => 'nullable|string|max:255'
        ]);

        try {
            $resultado = $this->inventarioService->registrarSalidaVariante(
                $request->variante_id,
                $request->cantidad,
                $request->motivo,
                Auth::id(),
                $request->referencia
            );

            if ($resultado) {
                return redirect()->back()->with('success', 'Salida de stock registrada correctamente');
            }

            return redirect()->back()->with('error', 'Error al registrar la salida de stock');
        } catch (\Exception $e) {
            Log::error('Error al registrar salida de variante', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error interno del servidor');
        }
    }

    /**
     * Ajustar stock de variante
     */
    public function ajustarStockVariante(Request $request)
    {
        $request->validate([
            'variante_id' => 'required|integer|exists:variantes_producto,variante_id',
            'nuevo_stock' => 'required|integer|min:0',
            'motivo' => 'required|string|max:255'
        ]);

        try {
            $resultado = $this->inventarioService->ajustarStockVariante(
                $request->variante_id,
                $request->nuevo_stock,
                $request->motivo,
                Auth::id()
            );

            if ($resultado) {
                return redirect()->back()->with('success', 'Stock ajustado correctamente');
            }

            return redirect()->back()->with('error', 'Error al ajustar el stock');
        } catch (\Exception $e) {
            Log::error('Error al ajustar stock de variante', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error interno del servidor');
        }
    }

    /**
     * Reporte de variantes
     */
    public function reporteVariantes(Request $request)
    {
        try {
            $productoId = $request->get('producto_id');
            $reporte = $this->inventarioService->getReporteInventarioVariantes($productoId);

            if ($request->wantsJson()) {
                return response()->json($reporte);
            }

            return view('pages.admin.inventario.variantes.reporte', compact('reporte'));
        } catch (\Exception $e) {
            Log::error('Error en reporte de variantes', ['error' => $e->getMessage()]);
            
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Error al generar reporte'], 500);
            }

            return redirect()->back()->with('error', 'Error al generar el reporte');
        }
    }

    /**
     * Movimientos de variantes
     */
    public function movimientosVariantes(Request $request)
    {
        try {
            $fechaInicio = $request->get('fecha_inicio') ? Carbon::parse($request->fecha_inicio) : now()->subMonth();
            $fechaFin = $request->get('fecha_fin') ? Carbon::parse($request->fecha_fin) : now();
            $varianteId = $request->get('variante_id');
            $tipoMovimiento = $request->get('tipo_movimiento');

            $query = MovimientoInventarioVariante::with(['variante.producto', 'usuario'])
                ->whereBetween('fecha_movimiento', [$fechaInicio, $fechaFin]);

            if ($varianteId) {
                $query->where('variante_id', $varianteId);
            }

            if ($tipoMovimiento) {
                $query->where('tipo_movimiento', $tipoMovimiento);
            }

            $movimientos = $query->orderBy('fecha_movimiento', 'desc')->paginate(20);

            return view('pages.admin.inventario.variantes.movimientos', compact(
                'movimientos',
                'fechaInicio',
                'fechaFin',
                'varianteId',
                'tipoMovimiento'
            ));
        } catch (\Exception $e) {
            Log::error('Error en movimientos de variantes', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al cargar los movimientos');
        }
    }
}
