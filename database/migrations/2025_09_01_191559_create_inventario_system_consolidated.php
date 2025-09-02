<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * MIGRACIÓN CONSOLIDADA FINAL - SISTEMA DE INVENTARIO
     * 
     * Esta migración incluye todas las tablas del sistema de inventario
     * que estaban fragmentadas en múltiples migraciones:
     * - Movimientos de inventario (productos y variantes)
     * - Reservas de stock
     * - Variantes de productos
     * - Imágenes de variantes
     * - Especificaciones de productos
     * 
     * NOTA: Las claves foráneas se agregan en una migración separada
     * para evitar problemas de dependencias
     */
    public function up(): void
    {
        // ===== TABLA DE VARIANTES DE PRODUCTOS =====
        Schema::create('variantes_producto', function (Blueprint $table) {
            $table->id('variante_id');
            $table->unsignedBigInteger('producto_id');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio_adicional', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('stock_reservado')->default(0);
            $table->boolean('disponible')->default(true);
            $table->string('sku')->nullable()->unique();
            $table->string('referencia')->nullable();
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['producto_id', 'disponible']);
            $table->index('stock');
            $table->index('sku');
        });
        
        // ===== TABLA DE IMÁGENES DE VARIANTES =====
        Schema::create('imagenes_variantes', function (Blueprint $table) {
            $table->id('imagen_id');
            $table->unsignedBigInteger('variante_id');
            $table->string('url_imagen');
            $table->string('alt_text')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('principal')->default(false);
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['variante_id', 'orden']);
            $table->index('principal');
        });
        
        // ===== TABLA DE MOVIMIENTOS DE INVENTARIO =====
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id('movimiento_id');
            $table->unsignedBigInteger('producto_id');
            $table->enum('tipo', [
                'entrada', 'salida', 'ajuste', 'reserva', 'liberacion_reserva',
                'transferencia', 'devolucion', 'merma', 'inventario_fisico'
            ]);
            $table->integer('cantidad');
            $table->string('motivo');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('referencia')->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['producto_id', 'tipo']);
            $table->index('fecha_movimiento');
            $table->index('usuario_id');
        });
        
        // ===== TABLA DE MOVIMIENTOS DE INVENTARIO DE VARIANTES =====
        Schema::create('movimientos_inventario_variantes', function (Blueprint $table) {
            $table->id('movimiento_id');
            $table->unsignedBigInteger('variante_id');
            $table->enum('tipo', [
                'entrada', 'salida', 'ajuste', 'reserva', 'liberacion_reserva',
                'transferencia', 'devolucion', 'merma', 'inventario_fisico', 'venta'
            ]);
            $table->integer('cantidad');
            $table->string('motivo');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('referencia')->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['variante_id', 'tipo']);
            $table->index('fecha_movimiento');
            $table->index('usuario_id');
        });
        
        // ===== TABLA DE RESERVAS DE STOCK DE VARIANTES =====
        Schema::create('reservas_stock_variantes', function (Blueprint $table) {
            $table->id('reserva_id');
            $table->unsignedBigInteger('variante_id');
            $table->unsignedBigInteger('usuario_id');
            $table->integer('cantidad');
            $table->string('motivo');
            $table->timestamp('fecha_expiracion');
            $table->enum('estado', ['activa', 'confirmada', 'expirada', 'cancelada'])->default('activa');
            $table->string('referencia_pedido', 50)->nullable();
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['variante_id', 'estado']);
            $table->index(['usuario_id', 'estado']);
            $table->index('fecha_expiracion');
            $table->index('referencia_pedido');
        });
        
        // ===== TABLA DE ESPECIFICACIONES DE CATEGORÍA =====
        Schema::create('especificaciones_categoria', function (Blueprint $table) {
            $table->id('especificacion_id');
            $table->unsignedBigInteger('categoria_id');
            $table->string('nombre_campo');
            $table->string('etiqueta');
            $table->enum('tipo_campo', ['texto', 'numero', 'select', 'checkbox', 'radio']);
            $table->text('opciones')->nullable();
            $table->string('unidad')->nullable();
            $table->boolean('requerido')->default(false);
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['categoria_id', 'activo']);
            $table->index('orden');
        });
        
        // ===== TABLA DE ESPECIFICACIONES DE PRODUCTO =====
        Schema::create('especificaciones_producto', function (Blueprint $table) {
            $table->id('especificacion_producto_id');
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('especificacion_id');
            $table->text('valor');
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->unique(['producto_id', 'especificacion_id']);
            $table->index('producto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especificaciones_producto');
        Schema::dropIfExists('especificaciones_categoria');
        Schema::dropIfExists('reservas_stock_variantes');
        Schema::dropIfExists('movimientos_inventario_variantes');
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('imagenes_variantes');
        Schema::dropIfExists('variantes_producto');
    }
};
