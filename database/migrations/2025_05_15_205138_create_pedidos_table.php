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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id('pedido_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('direccion_id');
            $table->datetime('fecha_pedido');
            $table->unsignedBigInteger('estado_id');
            $table->decimal('total', 10, 2);
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios');
            $table->foreign('direccion_id')->references('direccion_id')->on('direcciones');
            $table->foreign('estado_id')->references('estado_id')->on('estados_pedido');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
