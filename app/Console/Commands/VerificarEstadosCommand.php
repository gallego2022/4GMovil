<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EstadoPedido;

class VerificarEstadosCommand extends Command
{
    protected $signature = 'estados:verificar';
    protected $description = 'Verificar estados de pedido disponibles';

    public function handle()
    {
        $this->info('📋 Estados de pedido disponibles:');
        $this->info('===============================');
        
        $estados = EstadoPedido::all();
        
        foreach ($estados as $estado) {
            $this->line("• ID: {$estado->estado_id} - {$estado->nombre_estado}");
        }
        
        return 0;
    }
} 