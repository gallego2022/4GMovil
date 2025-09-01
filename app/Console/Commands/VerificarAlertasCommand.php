<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InventarioService;

class VerificarAlertasCommand extends Command
{
    protected $signature = 'inventario:verificar-alertas';
    protected $description = 'Verificar las alertas de inventario';

    public function handle()
    {
        $this->info('🔍 Verificando alertas de inventario...');
        
        $inventarioService = new InventarioService();
        $alertas = $inventarioService->getAlertasInventarioMejoradas();
        
        $this->info("\n📊 Alertas de Inventario:");
        $this->line("Stock crítico: {$alertas['stock_critico']} productos");
        $this->line("Stock bajo: {$alertas['stock_bajo']} productos");
        $this->line("Sin stock: {$alertas['sin_stock']} productos");
        $this->line("Stock excesivo: {$alertas['stock_excesivo']} productos");
        $this->line("Necesita reabastecimiento: {$alertas['necesita_reabastecimiento']} productos");
        $this->line("Stock reservado alto: {$alertas['stock_reservado_alto']} productos");
        $this->line("Productos inactivos: {$alertas['productos_inactivos']} productos");
        
        if ($alertas['stock_reservado_alto'] > 0) {
            $this->info("\n⚠️ ¡Hay productos con stock reservado alto!");
        } else {
            $this->line("\nℹ️ No hay productos con stock reservado alto");
        }
        
        return 0;
    }
}
