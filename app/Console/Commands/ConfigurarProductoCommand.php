<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;

class ConfigurarProductoCommand extends Command
{
    protected $signature = 'producto:configurar {id} {--stock-min=5} {--stock-max=100} {--costo-porcentaje=70}';
    protected $description = 'Configurar valores de inventario para un producto';

    public function handle()
    {
        $id = $this->argument('id');
        $stockMin = $this->option('stock-min');
        $stockMax = $this->option('stock-max');
        $costoPorcentaje = $this->option('costo-porcentaje') / 100;
        
        $producto = Producto::find($id);
        
        if (!$producto) {
            $this->error("Producto con ID {$id} no encontrado");
            return;
        }
        
        $this->info("=== CONFIGURANDO PRODUCTO ===");
        $this->line("Producto: {$producto->nombre_producto}");
        
        $cambios = [];
        
        // Configurar stock mínimo
        if ($producto->stock_minimo === null || $producto->stock_minimo == 0) {
            $producto->stock_minimo = $stockMin;
            $cambios[] = "Stock mínimo establecido en {$stockMin}";
        }
        
        // Configurar stock máximo
        if ($producto->stock_maximo === null) {
            $producto->stock_maximo = $stockMax;
            $cambios[] = "Stock máximo establecido en {$stockMax}";
        }
        
        // Configurar costo unitario
        if ($producto->costo_unitario === null || $producto->costo_unitario == 0) {
            $producto->costo_unitario = $producto->precio * $costoPorcentaje;
            $cambios[] = "Costo unitario establecido en $" . number_format($producto->costo_unitario, 2) . " ({$this->option('costo-porcentaje')}% del precio)";
        }
        
        if (!empty($cambios)) {
            $producto->save();
            $this->info("Cambios realizados:");
            foreach ($cambios as $cambio) {
                $this->line("  - {$cambio}");
            }
        } else {
            $this->info("El producto ya tiene configuración correcta");
        }
        
        // Mostrar estado final
        $this->info("=== ESTADO FINAL ===");
        $this->line("Stock: {$producto->stock}");
        $this->line("Stock mínimo: {$producto->stock_minimo}");
        $this->line("Stock máximo: {$producto->stock_maximo}");
        $this->line("Costo unitario: $" . number_format($producto->costo_unitario, 2));
        $this->line("Estado stock: {$producto->estado_stock}");
        $this->line("Valor en inventario: $" . number_format($producto->stock * $producto->costo_unitario, 2));
        
        // Mostrar alertas
        $this->info("=== ALERTAS ===");
        $this->line("Stock bajo: " . ($producto->stock_bajo ? 'Sí' : 'No'));
        $this->line("Stock crítico: " . ($producto->stock_critico ? 'Sí' : 'No'));
        $this->line("Stock excesivo: " . ($producto->stock_excesivo ? 'Sí' : 'No'));
    }
} 