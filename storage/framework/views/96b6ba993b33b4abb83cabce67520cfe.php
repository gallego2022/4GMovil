<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Verifica tu correo</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Hola <?php echo e($usuario->nombre_usuario ?? 'usuario'); ?>!</h1>
        
        <div class="content">
            <p>Gracias por registrarte en <strong style="color: #000;">4GMovil</strong>.</p>
            <p>Para completar tu registro, necesitamos verificar tu dirección de correo electrónico.</p>
            <p>Haz clic en el siguiente botón para verificar tu cuenta:</p>
        </div>
        
        <a href="<?php echo e($url); ?>" class="btn">Verificar correo electrónico</a>
        
        <div class="footer">
            <p>Si no solicitaste este registro, puedes ignorar este mensaje.</p>
            <p>Este enlace expirará en 60 minutos por seguridad.</p>
            <p>© 2025 4GMovil S.A.S. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html> <?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\correo\verificacion-forced.blade.php ENDPATH**/ ?>