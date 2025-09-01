<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class CorregirStockDisponibleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventario:corregir-stock-disponible';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige el campo stock_disponible de todos los productos para que sea igual a stock - stock_reservado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Iniciando corrección del stock disponible...');

        try {
            // Obtener todos los productos
            $productos = Producto::all();
            $totalProductos = $productos->count();
            $corregidos = 0;
            $errores = 0;

            $this->info("📦 Total de productos a procesar: {$totalProductos}");

            $bar = $this->output->createProgressBar($totalProductos);
            $bar->start();

            foreach ($productos as $producto) {
                try {
                    // Calcular el stock disponible correcto
                    $stockReservado = $producto->stock_reservado ?? 0;
                    $stockDisponibleCorrecto = max(0, $producto->stock - $stockReservado);

                    // Verificar si necesita corrección
                    if (($producto->stock_disponible ?? 0) != $stockDisponibleCorrecto) {
                        // Actualizar el producto usando update para evitar problemas con el linter
                        $producto->update(['stock_disponible' => $stockDisponibleCorrecto]);

                        $corregidos++;
                        
                        $this->line("\n✅ Producto #{$producto->producto_id} ({$producto->nombre_producto}) corregido:");
                        $this->line("   Stock: {$producto->stock}");
                        $this->line("   Stock Reservado: {$stockReservado}");
                        $this->line("   Stock Disponible: {$stockDisponibleCorrecto} (antes: " . ($producto->getRawOriginal('stock_disponible') ?? 0) . ")");
                    }
                } catch (\Exception $e) {
                    $errores++;
                    $this->error("\n❌ Error al corregir producto #{$producto->producto_id}: " . $e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            // Resumen final
            $this->info('🎯 Resumen de la corrección:');
            $this->info("   Total de productos procesados: {$totalProductos}");
            $this->info("   Productos corregidos: {$corregidos}");
            $this->info("   Errores encontrados: {$errores}");

            if ($corregidos > 0) {
                $this->info("\n✅ El stock disponible ha sido corregido exitosamente.");
                $this->info("   Ahora todos los productos muestran el stock disponible correcto.");
            } else {
                $this->info("\nℹ️  No se encontraron productos que necesiten corrección.");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error general: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
