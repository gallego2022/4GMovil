<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pedido Cancelado</title>
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
            color: #6c757d;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            font-size: 16px;
            background-color: #6c757d;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #5a6268;
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
        .cancel-icon {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #1976d2;
        }
        .next-steps {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps h4 {
            color: #495057;
            margin-top: 0;
        }
        .next-steps ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin: 8px 0;
        }
        .motivo-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="cancel-icon">🚫</div>
        <h1>Pedido Cancelado</h1>
        
        <div class="content">
            <p>¡Hola <strong><?php echo e($usuario->nombre_usuario ?? 'usuario'); ?></strong>!</p>
            <p>Tu pedido ha sido cancelado en <strong style="color: #000;">4GMovil</strong>.</p>
            <p>Entendemos que esto puede ser frustrante y estamos aquí para ayudarte.</p>
        </div>
        
        <div class="info-box">
            <strong>ℹ️ Información Importante:</strong><br>
            Tu pedido ha sido cancelado y no se procesará. Si tienes alguna pregunta, no dudes en contactarnos.
        </div>
        
        <?php if($motivo): ?>
        <div class="motivo-box">
            <strong>📋 Motivo de la Cancelación:</strong><br>
            <?php echo e($motivo); ?>

        </div>
        <?php endif; ?>
        
        <div class="order-details">
            <h3>📋 Detalles del Pedido</h3>
            <p><strong>Número de Pedido:</strong> #<?php echo e($pedido->pedido_id); ?></p>
            <p><strong>Total:</strong> $<?php echo e(number_format($pedido->total, 0, ',', '.')); ?></p>
            <p><strong>Fecha de Creación:</strong> <?php echo e(\Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i')); ?></p>
            <p><strong>Estado:</strong> <span style="color: #6c757d;">Cancelado</span></p>
        </div>
        
        <a href="<?php echo e($pedidoUrl); ?>" class="btn">📋 Ver Detalles del Pedido</a>
        
        <div class="next-steps">
            <h4>🔄 ¿Qué Puedes Hacer?</h4>
            <ul>
                <li><strong>Crear un nuevo pedido</strong> - Si cambiaste de opinión</li>
                <li><strong>Contactar soporte</strong> - Si tienes preguntas</li>
                <li><strong>Revisar otros productos</strong> - En nuestro catálogo</li>
                <li><strong>Verificar disponibilidad</strong> - De los productos</li>
            </ul>
        </div>
        
        <div class="content">
            <p><strong>¿Necesitas Ayuda?</strong></p>
            <p>• Nuestro equipo de soporte está disponible para ayudarte</p>
            <p>• Puedes contactarnos por email o teléfono</p>
            <p>• Estamos aquí para resolver cualquier duda</p>
        </div>
        
        <div class="footer">
            <p>Si tienes alguna pregunta sobre la cancelación, no dudes en contactarnos.</p>
            <p>Estamos comprometidos con tu satisfacción.</p>
            <p>© 2025 4GMovil S.A.S. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\correo\pedido-cancelado.blade.php ENDPATH**/ ?>