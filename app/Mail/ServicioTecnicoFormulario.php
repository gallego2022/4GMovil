<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServicioTecnicoFormulario extends Mailable
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
        return $this->subject('Nueva solicitud de servicio técnico - 4G Móvil')
                    ->view('emails.servicio-tecnico-formulario')
                    ->with([
                        'nombre' => $this->datos['nombre'],
                        'telefono' => $this->datos['telefono'],
                        'dispositivo' => $this->datos['dispositivo'],
                        'modelo' => $this->datos['modelo'],
                        'problema' => $this->datos['problema'],
                        'fecha' => $this->datos['fecha'],
                        'ip' => $this->datos['ip']
                    ]);
    }
}
