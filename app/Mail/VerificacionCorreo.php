<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificacionCorreo extends Mailable
{
    use Queueable, SerializesModels;

   
    public $usuario;
    public $url;

    public function __construct($usuario, $url)
    {
        $this->usuario = $usuario;
        $this->url = $url;
    }

    public function build()
    {
        return $this->subject('Confirma tu correo en 4GMovil S.A.S')
                    ->view('correo.verificacion')
                    ->with([
                        'usuario' => $this->usuario,
                        'url' => $this->url,
                    ]);
    }
}
