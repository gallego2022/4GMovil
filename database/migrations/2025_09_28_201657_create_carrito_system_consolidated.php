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
        // ===== CREAR TABLA CARRITOS =====
        if (!Schema::hasTable('carritos')) {
            Schema::create('carritos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('cascade');
                $table->timestamps();
                
                // Índices
                $table->index('usuario_id');
            });
        }

        // ===== CREAR TABLA CARRITO_ITEMS =====
        if (!Schema::hasTable('carrito_items')) {
            Schema::create('carrito_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('carrito_id')->constrained('carritos')->onDelete('cascade');
                $table->foreignId('producto_id')->references('producto_id')->on('productos')->onDelete('cascade');
                $table->foreignId('variante_id')->nullable()->references('variante_id')->on('variantes_producto')->onDelete('cascade');
                $table->integer('cantidad')->default(1);
                $table->decimal('precio_unitario', 10, 2)->nullable();
                $table->timestamps();
                
                // Índices
                $table->index('carrito_id');
                $table->index('producto_id');
                $table->index('variante_id');
                
                // Índice único para evitar duplicados
                $table->unique(['carrito_id', 'producto_id', 'variante_id'], 'unique_carrito_item');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ===== ELIMINAR TABLAS EN ORDEN INVERSO =====
        Schema::dropIfExists('carrito_items');
        Schema::dropIfExists('carritos');
    }
};
