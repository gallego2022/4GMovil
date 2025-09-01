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
        Schema::create('resenas', function (Blueprint $table) {
            $table->id('resena_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('producto_id');
            $table->integer('calificacion');
            $table->text('comentario');
            $table->datetime('fecha_creacion');
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios');
            $table->foreign('producto_id')->references('producto_id')->on('productos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
