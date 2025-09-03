<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen métodos de pago
        if (DB::table('metodos_pago')->count() > 0) {
            $this->command->info('La tabla metodos_pago ya tiene datos. Saltando seeder...');
            return;
        }

        // Insertar métodos de pago por defecto
        DB::table('metodos_pago')->insert([
            [
                'nombre' => 'Stripe',
                'descripcion' => 'Pago con tarjeta de crédito/débito a través de Stripe',
                'icono' => 'credit-card',
                'estado' => true,
                'orden' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Efectivo',
                'descripcion' => 'Pago en efectivo al momento de la entrega',
                'icono' => 'money-bill',
                'estado' => true,
                'orden' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Transferencia Bancaria',
                'descripcion' => 'Pago mediante transferencia bancaria',
                'icono' => 'university',
                'estado' => true,
                'orden' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $this->command->info('✅ Métodos de pago creados exitosamente:');
        $this->command->info('   - Stripe (Tarjeta de crédito/débito)');
        $this->command->info('   - Efectivo');
        $this->command->info('   - Transferencia Bancaria');
    }
}
