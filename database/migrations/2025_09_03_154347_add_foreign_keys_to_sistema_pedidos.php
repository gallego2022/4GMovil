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
        // ===== CLAVES FORÁNEAS PARA DIRECCIONES =====
        Schema::table('direcciones', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('cascade');
        });
        
        // ===== CLAVES FORÁNEAS PARA PEDIDOS =====
        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('cascade');
            $table->foreign('direccion_id')->references('direccion_id')->on('direcciones')->onDelete('restrict');
            $table->foreign('estado_id')->references('estado_id')->on('estados_pedido')->onDelete('restrict');
        });
        
        // ===== CLAVES FORÁNEAS PARA DETALLES DE PEDIDO =====
        Schema::table('detalles_pedido', function (Blueprint $table) {
            $table->foreign('pedido_id')->references('pedido_id')->on('pedidos')->onDelete('cascade');
            $table->foreign('producto_id')->references('producto_id')->on('productos')->onDelete('restrict');
            $table->foreign('variante_id')->references('variante_id')->on('variantes_producto')->onDelete('restrict');
        });
        
        // ===== CLAVES FORÁNEAS PARA RESEÑAS =====
        Schema::table('resenas', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('cascade');
            $table->foreign('producto_id')->references('producto_id')->on('productos')->onDelete('cascade');
            $table->foreign('pedido_id')->references('pedido_id')->on('pedidos')->onDelete('set null');
        });
        
        // ===== CLAVES FORÁNEAS PARA PAGOS =====
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreign('pedido_id')->references('pedido_id')->on('pedidos')->onDelete('cascade');
            $table->foreign('metodo_id')->references('metodo_id')->on('metodos_pago')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ===== ELIMINAR CLAVES FORÁNEAS DE PAGOS =====
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['pedido_id']);
            $table->dropForeign(['metodo_id']);
        });
        
        // ===== ELIMINAR CLAVES FORÁNEAS DE RESEÑAS =====
        Schema::table('resenas', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['producto_id']);
            $table->dropForeign(['pedido_id']);
        });
        
        // ===== ELIMINAR CLAVES FORÁNEAS DE DETALLES DE PEDIDO =====
        Schema::table('detalles_pedido', function (Blueprint $table) {
            $table->dropForeign(['pedido_id']);
            $table->dropForeign(['producto_id']);
            $table->dropForeign(['variante_id']);
        });
        
        // ===== ELIMINAR CLAVES FORÁNEAS DE PEDIDOS =====
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['direccion_id']);
            $table->dropForeign(['estado_id']);
        });
        
        // ===== ELIMINAR CLAVES FORÁNEAS DE DIRECCIONES =====
        Schema::table('direcciones', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });
    }
};
