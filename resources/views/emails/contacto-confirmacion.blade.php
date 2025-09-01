<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Mensaje - 4G Móvil</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Confirmación de Mensaje</h1>
        <p>4G Móvil - Gracias por contactarnos</p>
    </div>
    
    <div class="content">
        <div class="success-icon">✅</div>
        
        <h2>¡Hola {{ $nombre }} {{ $apellido }}!</h2>
        
        <p>Hemos recibido tu mensaje exitosamente. Te confirmamos los detalles:</p>
        
        <div class="field">
            <div class="label">📋 Asunto:</div>
            <div class="value">{{ $asunto }}</div>
        </div>
        
        <div class="field">
            <div class="label">💬 Tu Mensaje:</div>
            <div class="value">{{ $mensaje }}</div>
        </div>
        
        <div class="field">
            <div class="label">📅 Fecha de Envío:</div>
            <div class="value">{{ $fecha }}</div>
        </div>
        
        <div class="contact-info">
            <h3>📞 Información de Contacto</h3>
            <p><strong>Teléfono:</strong> +57 320 123 4567</p>
            <p><strong>Email:</strong> info@4gmovil.com.co</p>
            <p><strong>Dirección:</strong> Cra 52 #49-100, La Candelaria, Medellín</p>
            <p><strong>Horarios:</strong> Lun - Vie: 8:00 AM - 6:00 PM | Sáb: 9:00 AM - 4:00 PM</p>
        </div>
        
        <p><strong>¿Qué sigue?</strong></p>
        <ul>
            <li>Nuestro equipo revisará tu mensaje en las próximas 24 horas</li>
            <li>Te responderemos al email que proporcionaste</li>
            <li>Si es urgente, puedes llamarnos directamente</li>
        </ul>
        
        <p>¡Gracias por elegir 4G Móvil!</p>
    </div>
    
    <div class="footer">
        <p>Este es un mensaje automático de confirmación de 4G Móvil</p>
        <p>Fecha: {{ $fecha }}</p>
    </div>
</body>
</html>
