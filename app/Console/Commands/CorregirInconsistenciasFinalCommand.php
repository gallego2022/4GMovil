<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class CorregirInconsistenciasFinalCommand extends Command
{
    protected $signature = 'inventario:corregir-inconsistencias-final';
    protected $description = 'Corregir todas las inconsistencias encontradas en el inventario';

    public function handle()
    {
        $this->info("🔧 CORRIGIENDO INCONSISTENCIAS EN EL INVENTARIO");
        $this->line("");

        // 1. Eliminar movimientos duplicados
        $this->info("1️⃣ Eliminando movimientos duplicados...");
        
        $movimientosDuplicados = MovimientoInventario::select('producto_id', 'tipo_movimiento', 'cantidad', 'motivo', 'created_at')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('producto_id', 'tipo_movimiento', 'cantidad', 'motivo', 'created_at')
            ->having('total', '>', 1)
            ->get();
        
        $duplicadosEliminados = 0;
        foreach ($movimientosDuplicados as $duplicado) {
            $movimientos = MovimientoInventario::where('producto_id', $duplicado->producto_id)
                ->where('tipo_movimiento', $duplicado->tipo_movimiento)
                ->where('cantidad', $duplicado->cantidad)
                ->where('motivo', $duplicado->motivo)
                ->where('created_at', $duplicado->created_at)
                ->orderBy('movimiento_id')
                ->get();
            
            // Mantener solo el primero, eliminar los demás
            for ($i = 1; $i < count($movimientos); $i++) {
                $movimientos[$i]->delete();
                $duplicadosEliminados++;
            }
        }
        
        $this->info("✅ Se eliminaron {$duplicadosEliminados} movimientos duplicados");
        $this->line("");

        // 2. Corregir stock basado en movimientos reales
        $this->info("2️⃣ Corrigiendo stock basado en movimientos reales...");
        
        $productos = Producto::all();
        $productosCorregidos = 0;
        
        foreach ($productos as $producto) {
            // Calcular stock real basado en movimientos
            $entradas = $producto->movimientosInventario()
                ->where('tipo_movimiento', 'entrada')
                ->sum('cantidad');
            
            $salidas = $producto->movimientosInventario()
                ->where('tipo_movimiento', 'salida')
                ->sum('cantidad');
            
            $stockReal = $entradas - $salidas;
            
            // Si el stock actual no coincide con el calculado, corregirlo
            if ($producto->stock != $stockReal) {
                $stockAnterior = $producto->stock;
                $producto->stock = $stockReal;
                $producto->ultima_actualizacion_stock = now();
                $producto->save();
                
                $this->line("   - {$producto->nombre_producto} (ID: {$producto->producto_id})");
                $this->line("     Stock anterior: {$stockAnterior} → Stock corregido: {$stockReal}");
                $this->line("     Entradas: {$entradas}, Salidas: {$salidas}");
                $this->line("");
                
                $productosCorregidos++;
            }
        }
        
        $this->info("✅ Se corrigieron {$productosCorregidos} productos");
        $this->line("");

        // 3. Corregir stock_reservado si es mayor que stock
        $this->info("3️⃣ Corrigiendo stock reservado excesivo...");
        
        $productosReservadoCorregidos = 0;
        $productosReservadoAlto = Producto::whereRaw('stock_reservado > stock')->get();
        
        foreach ($productosReservadoAlto as $producto) {
            $stockReservadoAnterior = $producto->stock_reservado;
            $producto->stock_reservado = $producto->stock; // Cap al stock total
            $producto->ultima_actualizacion_stock = now();
            $producto->save();
            
            $this->line("   - {$producto->nombre_producto} (ID: {$producto->producto_id})");
            $this->line("     Stock reservado anterior: {$stockReservadoAnterior} → Corregido: {$producto->stock_reservado}");
            $this->line("");
            
            $productosReservadoCorregidos++;
        }
        
        $this->info("✅ Se corrigieron {$productosReservadoCorregidos} productos con stock reservado excesivo");
        $this->line("");

        // 4. Verificar que no haya stock negativo
        $this->info("4️⃣ Verificando que no haya stock negativo...");
        
        $productosStockNegativo = Producto::where('stock', '<', 0)->get();
        if ($productosStockNegativo->count() > 0) {
            foreach ($productosStockNegativo as $producto) {
                $producto->stock = 0;
                $producto->stock_reservado = 0;
                $producto->ultima_actualizacion_stock = now();
                $producto->save();
                
                $this->line("   - {$producto->nombre_producto} (ID: {$producto->producto_id})");
                $this->line("     Stock corregido a 0");
                $this->line("");
            }
            $this->info("✅ Se corrigieron productos con stock negativo");
        } else {
            $this->info("✅ No hay productos con stock negativo");
        }
        $this->line("");

        // 5. Mostrar estado final
        $this->info("5️⃣ Estado final del inventario:");
        $productosFinales = Producto::orderBy('created_at', 'desc')->limit(10)->get();
        
        foreach ($productosFinales as $producto) {
            $stockDisponible = $producto->stock - ($producto->stock_reservado ?? 0);
            $this->line("   - {$producto->nombre_producto} (ID: {$producto->producto_id})");
            $this->line("     Stock: {$producto->stock}, Reservado: {$producto->stock_reservado}, Disponible: {$stockDisponible}");
            $this->line("");
        }

        // 6. Resumen final
        $this->info("🎯 RESUMEN DE CORRECCIONES:");
        $this->line("✅ Movimientos duplicados eliminados: {$duplicadosEliminados}");
        $this->line("✅ Productos con stock corregido: {$productosCorregidos}");
        $this->line("✅ Productos con stock reservado corregido: {$productosReservadoCorregidos}");
        $this->line("");
        $this->info("✅ TODAS LAS INCONSISTENCIAS HAN SIDO CORREGIDAS");

        return 0;
    }
} 