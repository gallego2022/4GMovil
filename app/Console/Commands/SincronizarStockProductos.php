<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Services\StockSincronizacionService;
use Illuminate\Support\Facades\Log;

class SincronizarStockProductos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:sincronizar-stock {--producto-id= : ID del producto específico a sincronizar} {--force : Forzar sincronización sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza el stock de productos con la suma de sus variantes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productoId = $this->option('producto-id');
        $force = $this->option('force');

        if (!$force && !$this->confirm('¿Estás seguro de que quieres sincronizar el stock de los productos?')) {
            $this->info('Operación cancelada.');
            return 0;
        }

        try {
            if ($productoId) {
                $this->sincronizarProductoEspecifico($productoId);
            } else {
                $this->sincronizarTodosLosProductos();
            }

            $this->info('Sincronización completada exitosamente.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error durante la sincronización: ' . $e->getMessage());
            Log::error('Error en comando SincronizarStockProductos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Sincronizar un producto específico
     */
    private function sincronizarProductoEspecifico(int $productoId): void
    {
        $producto = Producto::find($productoId);
        
        if (!$producto) {
            $this->error("Producto con ID {$productoId} no encontrado.");
            return;
        }

        $this->info("Sincronizando producto: {$producto->nombre_producto}");
        
        $stockAnterior = $producto->stock;
        $producto->sincronizarStockConVariantes();
        $producto->refresh();
        
        $this->info("Stock actualizado: {$stockAnterior} → {$producto->stock}");
        
        if ($producto->tieneVariantes()) {
            $this->info("Variantes encontradas: " . $producto->variantes->count());
            foreach ($producto->variantes as $variante) {
                $this->line("  - {$variante->nombre}: {$variante->stock} unidades");
            }
        } else {
            $this->warn("Este producto no tiene variantes.");
        }
    }

    /**
     * Sincronizar todos los productos
     */
    private function sincronizarTodosLosProductos(): void
    {
        $this->info('Iniciando sincronización de stock para todos los productos...');
        
        $productos = Producto::all();
        $total = $productos->count();
        $sincronizados = 0;
        $conVariantes = 0;
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($productos as $producto) {
            try {
                $stockAnterior = $producto->stock;
                
                if ($producto->tieneVariantes()) {
                    $conVariantes++;
                    $producto->sincronizarStockConVariantes();
                    $producto->refresh();
                    
                    if ($stockAnterior !== $producto->stock) {
                        $sincronizados++;
                    }
                }
                
                $bar->advance();
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Error al sincronizar producto {$producto->producto_id}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Sincronización completada:");
        $this->line("  - Total de productos: {$total}");
        $this->line("  - Productos con variantes: {$conVariantes}");
        $this->line("  - Stock actualizado: {$sincronizados}");
    }
}
