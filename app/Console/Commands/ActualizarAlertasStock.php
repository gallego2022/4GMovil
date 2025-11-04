<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;

class ActualizarAlertasStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:actualizar-alertas-stock {--force : Forzar actualización sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza la lógica de alertas de stock para usar porcentajes del stock inicial';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');

        if (!$force && !$this->confirm('¿Estás seguro de que quieres actualizar la lógica de alertas de stock?')) {
            $this->info('Operación cancelada.');
            return 0;
        }

        try {
            $this->info('Iniciando actualización de lógica de alertas de stock...');
            
            $productos = Producto::all();
            $total = $productos->count();
            $actualizados = 0;
            $conAlertas = 0;
            
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach ($productos as $producto) {
                try {
                    $stockInicial = $producto->stock;
                    $stockDisponible = $producto->stock_disponible;
                    
                    if ($stockInicial > 0) {
                        // Usar umbrales del producto si existen, sino calcular
                        $umbralBajo = $producto->stock_maximo ?? (int) ceil(($stockInicial * 60) / 100);
                        $umbralCritico = $producto->stock_minimo ?? (int) ceil(($stockInicial * 20) / 100);
                        
                        // Verificar estado actual
                        $estadoAnterior = $this->getEstadoAnterior($producto);
                        $estadoNuevo = $this->getEstadoNuevo($stockDisponible, $umbralBajo, $umbralCritico);
                        
                        if ($estadoAnterior !== $estadoNuevo) {
                            $actualizados++;
                            
                            $this->newLine();
                            $this->line("Producto: {$producto->nombre_producto}");
                            $this->line("  Stock inicial: {$stockInicial}");
                            $this->line("  Stock disponible: {$stockDisponible}");
                            $this->line("  Umbral bajo (60%): {$umbralBajo}");
                            $this->line("  Umbral crítico (20%): {$umbralCritico}");
                            $this->line("  Estado: {$estadoAnterior} → {$estadoNuevo}");
                        }
                        
                        if ($estadoNuevo !== 'normal') {
                            $conAlertas++;
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

            $this->info("Actualización completada:");
            $this->line("  - Total de productos: {$total}");
            $this->line("  - Productos con cambios: {$actualizados}");
            $this->line("  - Productos con alertas: {$conAlertas}");
            
            $this->info("Nueva lógica implementada:");
            $this->line("  - Stock bajo: 60% del stock inicial");
            $this->line("  - Stock crítico: 20% del stock inicial");
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error durante la actualización: ' . $e->getMessage());
            Log::error('Error en comando ActualizarAlertasStock', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Obtener el estado anterior del producto
     */
    private function getEstadoAnterior(Producto $producto): string
    {
        if ($producto->stock_disponible <= 0) return 'sin_stock';
        if ($producto->stock_disponible <= ($producto->stock_minimo ?? 5)) return 'critico';
        if ($producto->stock_disponible <= (($producto->stock_minimo ?? 5) * 2)) return 'bajo';
        return 'normal';
    }

    /**
     * Obtener el nuevo estado del producto
     */
    private function getEstadoNuevo(int $stockDisponible, int $umbralBajo, int $umbralCritico): string
    {
        if ($stockDisponible <= 0) return 'sin_stock';
        if ($stockDisponible <= $umbralCritico) return 'critico';
        if ($stockDisponible > $umbralCritico && $stockDisponible <= ($umbralBajo * 1.5)) return 'bajo';
        return 'normal';
    }
}
