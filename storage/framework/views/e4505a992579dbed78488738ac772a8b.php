<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Restablece tu contraseña</title>
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
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            font-size: 16px;
            background-color: #0d6efd;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #0b5ed7;
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
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .link-text {
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Restablece tu contraseña</h1>
        
        <div class="content">
            <p>¡Hola <strong><?php echo e($usuario->nombre_usuario ?? 'usuario'); ?></strong>!</p>
            <p>Recibiste este correo porque se solicitó un restablecimiento de contraseña para tu cuenta en <strong style="color: #000;">4GMovil</strong>.</p>
            <p>Haz clic en el siguiente botón para crear tu nueva contraseña:</p>
        </div>
        
        <a href="<?php echo e($resetUrl); ?>" class="btn">🔄 Restablecer Contraseña</a>
        
        <div class="warning">
            <strong>⚠️ Importante:</strong> Este enlace expirará en 60 minutos por seguridad.
        </div>
        
        <div class="content">
            <p>Si no solicitaste este cambio, puedes ignorar este mensaje de forma segura.</p>
            <p>Tu cuenta está protegida y solo tú puedes cambiar tu contraseña.</p>
        </div>
        
        <div class="link-text">
            <strong>Enlace alternativo:</strong><br>
            <?php echo e($resetUrl); ?>

        </div>
        
        <div class="footer">
            <p>Si tienes problemas con el botón, copia y pega el enlace de arriba en tu navegador.</p>
            <p>© 2025 4GMovil S.A.S. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\correo\restablecer-contrasena.blade.php ENDPATH**/ ?>