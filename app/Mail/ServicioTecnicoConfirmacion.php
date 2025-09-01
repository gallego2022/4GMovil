<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServicioTecnicoConfirmacion extends Mailable
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
        return $this->subject('Confirmación de solicitud de servicio técnico - 4G Móvil')
                    ->view('emails.servicio-tecnico-confirmacion')
                    ->with([
                        'nombre' => $this->datos['nombre'],
                        'dispositivo' => $this->datos['dispositivo'],
                        'modelo' => $this->datos['modelo'],
                        'problema' => $this->datos['problema'],
                        'fecha' => $this->datos['fecha']
                    ]);
    }
}
