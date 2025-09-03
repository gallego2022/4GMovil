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

class NotificacionPedidoNuevo extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $usuario;
    public $metodoPago;
    public $adminUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Pedido $pedido, Usuario $usuario, string $metodoPago, string $adminUrl)
    {
        $this->pedido = $pedido;
        $this->usuario = $usuario;
        $this->metodoPago = $metodoPago;
        $this->adminUrl = $adminUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🆕 Nuevo Pedido Confirmado - 4GMovil',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'correo.notificacion-pedido-nuevo',
            with: [
                'pedido' => $this->pedido,
                'usuario' => $this->usuario,
                'metodoPago' => $this->metodoPago,
                'adminUrl' => $this->adminUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
