<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * MIGRACIÓN CONSOLIDADA FINAL - SISTEMA DE PEDIDOS
     * 
     * Esta migración incluye todas las tablas del sistema de pedidos
     * que estaban fragmentadas en múltiples migraciones:
     * - Estados de pedido
     - Pedidos principales
     * - Detalles de pedidos
     * - Direcciones de envío
     * - Métodos de pago
     * - Pagos
     * - Reseñas
     */
    public function up(): void
    {
        // ===== TABLA DE CATEGORÍAS =====
        Schema::create('categorias', function (Blueprint $table) {
            $table->id('categoria_id');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('imagen_url')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
            
            $table->index('activo');
            $table->index('orden');
        });
        
        // ===== TABLA DE MARCAS =====
        Schema::create('marcas', function (Blueprint $table) {
            $table->id('marca_id');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('logo_url')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index('activo');
        });
        
        // ===== TABLA DE ESTADOS DE PEDIDO =====
        Schema::create('estados_pedido', function (Blueprint $table) {
            $table->id('estado_id');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('color')->default('#6B7280');
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
            
            $table->index(['activo', 'orden']);
        });
        
        // ===== TABLA DE MÉTODOS DE PAGO =====
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id('metodo_id');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('icono')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
            
            $table->index(['activo', 'orden']);
        });
        
        // ===== TABLA DE DIRECCIONES =====
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id('direccion_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('nombre_destinatario');
            $table->string('telefono');
            $table->string('calle');
            $table->string('numero');
            $table->string('piso')->nullable();
            $table->string('departamento')->nullable();
            $table->string('codigo_postal');
            $table->string('ciudad');
            $table->string('provincia');
            $table->string('pais')->default('Argentina');
            $table->text('referencias')->nullable();
            $table->boolean('predeterminada')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['usuario_id', 'activo']);
            $table->index('predeterminada');
        });
        
        // ===== TABLA DE PEDIDOS =====
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id('pedido_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('direccion_id');
            $table->unsignedBigInteger('estado_id');
            $table->datetime('fecha_pedido');
            $table->decimal('total', 10, 2);
            $table->text('notas')->nullable();
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['usuario_id', 'estado_id']);
            $table->index('fecha_pedido');
            $table->index('estado_id');
        });
        
        // ===== TABLA DE DETALLES DE PEDIDO =====
        Schema::create('detalles_pedido', function (Blueprint $table) {
            $table->id('detalle_id');
            $table->unsignedBigInteger('pedido_id');
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('variante_id')->nullable();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['pedido_id', 'producto_id']);
            $table->index('variante_id');
        });
        
        // ===== TABLA DE RESEÑAS =====
        Schema::create('resenas', function (Blueprint $table) {
            $table->id('resena_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->integer('calificacion');
            $table->text('comentario')->nullable();
            $table->boolean('verificada')->default(false);
            $table->boolean('activa')->default(true);
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->unique(['usuario_id', 'producto_id']);
            $table->index(['producto_id', 'activa']);
            $table->index('calificacion');
        });
        
        // ===== TABLA DE IMÁGENES DE PRODUCTOS =====
        Schema::create('imagenes_productos', function (Blueprint $table) {
            $table->id('imagen_id');
            $table->unsignedBigInteger('producto_id');
            $table->string('url_imagen');
            $table->string('alt_text')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('principal')->default(false);
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['producto_id', 'orden']);
            $table->index('principal');
        });
        
        // ===== TABLA DE OTP (ONE-TIME PASSWORD) =====
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id('otp_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('codigo', 6);
            $table->string('tipo'); // 'email_verification', 'password_reset', 'login'
            $table->timestamp('fecha_expiracion');
            $table->boolean('usado')->default(false);
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['usuario_id', 'tipo']);
            $table->index('fecha_expiracion');
            $table->index('usado');
        });
        
        // ===== TABLA DE WEBHOOK EVENTS =====
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('provider'); // 'stripe', 'paypal', etc.
            $table->string('event_type');
            $table->text('payload');
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            // Índices (las claves foráneas se agregan después)
            $table->index(['provider', 'event_type']);
            $table->index('status');
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('otp_codes');
        Schema::dropIfExists('imagenes_productos');
        Schema::dropIfExists('resenas');
        Schema::dropIfExists('detalles_pedido');
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('direcciones');
        Schema::dropIfExists('metodos_pago');
        Schema::dropIfExists('estados_pedido');
        Schema::dropIfExists('marcas');
        Schema::dropIfExists('categorias');
    }
};
