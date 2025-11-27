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
    public $variantesProblematicas;

    public function __construct(Producto $producto, $porcentajeActual, $stockMinimo = null)
    {
        $this->producto = $producto;
        $this->porcentajeActual = $porcentajeActual;
        if ($stockMinimo === null) {
            $umbralBajo = $producto->getAttribute('umbral_stock_bajo');
            if ($umbralBajo === null || $umbralBajo <= 0) {
                $umbralBajo = ($producto->stock_inicial ?? 0) > 0 ? (int) ceil(($producto->stock_inicial * 60) / 100) : 0;
            }
            $this->stockMinimo = $umbralBajo;
        } else {
            $this->stockMinimo = $stockMinimo;
        }
        $this->productoUrl = route('productos.show', $producto->producto_id, true);
        $alertService = app(\App\Services\Inventory\StockAlertService::class);
        $this->variantesProblematicas = $alertService->getVariantesProblematicas($producto->producto_id);
        
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
                        'variantesProblematicas' => $this->variantesProblematicas,
                    ]);
    }
}
