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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('pago_id');
            $table->unsignedBigInteger('pedido_id');
            $table->decimal('monto', 10, 2);
            $table->unsignedBigInteger('metodo_id');
            $table->datetime('fecha_pago');
            $table->foreign('pedido_id')->references('pedido_id')->on('pedidos');
            $table->foreign('metodo_id')->references('metodo_id')->on('metodos_pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
