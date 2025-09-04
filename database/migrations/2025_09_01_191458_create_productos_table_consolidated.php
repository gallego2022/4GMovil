<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * MIGRACIÓN CONSOLIDADA FINAL - TABLA PRODUCTOS
     * 
     * Esta migración incluye todos los campos de productos que estaban
     * fragmentados en múltiples migraciones:
     * - Campos básicos (nombre, descripción, precio, stock)
     * - Sistema de stock con umbrales de alerta
     * - Estado del producto (nuevo/usado)
     * - Sistema de stock reservado
     * - Tabla de imágenes de productos
     * 
     * NOTA: Las claves foráneas se agregan en una migración separada
     * para evitar problemas de dependencias
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            // ===== CAMPOS BÁSICOS =====
            $table->id('producto_id');
            $table->string('nombre_producto', 255);
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0)->comment('Stock padre calculado automáticamente desde variantes');
            $table->integer('stock_inicial')->default(0)->comment('Stock inicial establecido por admin para calcular alertas');
            
            // ===== ESTADO DEL PRODUCTO =====
            $table->enum('estado', ['nuevo', 'usado'])->default('nuevo');
            $table->boolean('activo')->default(true);
            
            // ===== SISTEMA DE STOCK RESERVADO =====
            $table->integer('stock_reservado')->default(0);
            $table->integer('stock_disponible')->default(0);
            
            // ===== UMBRALES DE ALERTA DE STOCK =====
            $table->integer('stock_minimo')->nullable()->comment('Umbral crítico: 20% del stock inicial');
            $table->integer('stock_maximo')->nullable()->comment('Umbral bajo: 60% del stock inicial');
            
            // ===== CAMPOS ADICIONALES =====
            $table->string('sku')->nullable()->unique();
            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->decimal('peso', 8, 2)->nullable();
            $table->string('dimensiones')->nullable();
            $table->string('codigo_barras')->nullable();
            $table->text('notas_inventario')->nullable();
            $table->timestamp('ultima_actualizacion_stock')->nullable();
            
            // ===== RELACIONES (se agregarán después) =====
            $table->unsignedBigInteger('categoria_id')->nullable();  // Nullable temporalmente
            $table->unsignedBigInteger('marca_id')->nullable();      // Nullable temporalmente
            
            // ===== MEDIA =====
            $table->string('imagen_url')->nullable();
            
            // ===== TIMESTAMPS =====
            $table->timestamps();
            
            // ===== ÍNDICES OPTIMIZADOS =====
            $table->index(['stock_disponible', 'estado']);
            $table->index(['stock_reservado', 'estado']);
            $table->index(['stock_inicial', 'stock_disponible'], 'idx_alertas_stock_inicial');
            $table->index(['stock_minimo', 'stock_disponible'], 'idx_alertas_stock');
            $table->index(['stock_maximo', 'stock_disponible'], 'idx_umbrales_stock');
            $table->index('categoria_id');
            $table->index('marca_id');
            $table->index('precio');
            $table->index('activo');
            $table->index('sku');
            
            // ===== DOCUMENTACIÓN =====
            $table->comment('Tabla consolidada de productos - Sistema de e-commerce 4GMovil con umbrales de alerta');
        });

        // ===== TABLA DE IMÁGENES DE PRODUCTOS =====
        Schema::create('imagenes_productos', function (Blueprint $table) {
            $table->id('imagen_id');
            $table->unsignedBigInteger('producto_id');
            $table->string('ruta_imagen');
            $table->string('alt_text')->nullable();
            $table->string('titulo')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('principal')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices
            $table->index(['producto_id', 'activo']);
            $table->index(['producto_id', 'principal']);
            $table->index('orden');
            
            // Comentario
            $table->comment('Tabla de imágenes de productos - Sistema de e-commerce 4GMovil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes_productos');
        Schema::dropIfExists('productos');
    }
};
