<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Campos para control de inventario
            $table->integer('stock_minimo')->default(0)->after('stock');
            $table->integer('stock_maximo')->nullable()->after('stock_minimo');
            $table->string('codigo_barras', 50)->nullable()->after('stock_maximo');
            $table->string('sku', 50)->unique()->nullable()->after('codigo_barras');
            $table->decimal('costo_unitario', 10, 2)->default(0)->after('precio');
            $table->decimal('peso', 8, 2)->nullable()->after('costo_unitario');
            $table->string('dimensiones')->nullable()->after('peso'); // Largo x Ancho x Alto
            $table->boolean('activo')->default(true)->after('dimensiones');
            $table->timestamp('ultima_actualizacion_stock')->nullable()->after('activo');
            $table->text('notas_inventario')->nullable()->after('ultima_actualizacion_stock');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'stock_minimo',
                'stock_maximo', 
                'codigo_barras',
                'sku',
                'costo_unitario',
                'peso',
                'dimensiones',
                'activo',
                'ultima_actualizacion_stock',
                'notas_inventario'
            ]);
        });
    }
}; 