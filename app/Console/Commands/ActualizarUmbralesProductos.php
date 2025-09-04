<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;

class ActualizarUmbralesProductos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:actualizar-umbrales {--force : Forzar actualización sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los umbrales de stock (stock_minimo y stock_maximo) basados en la nueva lógica de porcentajes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        if (!$force && !$this->confirm('¿Estás seguro de que quieres actualizar los umbrales de stock de todos los productos?')) {
            $this->info('Operación cancelada.');
            return 0;
        }

        try {
            $this->info('Iniciando actualización de umbrales de stock...');
            
            $productos = Producto::all();
            $total = $productos->count();
            $actualizados = 0;
            $conStockInicial = 0;
            
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach ($productos as $producto) {
                try {
                    $stockInicial = $producto->stock;
                    
                    if ($stockInicial > 0) {
                        $conStockInicial++;
                        
                        // Calcular nuevos umbrales
                        $umbralBajo = (int) ceil(($stockInicial * 60) / 100);
                        $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
                        
                        // Verificar si hay cambios
                        $cambioMinimo = $producto->stock_minimo != $umbralCritico;
                        $cambioMaximo = $producto->stock_maximo != $umbralBajo;
                        
                        if ($cambioMinimo || $cambioMaximo) {
                            $producto->update([
                                'stock_minimo' => $umbralCritico,
                                'stock_maximo' => $umbralBajo
                            ]);
                            
                            $actualizados++;
                            
                            $this->newLine();
                            $this->line("Producto: {$producto->nombre_producto}");
                            $this->line("  Stock inicial: {$stockInicial}");
                            $this->line("  Umbral bajo (60%): {$umbralBajo} (antes: {$producto->getOriginal('stock_maximo')})");
                            $this->line("  Umbral crítico (20%): {$umbralCritico} (antes: {$producto->getOriginal('stock_minimo')})");
                        }
                    }
                    
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->warn("Error al procesar producto {$producto->producto_id}: " . $e->getMessage());
                }
            }

            $bar->finish();
            $this->newLine(2);

            $this->info("Actualización de umbrales completada:");
            $this->line("  - Total de productos: {$total}");
            $this->line("  - Productos con stock inicial > 0: {$conStockInicial}");
            $this->line("  - Productos con umbrales actualizados: {$actualizados}");
            
            $this->info("Nueva lógica implementada:");
            $this->line("  - stock_minimo = Umbral crítico (20% del stock inicial)");
            $this->line("  - stock_maximo = Umbral bajo (60% del stock inicial)");
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error durante la actualización: ' . $e->getMessage());
            Log::error('Error en comando ActualizarUmbralesProductos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
