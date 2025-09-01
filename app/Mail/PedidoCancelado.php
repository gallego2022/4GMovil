<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Pedido;

class PedidoCancelado extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $pedido;
    public $motivo;
    public $pedidoUrl;

    public function __construct($usuario, Pedido $pedido, $motivo = null)
    {
        $this->usuario = $usuario;
        $this->pedido = $pedido;
        $this->motivo = $motivo;
        $this->pedidoUrl = route('pedidos.show', $pedido->pedido_id, true);
    }

    public function build()
    {
        return $this->subject('🚫 Pedido Cancelado - 4GMovil')
                    ->view('correo.pedido-cancelado')
                    ->with([
                        'usuario' => $this->usuario,
                        'pedido' => $this->pedido,
                        'motivo' => $this->motivo,
                        'pedidoUrl' => $this->pedidoUrl,
                    ]);
    }
}
