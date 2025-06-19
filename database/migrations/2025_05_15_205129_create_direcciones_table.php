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
            $table->text('direccion');
            $table->string('ciudad', 100);
            $table->string('estado', 100);
            $table->string('codigo_postal', 20);
            $table->string('pais', 100);
            $table->timestamps();
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('cascade');
            
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
