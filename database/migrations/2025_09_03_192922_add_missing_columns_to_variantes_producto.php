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
            // Agregar columnas faltantes para funcionalidad completa
            $table->string('codigo_variante')->nullable()->after('nombre');
            $table->integer('stock_disponible')->default(0)->after('stock');
            $table->boolean('activo')->default(true)->after('disponible');
            $table->decimal('peso', 8, 3)->nullable()->after('activo');
            $table->string('dimensiones')->nullable()->after('peso');
            $table->text('notas')->nullable()->after('dimensiones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variantes_producto', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_variante',
                'stock_disponible',
                'activo',
                'peso',
                'dimensiones',
                'notas'
            ]);
        });
    }
};
