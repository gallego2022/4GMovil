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
        Schema::create('imagenes_variantes', function (Blueprint $table) {
            $table->id('imagen_variante_id');
            $table->unsignedBigInteger('variante_id');
            $table->string('ruta_imagen');
            $table->string('nombre_archivo');
            $table->string('tipo_mime');
            $table->integer('tamaño_bytes');
            $table->integer('orden')->default(0);
            $table->boolean('es_principal')->default(false);
            $table->timestamps();

            $table->foreign('variante_id')
                  ->references('variante_id')
                  ->on('variantes_producto')
                  ->onDelete('cascade');

            $table->index(['variante_id', 'orden']);
            $table->index(['variante_id', 'es_principal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes_variantes');
    }
};
