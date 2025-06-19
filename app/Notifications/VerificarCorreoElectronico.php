<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use App\Mail\VerificacionCorreo;
use Illuminate\Support\Facades\Mail;

class VerificarCorreoElectronico extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

   public function toMail($notifiable)
{
    $verificationUrl = $this->verificationUrl($notifiable);

    \Log::info(" Paso 2: 📨 Enviando verificación usando Mailable a: " . $notifiable->correo_electronico);
    \Log::info("🔗 URL generada: " . $verificationUrl);

    // Enviar directamente el Mailable
    Mail::to($notifiable->correo_electronico)->send(new VerificacionCorreo($notifiable, $verificationUrl));

    // Retornar un MailMessage vacío para cumplir el contrato
      return (new MailMessage)->line('');
}


    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->correo_electronico),
            ]
        );
    }
}
