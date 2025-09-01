<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Pedido;

class StripePagoFallido extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $pedido;
    public $paymentIntent;
    public $retryUrl;

    public function __construct($usuario, Pedido $pedido, $paymentIntent = null)
    {
        $this->usuario = $usuario;
        $this->pedido = $pedido;
        $this->paymentIntent = $paymentIntent;
        $this->retryUrl = route('stripe.payment', $pedido->pedido_id, true);
    }

    public function build()
    {
        return $this->subject('❌ Pago Fallido - Pedido #' . $this->pedido->pedido_id)
                    ->view('correo.stripe-pago-fallido')
                    ->with([
                        'usuario' => $this->usuario,
                        'pedido' => $this->pedido,
                        'paymentIntent' => $this->paymentIntent,
                        'retryUrl' => $this->retryUrl,
                    ]);
    }
}
