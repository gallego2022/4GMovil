<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MovimientoInventario;

class VerMovimientosProducto extends Command
{
    protected $signature = 'inventario:ver-movimientos {producto_id}';
    protected $description = 'Ver movimientos de inventario de un producto específico';

    public function handle()
    {
        $productoId = $this->argument('producto_id');
        
        $this->info("Mostrando movimientos para el producto ID: {$productoId}");
        
        $movimientos = MovimientoInventario::where('producto_id', $productoId)
            ->orderBy('created_at')
            ->get();

        if ($movimientos->count() == 0) {
            $this->warn("No se encontraron movimientos para el producto ID: {$productoId}");
            return 0;
        }

        $this->table(
            ['ID', 'Tipo', 'Cantidad', 'Stock Anterior', 'Stock Nuevo', 'Motivo', 'Fecha'],
            $movimientos->map(function ($movimiento) {
                return [
                    $movimiento->movimiento_id,
                    $movimiento->tipo_movimiento,
                    $movimiento->cantidad,
                    $movimiento->stock_anterior,
                    $movimiento->stock_nuevo,
                    $movimiento->motivo,
                    $movimiento->created_at->format('d/m/Y H:i:s')
                ];
            })
        );

        $this->info("Total de movimientos: {$movimientos->count()}");
        
        return 0;
    }
}
