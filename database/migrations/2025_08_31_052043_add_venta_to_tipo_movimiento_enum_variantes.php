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
        // Como MySQL no permite modificar ENUMs directamente, necesitamos recrear la tabla
        // 1. Crear tabla temporal con el nuevo ENUM
        Schema::create('movimientos_inventario_variantes_temp', function (Blueprint $table) {
            $table->id('movimiento_id');
            $table->unsignedBigInteger('variante_id');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'reserva', 'liberacion', 'venta']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->string('motivo');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('referencia')->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->timestamps();
            
            $table->foreign('variante_id')->references('variante_id')->on('variantes_producto')->onDelete('cascade');
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('set null');
            
            $table->index(['variante_id', 'fecha_movimiento'], 'idx_variante_fecha');
            $table->index('tipo_movimiento', 'idx_tipo_movimiento');
        });

        // 2. Copiar datos existentes
        DB::statement('INSERT INTO movimientos_inventario_variantes_temp SELECT * FROM movimientos_inventario_variantes');

        // 3. Eliminar tabla original
        Schema::dropIfExists('movimientos_inventario_variantes');

        // 4. Renombrar tabla temporal
        Schema::rename('movimientos_inventario_variantes_temp', 'movimientos_inventario_variantes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a los tipos originales
        Schema::create('movimientos_inventario_variantes_temp', function (Blueprint $table) {
            $table->id('movimiento_id');
            $table->unsignedBigInteger('variante_id');
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'reserva', 'liberacion']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->string('motivo');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('referencia')->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->timestamps();
            
            $table->foreign('variante_id')->references('variante_id')->on('variantes_producto')->onDelete('cascade');
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('set null');
            
            $table->index(['variante_id', 'fecha_movimiento'], 'idx_variante_fecha');
            $table->index('tipo_movimiento', 'idx_tipo_movimiento');
        });

        // Copiar solo los registros con tipos válidos
        DB::statement("INSERT INTO movimientos_inventario_variantes_temp SELECT * FROM movimientos_inventario_variantes WHERE tipo_movimiento IN ('entrada', 'salida', 'reserva', 'liberacion')");

        Schema::dropIfExists('movimientos_inventario_variantes');
        Schema::rename('movimientos_inventario_variantes_temp', 'movimientos_inventario_variantes');
    }
};
