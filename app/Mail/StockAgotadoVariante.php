<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\VarianteProducto;

class StockAgotadoVariante extends Mailable
{
    use Queueable, SerializesModels;

    public $variante;
    public $producto;
    public $productoUrl;
    public $fechaAgotamiento;

    public function __construct(VarianteProducto $variante)
    {
        $this->variante = $variante;
        $this->producto = $variante->producto;
        $this->productoUrl = route('productos.show', $variante->producto->producto_id, true);
        $this->fechaAgotamiento = now()->format('d/m/Y H:i:s');
    }

    public function build()
    {
        return $this->subject('🚨 STOCK AGOTADO - Variante: ' . $this->variante->nombre . ' - ' . $this->producto->nombre_producto)
                    ->view('correo.stock-agotado-variante')
                    ->with([
                        'variante' => $this->variante,
                        'producto' => $this->producto,
                        'productoUrl' => $this->productoUrl,
                        'fechaAgotamiento' => $this->fechaAgotamiento,
                    ]);
    }
}
