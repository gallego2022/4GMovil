<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PopulateDatabase extends Command
{
    protected $signature = 'db:populate {--fresh : Ejecutar migraciones frescas antes de poblar}';
    protected $description = 'Pobla la base de datos con datos por defecto (métodos de pago, usuario admin, etc.)';

    public function handle()
    {
        $this->info('🚀 Iniciando población de base de datos...');

        // Opción para ejecutar migraciones frescas
        if ($this->option('fresh')) {
            $this->warn('⚠️  Ejecutando migraciones frescas...');
            $this->call('migrate:fresh');
        }

        // Ejecutar seeders
        $this->info('📦 Ejecutando seeders...');
        $this->call('db:seed');

        $this->info('✅ Base de datos poblada exitosamente!');
        $this->info('');
        $this->info('📋 Resumen de lo creado:');
        $this->info('   👤 Usuario administrador: admin@4gmovil.com / Admin123!');
        $this->info('   💳 Método de pago: Stripe');
        $this->info('   💰 Método de pago: Efectivo');
        $this->info('   🏦 Método de pago: Transferencia Bancaria');
        $this->info('   📦 Estados de pedido: 6 estados configurados');
        $this->info('');
        $this->info('🔧 Para verificar, ejecuta: php artisan test:google-password admin@4gmovil.com');
    }
}
