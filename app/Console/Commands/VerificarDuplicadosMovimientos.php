<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class VerificarDuplicadosMovimientos extends Command
{
    protected $signature = 'inventario:verificar-duplicados';
    protected $description = 'Verificar duplicados en movimientos de inventario';

    public function handle()
    {
        $this->info('Verificando duplicados en movimientos de inventario...');

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

        if ($duplicados->count() > 0) {
            $this->warn("Se encontraron {$duplicados->count()} grupos de duplicados:");
            
            foreach ($duplicados as $duplicado) {
                $this->line("Producto ID: {$duplicado->producto_id}");
                $this->line("Tipo: {$duplicado->tipo_movimiento}");
                $this->line("Cantidad: {$duplicado->cantidad}");
                $this->line("Fecha: {$duplicado->created_at}");
                $this->line("---");
            }
        } else {
            $this->info('No se encontraron duplicados exactos.');
        }

        // Verificar duplicados por fecha y producto (más específico)
        $duplicadosFecha = MovimientoInventario::select(
            'producto_id', 
            'tipo_movimiento', 
            'cantidad', 
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('producto_id', 'tipo_movimiento', 'cantidad', DB::raw('DATE(created_at)'))
        ->havingRaw('COUNT(*) > 1')
        ->get();

        if ($duplicadosFecha->count() > 0) {
            $this->warn("Se encontraron {$duplicadosFecha->count()} grupos de duplicados por fecha:");
            
            foreach ($duplicadosFecha as $dup) {
                $this->line("Producto ID: {$dup->producto_id}, Tipo: {$dup->tipo_movimiento}, Cantidad: {$dup->cantidad}, Fecha: {$dup->fecha}, Total: {$dup->total}");
            }
        }

        return 0;
    }
}
