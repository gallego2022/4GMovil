<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Producto;

class StockBajo extends Mailable
{
    use Queueable, SerializesModels;

    public $producto;
    public $porcentajeActual;
    public $stockMinimo;
    public $productoUrl;

    public function __construct(Producto $producto, $porcentajeActual, $stockMinimo = 10)
    {
        $this->producto = $producto;
        $this->porcentajeActual = $porcentajeActual;
        $this->stockMinimo = $stockMinimo;
        $this->productoUrl = route('productos.show', $producto->producto_id, true);
        
    }

    public function build()
    {
        return $this->subject('⚠️ Stock Bajo - Producto: ' . $this->producto->nombre_producto)
                    ->view('correo.stock-bajo')
                    ->with([
                        'producto' => $this->producto,
                        'porcentajeActual' => $this->porcentajeActual,
                        'stockMinimo' => $this->stockMinimo,
                        'productoUrl' => $this->productoUrl,
                    ]);
    }
}
