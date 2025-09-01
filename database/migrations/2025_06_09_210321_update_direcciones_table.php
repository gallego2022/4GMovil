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
        Schema::table('direcciones', function (Blueprint $table) {
            // Eliminar columnas antiguas si existen
            $table->dropColumn(['direccion', 'ciudad', 'estado', 'codigo_postal', 'pais']);

            // Agregar nuevas columnas
            $table->string('tipo_direccion')->default('casa'); // casa, apartamento, oficina
            $table->string('departamento', 100);
            $table->string('ciudad', 100);
            $table->string('barrio', 100);
            $table->string('direccion');
            $table->string('codigo_postal', 10);
            $table->string('telefono', 20);
            $table->text('instrucciones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direcciones', function (Blueprint $table) {
            // Eliminar las nuevas columnas
            $table->dropColumn([
                'tipo_direccion',
                'departamento',
                'ciudad',
                'barrio',
                'direccion',
                'codigo_postal',
                'telefono',
                'instrucciones'
            ]);

            // Restaurar las columnas originales
            $table->string('direccion');
            $table->string('ciudad', 100);
            $table->string('estado', 100);
            $table->string('codigo_postal', 20)->nullable();
            $table->string('pais', 100);
        });
    }
};
