<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Código de Verificación OTP</title>
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
            color: #0d6efd;
            margin-bottom: 20px;
        }
        .otp-code {
            background-color: #f8f9fa;
            border: 2px solid #0d6efd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #0d6efd;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
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
        .timer {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Hola <?php echo e($usuario->nombre_usuario ?? 'usuario'); ?>!</h1>
        
        <div class="content">
            <?php if($tipo === 'email_verification'): ?>
                <p>Gracias por registrarte en <strong style="color: #000;">4GMovil</strong>.</p>
                <p>Para completar tu registro, necesitamos verificar tu dirección de correo electrónico.</p>
                <p>Tu código de verificación es:</p>
            <?php elseif($tipo === 'password_reset'): ?>
                <p>Has solicitado restablecer tu contraseña en <strong style="color: #000;">4GMovil</strong>.</p>
                <p>Tu código de verificación es:</p>
            <?php else: ?>
                <p>Has solicitado un código de verificación en <strong style="color: #000;">4GMovil</strong>.</p>
                <p>Tu código de verificación es:</p>
            <?php endif; ?>
        </div>
        
        <div class="otp-code"><?php echo e($codigo); ?></div>
        
        <div class="warning">
            <strong>⚠️ Importante:</strong><br>
            • Este código expirará en <span class="timer"><?php echo e($tiempoExpiracion); ?> minutos</span><br>
            • No compartas este código con nadie<br>
            • Si no solicitaste este código, ignora este mensaje
        </div>
        
        <div class="footer">
            <p>Este es un mensaje automático, no respondas a este correo.</p>
            <p>© 2025 4GMovil S.A.S. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/correo/otp-verification.blade.php ENDPATH**/ ?>