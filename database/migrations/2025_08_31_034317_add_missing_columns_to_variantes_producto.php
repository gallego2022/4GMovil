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
        Schema::table('variantes_producto', function (Blueprint $table) {
            // Verificar si las columnas existen antes de agregarlas
            if (!Schema::hasColumn('variantes_producto', 'stock_minimo')) {
                $table->integer('stock_minimo')->default(5)->after('stock');
            }
            
            if (!Schema::hasColumn('variantes_producto', 'stock_maximo')) {
                $table->integer('stock_maximo')->default(100)->after('stock_minimo');
            }
            
            // Renombrar columna stock a stock_disponible si existe
            if (Schema::hasColumn('variantes_producto', 'stock') && !Schema::hasColumn('variantes_producto', 'stock_disponible')) {
                $table->renameColumn('stock', 'stock_disponible');
            }
        });

        // Crear tabla para movimientos de inventario de variantes
        if (!Schema::hasTable('movimientos_inventario_variantes')) {
            Schema::create('movimientos_inventario_variantes', function (Blueprint $table) {
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
                
                $table->foreign('variante_id')->references('variante_id')->on('variantes_producto')->onDelete('cascade');
                $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');
                
                $table->index(['variante_id', 'fecha_movimiento'], 'idx_variante_fecha');
                $table->index('tipo_movimiento', 'idx_tipo_movimiento');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar tabla de movimientos de inventario de variantes
        Schema::dropIfExists('movimientos_inventario_variantes');

        // Revertir cambios en tabla variantes_producto
        Schema::table('variantes_producto', function (Blueprint $table) {
            $table->dropColumn(['stock_minimo', 'stock_maximo']);
            
            // Revertir nombre de columna si es necesario
            if (Schema::hasColumn('variantes_producto', 'stock_disponible') && !Schema::hasColumn('variantes_producto', 'stock')) {
                $table->renameColumn('stock_disponible', 'stock');
            }
        });
    }
};
