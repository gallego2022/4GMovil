<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id('movimiento_id');
            $table->unsignedBigInteger('producto_id');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste', 'devolucion', 'reserva', 'liberacion']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->text('motivo');
            $table->unsignedBigInteger('usuario_id')->nullable(); // Quien realizó el movimiento
            $table->unsignedBigInteger('pedido_id')->nullable(); // Si está relacionado con un pedido
            $table->string('referencia')->nullable(); // Número de factura, nota de crédito, etc.
            $table->decimal('costo_unitario', 10, 2)->nullable(); // Costo al momento del movimiento
            $table->timestamps();
            
            $table->foreign('producto_id')->references('producto_id')->on('productos')->onDelete('cascade');
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('set null');
            $table->foreign('pedido_id')->references('pedido_id')->on('pedidos')->onDelete('set null');
            
            $table->index(['producto_id', 'created_at']);
            $table->index(['tipo_movimiento', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
}; 