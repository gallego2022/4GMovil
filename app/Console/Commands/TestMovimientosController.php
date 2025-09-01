<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\InventarioController;
use App\Services\InventarioService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestMovimientosController extends Command
{
    protected $signature = 'test:movimientos-controller';
    protected $description = 'Probar el controlador de movimientos para ver qué está devolviendo';

    public function handle()
    {
        $this->info('Probando el controlador de movimientos...');

        try {
            // Crear una instancia del servicio
            $inventarioService = app(InventarioService::class);
            
            // Simular fechas (último mes)
            $fechaInicio = now()->subMonth();
            $fechaFin = now();
            
            $this->info("Fechas de prueba: {$fechaInicio->format('d/m/Y')} - {$fechaFin->format('d/m/Y')}");
            
            // Obtener reporte usando el servicio directamente
            $reporte = $inventarioService->getReporteMovimientos($fechaInicio, $fechaFin);
            $movimientos = $reporte['movimientos'];
            
            $this->info("Total de movimientos obtenidos: {$movimientos->count()}");
            
            // Mostrar los primeros 5 movimientos
            $this->info("Primeros 5 movimientos:");
            $movimientos->take(5)->each(function ($movimiento) {
                $this->line("  - ID: {$movimiento->movimiento_id}, Producto: {$movimiento->producto->nombre_producto}, Tipo: {$movimiento->tipo_movimiento}, Fecha: {$movimiento->created_at->format('d/m/Y H:i')}");
            });
            
            // Verificar si hay duplicados en la colección
            $ids = $movimientos->pluck('movimiento_id');
            $duplicados = $ids->duplicates();
            
            if ($duplicados->count() > 0) {
                $this->warn("¡ATENCIÓN! Hay IDs duplicados en la colección:");
                foreach ($duplicados as $duplicado) {
                    $this->line("  - ID duplicado: {$duplicado}");
                }
            } else {
                $this->info("No hay IDs duplicados en la colección");
            }
            
            // Verificar movimientos del producto 1 específicamente
            $movimientosProducto1 = $movimientos->where('producto_id', 1);
            $this->info("Movimientos del producto 1: {$movimientosProducto1->count()}");
            
            $movimientosProducto1->each(function ($movimiento) {
                $this->line("  - ID: {$movimiento->movimiento_id}, Tipo: {$movimiento->tipo_movimiento}, Cantidad: {$movimiento->cantidad}, Fecha: {$movimiento->created_at->format('d/m/Y H:i')}");
            });
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
        }
        
        return 0;
    }
}
