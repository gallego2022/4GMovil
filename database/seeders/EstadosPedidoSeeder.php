<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosPedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen estados de pedido
        if (DB::table('estados_pedido')->count() > 0) {
            $this->command->info('La tabla estados_pedido ya tiene datos. Saltando seeder...');
            return;
        }

        // Insertar estados de pedido por defecto
        DB::table('estados_pedido')->insert([
            [
                'nombre' => 'Pendiente',
                'descripcion' => 'Pedido recibido y pendiente de procesamiento',
                'color' => '#fbbf24',
                'activo' => true,
                'orden' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Confirmado',
                'descripcion' => 'Pedido confirmado y en preparación',
                'color' => '#3b82f6',
                'activo' => true,
                'orden' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Cancelado',
                'descripcion' => 'Pedido cancelado por el cliente o sistema',
                'color' => '#ef4444',
                'activo' => true,
                'orden' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $this->command->info('✅ Estados de pedido creados exitosamente:');
        $this->command->info('   - Pendiente (ID: 1)');
        $this->command->info('   - Confirmado (ID: 2)');
        $this->command->info('   - Cancelado (ID: 3)');
    }
}
