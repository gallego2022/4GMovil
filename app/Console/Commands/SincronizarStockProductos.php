<?php

namespace App\Console\Commands;

use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SincronizarStockProductos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:sincronizar-stock {--producto-id= : ID específico del producto} {--force : Forzar sincronización sin confirmación}';

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

        if ($productoId) {
            $producto = Producto::find($productoId);
            if (!$producto) {
                $this->error("❌ Producto con ID {$productoId} no encontrado");
                return Command::FAILURE;
            }
            $productos = collect([$producto]);
            $this->info("🔄 Sincronizando stock para producto: {$producto->nombre_producto}");
        } else {
            $productos = Producto::with('variantes')->get();
            $this->info("🔄 Sincronizando stock para {$productos->count()} productos");
        }

        if (!$force && !$this->confirm('¿Deseas continuar con la sincronización?')) {
            $this->info('❌ Sincronización cancelada');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($productos->count());
        $bar->start();

        $sincronizados = 0;
        $errores = 0;

        foreach ($productos as $producto) {
            try {
                $stockAnterior = $producto->stock;
                $producto->sincronizarStockConVariantes();
                $stockNuevo = $producto->fresh()->stock;
                
                if ($stockAnterior !== $stockNuevo) {
                    $this->newLine();
                    $this->line("📊 Producto: {$producto->nombre_producto}");
                    $this->line("   Stock anterior: {$stockAnterior}");
                    $this->line("   Stock nuevo: {$stockNuevo}");
                    $this->line("   Variantes: {$producto->variantes->count()}");
                }
                
                $sincronizados++;
            } catch (\Exception $e) {
                $errores++;
                Log::error('Error al sincronizar stock del producto', [
                    'producto_id' => $producto->producto_id,
                    'error' => $e->getMessage()
                ]);
                $this->newLine();
                $this->error("❌ Error en producto {$producto->nombre_producto}: {$e->getMessage()}");
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Resumen
        $this->info("✅ Sincronización completada:");
        $this->line("   📦 Productos sincronizados: {$sincronizados}");
        if ($errores > 0) {
            $this->error("   ❌ Errores: {$errores}");
        }

        // Mostrar productos con variantes
        $productosConVariantes = $productos->filter(function ($producto) {
            return $producto->tieneVariantes();
        });

        if ($productosConVariantes->count() > 0) {
            $this->newLine();
            $this->info("📋 Resumen de productos con variantes:");
            
            foreach ($productosConVariantes as $producto) {
                $stockTotal = $producto->stock;
                $variantesConStock = $producto->variantes->where('stock_disponible', '>', 0)->count();
                
                $this->line("   • {$producto->nombre_producto}: {$stockTotal} unidades totales ({$variantesConStock} variantes con stock)");
            }
        }

        return Command::SUCCESS;
    }
}
