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
        // Limpiar la tabla primero (usando delete para evitar problemas de clave foránea)
        DB::table('estados_pedido')->delete();

        // Insertar solo los 3 estados necesarios con IDs específicos
        DB::table('estados_pedido')->insert([
            [
                'estado_id' => 1,
                'nombre' => 'Pendiente',
                'descripcion' => 'Pedido recibido y pendiente de procesamiento',
                'color' => '#fbbf24',
                'orden' => 1,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'estado_id' => 2,
                'nombre' => 'Confirmado',
                'descripcion' => 'Pedido confirmado y en preparación',
                'color' => '#3b82f6',
                'orden' => 2,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'estado_id' => 3,
                'nombre' => 'Cancelado',
                'descripcion' => 'Pedido cancelado por el cliente o sistema',
                'color' => '#ef4444',
                'orden' => 3,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $this->command->info('✅ Estados de pedido simplificados creados exitosamente:');
        $this->command->info('   - Pendiente (ID: 1)');
        $this->command->info('   - Confirmado (ID: 2)');
        $this->command->info('   - Cancelado (ID: 3)');
    }
}
