<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Usuario;

class BienvenidaUsuario extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $tipoRegistro;

    public function __construct(Usuario $usuario, string $tipoRegistro = 'tradicional')
    {
        $this->usuario = $usuario;
        $this->tipoRegistro = $tipoRegistro;
    }

    public function build()
    {
        return $this->subject('¡Bienvenido a 4GMovil!')
            ->view('correo.bienvenida-usuario')
            ->with([
                'usuario' => $this->usuario,
                'tipoRegistro' => $this->tipoRegistro,
            ]);
    }
}
