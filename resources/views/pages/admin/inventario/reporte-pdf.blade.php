<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inventario</title>
    <style>
        @@page { margin: 20mm 15mm; }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            font-size: 10px;
            color: #2c3e50;
            line-height: 1.6;
            background-color: #ffffff;
        }
        .accent { color: #3A7AFE; }
        .header-title { font-size: 26px; letter-spacing: 1px; font-weight: 300; color: #3A7AFE; }
        .invoice-number { margin-top: 5px; font-size: 12px; color: #555; }
        .divider { height: 1px; background: #cbd8ff; margin: 24px 0; }
        .section-title { font-size: 10px; letter-spacing: 1px; text-transform: uppercase; color: #3A7AFE; margin-bottom: 10px; font-weight: 600; }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm 15mm;
            background-color: #ffffff;
        }
        
        /* Header minimalista */
        .header {
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        
        .company {
            flex: 1;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 300;
            letter-spacing: 2px;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        
        .company-tagline {
            font-size: 9px;
            color: #7a7a7a;
            font-weight: 400;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }
        
        .report-meta {
            text-align: right;
        }
        
        .report-title { font-size: 11px; font-weight: 400; letter-spacing: 2px; color: #1a1a1a; margin-bottom: 12px; text-transform: uppercase; }
        
        .report-date {
            font-size: 9px;
            color: #7a7a7a;
            margin-bottom: 12px;
        }
        
        .period {
            font-size: 9px;
            color: #5a5a5a;
            margin-bottom: 8px;
        }
        
        /* Secciones */
        .section {
            margin-bottom: 35px;
        }
        
        .section-title { font-size: 10px; letter-spacing: 1px; text-transform: uppercase; color: #3A7AFE; margin-bottom: 10px; font-weight: 600; }
        
        /* Estadísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            padding: 15px;
            border: 1px solid #e0e0e0;
            background-color: #fafafa;
        }
        
        .stat-label {
            font-size: 8px;
            color: #7a7a7a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-value {
            font-size: 16px;
            font-weight: 400;
            color: #1a1a1a;
        }
        
        /* Tablas */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table thead { background-color: transparent; }
        
        .table th { font-size: 9px; font-weight: 600; color: #3A7AFE; text-align: left; padding: 10px 0 10px 0; border-bottom: 1px solid #3A7AFE; text-transform: uppercase; letter-spacing: 1px; }
        
        .table td { font-size: 10px; color: #1a1a1a; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
        .table tbody tr { page-break-inside: avoid; }
        .table tbody tr:nth-child(even) { background-color: #fcfcfc; }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .table tbody tr:hover {
            background-color: #fafafa;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 8px;
            font-weight: 500;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .badge-success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .badge-warning {
            background-color: #fff3e0;
            color: #e65100;
        }
        
        .badge-danger {
            background-color: #ffebee;
            color: #c62828;
        }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 8px;
            color: #7a7a7a;
        }
        
        /* Utilidades */
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: 600;
        }
        
        .mb-10 {
            margin-bottom: 10px;
        }
        
        .mt-20 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1 class="header-title">4GMovil</h1>
            <div class="invoice-number">
                Reporte de Inventario<br>
                Generado el: {{ now()->format('d/m/Y H:i:s') }}
                <br>Generado por: {{ auth()->user()->nombre ?? auth()->user()->name ?? auth()->user()->email ?? 'Usuario' }}
                @php $rolVal = auth()->user()->rol ?? null; @endphp
                @if($rolVal)
                <br>Rol: {{ is_string($rolVal) ? $rolVal : ($rolVal->nombre ?? $rolVal->name ?? 'Rol') }}
                @endif
                @if(isset($fecha_inicio) && isset($fecha_fin))
                <br>Período: {{ $fecha_inicio->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}
                @endif
            </div>
        </div>
        <div class="divider"></div>

        <!-- Resumen General -->
        <div class="section">
            <div class="section-title">Resumen General</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Productos</div>
                    <div class="stat-value">{{ $estadisticas['total_productos'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Variantes</div>
                    <div class="stat-value">{{ $estadisticas['total_variantes'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Valor Total</div>
                    <div class="stat-value">${{ number_format($estadisticas['valor_inventario'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Stock Total</div>
                    <div class="stat-value">{{ number_format($estadisticas['stock_total'] ?? 0) }}</div>
                </div>
            </div>
        </div>

        <!-- Alertas de Inventario -->
        <div class="section">
            <div class="section-title">Alertas de Inventario</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Sin Stock</div>
                    <div class="stat-value">{{ $estadisticas['productos_sin_stock'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Stock Crítico</div>
                    <div class="stat-value">{{ $estadisticas['productos_stock_critico'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Stock Bajo</div>
                    <div class="stat-value">{{ $estadisticas['productos_stock_bajo'] ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Entradas</div>
                    <div class="stat-value">{{ $estadisticas['movimientos_entrada'] ?? 0 }}</div>
                </div>
            </div>
            <div class="stats-grid mt-20">
                <div class="stat-card">
                    <div class="stat-label">Salidas</div>
                    <div class="stat-value">{{ $estadisticas['movimientos_salida'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Productos en Inventario -->
        @if($productos->count() > 0)
        <div class="section">
            <div class="section-title">Productos en Inventario</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Marca</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th class="text-right">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $producto)
                    <tr>
                        <td>
                            <div class="font-bold">{{ $producto->nombre_producto }}</div>
                            <div style="font-size: 8px; color: #7a7a7a;">ID: {{ $producto->producto_id }}</div>
                        </td>
                        <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                        <td>{{ $producto->marca->nombre ?? 'Sin marca' }}</td>
                        <td>
                            @php
                                $umbralCritico = $producto->getAttribute('umbral_stock_critico');
                                $umbralBajo = $producto->getAttribute('umbral_stock_bajo');
                                if ($umbralCritico === null || $umbralCritico <= 0) {
                                    $umbralCritico = ($producto->stock_inicial ?? 0) > 0 ? (int) ceil(($producto->stock_inicial * 20) / 100) : 5;
                                }
                                if ($umbralBajo === null || $umbralBajo <= 0) {
                                    $umbralBajo = ($producto->stock_inicial ?? 0) > 0 ? (int) ceil(($producto->stock_inicial * 60) / 100) : 0;
                                }
                            @endphp
                            @if($producto->stock <= 0 || $producto->stock <= $umbralCritico)
                                <span class="badge badge-danger">{{ $producto->stock }} unidades</span>
                            @elseif($producto->stock <= $umbralBajo)
                                <span class="badge badge-warning">{{ $producto->stock }} unidades</span>
                            @else
                                <span class="badge badge-success">{{ $producto->stock }} unidades</span>
                            @endif
                        </td>
                        <td>${{ number_format($producto->precio, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">${{ number_format($producto->stock * $producto->precio, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Movimientos Recientes -->
        @if(isset($movimientos) && $movimientos->count() > 0)
        <div class="section">
            <div class="section-title">Movimientos Recientes</div>
            <table class="table">
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
                <tbody>
                    @foreach($movimientos->take(20) as $movimiento)
                    <tr>
                        <td>{{ $movimiento->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $movimiento->variante->producto->nombre_producto ?? 'N/A' }}</td>
                        <td>{{ $movimiento->variante->nombre ?? 'N/A' }}</td>
                        <td>
                            @php $tipoVal = $movimiento->tipo_movimiento ?? $movimiento->tipo; @endphp
                            @if($tipoVal === 'entrada')
                                <span class="badge badge-success">{{ ucfirst($tipoVal) }}</span>
                            @else
                                <span class="badge badge-danger">{{ ucfirst($tipoVal) }}</span>
                            @endif
                        </td>
                        <td>{{ $movimiento->cantidad }}</td>
                        <td>{{ $movimiento->motivo }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            4GMovil – Reporte de Inventario
            <br>Gracias por tu preferencia
            <br>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $size = 8;
            $pdf->page_text(520, 820, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, $size, array(0.47,0.47,0.47));
        }
    </script>
</body>
</html>
