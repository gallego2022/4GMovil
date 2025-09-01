<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;

class VerificarProductosCommand extends Command
{
    protected $signature = 'productos:verificar';
    protected $description = 'Verifica el estado de todos los productos en la base de datos';

    public function handle()
    {
        $this->info('🔍 Verificando productos en la base de datos...');
        
        $productos = Producto::all(['producto_id', 'nombre_producto', 'stock', 'stock_reservado', 'stock_disponible']);
        
        if ($productos->isEmpty()) {
            $this->info('No hay productos en la base de datos.');
            return;
        }
        
        $this->table(
            ['ID', 'Nombre', 'Stock Total', 'Stock Reservado', 'Stock Disponible (BD)', 'Stock Disponible (Calc)'],
            $productos->map(function ($producto) {
                $stockDisponibleCalculado = max(0, $producto->stock - ($producto->stock_reservado ?? 0));
                return [
                    $producto->producto_id,
                    $producto->nombre_producto,
                    $producto->stock,
                    $producto->stock_reservado ?? 0,
                    $producto->stock_disponible ?? 0,
                    $stockDisponibleCalculado
                ];
            })
        );
        
        $this->info('✅ Verificación completada.');
    }
}
