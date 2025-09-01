<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use Illuminate\Support\Facades\Schema;

class VerificarStockProductoCommand extends Command
{
    protected $signature = 'inventario:verificar-producto {id : ID del producto} {--reservar= : Cantidad a reservar}';
    protected $description = 'Verificar el stock de un producto específico';

    public function handle()
    {
        $id = $this->argument('id');
        $cantidadReservar = $this->option('reservar');
        
        // Verificar si el campo stock_reservado existe
        $columns = Schema::getColumnListing('productos');
        $this->info("📋 Campos de la tabla productos:");
        foreach ($columns as $column) {
            if (strpos($column, 'stock') !== false) {
                $this->line("- {$column}");
            }
        }
        
        // Verificar si el campo stock_reservado existe realmente
        if (!in_array('stock_reservado', $columns)) {
            $this->error("❌ El campo stock_reservado no existe en la tabla productos!");
            return 1;
        }
        
        $producto = Producto::find($id);
        
        if (!$producto) {
            $this->error("❌ Producto con ID {$id} no encontrado.");
            return 1;
        }
        
        $this->info("\n📊 Información del producto: {$producto->nombre_producto}");
        $this->line("ID: {$producto->producto_id}");
        $this->line("Stock total: {$producto->stock}");
        $this->line("Stock reservado: " . ($producto->stock_reservado ?? 0));
        $this->line("Stock disponible: {$producto->stock_disponible}");
        $this->line("Activo: " . ($producto->activo ? 'Sí' : 'No'));
        
        // Verificar si tiene stock reservado alto
        $porcentajeReservado = $producto->stock > 0 ? ($producto->stock_reservado / $producto->stock) * 100 : 0;
        $this->line("Porcentaje reservado: " . number_format($porcentajeReservado, 2) . "%");
        
        if ($producto->stock_reservado > $producto->stock * 0.5) {
            $this->info("⚠️ Este producto tiene stock reservado alto!");
        } else {
            $this->line("ℹ️ Este producto NO tiene stock reservado alto (necesita >50%)");
        }
        
        return 0;
    }
}
