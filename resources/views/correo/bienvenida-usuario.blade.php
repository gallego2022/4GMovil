<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a 4GMovil</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa; }
        .container { background-color: #ffffff; border-radius: 10px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 3px solid #0088ff; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #0088ff; margin-bottom: 10px; }
        .title { font-size: 24px; color: #2c3e50; margin: 0; }
        .content { font-size: 16px; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; color: #6c757d; font-size: 14px; }
    </style>
    </head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">4GMovil</div>
            <h1 class="title">¡Bienvenido!</h1>
        </div>

        <div class="content">
            <p>Hola {{ $usuario->nombre_usuario }},</p>
            <p>Gracias por registrarte en <strong>4GMovil</strong>. Ya puedes acceder a tu cuenta y gestionar tus pedidos, direcciones y preferencias.</p>
            @if(isset($tipoRegistro) && $tipoRegistro === 'google')
            <p>Tu correo ya fue verificado por Google. Te invitamos a completar tu perfil para una mejor experiencia.</p>
            @else
            <p>Recuerda verificar tu correo electrónico para activar todas las funciones y completa tu perfil.</p>
            @endif
            <p>Estamos aquí para ayudarte en lo que necesites.</p>
            <p style="text-align:center; margin-top: 20px;">
                <a href="{{ url('/perfil') }}" style="display:inline-block;background:#0088ff;color:#fff;padding:12px 20px;border-radius:6px;text-decoration:none;font-weight:bold;">Ir a mi perfil</a>
            </p>
            <p>— Equipo 4GMovil</p>
        </div>

        <div class="footer">
            <p>Este correo fue enviado automáticamente por el sistema</p>
            <p>Fecha: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
