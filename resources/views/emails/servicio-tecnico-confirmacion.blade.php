<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Solicitud de Servicio Técnico - 4G Móvil</title>
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
            background: linear-gradient(135deg, #10B981, #059669);
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
        .success-icon {
            text-align: center;
            font-size: 48px;
            margin: 20px 0;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #059669;
        }
        .value {
            background: white;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #10B981;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .contact-info {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .next-steps {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Confirmación de Solicitud</h1>
        <p>4G Móvil - Servicio Técnico</p>
    </div>
    
    <div class="content">
        <div class="success-icon">🔧</div>
        
        <h2>¡Hola {{ $nombre }}!</h2>
        
        <p>Hemos recibido tu solicitud de servicio técnico exitosamente. Te confirmamos los detalles:</p>
        
        <div class="field">
            <div class="label">📱 Dispositivo:</div>
            <div class="value">{{ ucfirst($dispositivo) }}</div>
        </div>
        
        @if($modelo)
        <div class="field">
            <div class="label">🏷️ Marca y Modelo:</div>
            <div class="value">{{ $modelo }}</div>
        </div>
        @endif
        
        <div class="field">
            <div class="label">🔍 Problema Reportado:</div>
            <div class="value">{{ $problema }}</div>
        </div>
        
        <div class="field">
            <div class="label">📅 Fecha de Solicitud:</div>
            <div class="value">{{ $fecha }}</div>
        </div>
        
        <div class="next-steps">
            <h3>⏰ ¿Qué sigue?</h3>
            <ol>
                <li><strong>En las próximas 2 horas</strong> te contactaremos por teléfono</li>
                <li>Agendaremos una cita para el diagnóstico</li>
                <li>Realizaremos una evaluación completa de tu dispositivo</li>
                <li>Te daremos una cotización detallada</li>
                <li>Procederemos con la reparación si estás de acuerdo</li>
            </ol>
        </div>
        
        <div class="contact-info">
            <h3>📞 Información de Contacto</h3>
            <p><strong>Teléfono:</strong> +57 302 597 0220</p>
            <p><strong>WhatsApp:</strong> +57 302 597 0220</p>
            <p><strong>Email:</strong> osmandavidgallego@gmail.com</p>
            <p><strong>Dirección:</strong> Cra 52 #49-100, La Candelaria, Medellín</p>
            <p><strong>Horarios:</strong> Lun - Vie: 8:00 AM - 5:00 PM | Sáb: 9:00 AM - 4:00 PM</p>
        </div>
        
        <div style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3>💡 Consejos para el Diagnóstico</h3>
            <ul>
                <li>Lleva tu dispositivo con suficiente batería</li>
                <li>Si es posible, haz una copia de seguridad de tus datos</li>
                <li>Trae el cargador y accesorios originales</li>
                <li>Describe el problema con el mayor detalle posible</li>
            </ul>
        </div>
        
        <p><strong>¡Gracias por confiar en 4G Móvil para el servicio técnico de tu dispositivo!</strong></p>
    </div>
    
    <div class="footer">
        <p>Este es un mensaje automático de confirmación de 4G Móvil</p>
        <p>Fecha: {{ $fecha }}</p>
    </div>
</body>
</html>
