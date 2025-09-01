<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\VarianteProducto;

class StockBajoVariante extends Mailable
{
    use Queueable, SerializesModels;

    public $variante;
    public $producto;
    public $stockActual;
    public $stockMinimo;
    public $porcentajeActual;
    public $productoUrl;
    public $tipoAlerta; // 'bajo', 'critico', 'agotado'

    public function __construct(VarianteProducto $variante, $tipoAlerta = 'bajo')
    {
        $this->variante = $variante;
        $this->producto = $variante->producto;
        $this->stockActual = $variante->stock_disponible;
        $this->stockMinimo = $variante->stock_minimo;
        $this->tipoAlerta = $tipoAlerta;
        $this->productoUrl = route('productos.show', $variante->producto->producto_id, true);
        
        // Calcular porcentaje actual
        $this->porcentajeActual = $this->stockMinimo > 0 
            ? round(($this->stockActual / $this->stockMinimo) * 100, 1) 
            : 0;
    }

    public function build()
    {
        $asunto = match($this->tipoAlerta) {
            'agotado' => '🚨 STOCK AGOTADO - Variante: ' . $this->variante->nombre . ' - ' . $this->producto->nombre_producto,
            'critico' => '⚠️ STOCK CRÍTICO - Variante: ' . $this->variante->nombre . ' - ' . $this->producto->nombre_producto,
            default => '📉 STOCK BAJO - Variante: ' . $this->variante->nombre . ' - ' . $this->producto->nombre_producto
        };

        return $this->subject($asunto)
                    ->view('correo.stock-bajo-variante')
                    ->with([
                        'variante' => $this->variante,
                        'producto' => $this->producto,
                        'stockActual' => $this->stockActual,
                        'stockMinimo' => $this->stockMinimo,
                        'porcentajeActual' => $this->porcentajeActual,
                        'tipoAlerta' => $this->tipoAlerta,
                        'productoUrl' => $this->productoUrl,
                    ]);
    }
}
