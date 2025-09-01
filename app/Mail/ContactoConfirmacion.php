<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactoConfirmacion extends Mailable
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
        return $this->subject('Confirmación de mensaje - 4G Móvil')
                    ->view('emails.contacto-confirmacion')
                    ->with([
                        'nombre' => $this->datos['nombre'],
                        'apellido' => $this->datos['apellido'],
                        'asunto' => $this->datos['asunto'],
                        'mensaje' => $this->datos['mensaje'],
                        'fecha' => $this->datos['fecha']
                    ]);
    }
}
