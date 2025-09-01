<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pago Confirmado</title>
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
        .success-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #155724;
        }
        .next-steps {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps h4 {
            color: #1976d2;
            margin-top: 0;
        }
        .next-steps ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>¡Pago Confirmado!</h1>
        
        <div class="content">
            <p>¡Hola <strong><?php echo e($usuario->nombre_usuario ?? 'usuario'); ?></strong>!</p>
            <p>Tu pago ha sido procesado exitosamente en <strong style="color: #000;">4GMovil</strong>.</p>
            <p>¡Gracias por tu compra! Tu pedido será procesado y enviado pronto.</p>
        </div>
        
        <div class="success-box">
            <strong>🎉 ¡Transacción Exitosa!</strong><br>
            Tu pago ha sido confirmado y tu pedido está siendo procesado.
        </div>
        
        <div class="order-details">
            <h3>📋 Detalles del Pedido</h3>
            <p><strong>Número de Pedido:</strong> #<?php echo e($pedido->pedido_id); ?></p>
            <p><strong>Total Pagado:</strong> $<?php echo e(number_format($pedido->total, 0, ',', '.')); ?></p>
            <p><strong>Fecha:</strong> <?php echo e(\Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i')); ?></p>
            <p><strong>Método de Pago:</strong> Tarjeta de crédito/débito (Stripe)</p>
            <?php if($paymentIntent): ?>
            <p><strong>Referencia:</strong> <?php echo e($paymentIntent->id); ?></p>
            <?php endif; ?>
        </div>
        
        <a href="<?php echo e($pedidoUrl); ?>" class="btn">📦 Ver Detalles del Pedido</a>
        
        <div class="next-steps">
            <h4>🚀 ¿Qué Sigue?</h4>
            <ol>
                <li><strong>Confirmamos tu pedido</strong> - Ya está hecho ✅</li>
                <li><strong>Procesamos tu pedido</strong> - En curso 🔄</li>
                <li><strong>Lo preparamos para envío</strong> - Próximamente 📦</li>
                <li><strong>Te notificamos cuando esté en camino</strong> - 🚚</li>
                <li><strong>¡Recibes tu pedido!</strong> - 🎉</li>
            </ol>
        </div>
        
        <div class="content">
            <p><strong>Información Importante:</strong></p>
            <p>• Tu recibo fiscal está disponible en tu perfil de usuario</p>
            <p>• Recibirás actualizaciones por email sobre el estado de tu pedido</p>
            <p>• Si tienes alguna pregunta, no dudes en contactarnos</p>
        </div>
        
        <div class="footer">
            <p>Gracias por confiar en 4GMovil para tus compras tecnológicas.</p>
            <p>Tu satisfacción es nuestra prioridad.</p>
            <p>© 2025 4GMovil S.A.S. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Proyecto V11.3\4GMovil\resources\views/correo/stripe-pago-exitoso.blade.php ENDPATH**/ ?>