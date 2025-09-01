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
        // Primero, crear los estados que necesitamos
        // Verificar si existe el estado "Pendiente" con ID 1
        $pendiente = DB::table('estados_pedido')->where('estado_id', 1)->first();
        if (!$pendiente) {
            // Crear el estado Pendiente si no existe
            DB::table('estados_pedido')->insert([
                'estado_id' => 1,
                'nombre' => 'Pendiente',
                'descripcion' => 'Pedido recibido y pendiente de procesamiento',
                'color' => '#fbbf24',
                'orden' => 1,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Verificar si existe el estado "Confirmado" con ID 2
        $confirmado = DB::table('estados_pedido')->where('estado_id', 2)->first();
        if (!$confirmado) {
            // Crear el estado Confirmado si no existe
            DB::table('estados_pedido')->insert([
                'estado_id' => 2,
                'nombre' => 'Confirmado',
                'descripcion' => 'Pedido confirmado y en preparación',
                'color' => '#3b82f6',
                'orden' => 2,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Verificar si existe el estado "Cancelado" con ID 3
        $cancelado = DB::table('estados_pedido')->where('estado_id', 3)->first();
        if (!$cancelado) {
            // Crear el estado Cancelado si no existe
            DB::table('estados_pedido')->insert([
                'estado_id' => 3,
                'nombre' => 'Cancelado',
                'descripcion' => 'Pedido cancelado por el cliente o sistema',
                'color' => '#ef4444',
                'orden' => 3,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Ahora actualizar los pedidos que tengan estado_id 6 a 3 (Cancelado)
        DB::table('pedidos')
            ->where('estado_id', 6)
            ->update(['estado_id' => 3]);
        
        // Eliminar cualquier estado que no sea 1, 2 o 3
        DB::table('estados_pedido')
            ->whereNotIn('estado_id', [1, 2, 3])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario hacer nada en el down ya que estamos simplificando
    }
}; 