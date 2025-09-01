<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InventarioService;
use Carbon\Carbon;

class VerificarFiltrosMovimientos extends Command
{
    protected $signature = 'inventario:verificar-filtros';
    protected $description = 'Verificar qué filtros están activos en la vista de movimientos';

    public function handle()
    {
        $this->info('Verificando filtros activos en movimientos...');

        try {
            $inventarioService = app(InventarioService::class);
            
            // Simular diferentes escenarios de filtros
            
            // 1. Sin filtros (último mes)
            $fechaInicio = now()->subMonth();
            $fechaFin = now();
            
            $this->info("1. SIN FILTROS - Fechas: {$fechaInicio->format('d/m/Y')} - {$fechaFin->format('d/m/Y')}");
            $reporte = $inventarioService->getReporteMovimientos($fechaInicio, $fechaFin);
            $movimientos = $reporte['movimientos'];
            $this->line("   Total movimientos: {$movimientos->count()}");
            
            // Mostrar movimientos del producto 1
            $movimientosProducto1 = $movimientos->where('producto_id', 1);
            $this->line("   Movimientos del producto 1: {$movimientosProducto1->count()}");
            
            // 2. Con filtro por fecha específica (15/08/2025)
            $fechaEspecifica = Carbon::create(2025, 8, 15);
            $this->info("2. FILTRO POR FECHA - Solo 15/08/2025");
            $reporteFecha = $inventarioService->getReporteMovimientos($fechaEspecifica, $fechaEspecifica);
            $movimientosFecha = $reporteFecha['movimientos'];
            $this->line("   Total movimientos: {$movimientosFecha->count()}");
            
            // Mostrar movimientos del producto 1 en esa fecha
            $movimientosProducto1Fecha = $movimientosFecha->where('producto_id', 1);
            $this->line("   Movimientos del producto 1 en esa fecha: {$movimientosProducto1Fecha->count()}");
            
            if ($movimientosProducto1Fecha->count() > 0) {
                $this->line("   Detalles de movimientos del producto 1 en 15/08/2025:");
                foreach ($movimientosProducto1Fecha as $mov) {
                    $this->line("     - ID: {$mov->movimiento_id}, Tipo: {$mov->tipo_movimiento}, Cantidad: {$mov->cantidad}, Hora: {$mov->created_at->format('H:i:s')}");
                }
            }
            
            // 3. Con filtro por producto específico (ID: 1)
            $this->info("3. FILTRO POR PRODUCTO - Solo producto ID: 1");
            $movimientosProducto = $inventarioService->getMovimientosProducto(1, $fechaInicio, $fechaFin);
            $this->line("   Total movimientos del producto 1: {$movimientosProducto->count()}");
            
            if ($movimientosProducto->count() > 0) {
                $this->line("   Detalles de todos los movimientos del producto 1:");
                foreach ($movimientosProducto as $mov) {
                    $this->line("     - ID: {$mov->movimiento_id}, Tipo: {$mov->tipo_movimiento}, Cantidad: {$mov->cantidad}, Fecha: {$mov->created_at->format('d/m/Y H:i')}");
                }
            }
            
            // 4. Combinación de filtros (producto 1 + fecha específica)
            $this->info("4. FILTROS COMBINADOS - Producto 1 + Fecha 15/08/2025");
            $movimientosCombinados = $inventarioService->getMovimientosProducto(1, $fechaEspecifica, $fechaEspecifica);
            $this->line("   Total movimientos combinados: {$movimientosCombinados->count()}");
            
            if ($movimientosCombinados->count() > 0) {
                $this->line("   Detalles de movimientos combinados:");
                foreach ($movimientosCombinados as $mov) {
                    $this->line("     - ID: {$mov->movimiento_id}, Tipo: {$mov->tipo_movimiento}, Cantidad: {$mov->cantidad}, Hora: {$mov->created_at->format('H:i:s')}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
        
        return 0;
    }
}
