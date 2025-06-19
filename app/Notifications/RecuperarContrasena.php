<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class RecuperarContrasena extends ResetPassword
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Recupera tu contraseña en 4GMovil')
            ->greeting('Hola ' . $notifiable->nombre_usuario . ',')
            ->line('Recibiste este correo porque se solicitó un restablecimiento de contraseña para tu cuenta en 4GMovil.')
            ->action('Restablecer contraseña', url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->correo_electronico,
            ], false)))
            ->line('Este enlace expirará en 60 minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este mensaje.');
    }
}
