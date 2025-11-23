<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Renombra las columnas de umbrales de stock para que tengan nombres más descriptivos:
     * - stock_minimo -> umbral_stock_critico (umbral crítico: 20% del stock inicial)
     * - stock_maximo -> umbral_stock_bajo (umbral bajo: 60% del stock inicial)
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->renameColumn('stock_minimo', 'umbral_stock_critico');
            $table->renameColumn('stock_maximo', 'umbral_stock_bajo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->renameColumn('umbral_stock_critico', 'stock_minimo');
            $table->renameColumn('umbral_stock_bajo', 'stock_maximo');
        });
    }
};
