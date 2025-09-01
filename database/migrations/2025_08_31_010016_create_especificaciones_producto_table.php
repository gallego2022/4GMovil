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
        Schema::create('especificaciones_producto', function (Blueprint $table) {
            $table->id('especificacion_producto_id');
            $table->foreignId('producto_id')->constrained('productos', 'producto_id')->onDelete('cascade');
            $table->foreignId('especificacion_id')->constrained('especificaciones_categoria', 'especificacion_id')->onDelete('cascade');
            $table->text('valor'); // El valor de la especificación
            $table->timestamps();
            
            // Índices
            $table->index(['producto_id', 'especificacion_id']);
            $table->unique(['producto_id', 'especificacion_id'], 'unique_producto_especificacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especificaciones_producto');
    }
};
