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
        Schema::table('resenas', function (Blueprint $table) {
            // Eliminar la restricción única que limita a una reseña por usuario/producto
            // Intentar eliminar con diferentes nombres posibles de la restricción
            try {
                $table->dropUnique(['usuario_id', 'producto_id']);
            } catch (\Exception $e) {
                // Si falla, intentar con el nombre estándar de Laravel
                try {
                    $table->dropUnique('resenas_usuario_id_producto_id_unique');
                } catch (\Exception $e2) {
                    // Si aún falla, usar SQL directo
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE resenas DROP INDEX resenas_usuario_id_producto_id_unique');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resenas', function (Blueprint $table) {
            // Restaurar la restricción única
            $table->unique(['usuario_id', 'producto_id']);
        });
    }
};

