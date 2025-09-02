<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Mensaje de Contacto - 4G Móvil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #3B82F6, #1E40AF);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 10px 10px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #1E40AF;
        }
        .value {
            background: white;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #3B82F6;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📧 Nuevo Mensaje de Contacto</h1>
        <p>4G Móvil - Formulario de Contacto</p>
    </div>
    
    <div class="content">
        <p>Se ha recibido un nuevo mensaje de contacto a través del sitio web.</p>
        
        <div class="field">
            <div class="label">👤 Nombre Completo:</div>
            <div class="value"><?php echo e($nombre); ?> <?php echo e($apellido); ?></div>
        </div>
        
        <div class="field">
            <div class="label">📧 Email:</div>
            <div class="value"><?php echo e($email); ?></div>
        </div>
        
        <div class="field">
            <div class="label">📞 Teléfono:</div>
            <div class="value"><?php echo e($telefono); ?></div>
        </div>
        
        <div class="field">
            <div class="label">📋 Asunto:</div>
            <div class="value"><?php echo e($asunto); ?></div>
        </div>
        
        <div class="field">
            <div class="label">💬 Mensaje:</div>
            <div class="value"><?php echo e($mensaje); ?></div>
        </div>
        
        <div class="field">
            <div class="label">📅 Fecha y Hora:</div>
            <div class="value"><?php echo e($fecha); ?></div>
        </div>
        
        <div class="field">
            <div class="label">🌐 IP del Usuario:</div>
            <div class="value"><?php echo e($ip); ?></div>
        </div>
    </div>
    
    <div class="footer">
        <p>Este mensaje fue enviado desde el formulario de contacto de 4G Móvil</p>
        <p>Fecha: <?php echo e($fecha); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/emails/contacto-formulario.blade.php ENDPATH**/ ?>