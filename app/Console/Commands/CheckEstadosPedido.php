<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EstadoPedido;

class CheckEstadosPedido extends Command
{
    protected $signature = 'check:estados-pedido';
    protected $description = 'Verificar estados de pedido existentes';

    public function handle()
    {
        $this->info('🔍 Verificando estados de pedido...');
        
        $estados = EstadoPedido::all();
        
        if ($estados->isEmpty()) {
            $this->warn('⚠️ No hay estados de pedido registrados');
            return 1;
        }

        $this->info('📋 Estados de pedido existentes:');
        foreach ($estados as $estado) {
            $this->line("  {$estado->estado_id} - {$estado->nombre_estado}");
        }

        return 0;
    }
}
