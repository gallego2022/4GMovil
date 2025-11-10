<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - Pedido #{{ $pedido->pedido_id }}</title>
    <style>
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
        
        .company-contact {
            font-size: 9px;
            color: #5a5a5a;
            line-height: 1.8;
        }
        
        .company-contact p {
            margin: 2px 0;
        }
        
        .invoice-meta {
            text-align: right;
        }
        
        .invoice-title {
            font-size: 11px;
            font-weight: 400;
            letter-spacing: 3px;
            color: #1a1a1a;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .invoice-number {
            font-size: 18px;
            font-weight: 300;
            color: #1a1a1a;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .invoice-date {
            font-size: 9px;
            color: #7a7a7a;
            margin-bottom: 12px;
        }
        
        .status {
            display: inline-block;
            font-size: 8px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 4px 12px;
            border: 1px solid #d0d0d0;
            color: #5a5a5a;
        }
        
        .status-confirmado {
            border-color: #4a9b4a;
            color: #4a9b4a;
        }
        
        .status-pendiente {
            border-color: #d4a574;
            color: #d4a574;
        }
        
        .status-cancelado {
            border-color: #c44;
            color: #c44;
        }
        
        /* Información del cliente y dirección - Layout de dos columnas */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .info-block {
            padding: 0;
        }
        
        .info-label {
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #7a7a7a;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-content {
            font-size: 10px;
            color: #2c3e50;
            line-height: 1.8;
        }
        
        .info-content p {
            margin: 6px 0;
        }
        
        .info-content strong {
            font-weight: 500;
            color: #1a1a1a;
            display: inline-block;
            min-width: 80px;
            font-size: 9px;
        }
        
        /* Tabla de productos minimalista */
        .products-section {
            margin-bottom: 30px;
        }
        
        .table-header {
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #7a7a7a;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 15px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .products-table thead {
            border-bottom: 2px solid #1a1a1a;
        }
        
        .products-table th {
            padding: 12px 0;
            text-align: left;
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #1a1a1a;
        }
        
        .products-table th:first-child {
            padding-left: 0;
        }
        
        .products-table th:last-child {
            padding-right: 0;
            text-align: right;
        }
        
        .products-table td {
            padding: 14px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 10px;
            vertical-align: top;
        }
        
        .products-table td:first-child {
            padding-left: 0;
        }
        
        .products-table td:last-child {
            padding-right: 0;
            text-align: right;
        }
        
        .products-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .product-name {
            font-weight: 400;
            color: #1a1a1a;
            margin-bottom: 3px;
        }
        
        .product-variant {
            font-size: 9px;
            color: #7a7a7a;
            font-weight: 300;
            font-style: italic;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Resumen de totales minimalista */
        .summary-section {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        
        .summary {
            width: 280px;
            padding: 0;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-row.total {
            border-top: 2px solid #1a1a1a;
            border-bottom: 2px solid #1a1a1a;
            padding: 12px 0;
            margin-top: 8px;
        }
        
        .summary-label {
            color: #7a7a7a;
            font-weight: 400;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        
        .summary-value {
            color: #1a1a1a;
            font-weight: 400;
        }
        
        .summary-row.total .summary-label {
            color: #1a1a1a;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .summary-row.total .summary-value {
            color: #1a1a1a;
            font-size: 14px;
            font-weight: 300;
            letter-spacing: 0.5px;
        }
        
        /* Información de pago minimalista */
        .payment-section {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 1px solid #e0e0e0;
        }
        
        .payment-label {
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #7a7a7a;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .payment-content {
            font-size: 10px;
            color: #2c3e50;
            line-height: 1.8;
        }
        
        .payment-content p {
            margin: 6px 0;
        }
        
        .payment-content strong {
            font-weight: 500;
            color: #1a1a1a;
            display: inline-block;
            min-width: 100px;
            font-size: 9px;
        }
        
        .payment-status {
            text-transform: capitalize;
            font-weight: 400;
            color: #5a5a5a;
        }
        
        /* Pie de página minimalista */
        .footer {
            margin-top: 50px;
            padding-top: 25px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
        }
        
        .footer-content {
            font-size: 8px;
            color: #7a7a7a;
            line-height: 1.8;
        }
        
        .footer-content p {
            margin: 4px 0;
        }
        
        .footer-company {
            font-weight: 400;
            color: #5a5a5a;
            margin-bottom: 8px;
        }
        
        .footer-thanks {
            color: #7a7a7a;
            font-style: italic;
            margin: 8px 0;
        }
        
        .footer-date {
            color: #9a9a9a;
            font-size: 7px;
            margin-top: 12px;
        }
        
        /* Utilidades */
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 30px 0;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
                background-color: white;
            }
            
            .container {
                padding: 15mm 10mm;
                max-width: 100%;
            }
        }
        
        @page {
            margin: 0;
            size: A4;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <div class="header-top">
                <div class="company">
                    <div class="company-name">4GMovil</div>
                    <div class="company-tagline">Tu tienda de tecnología móvil</div>
                    <div class="company-contact">
                        <p>Teléfono: +57 302 597 0220</p>
                        <p>Email: contacto@4gmovil.com</p>
                        <p>Web: www.4gmovil.com</p>
                    </div>
                </div>
                <div class="invoice-meta">
                    <div class="invoice-title">Factura</div>
                    <div class="invoice-number">#{{ str_pad($pedido->pedido_id, 6, '0', STR_PAD_LEFT) }}</div>
                    <div class="invoice-date">
                        @if($pedido->fecha_pedido instanceof \DateTime)
                            {{ $pedido->fecha_pedido->format('d/m/Y') }}
                        @else
                            {{ date('d/m/Y', strtotime($pedido->fecha_pedido)) }}
                        @endif
                    </div>
                    <div class="status status-{{ strtolower($pedido->estado->nombre) }}">
                        {{ $pedido->estado->nombre }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del cliente y dirección -->
        <div class="info-grid">
            <div class="info-block">
                <div class="info-label">Cliente</div>
                <div class="info-content">
                    <p><strong>Nombre:</strong> {{ $pedido->usuario->nombre ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $pedido->usuario->correo_electronico ?? 'N/A' }}</p>
                    @if($pedido->usuario->telefono ?? null)
                        <p><strong>Teléfono:</strong> {{ $pedido->usuario->telefono }}</p>
                    @endif
                </div>
            </div>
            <div class="info-block">
                <div class="info-label">Dirección de Envío</div>
                <div class="info-content">
                    <p><strong>Destinatario:</strong> {{ $pedido->direccion->nombre_destinatario }}</p>
                    <p>{{ $pedido->direccion->direccion_completa }}</p>
                    <p>{{ $pedido->direccion->ciudad }}, {{ $pedido->direccion->provincia }}</p>
                    <p><strong>Código Postal:</strong> {{ $pedido->direccion->codigo_postal }}</p>
                    <p><strong>Teléfono:</strong> {{ $pedido->direccion->telefono }}</p>
                </div>
            </div>
        </div>

        <!-- Tabla de productos -->
        <div class="products-section">
            <div class="table-header">Productos</div>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-right">Precio Unitario</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedido->detalles as $detalle)
                        <tr>
                            <td>
                                <div class="product-name">{{ $detalle->producto->nombre_producto }}</div>
                                @if($detalle->variante)
                                    <div class="product-variant">Variante: {{ $detalle->variante->nombre }}</div>
                                @endif
                            </td>
                            <td class="text-center">{{ $detalle->cantidad }}</td>
                            <td class="text-right">${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                            <td class="text-right">${{ number_format($detalle->cantidad * $detalle->precio_unitario, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Resumen de totales -->
        <div class="summary-section">
            <div class="summary">
                <div class="summary-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value">${{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Envío</span>
                    <span class="summary-value">Gratis</span>
                </div>
                <div class="summary-row total">
                    <span class="summary-label">Total</span>
                    <span class="summary-value">${{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Información de pago -->
        @if($pedido->pago && $pedido->pago->metodoPago)
            <div class="payment-section">
                <div class="payment-label">Información de Pago</div>
                <div class="payment-content">
                    <p><strong>Método de Pago:</strong> {{ $pedido->pago->metodoPago->nombre }}</p>
                    <p><strong>Estado:</strong> <span class="payment-status">{{ ucfirst($pedido->pago->estado ?? 'pendiente') }}</span></p>
                    @if($pedido->pago->fecha_pago)
                        <p><strong>Fecha de Pago:</strong> 
                            @if($pedido->pago->fecha_pago instanceof \Carbon\Carbon)
                                {{ $pedido->pago->fecha_pago->format('d/m/Y H:i') }}
                            @else
                                {{ \Carbon\Carbon::parse($pedido->pago->fecha_pago)->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    @endif
                    @if($pedido->pago->referencia_externa)
                        <p><strong>Referencia:</strong> {{ $pedido->pago->referencia_externa }}</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Pie de página -->
        <div class="footer">
            <div class="footer-content">
                <p class="footer-company">4GMovil - Tu tienda de tecnología móvil</p>
                <p class="footer-thanks">Gracias por tu compra</p>
                <p class="footer-date">Factura generada el {{ date('d/m/Y H:i:s') }}</p>
                <p class="footer-date">Este documento es válido como comprobante de compra</p>
            </div>
        </div>
    </div>
</body>
</html>

