<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, necesitamos modificar el enum para incluir los nuevos tipos
        // Como MySQL no permite modificar enums directamente, usamos una estrategia diferente
        
        // 1. Crear una tabla temporal con la nueva estructura
        Schema::create('movimientos_inventario_temp', function (Blueprint $table) {
            $table->id('movimiento_id');
            $table->unsignedBigInteger('producto_id');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste', 'devolucion', 'reserva', 'liberacion']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->text('motivo');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->string('referencia')->nullable();
            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('producto_id')->references('producto_id')->on('productos')->onDelete('cascade');
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('set null');
            $table->foreign('pedido_id')->references('pedido_id')->on('pedidos')->onDelete('set null');
            
            $table->index(['producto_id', 'created_at']);
            $table->index(['tipo_movimiento', 'created_at']);
        });

        // 2. Copiar datos existentes
        DB::statement('INSERT INTO movimientos_inventario_temp SELECT * FROM movimientos_inventario');

        // 3. Eliminar la tabla original
        Schema::dropIfExists('movimientos_inventario');

        // 4. Renombrar la tabla temporal
        Schema::rename('movimientos_inventario_temp', 'movimientos_inventario');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a los tipos originales
        Schema::create('movimientos_inventario_temp', function (Blueprint $table) {
            $table->id('movimiento_id');
            $table->unsignedBigInteger('producto_id');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste', 'devolucion']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->text('motivo');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->string('referencia')->nullable();
            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('producto_id')->references('producto_id')->on('productos')->onDelete('cascade');
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('set null');
            $table->foreign('pedido_id')->references('pedido_id')->on('pedidos')->onDelete('set null');
            
            $table->index(['producto_id', 'created_at']);
            $table->index(['tipo_movimiento', 'created_at']);
        });

        // Copiar solo los registros con tipos válidos
        DB::statement("INSERT INTO movimientos_inventario_temp SELECT * FROM movimientos_inventario WHERE tipo_movimiento IN ('entrada', 'salida', 'ajuste', 'devolucion')");

        Schema::dropIfExists('movimientos_inventario');
        Schema::rename('movimientos_inventario_temp', 'movimientos_inventario');
    }
};
