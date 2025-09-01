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
                'tipo' => 'stripe',
                'configuracion' => json_encode([
                    'public_key' => env('STRIPE_KEY', ''),
                    'secret_key' => env('STRIPE_SECRET', ''),
                    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
                    'currency' => 'cop',
                    'enabled' => true
                ]),
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Efectivo',
                'descripcion' => 'Pago en efectivo al momento de la entrega',
                'tipo' => 'efectivo',
                'configuracion' => json_encode([
                    'enabled' => true,
                    'cambio_exacto' => false
                ]),
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Transferencia Bancaria',
                'descripcion' => 'Pago mediante transferencia bancaria',
                'tipo' => 'transferencia',
                'configuracion' => json_encode([
                    'enabled' => true,
                    'cuenta_bancaria' => env('BANK_ACCOUNT', ''),
                    'banco' => env('BANK_NAME', ''),
                    'tipo_cuenta' => 'ahorros'
                ]),
                'estado' => true,
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
