<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Inventario - 4GMovil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
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
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
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
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
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
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE INVENTARIO - 4GMovil</h1>
        <p>Fecha: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @if(in_array('resumen', $secciones))
    <div class="section">
        <h2>RESUMEN GENERAL</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <h3>Total Productos</h3>
                <p>{{ $reporte['resumen_general']['total_productos'] ?? 0 }}</p>
            </div>
            <div class="summary-item">
                <h3>Productos Activos</h3>
                <p>{{ $reporte['resumen_general']['productos_activos'] ?? 0 }}</p>
            </div>
            <div class="summary-item">
                <h3>Valor Total</h3>
                <p>${{ number_format($reporte['resumen_general']['valor_total_inventario'] ?? 0, 2) }}</p>
            </div>
            <div class="summary-item">
                <h3>Stock Total</h3>
                <p>{{ $reporte['resumen_general']['stock_total'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(in_array('alertas', $secciones))
    <div class="section">
        <h2>ALERTAS DE INVENTARIO</h2>
        <table>
            <thead>
                <tr>
                    <th>Tipo de Alerta</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Stock Crítico</td>
                    <td>{{ $reporte['alertas']['stock_critico'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Stock Bajo</td>
                    <td>{{ $reporte['alertas']['stock_bajo'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Sin Stock</td>
                    <td>{{ $reporte['alertas']['sin_stock'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Stock Excesivo</td>
                    <td>{{ $reporte['alertas']['stock_excesivo'] ?? 0 }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    @if(in_array('productos', $secciones) && isset($reporte['productos_stock_bajo']))
    <div class="section">
        <h2>PRODUCTOS CON STOCK BAJO</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Stock</th>
                    <th>Stock Mínimo</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte['productos_stock_bajo'] as $producto)
                <tr>
                    <td>{{ $producto->producto_id }}</td>
                    <td>{{ $producto->nombre_producto }}</td>
                    <td>{{ $producto->stock }}</td>
                    <td>{{ $producto->stock_minimo }}</td>
                    <td>${{ number_format($producto->precio, 2) }}</td>
                    <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(in_array('categorias', $secciones) && isset($reporte['valor_por_categoria']))
    <div class="section">
        <h2>VALOR POR CATEGORÍA</h2>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Productos</th>
                    <th>Stock Total</th>
                    <th>Valor Total</th>
                    <th>Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte['valor_por_categoria'] as $item)
                @php
                    $valorTotal = $reporte['resumen_general']['valor_total_inventario'] ?? 0;
                    $porcentaje = $valorTotal > 0 ? ($item['valor_total'] / $valorTotal) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $item['categoria']->nombre ?? 'Sin categoría' }}</td>
                    <td>{{ $item['productos_count'] ?? 0 }}</td>
                    <td>{{ $item['stock_total'] ?? 0 }}</td>
                    <td>${{ number_format($item['valor_total'], 2) }}</td>
                    <td>{{ number_format($porcentaje, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>Reporte generado automáticamente por el sistema 4GMovil</p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 
