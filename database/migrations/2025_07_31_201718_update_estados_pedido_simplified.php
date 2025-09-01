<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, actualizar pedidos que tengan estados que vamos a eliminar
        // Cambiar "En preparación" a "Confirmado"
        DB::table('pedidos')
            ->where('estado_id', 3)
            ->update(['estado_id' => 2]);
        
        // Cambiar "En camino" a "Confirmado"
        DB::table('pedidos')
            ->where('estado_id', 4)
            ->update(['estado_id' => 2]);
        
        // Cambiar "Entregado" a "Confirmado"
        DB::table('pedidos')
            ->where('estado_id', 5)
            ->update(['estado_id' => 2]);
        
        // Eliminar los estados que no necesitamos
        DB::table('estados_pedido')
            ->whereIn('estado_id', [3, 4, 5])
            ->delete();
        
        // Actualizar los IDs para que sean consecutivos
        DB::statement('ALTER TABLE estados_pedido AUTO_INCREMENT = 1');
        
        // Verificar que solo quedan los 3 estados que necesitamos
        $estadosActuales = DB::table('estados_pedido')->get();
        
        if ($estadosActuales->count() !== 3) {
            // Si no están los 3 estados correctos, recrear la tabla
            DB::table('estados_pedido')->truncate();
            
            DB::table('estados_pedido')->insert([
                [
                    'estado_id' => 1, 
                    'nombre' => 'Pendiente',
                    'descripcion' => 'Pedido recibido y pendiente de procesamiento',
                    'color' => '#fbbf24',
                    'orden' => 1,
                    'estado' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'estado_id' => 2, 
                    'nombre' => 'Confirmado',
                    'descripcion' => 'Pedido confirmado y en preparación',
                    'color' => '#3b82f6',
                    'orden' => 2,
                    'estado' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'estado_id' => 3, 
                    'nombre' => 'Cancelado',
                    'descripcion' => 'Pedido cancelado por el cliente o sistema',
                    'color' => '#ef4444',
                    'orden' => 3,
                    'estado' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar los estados originales
        DB::table('estados_pedido')->truncate();
        
        DB::table('estados_pedido')->insert([
            [
                'estado_id' => 1, 
                'nombre' => 'Pendiente',
                'descripcion' => 'Pedido recibido y pendiente de procesamiento',
                'color' => '#fbbf24',
                'orden' => 1,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'estado_id' => 2, 
                'nombre' => 'Confirmado',
                'descripcion' => 'Pedido confirmado y en preparación',
                'color' => '#3b82f6',
                'orden' => 2,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'estado_id' => 3, 
                'nombre' => 'En Preparación',
                'descripcion' => 'Productos siendo preparados para envío',
                'color' => '#8b5cf6',
                'orden' => 3,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'estado_id' => 4, 
                'nombre' => 'Enviado',
                'descripcion' => 'Pedido enviado al cliente',
                'color' => '#10b981',
                'orden' => 4,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'estado_id' => 5, 
                'nombre' => 'Entregado',
                'descripcion' => 'Pedido entregado al cliente',
                'color' => '#059669',
                'orden' => 5,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'estado_id' => 6, 
                'nombre' => 'Cancelado',
                'descripcion' => 'Pedido cancelado por el cliente o sistema',
                'color' => '#ef4444',
                'orden' => 6,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
};
