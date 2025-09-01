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
        Schema::create('reservas_stock_variantes', function (Blueprint $table) {
            $table->id('reserva_id');
            $table->unsignedBigInteger('variante_id');
            $table->unsignedBigInteger('usuario_id');
            $table->integer('cantidad');
            $table->datetime('fecha_reserva');
            $table->datetime('fecha_expiracion');
            $table->enum('estado', ['activa', 'confirmada', 'expirada', 'cancelada'])->default('activa');
            $table->string('referencia_pedido', 50)->nullable(); // ID del pedido asociado
            $table->text('motivo')->nullable();
            $table->timestamps();
            
            // Índices
            $table->foreign('variante_id')->references('variante_id')->on('variantes_producto')->onDelete('cascade');
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('cascade');
            $table->index(['variante_id', 'estado']);
            $table->index(['usuario_id', 'estado']);
            $table->index(['fecha_expiracion', 'estado']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas_stock_variantes');
    }
};
