<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Pedido Confirmado - 4GMovil</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #0088ff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #0088ff;
            margin-bottom: 10px;
        }
        .title {
            font-size: 24px;
            color: #2c3e50;
            margin: 0;
        }
        .alert {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
        }
        .pedido-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #495057;
        }
        .value {
            color: #6c757d;
        }
        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            background-color: #0088ff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .method-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .stripe-badge {
            background-color: #6772e5;
        }
        .efectivo-badge {
            background-color: #28a745;
        }
        .transferencia-badge {
            background-color: #17a2b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">4GMovil</div>
            <h1 class="title">🆕 Nuevo Pedido Confirmado</h1>
        </div>

        <div class="alert">
            ⚠️ Se ha confirmado un nuevo pedido que requiere tu atención
        </div>

        <div class="pedido-info">
            <h3 style="margin-top: 0; color: #2c3e50;">📋 Información del Pedido</h3>
            
            <div class="info-row">
                <span class="label">Número de Pedido:</span>
                <span class="value">#{{ $pedido->pedido_id }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Fecha de Confirmación:</span>
                <span class="value">{{ $pedido->fecha_pedido->format('d/m/Y H:i') }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Total del Pedido:</span>
                <span class="value">${{ number_format($pedido->total, 0, ',', '.') }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Método de Pago:</span>
                <span class="value">
                    <span class="method-badge 
                        @if($metodoPago === 'Stripe') stripe-badge
                        @elseif($metodoPago === 'Efectivo') efectivo-badge
                        @elseif($metodoPago === 'Transferencia Bancaria') transferencia-badge
                        @endif">
                        {{ $metodoPago }}
                    </span>
                </span>
            </div>
            
            <div class="info-row">
                <span class="label">Estado Actual:</span>
                <span class="value">✅ Confirmado</span>
            </div>
        </div>

        <div class="pedido-info">
            <h3 style="margin-top: 0; color: #2c3e50;">👤 Información del Cliente</h3>
            
            <div class="info-row">
                <span class="label">Nombre:</span>
                <span class="value">{{ $usuario->nombre_usuario }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value">{{ $usuario->correo_electronico }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Teléfono:</span>
                <span class="value">{{ $usuario->telefono ?? 'No especificado' }}</span>
            </div>
        </div>

        @if($metodoPago !== 'Stripe')
        <div class="highlight">
            <strong>⚠️ Atención:</strong> Este pedido fue confirmado con método de pago <strong>{{ $metodoPago }}</strong>. 
            Verifica que el pago se haya realizado correctamente antes de proceder con el envío.
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ $adminUrl }}" class="btn">
                🔍 Ver Detalle del Pedido
            </a>
        </div>

        <div class="footer">
            <p><strong>4GMovil</strong> - Sistema de Gestión de Pedidos</p>
            <p>Este correo fue enviado automáticamente por el sistema</p>
            <p>Fecha: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
