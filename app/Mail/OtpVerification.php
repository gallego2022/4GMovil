<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $codigo;
    public $tipo;
    public $tiempoExpiracion;

    public function __construct($usuario, $codigo, $tipo = 'email_verification', $tiempoExpiracion = 10)
    {
        $this->usuario = $usuario;
        $this->codigo = $codigo;
        $this->tipo = $tipo;
        $this->tiempoExpiracion = $tiempoExpiracion;
    }

    public function build()
    {
        $asunto = match($this->tipo) {
            'email_verification' => 'Verifica tu correo en 4GMovil S.A.S',
            'password_reset' => 'Restablece tu contraseña en 4GMovil S.A.S',
            default => 'Código de verificación 4GMovil S.A.S'
        };

        return $this->subject($asunto)
                    ->view('correo.otp-verification')
                    ->with([
                        'usuario' => $this->usuario,
                        'codigo' => $this->codigo,
                        'tipo' => $this->tipo,
                        'tiempoExpiracion' => $this->tiempoExpiracion
                    ]);
    }
}
