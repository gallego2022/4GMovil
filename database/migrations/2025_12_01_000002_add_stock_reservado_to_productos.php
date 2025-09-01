<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Campos para stock reservado
            $table->integer('stock_reservado')->default(0)->after('stock');
            $table->integer('stock_disponible')->default(0)->after('stock_reservado');
            
            // Índices para optimizar consultas
            $table->index(['stock_disponible', 'activo']);
            $table->index(['stock_reservado', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex(['stock_disponible', 'activo']);
            $table->dropIndex(['stock_reservado', 'activo']);
            $table->dropColumn(['stock_reservado', 'stock_disponible']);
        });
    }
}; 