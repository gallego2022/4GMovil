<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $pedido->pedido_id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            margin: 40px;
        }

        .accent {
            color: #3A7AFE;
        }

        /* Encabezado */
        .header-title {
            font-size: 26px;
            letter-spacing: 1px;
            font-weight: 300;
            color: #3A7AFE;
        }

        .invoice-number {
            margin-top: 5px;
            font-size: 14px;
            color: #555;
        }

        /* Línea divisoria */
        .divider {
            height: 1px;
            background: #cbd8ff;
            margin: 30px 0;
        }

        /* Título de sección */
        .section-title {
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #3A7AFE;
            margin-bottom: 10px;
            font-weight: 600;
        }

        /* Información */
        .info p {
            margin: 3px 0;
            font-size: 11px;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead th {
            text-align: left;
            padding-bottom: 10px;
            font-size: 9px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #3A7AFE;
            border-bottom: 1px solid #3A7AFE;
        }

        table tbody td {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        /* Contenedor de totales y pago */
        .summary-payment-grid {
            width: 100%;
            margin-top: 40px;
            overflow: hidden;
        }

        /* Totales a la izquierda */
        .summary-left {
            float: left;
            width: 230px;
        }

        /* Pago a la derecha */
        .summary-right {
            float: right;
            width: 230px;
        }

        /* Filas de totales */
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .summary-row.total {
            border-top: 2px solid #3A7AFE;
            border-bottom: 2px solid #3A7AFE;
            font-size: 14px;
            padding: 12px 0;
            font-weight: 300;
            color: #1a1a1a;
            margin-top: 10px;
        }

        /* Caja de pago */
        .payment-box {
            padding: 15px 20px;
            border: 1px solid #cbd8ff;
            border-radius: 6px;
            word-wrap: break-word;
        }

        .footer {
            text-align: center;
            margin-top: 60px;
            font-size: 9px;
            color: #777;
            line-height: 1.6;
        }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <div class="header">
        <h1 class="header-title">4GMovil</h1>

        <div class="invoice-number">
            Factura #{{ str_pad($pedido->pedido_id, 6, '0', STR_PAD_LEFT) }}
            <br>
            Fecha:
            @if($pedido->fecha_pedido instanceof \DateTime)
            {{ $pedido->fecha_pedido->format('d/m/Y') }}
            @else
            {{ date('d/m/Y', strtotime($pedido->fecha_pedido)) }}
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <!-- CLIENTE -->
    <div class="section-title">Cliente</div>
    <div class="info">
        <p><strong>Nombre:</strong> {{ $pedido->usuario->nombre ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $pedido->usuario->correo_electronico ?? 'N/A' }}</p>
        @if($pedido->usuario->telefono)
        <p><strong>Teléfono:</strong> {{ $pedido->usuario->telefono }}</p>
        @endif
    </div>

    <div class="divider"></div>

    <!-- DIRECCIÓN -->
    <div class="section-title">Dirección de Envío</div>
    <div class="info">
        <p><strong>Destinatario:</strong> {{ $pedido->direccion->nombre_destinatario }}</p>
        <p>{{ $pedido->direccion->direccion_completa }}</p>
        <p>{{ $pedido->direccion->ciudad }}, {{ $pedido->direccion->provincia }}</p>
        <p><strong>Código Postal:</strong> {{ $pedido->direccion->codigo_postal }}</p>
        <p><strong>Teléfono:</strong> {{ $pedido->direccion->telefono }}</p>
    </div>

    <div class="divider"></div>

    <!-- PRODUCTOS -->
    <div class="section-title">Productos</div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-right">Cant</th>
                <th class="text-right">Precio</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $detalle)
            <tr>
                <td>
                    {{ $detalle->producto->nombre_producto }}
                    @if($detalle->variante)
                    <br><span style="font-size:9px; color:#666;">Variante: {{ $detalle->variante->nombre }}</span>
                    @endif
                </td>
                <td class="text-right">{{ $detalle->cantidad }}</td>
                <td class="text-right">${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</td>
                <td class="text-right">${{ number_format($detalle->cantidad * $detalle->precio_unitario, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width:100%; margin-top:40px;">
    <tr>
        <!-- Columna Totales -->
        <td style="width:50%; vertical-align:top; padding-right:20px;">

            <div class="summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <div class="summary-row">
                    <span>Envío</span>
                    <span>Gratis</span>
                </div>

                <div class="summary-row total">
                    <span>Total</span>
                    <span>${{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

        </td>

        <!-- Columna Pago -->
        <td style="width:50%; vertical-align:top; padding-left:20px;">

            @if($pedido->pago && $pedido->pago->metodoPago)
            <div class="payment-box">
                <div class="payment-title">Pago</div>

                <p><strong>Método:</strong> {{ $pedido->pago->metodoPago->nombre }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($pedido->pago->estado ?? 'pendiente') }}</p>

                @if($pedido->pago->fecha_pago)
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($pedido->pago->fecha_pago)->format('d/m/Y H:i') }}</p>
                @endif

                @if($pedido->pago->referencia_externa)
                <p><strong>Referencia:</strong> {{ $pedido->pago->referencia_externa }}</p>
                @endif
            </div>
            @endif

        </td>
    </tr>
</table>


    <!-- FOOTER -->
    <div class="footer">
        4GMovil – Tu tienda de tecnología móvil
        <br>Gracias por tu compra
        <br>Factura generada el {{ date('d/m/Y H:i:s') }}
    </div>

</body>

</html>