<?php

namespace App\Console\Commands;

use App\Services\ReservaStockService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LimpiarReservasExpiradas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservas:limpiar-expiradas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar reservas de stock expiradas automáticamente';

    /**
     * Execute the console command.
     */
    public function handle(ReservaStockService $reservaStockService)
    {
        $this->info('Iniciando limpieza de reservas expiradas...');

        try {
            $contador = $reservaStockService->limpiarReservasExpiradas();

            if ($contador > 0) {
                $this->info("Se limpiaron {$contador} reservas expiradas.");
                Log::info("Comando limpiar reservas expiradas ejecutado", ['reservas_limpiadas' => $contador]);
            } else {
                $this->info('No se encontraron reservas expiradas para limpiar.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error al limpiar reservas expiradas: ' . $e->getMessage());
            Log::error('Error en comando limpiar reservas expiradas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
}
