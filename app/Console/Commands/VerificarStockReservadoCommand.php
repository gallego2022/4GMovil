<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;

class VerificarStockReservadoCommand extends Command
{
    protected $signature = 'inventario:verificar-stock-reservado {--reservar= : Cantidad a reservar}';
    protected $description = 'Verificar el stock reservado del producto 9';

    public function handle()
    {
        $this->info('🔍 Verificando stock reservado del producto 9...');
        
        // Verificar directamente en la base de datos
        $productoDB = \Illuminate\Support\Facades\DB::table('productos')
            ->select('stock_reservado', 'stock', 'nombre_producto')
            ->where('producto_id', 9)
            ->first();
        
        if ($productoDB) {
            $this->line("📦 {$productoDB->nombre_producto} (BD)");
            $this->line("Stock total (BD): {$productoDB->stock}");
            $this->line("Stock reservado (BD): " . ($productoDB->stock_reservado ?? 0));
            
            $porcentaje = ($productoDB->stock_reservado / $productoDB->stock) * 100;
            $this->line("Porcentaje reservado (BD): " . number_format($porcentaje, 2) . "%");
            
            if ($productoDB->stock_reservado > $productoDB->stock * 0.3) {
                $this->info("⚠️ Stock reservado alto en BD! (>30%)");
            }
        }
        
        $producto = Producto::find(9);
        
        if (!$producto) {
            $this->error('❌ Producto 9 no encontrado');
            return 1;
        }
        
        $this->line("\n📦 {$producto->nombre_producto} (Modelo)");
        $this->line("Stock total: {$producto->stock}");
        $this->line("Stock reservado: " . ($producto->stock_reservado ?? 0));
        $this->line("Stock disponible: {$producto->stock_disponible}");
        
        $stockReservado = $producto->stock_reservado ?? 0;
        if ($stockReservado > 0) {
            $porcentaje = ($stockReservado / $producto->stock) * 100;
            $this->line("Porcentaje reservado: " . number_format($porcentaje, 2) . "%");
            
            if ($stockReservado > $producto->stock * 0.3) {
                $this->info("⚠️ Stock reservado alto! (>30%)");
            } else {
                $this->line("ℹ️ No es stock reservado alto (necesita >30%)");
            }
        } else {
            $this->line("ℹ️ No tiene stock reservado");
        }
        
        // Opción para reservar stock
        $cantidadReservar = $this->option('reservar');
        if ($cantidadReservar) {
            $this->line("\n🔒 Reservando {$cantidadReservar} unidades...");
            
            $resultado = $producto->reservarStock(
                (int) $cantidadReservar,
                "Prueba de reserva desde comando",
                1,
                null
            );
            
            if ($resultado) {
                $this->info("✅ Reserva exitosa!");
                $producto->refresh();
                
                $this->line("\nDespués de la reserva:");
                $this->line("Stock reservado: " . ($producto->stock_reservado ?? 0));
                $this->line("Stock disponible: {$producto->stock_disponible}");
                
                // Verificar directamente en la base de datos
                $productoDB = \Illuminate\Support\Facades\DB::table('productos')
                    ->select('stock_reservado', 'stock')
                    ->where('producto_id', 9)
                    ->first();
                
                $this->line("\nVerificación directa en BD:");
                $this->line("Stock reservado (BD): " . ($productoDB->stock_reservado ?? 0));
                $this->line("Stock total (BD): {$productoDB->stock}");
                
                $nuevoPorcentaje = ($productoDB->stock_reservado / $productoDB->stock) * 100;
                $this->line("Porcentaje reservado: " . number_format($nuevoPorcentaje, 2) . "%");
                
                if ($productoDB->stock_reservado > $productoDB->stock * 0.3) {
                    $this->info("⚠️ Ahora tiene stock reservado alto! (>30%)");
                }
            } else {
                $this->error("❌ No se pudo reservar stock");
            }
        }
        
        // Verificar si el producto 9 aparece en la consulta del dashboard
        // Obtener productos con stock reservado alto usando consulta directa
        $productosStockReservadoAlto = \Illuminate\Support\Facades\DB::table('productos')
            ->where('activo', true)
            ->where('stock_reservado', '>', 0)
            ->whereRaw('stock_reservado > stock * 0.3')
            ->select('producto_id', 'nombre_producto', 'stock', 'stock_reservado', 'stock_disponible')
            ->get();
        
        $this->line("\n🔍 Verificando consulta del dashboard:");
        $this->line("Productos con stock reservado alto: {$productosStockReservadoAlto->count()}");
        
        // Verificación adicional
        $this->line("\n🔍 Verificación adicional:");
        $producto9Directo = \Illuminate\Support\Facades\DB::table('productos')
            ->where('producto_id', 9)
            ->where('activo', true)
            ->where('stock_reservado', '>', 0)
            ->whereRaw('stock_reservado > stock * 0.3')
            ->first();
        
        if ($producto9Directo) {
            $this->line("✅ Producto 9 encontrado en consulta directa");
            $this->line("Stock: {$producto9Directo->stock}, Reservado: {$producto9Directo->stock_reservado}");
        } else {
            $this->line("❌ Producto 9 NO encontrado en consulta directa");
        }
        
        foreach ($productosStockReservadoAlto as $producto) {
            $porcentaje = ($producto->stock_reservado / $producto->stock) * 100;
            $this->line("- {$producto->nombre_producto}: {$producto->stock_reservado}/{$producto->stock} ({$porcentaje}%)");
        }
        
        return 0;
    }
}
