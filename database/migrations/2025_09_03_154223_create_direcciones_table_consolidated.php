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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id('direccion_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('nombre_destinatario');
            $table->string('telefono');
            $table->string('calle');
            $table->string('numero');
            $table->string('piso')->nullable();
            $table->string('departamento')->nullable();
            $table->string('codigo_postal');
            $table->string('ciudad');
            $table->string('provincia');
            $table->string('pais')->default('Argentina');
            $table->text('referencias')->nullable();
            $table->boolean('predeterminada')->default(false);
            $table->boolean('activo')->default(true);
            $table->string('tipo_direccion')->default('casa');
            $table->timestamps();
            
            // Índices
            $table->index(['usuario_id', 'activo']);
            $table->index('predeterminada');
            $table->index('tipo_direccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
