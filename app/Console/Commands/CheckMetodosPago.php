<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MetodoPago;

class CheckMetodosPago extends Command
{
    protected $signature = 'check:metodos-pago';
    protected $description = 'Verificar métodos de pago existentes';

    public function handle()
    {
        $metodos = MetodoPago::all();
        
        if ($metodos->isEmpty()) {
            $this->info('No hay métodos de pago registrados.');
            return;
        }
        
        $this->info('Métodos de pago existentes:');
        foreach ($metodos as $metodo) {
            $this->line("  {$metodo->metodo_id} - {$metodo->nombre_metodo}");
        }
    }
}
