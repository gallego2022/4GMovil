<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movimientos_inventario_variantes', function (Blueprint $table) {
            // Eliminar la clave foránea incorrecta
            $table->dropForeign(['usuario_id']);
            
            // Agregar la clave foránea correcta
            $table->foreign('usuario_id')
                  ->references('usuario_id')
                  ->on('usuarios')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_inventario_variantes', function (Blueprint $table) {
            // Eliminar la clave foránea correcta
            $table->dropForeign(['usuario_id']);
            
            // Restaurar la clave foránea incorrecta
            $table->foreign('usuario_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }
};
