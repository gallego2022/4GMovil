<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactoFormulario extends Mailable
{
    use Queueable, SerializesModels;

    public $datos;

    /**
     * Create a new message instance.
     */
    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Nuevo mensaje de contacto - 4G Móvil')
                    ->view('emails.contacto-formulario')
                    ->with([
                        'nombre' => $this->datos['nombre'],
                        'apellido' => $this->datos['apellido'],
                        'email' => $this->datos['email'],
                        'telefono' => $this->datos['telefono'],
                        'asunto' => $this->datos['asunto'],
                        'mensaje' => $this->datos['mensaje'],
                        'fecha' => $this->datos['fecha'],
                        'ip' => $this->datos['ip']
                    ]);
    }
}
