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
        Schema::create('especificaciones_categoria', function (Blueprint $table) {
            $table->id('especificacion_id');
            $table->foreignId('categoria_id')->constrained('categorias', 'categoria_id')->onDelete('cascade');
            $table->string('nombre_campo', 100); // Ej: 'ram', 'almacenamiento', 'bateria'
            $table->string('etiqueta', 255); // Ej: 'Memoria RAM', 'Almacenamiento', 'Batería'
            $table->enum('tipo_campo', ['text', 'number', 'select', 'textarea', 'checkbox', 'radio'])->default('text');
            $table->text('opciones')->nullable(); // Para campos select/radio (JSON)
            $table->string('unidad', 50)->nullable(); // Ej: 'GB', 'mAh', 'pulgadas'
            $table->text('descripcion')->nullable(); // Descripción del campo
            $table->boolean('requerido')->default(false);
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices
            $table->index(['categoria_id', 'activo']);
            $table->index('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especificaciones_categoria');
    }
};
