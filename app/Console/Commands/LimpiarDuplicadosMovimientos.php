<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class LimpiarDuplicadosMovimientos extends Command
{
    protected $signature = 'inventario:limpiar-duplicados {--dry-run : Solo mostrar qué se eliminaría sin ejecutar}';
    protected $description = 'Limpiar duplicados en movimientos de inventario';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('MODO SIMULACIÓN: Solo se mostrará qué se eliminaría');
        }

        $this->info('Buscando duplicados en movimientos de inventario...');

        // Buscar duplicados exactos
        $duplicados = MovimientoInventario::select(
            'producto_id', 
            'tipo_movimiento', 
            'cantidad', 
            'stock_anterior', 
            'stock_nuevo', 
            'motivo', 
            'created_at'
        )
        ->groupBy(
            'producto_id', 
            'tipo_movimiento', 
            'cantidad', 
            'stock_anterior', 
            'stock_nuevo', 
            'motivo', 
            'created_at'
        )
        ->havingRaw('COUNT(*) > 1')
        ->get();

        if ($duplicados->count() == 0) {
            $this->info('No se encontraron duplicados para limpiar.');
            return 0;
        }

        $this->warn("Se encontraron {$duplicados->count()} grupos de duplicados:");

        $totalEliminados = 0;

        foreach ($duplicados as $duplicado) {
            $this->line("Procesando duplicados para:");
            $this->line("  - Producto ID: {$duplicado->producto_id}");
            $this->line("  - Tipo: {$duplicado->tipo_movimiento}");
            $this->line("  - Cantidad: {$duplicado->cantidad}");
            $this->line("  - Fecha: {$duplicado->created_at}");

            // Obtener todos los registros duplicados para este grupo
            $registrosDuplicados = MovimientoInventario::where([
                'producto_id' => $duplicado->producto_id,
                'tipo_movimiento' => $duplicado->tipo_movimiento,
                'cantidad' => $duplicado->cantidad,
                'stock_anterior' => $duplicado->stock_anterior,
                'stock_nuevo' => $duplicado->stock_nuevo,
                'motivo' => $duplicado->motivo,
            ])
            ->whereRaw('DATE(created_at) = ?', [$duplicado->created_at->toDateString()])
            ->orderBy('movimiento_id')
            ->get();

            $this->line("  - Registros encontrados: {$registrosDuplicados->count()}");

            if ($registrosDuplicados->count() > 1) {
                // Mantener el primer registro (más antiguo) y eliminar los demás
                $registrosAEliminar = $registrosDuplicados->slice(1);
                $cantidadAEliminar = $registrosAEliminar->count();

                $this->line("  - Se mantendrá 1 registro y se eliminarán {$cantidadAEliminar} duplicados");

                if (!$dryRun) {
                    foreach ($registrosAEliminar as $registro) {
                        $registro->delete();
                        $totalEliminados++;
                    }
                    $this->info("  ✓ Duplicados eliminados");
                } else {
                    $this->line("  - [SIMULACIÓN] Se eliminarían {$cantidadAEliminar} registros");
                    $totalEliminados += $cantidadAEliminar;
                }
            }

            $this->line("---");
        }

        if ($dryRun) {
            $this->info("SIMULACIÓN COMPLETADA: Se eliminarían {$totalEliminados} registros duplicados");
            $this->info("Para ejecutar realmente, ejecuta: php artisan inventario:limpiar-duplicados");
        } else {
            $this->info("LIMPIEZA COMPLETADA: Se eliminaron {$totalEliminados} registros duplicados");
        }

        return 0;
    }
}
