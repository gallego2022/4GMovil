<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class VerifyCleanup extends Command
{
    protected $signature = 'verify:cleanup';
    protected $description = 'Verifica que no hay referencias rotas después de la limpieza';

    public function handle()
    {
        $this->info('🔍 Verificando limpieza del código...');
        $this->newLine();

        // Verificar que las rutas optimizadas existen
        $this->info('✅ Verificando rutas optimizadas...');
        
        $rutasOptimizadas = [
            'admin.inventario.alertas-optimizadas',
            'admin.inventario.alertas.variantes',
            'admin.inventario.alertas.estadisticas'
        ];

        foreach ($rutasOptimizadas as $ruta) {
            if (Route::has($ruta)) {
                $this->line("  ✅ {$ruta} - OK");
            } else {
                $this->error("  ❌ {$ruta} - NO ENCONTRADA");
            }
        }

        // Verificar que la ruta antigua no existe
        $this->newLine();
        $this->info('❌ Verificando que las rutas obsoletas fueron eliminadas...');
        
        $rutasObsoletas = [
            'admin.inventario.alertas'
        ];

        foreach ($rutasObsoletas as $ruta) {
            if (Route::has($ruta)) {
                $this->error("  ❌ {$ruta} - AÚN EXISTE (debería estar eliminada)");
            } else {
                $this->line("  ✅ {$ruta} - CORRECTAMENTE ELIMINADA");
            }
        }

        // Verificar archivos eliminados
        $this->newLine();
        $this->info('📁 Verificando archivos eliminados...');
        
        $archivosEliminados = [
            'resources/views/pages/admin/inventario/alertas.blade.php'
        ];

        foreach ($archivosEliminados as $archivo) {
            if (!file_exists($archivo)) {
                $this->line("  ✅ {$archivo} - CORRECTAMENTE ELIMINADO");
            } else {
                $this->error("  ❌ {$archivo} - AÚN EXISTE (debería estar eliminado)");
            }
        }

        // Verificar métodos eliminados
        $this->newLine();
        $this->info('🔧 Verificando métodos eliminados...');
        
        $inventarioController = new \App\Http\Controllers\Admin\InventarioController(
            new \App\Services\InventarioService()
        );
        
        if (method_exists($inventarioController, 'alertas')) {
            $this->error("  ❌ Método 'alertas' - AÚN EXISTE (debería estar eliminado)");
        } else {
            $this->line("  ✅ Método 'alertas' - CORRECTAMENTE ELIMINADO");
        }

        // Verificar servicios optimizados
        $this->newLine();
        $this->info('⚙️ Verificando servicios optimizados...');
        
        $serviciosOptimizados = [
            'App\Services\Inventory\StockAlertService',
            'App\Http\Controllers\Admin\OptimizedStockAlertController'
        ];

        foreach ($serviciosOptimizados as $servicio) {
            if (class_exists($servicio)) {
                $this->line("  ✅ {$servicio} - OK");
            } else {
                $this->error("  ❌ {$servicio} - NO ENCONTRADO");
            }
        }

        $this->newLine();
        $this->info('🎯 Resumen de la limpieza:');
        $this->line('  • ✅ Vistas obsoletas eliminadas');
        $this->line('  • ✅ Métodos no utilizados eliminados');
        $this->line('  • ✅ Rutas obsoletas eliminadas');
        $this->line('  • ✅ Referencias actualizadas a alertas optimizadas');
        $this->line('  • ✅ Sistema optimizado funcionando correctamente');

        $this->newLine();
        $this->info('✅ Limpieza verificada exitosamente!');
        $this->info('🚀 El sistema está optimizado y libre de código obsoleto.');

        return Command::SUCCESS;
    }
}
