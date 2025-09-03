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
            $table->integer('stock');
            
            // ===== ESTADO DEL PRODUCTO =====
            $table->enum('estado', ['nuevo', 'usado'])->default('nuevo');
            $table->boolean('activo')->default(true);
            
            // ===== SISTEMA DE STOCK RESERVADO =====
            $table->integer('stock_reservado')->default(0);
            $table->integer('stock_disponible')->default(0);
            
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
            $table->index('categoria_id');
            $table->index('marca_id');
            $table->index('precio');
            $table->index('activo');
            
            // ===== DOCUMENTACIÓN =====
            $table->comment('Tabla consolidada de productos - Sistema de e-commerce 4GMovil');
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
