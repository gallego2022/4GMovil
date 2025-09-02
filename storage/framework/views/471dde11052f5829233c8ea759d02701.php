<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Servicio Técnico - 4G Móvil</title>
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
        .device-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .priority {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔧 Nueva Solicitud de Servicio Técnico</h1>
        <p>4G Móvil - Formulario de Servicio Técnico</p>
    </div>
    
    <div class="content">
        <p>Se ha recibido una nueva solicitud de servicio técnico a través del sitio web.</p>
        
        <div class="priority">
            <strong>⚠️ Acción Requerida:</strong> Contactar al cliente en las próximas 2 horas para agendar el diagnóstico.
        </div>
        
        <div class="field">
            <div class="label">👤 Cliente:</div>
            <div class="value"><?php echo e($nombre); ?></div>
        </div>
        
        <div class="field">
            <div class="label">📞 Teléfono de Contacto:</div>
            <div class="value"><?php echo e($telefono); ?></div>
        </div>
        
        <div class="device-info">
            <h3>📱 Información del Dispositivo</h3>
            <div class="field">
                <div class="label">Tipo de Dispositivo:</div>
                <div class="value"><?php echo e(ucfirst($dispositivo)); ?></div>
            </div>
            <?php if($modelo): ?>
            <div class="field">
                <div class="label">Marca y Modelo:</div>
                <div class="value"><?php echo e($modelo); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="field">
            <div class="label">🔍 Descripción del Problema:</div>
            <div class="value"><?php echo e($problema); ?></div>
        </div>
        
        <div class="field">
            <div class="label">📅 Fecha y Hora de Solicitud:</div>
            <div class="value"><?php echo e($fecha); ?></div>
        </div>
        
        <div class="field">
            <div class="label">🌐 IP del Cliente:</div>
            <div class="value"><?php echo e($ip); ?></div>
        </div>
        
        <div style="background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3>📋 Próximos Pasos Recomendados:</h3>
            <ol>
                <li>Contactar al cliente por teléfono: <strong><?php echo e($telefono); ?></strong></li>
                <li>Confirmar disponibilidad para diagnóstico</li>
                <li>Agendar cita en el horario más conveniente</li>
                <li>Preparar herramientas necesarias según el dispositivo</li>
                <li>Realizar diagnóstico y cotización detallada</li>
            </ol>
        </div>
    </div>
    
    <div class="footer">
        <p>Este mensaje fue enviado desde el formulario de servicio técnico de 4G Móvil</p>
        <p>Fecha: <?php echo e($fecha); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\Users\usuario\Documents\GitHub\4GMovil\resources\views/emails/servicio-tecnico-formulario.blade.php ENDPATH**/ ?>