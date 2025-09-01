<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class RevisarInconsistenciasCommand extends Command
{
    protected $signature = 'inventario:revisar-inconsistencias';
    protected $description = 'Revisar inconsistencias en el inventario después del proceso de prueba';

    public function handle()
    {
        $this->info("🔍 REVISANDO INCONSISTENCIAS EN EL INVENTARIO");
        $this->line("");

        // 1. Revisar productos con stock negativo
        $this->info("1️⃣ Verificando productos con stock negativo...");
        $productosStockNegativo = Producto::where('stock', '<', 0)->get();
        
        if ($productosStockNegativo->count() > 0) {
            $this->error("❌ Se encontraron productos con stock negativo:");
            foreach ($productosStockNegativo as $producto) {
                $this->line("   - {$producto->nombre_producto} (ID: {$producto->producto_id}): Stock = {$producto->stock}");
            }
        } else {
            $this->info("✅ No hay productos con stock negativo");
        }
        $this->line("");

        // 2. Revisar productos con stock_reservado mayor que stock
        $this->info("2️⃣ Verificando productos con stock reservado mayor que stock total...");
        $productosReservadoAlto = Producto::whereRaw('stock_reservado > stock')->get();
        
        if ($productosReservadoAlto->count() > 0) {
            $this->error("❌ Se encontraron productos con stock reservado mayor que stock total:");
            foreach ($productosReservadoAlto as $producto) {
                $this->line("   - {$producto->nombre_producto} (ID: {$producto->producto_id})");
                $this->line("     Stock total: {$producto->stock}, Stock reservado: {$producto->stock_reservado}");
            }
        } else {
            $this->info("✅ No hay productos con stock reservado mayor que stock total");
        }
        $this->line("");

        // 3. Revisar productos con stock disponible negativo
        $this->info("3️⃣ Verificando productos con stock disponible negativo...");
        $productosDisponibleNegativo = Producto::whereRaw('stock - COALESCE(stock_reservado, 0) < 0')->get();
        
        if ($productosDisponibleNegativo->count() > 0) {
            $this->error("❌ Se encontraron productos con stock disponible negativo:");
            foreach ($productosDisponibleNegativo as $producto) {
                $stockDisponible = $producto->stock - ($producto->stock_reservado ?? 0);
                $this->line("   - {$producto->nombre_producto} (ID: {$producto->producto_id})");
                $this->line("     Stock: {$producto->stock}, Reservado: {$producto->stock_reservado}, Disponible: {$stockDisponible}");
            }
        } else {
            $this->info("✅ No hay productos con stock disponible negativo");
        }
        $this->line("");

        // 4. Revisar movimientos duplicados
        $this->info("4️⃣ Verificando movimientos duplicados...");
        $movimientosDuplicados = MovimientoInventario::select('producto_id', 'tipo_movimiento', 'cantidad', 'motivo', 'created_at')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('producto_id', 'tipo_movimiento', 'cantidad', 'motivo', 'created_at')
            ->having('total', '>', 1)
            ->get();
        
        if ($movimientosDuplicados->count() > 0) {
            $this->error("❌ Se encontraron movimientos duplicados:");
            foreach ($movimientosDuplicados as $duplicado) {
                $producto = Producto::find($duplicado->producto_id);
                $this->line("   - Producto: {$producto->nombre_producto} (ID: {$duplicado->producto_id})");
                $this->line("     Tipo: {$duplicado->tipo_movimiento}, Cantidad: {$duplicado->cantidad}");
                $this->line("     Motivo: {$duplicado->motivo}, Fecha: {$duplicado->created_at}");
                $this->line("     Duplicados: {$duplicado->total} veces");
            }
        } else {
            $this->info("✅ No hay movimientos duplicados");
        }
        $this->line("");

        // 5. Revisar consistencia entre stock y movimientos
        $this->info("5️⃣ Verificando consistencia entre stock y movimientos...");
        $productos = Producto::all();
        $inconsistencias = [];
        
        foreach ($productos as $producto) {
            // Calcular stock basado en movimientos
            $entradas = $producto->movimientosInventario()
                ->where('tipo_movimiento', 'entrada')
                ->sum('cantidad');
            
            $salidas = $producto->movimientosInventario()
                ->where('tipo_movimiento', 'salida')
                ->sum('cantidad');
            
            $stockCalculado = $entradas - $salidas;
            
            if ($stockCalculado != $producto->stock) {
                $inconsistencias[] = [
                    'producto' => $producto,
                    'stock_actual' => $producto->stock,
                    'stock_calculado' => $stockCalculado,
                    'entradas' => $entradas,
                    'salidas' => $salidas
                ];
            }
        }
        
        if (count($inconsistencias) > 0) {
            $this->error("❌ Se encontraron inconsistencias entre stock y movimientos:");
            foreach ($inconsistencias as $inconsistencia) {
                $p = $inconsistencia['producto'];
                $this->line("   - {$p->nombre_producto} (ID: {$p->producto_id})");
                $this->line("     Stock actual: {$inconsistencia['stock_actual']}");
                $this->line("     Stock calculado: {$inconsistencia['stock_calculado']}");
                $this->line("     Entradas: {$inconsistencia['entradas']}, Salidas: {$inconsistencia['salidas']}");
            }
        } else {
            $this->info("✅ El stock coincide con los movimientos registrados");
        }
        $this->line("");

        // 6. Revisar productos recientes (últimos 10)
        $this->info("6️⃣ Revisando productos más recientes...");
        $productosRecientes = Producto::orderBy('created_at', 'desc')->limit(10)->get();
        
        $this->line("📊 Últimos 10 productos creados:");
        foreach ($productosRecientes as $producto) {
            $stockDisponible = $producto->stock - ($producto->stock_reservado ?? 0);
            $this->line("   - {$producto->nombre_producto} (ID: {$producto->producto_id})");
            $this->line("     Stock: {$producto->stock}, Reservado: {$producto->stock_reservado}, Disponible: {$stockDisponible}");
            $this->line("     Creado: {$producto->created_at}");
            $this->line("");
        }

        // 7. Resumen final
        $this->info("🎯 RESUMEN FINAL:");
        $totalProblemas = $productosStockNegativo->count() + 
                         $productosReservadoAlto->count() + 
                         $productosDisponibleNegativo->count() + 
                         $movimientosDuplicados->count() + 
                         count($inconsistencias);
        
        if ($totalProblemas == 0) {
            $this->info("✅ NO SE ENCONTRARON INCONSISTENCIAS EN EL INVENTARIO");
        } else {
            $this->error("❌ SE ENCONTRARON {$totalProblemas} PROBLEMAS EN EL INVENTARIO");
        }

        return 0;
    }
} 