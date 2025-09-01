<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pago Fallido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            padding: 20px;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            font-size: 16px;
            background-color: #dc3545;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #c82333;
        }
        .footer {
            font-size: 12px;
            color: #6c757d;
            margin-top: 30px;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }
        .content {
            margin: 20px 0;
            line-height: 1.6;
        }
        .order-details {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .order-details h3 {
            color: #495057;
            margin-top: 0;
        }
        .order-details p {
            margin: 8px 0;
        }
        .error-icon {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .causes-list {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .causes-list h4 {
            color: #495057;
            margin-top: 0;
        }
        .causes-list ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .causes-list li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">❌</div>
        <h1>Pago Fallido</h1>
        
        <div class="content">
            <p>¡Hola <strong>{{ $usuario->nombre_usuario ?? 'usuario' }}</strong>!</p>
            <p>Lamentamos informarte que tu pago no pudo ser procesado en <strong style="color: #000;">4GMovil</strong>.</p>
            <p>No te preocupes, tu pedido sigue activo y puedes intentar el pago nuevamente.</p>
        </div>
        
        <div class="order-details">
            <h3>📋 Detalles del Pedido</h3>
            <p><strong>Número de Pedido:</strong> #{{ $pedido->pedido_id }}</p>
            <p><strong>Total:</strong> ${{ number_format($pedido->total, 0, ',', '.') }}</p>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i') }}</p>
            <p><strong>Método de Pago:</strong> Tarjeta de crédito/débito (Stripe)</p>
            @if($paymentIntent)
            <p><strong>Referencia:</strong> {{ $paymentIntent->id }}</p>
            @endif
        </div>
        
        <div class="warning-box">
            <strong>⚠️ Importante:</strong> Tu pedido sigue activo y no se ha cancelado. 
            Puedes intentar el pago nuevamente cuando lo desees.
        </div>
        
        <a href="{{ $retryUrl }}" class="btn">🔄 Intentar Pago Nuevamente</a>
        
        <div class="causes-list">
            <h4>🔍 Posibles Causas del Fallo</h4>
            <ul>
                <li>Fondos insuficientes en la tarjeta</li>
                <li>Tarjeta bloqueada o expirada</li>
                <li>Información de pago incorrecta</li>
                <li>Límite de la tarjeta excedido</li>
                <li>Restricciones de seguridad del banco</li>
            </ul>
        </div>
        
        <div class="content">
            <p><strong>¿Qué puedes hacer?</strong></p>
            <p>1. Verifica que tu tarjeta tenga fondos suficientes</p>
            <p>2. Confirma que la información de pago sea correcta</p>
            <p>3. Intenta con otra tarjeta si es necesario</p>
            <p>4. Contacta a tu banco si el problema persiste</p>
        </div>
        
        <div class="footer">
            <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.</p>
            <p>Estamos aquí para ayudarte a completar tu compra.</p>
            <p>© 2025 4GMovil S.A.S. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
