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
            margin-bottom: 20px;
        }
        h1 {
            color: #0d6efd;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 16px;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            font-size: 12px;
            color: #6c757d;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cambia esta URL por la ruta pública de tu logo -->
        <img src="{{ asset(path: 'img/Logo_2.png') }}" alt="4GMovil Logo" class="logo">

        <h1>¡Hola {{ $usuario->nombre_usuario ?? 'usuario' }}!</h1>
        <p>Gracias por registrarte en <strong>4GMovil</strong>.</p>
        <p>Haz clic en el siguiente botón para verificar tu dirección de correo electrónico:</p>
        <a href="{{ $url }}" class="btn">Verificar correo</a>
        <p class="footer">
            Si no solicitaste este registro, puedes ignorar este mensaje.
        </p>
    </div>
</body>
</html>
