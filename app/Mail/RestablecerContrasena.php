<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RestablecerContrasena extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $resetUrl;

    public function __construct($usuario, $resetUrl)
    {
        $this->usuario = $usuario;
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        return $this->subject('Restablece tu contraseña en 4GMovil')
                    ->view('correo.restablecer-contrasena')
                    ->with([
                        'usuario' => $this->usuario,
                        'resetUrl' => $this->resetUrl,
                    ]);
    }
}
