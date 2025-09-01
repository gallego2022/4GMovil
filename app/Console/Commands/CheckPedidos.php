<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;

class CheckPedidos extends Command
{
    protected $signature = 'check:pedidos';
    protected $description = 'Verificar pedidos existentes';

    public function handle()
    {
        $pedidos = Pedido::all();
        
        if ($pedidos->isEmpty()) {
            $this->info('No hay pedidos registrados.');
            return;
        }
        
        $this->info('Pedidos existentes:');
        foreach ($pedidos as $pedido) {
            $this->line("  {$pedido->pedido_id} - Usuario: {$pedido->usuario_id} - Estado: {$pedido->estado_id} - Total: $" . number_format($pedido->total, 0, ',', '.'));
        }
    }
}
