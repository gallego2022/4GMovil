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
        DB::table('estados_pedido')->insert([
            ['estado_id' => 1, 'nombre_estado' => 'Pendiente'],
            ['estado_id' => 2, 'nombre_estado' => 'Confirmado'],
            ['estado_id' => 3, 'nombre_estado' => 'En preparación'],
            ['estado_id' => 4, 'nombre_estado' => 'En camino'],
            ['estado_id' => 5, 'nombre_estado' => 'Entregado'],
            ['estado_id' => 6, 'nombre_estado' => 'Cancelado']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('estados_pedido')->whereIn('estado_id', [1, 2, 3, 4, 5, 6])->delete();
    }
}; 