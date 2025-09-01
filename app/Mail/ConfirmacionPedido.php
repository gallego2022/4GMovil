<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Pedido;

class ConfirmacionPedido extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $pedido;
    public $pedidoUrl;

    public function __construct($usuario, Pedido $pedido, $pedidoUrl)
    {
        $this->usuario = $usuario;
        $this->pedido = $pedido;
        $this->pedidoUrl = $pedidoUrl;
    }

    public function build()
    {
        return $this->subject('Pedido Confirmado - 4GMovil')
                    ->view('correo.confirmacion-pedido')
                    ->with([
                        'usuario' => $this->usuario,
                        'pedido' => $this->pedido,
                        'pedidoUrl' => $this->pedidoUrl,
                    ]);
    }
}
