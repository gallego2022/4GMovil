<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Pedido;
use App\Models\Usuario;

class StripePagoExitoso extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $pedido;
    public $paymentIntent;
    public $pedidoUrl;

    public function __construct(Usuario $usuario, Pedido $pedido, ?string $paymentIntent = null)
    {
        $this->usuario = $usuario;
        $this->pedido = $pedido;
        $this->paymentIntent = $paymentIntent;
        $this->pedidoUrl = route('pedidos.show', $pedido->pedido_id, true);
    }

    public function build()
    {
        return $this->subject('Pago Confirmado - Pedido #' . $this->pedido->pedido_id)
                    ->view('correo.stripe-pago-exitoso')
                    ->with([
                        'usuario' => $this->usuario,
                        'pedido' => $this->pedido,
                        'paymentIntent' => $this->paymentIntent,
                        'pedidoUrl' => $this->pedidoUrl,
                    ]);
    }
}
