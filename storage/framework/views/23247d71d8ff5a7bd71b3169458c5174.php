<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pedido Confirmado</title>
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
        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        h1 {
            color: #28a745;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            font-size: 16px;
            background-color: #28a745;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #218838;
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
        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">📦</div>
        <h1>¡Pedido Confirmado!</h1>
        
        <div class="content">
            <p>¡Hola <strong><?php echo e($usuario->nombre_usuario ?? 'usuario'); ?></strong>!</p>
            <p>Tu pedido ha sido confirmado exitosamente en <strong style="color: #000;">4GMovil</strong>.</p>
            <p>Estamos procesando tu pedido y te notificaremos cuando esté listo para envío.</p>
        </div>
        
        <div class="order-details">
            <h3>📋 Detalles del Pedido</h3>
            <p><strong>Número de Pedido:</strong> #<?php echo e($pedido->pedido_id); ?></p>
            <p><strong>Fecha:</strong> <?php echo e(\Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i')); ?></p>
            <p><strong>Total:</strong> $<?php echo e(number_format($pedido->total, 0, ',', '.')); ?></p>
            <p><strong>Estado:</strong> <span style="color: #28a745;">Confirmado</span></p>
        </div>
        
        <a href="<?php echo e($pedidoUrl); ?>" class="btn">Ver Detalles del Pedido</a>
        
        <div class="content">
            <p><strong>¿Qué sigue?</strong></p>
            <p>1. Procesamos tu pedido</p>
            <p>2. Lo preparamos para envío</p>
            <p>3. Te notificamos cuando esté en camino</p>
        </div>
        
        <div class="footer">
            <p>Gracias por confiar en 4GMovil para tus compras tecnológicas.</p>
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
            <p>© 2025 4GMovil S.A.S. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\correo\confirmacion-pedido.blade.php ENDPATH**/ ?>