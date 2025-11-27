<?php

namespace App\Console\Commands;

use App\Services\Inventory\StockAlertService;
use App\Services\Inventory\InventarioService;
use Illuminate\Console\Command;

class LimpiarCacheAlertas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:alertas {--all : Limpiar todo el caché relacionado con inventario}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia el caché de alertas de stock e inventario';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Limpiando caché de alertas...');

        try {
            $alertService = app(StockAlertService::class);
            $inventarioService = app(InventarioService::class);

            // Limpiar caché de alertas
            $alertService->limpiarCache();
            $this->info('✓ Caché de alertas limpiado');

            if ($this->option('all')) {
                // Limpiar todo el caché relacionado con inventario
                $inventarioService->limpiarCacheDashboard();
                $this->info('✓ Caché del dashboard de inventario limpiado');
                
                // Limpiar caché de vistas compiladas
                \Artisan::call('view:clear');
                $this->info('✓ Caché de vistas compiladas limpiado');
                
                // Limpiar caché general de la aplicación
                \Artisan::call('cache:clear');
                $this->info('✓ Caché general de la aplicación limpiado');
            }

            $this->info('Caché limpiado exitosamente.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error al limpiar el caché: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
