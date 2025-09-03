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
        // ===== TABLA DE ESTADOS DE PEDIDO =====
        Schema::create('estados_pedido', function (Blueprint $table) {
            $table->id('estado_id');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('color')->default('#6B7280');
            $table->boolean('estado')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
            
            $table->index(['estado', 'orden']);
        });
        
        // ===== TABLA DE MÉTODOS DE PAGO =====
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id('metodo_id');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('icono')->nullable();
            $table->string('configuracion')->nullable();
            $table->boolean('estado')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
            
            $table->index(['estado', 'orden']);
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
            
            $table->unique(['usuario_id', 'producto_id']);
            $table->index(['producto_id', 'activa']);
            $table->index('calificacion');
        });
        
        // ===== TABLA DE PAGOS =====
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('pago_id');
            $table->unsignedBigInteger('pedido_id');
            $table->unsignedBigInteger('metodo_id');
            $table->string('referencia_externa')->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('estado'); // 'pendiente', 'completado', 'fallido', 'reembolsado'
            $table->text('detalles')->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->timestamps();
            
            $table->index(['pedido_id', 'estado']);
            $table->index('metodo_id');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('resenas');
        Schema::dropIfExists('detalles_pedido');
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('metodos_pago');
        Schema::dropIfExists('estados_pedido');
    }
};
