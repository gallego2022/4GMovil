<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * MIGRACIÓN DE CLAVES FORÁNEAS - SISTEMA COMPLETO
     * 
     * Esta migración agrega todas las claves foráneas después de que
     * todas las tablas estén creadas, evitando problemas de dependencias.
     */
    public function up(): void
    {
        // ===== CLAVES FORÁNEAS PARA PRODUCTOS =====
        Schema::table('productos', function (Blueprint $table) {
            // Hacer las columnas NOT NULL
            $table->unsignedBigInteger('categoria_id')->nullable(false)->change();
            $table->unsignedBigInteger('marca_id')->nullable(false)->change();
            
            // Agregar claves foráneas solo si no existen
            if (!$this->foreignKeyExists('productos', 'categoria_id')) {
                $table->foreign('categoria_id')
                      ->references('categoria_id')
                      ->on('categorias')
                      ->onDelete('cascade');
            }
            
            if (!$this->foreignKeyExists('productos', 'marca_id')) {
                $table->foreign('marca_id')
                      ->references('marca_id')
                      ->on('marcas')
                      ->onDelete('cascade');
            }
        });
        
        // ===== CLAVES FORÁNEAS PARA SISTEMA DE INVENTARIO =====
        Schema::table('variantes_producto', function (Blueprint $table) {
            if (!$this->foreignKeyExists('variantes_producto', 'producto_id')) {
                $table->foreign('producto_id')
                      ->references('producto_id')
                      ->on('productos')
                      ->onDelete('cascade');
            }
        });
        
        Schema::table('imagenes_variantes', function (Blueprint $table) {
            if (!$this->foreignKeyExists('imagenes_variantes', 'variante_id')) {
                $table->foreign('variante_id')
                      ->references('variante_id')
                      ->on('variantes_producto')
                      ->onDelete('cascade');
            }
        });
        
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            if (!$this->foreignKeyExists('movimientos_inventario', 'producto_id')) {
                $table->foreign('producto_id')
                      ->references('producto_id')
                      ->on('productos')
                      ->onDelete('cascade');
            }
            
            if (!$this->foreignKeyExists('movimientos_inventario', 'usuario_id')) {
                $table->foreign('usuario_id')
                      ->references('usuario_id')
                      ->on('usuarios')
                      ->onDelete('set null');
            }
        });
        
        Schema::table('movimientos_inventario_variantes', function (Blueprint $table) {
            if (!$this->foreignKeyExists('movimientos_inventario_variantes', 'variante_id')) {
                $table->foreign('variante_id')
                      ->references('variante_id')
                      ->on('variantes_producto')
                      ->onDelete('cascade');
            }
            
            if (!$this->foreignKeyExists('movimientos_inventario_variantes', 'usuario_id')) {
                $table->foreign('usuario_id')
                      ->references('usuario_id')
                      ->on('usuarios')
                      ->onDelete('set null');
            }
        });
        
        Schema::table('reservas_stock_variantes', function (Blueprint $table) {
            if (!$this->foreignKeyExists('reservas_stock_variantes', 'variante_id')) {
                $table->foreign('variante_id')
                      ->references('variante_id')
                      ->on('variantes_producto')
                      ->onDelete('cascade');
            }
            
            if (!$this->foreignKeyExists('reservas_stock_variantes', 'usuario_id')) {
                $table->foreign('usuario_id')
                      ->references('usuario_id')
                      ->on('usuarios')
                      ->onDelete('cascade');
            }
        });
        
        Schema::table('especificaciones_categoria', function (Blueprint $table) {
            if (!$this->foreignKeyExists('especificaciones_categoria', 'categoria_id')) {
                $table->foreign('categoria_id')
                      ->references('categoria_id')
                      ->on('categorias')
                      ->onDelete('cascade');
            }
        });
        
        Schema::table('especificaciones_producto', function (Blueprint $table) {
            if (!$this->foreignKeyExists('especificaciones_producto', 'producto_id')) {
                $table->foreign('producto_id')
                      ->references('producto_id')
                      ->on('productos')
                      ->onDelete('cascade');
            }
            
            if (!$this->foreignKeyExists('especificaciones_producto', 'especificacion_id')) {
                $table->foreign('especificacion_id')
                      ->references('especificacion_id')
                      ->on('especificaciones_categoria')
                      ->onDelete('cascade');
            }
        });
        
        // ===== CLAVES FORÁNEAS PARA PEDIDOS =====
        Schema::table('pedidos', function (Blueprint $table) {
            if (!$this->foreignKeyExists('pedidos', 'usuario_id')) {
                $table->foreign('usuario_id')
                      ->references('usuario_id')
                      ->on('usuarios')
                      ->onDelete('cascade');
            }
            
            if (!$this->foreignKeyExists('pedidos', 'direccion_id')) {
                $table->foreign('direccion_id')
                      ->references('direccion_id')
                      ->on('direcciones')
                      ->onDelete('restrict');
            }
            
            if (!$this->foreignKeyExists('pedidos', 'estado_id')) {
                $table->foreign('estado_id')
                      ->references('estado_id')
                      ->on('estados_pedido')
                      ->onDelete('restrict');
            }
        });
        
        // ===== CLAVES FORÁNEAS PARA DETALLES DE PEDIDO =====
        Schema::table('detalles_pedido', function (Blueprint $table) {
            if (!$this->foreignKeyExists('detalles_pedido', 'pedido_id')) {
                $table->foreign('pedido_id')
                      ->references('pedido_id')
                      ->on('pedidos')
                      ->onDelete('cascade');
            }
            
            if (!$this->foreignKeyExists('detalles_pedido', 'producto_id')) {
                $table->foreign('producto_id')
                      ->references('producto_id')
                      ->on('productos')
                      ->onDelete('restrict');
            }
            
            if (!$this->foreignKeyExists('detalles_pedido', 'variante_id')) {
                $table->foreign('variante_id')
                      ->references('variante_id')
                      ->on('variantes_producto')
                      ->onDelete('restrict');
            }
        });
        
        // ===== CLAVES FORÁNEAS PARA RESEÑAS =====
        Schema::table('resenas', function (Blueprint $table) {
            if (!$this->foreignKeyExists('resenas', 'usuario_id')) {
                $table->foreign('usuario_id')
                      ->references('usuario_id')
                      ->on('usuarios')
                      ->onDelete('cascade');
            }
            
            if (!$this->foreignKeyExists('resenas', 'producto_id')) {
                $table->foreign('producto_id')
                      ->references('producto_id')
                      ->on('productos')
                      ->onDelete('cascade');
            }
            
            if (!$this->foreignKeyExists('resenas', 'pedido_id')) {
                $table->foreign('pedido_id')
                      ->references('pedido_id')
                      ->on('pedidos')
                      ->onDelete('set null');
            }
        });
        
        // ===== CLAVES FORÁNEAS PARA IMÁGENES DE PRODUCTOS =====
        Schema::table('imagenes_productos', function (Blueprint $table) {
            if (!$this->foreignKeyExists('imagenes_productos', 'producto_id')) {
                $table->foreign('producto_id')
                      ->references('producto_id')
                      ->on('productos')
                      ->onDelete('cascade');
            }
        });
        
        // ===== CLAVES FORÁNEAS PARA OTP =====
        Schema::table('otp_codes', function (Blueprint $table) {
            if (!$this->foreignKeyExists('otp_codes', 'usuario_id')) {
                $table->foreign('usuario_id')
                      ->references('usuario_id')
                      ->on('usuarios')
                      ->onDelete('cascade');
            }
        });
        
        // ===== CLAVES FORÁNEAS PARA DIRECCIONES =====
        Schema::table('direcciones', function (Blueprint $table) {
            if (!$this->foreignKeyExists('direcciones', 'usuario_id')) {
                $table->foreign('usuario_id')
                      ->references('usuario_id')
                      ->on('usuarios')
                      ->onDelete('cascade');
            }
        });
        
        // ===== CLAVES FORÁNEAS PARA SUSCRIPCIONES =====
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!$this->foreignKeyExists('subscriptions', 'usuario_id')) {
                $table->foreign('usuario_id')
                      ->references('usuario_id')
                      ->on('usuarios')
                      ->onDelete('cascade');
            }
        });
        
        // ===== CLAVES FORÁNEAS PARA ITEMS DE SUSCRIPCIÓN =====
        Schema::table('subscription_items', function (Blueprint $table) {
            if (!$this->foreignKeyExists('subscription_items', 'subscription_id')) {
                $table->foreign('subscription_id')
                      ->references('subscription_id')
                      ->on('subscriptions')
                      ->onDelete('cascade');
            }
        });
        
        // ===== CLAVES FORÁNEAS PARA PAGOS =====
        Schema::table('pagos', function (Blueprint $table) {
            if (!$this->foreignKeyExists('pagos', 'pedido_id')) {
                $table->foreign('pedido_id')
                      ->references('pedido_id')
                      ->on('pedidos')
                      ->onDelete('cascade');                               // Si se elimina el pedido, se elimina el pago
            }
            
            if (!$this->foreignKeyExists('pagos', 'metodo_id')) {
                $table->foreign('metodo_id')
                      ->references('metodo_id')
                      ->on('metodos_pago')
                      ->onDelete('restrict');                              // No permitir eliminar método de pago si hay pagos asociados
            }
        });
    }
    
    /**
     * Verifica si una clave foránea ya existe
     */
    private function foreignKeyExists($table, $column)
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = ? 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$table, $column]);
        
        return !empty($foreignKeys);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ===== ELIMINAR CLAVES FORÁNEAS EN ORDEN INVERSO =====
        
        // Subscription items
        Schema::table('subscription_items', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
        });
        
        // Subscriptions
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });
        
        // Pagos
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['pedido_id', 'metodo_id']);
        });
        
        // Direcciones
        Schema::table('direcciones', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });
        
        // OTP
        Schema::table('otp_codes', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
        });
        
        // Imágenes de productos
        Schema::table('imagenes_productos', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
        });
        
        // Reseñas
        Schema::table('resenas', function (Blueprint $table) {
            $table->dropForeign(['usuario_id', 'producto_id', 'pedido_id']);
        });
        
        // Detalles de pedido
        Schema::table('detalles_pedido', function (Blueprint $table) {
            $table->dropForeign(['pedido_id', 'producto_id', 'variante_id']);
        });
        
        // Pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id', 'direccion_id', 'estado_id']);
        });
        
        // Especificaciones de producto
        Schema::table('especificaciones_producto', function (Blueprint $table) {
            $table->dropForeign(['producto_id', 'especificacion_id']);
        });
        
        // Especificaciones de categoría
        Schema::table('especificaciones_categoria', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
        });
        
        // Reservas de stock
        Schema::table('reservas_stock_variantes', function (Blueprint $table) {
            $table->dropForeign(['variante_id', 'usuario_id']);
        });
        
        // Movimientos de inventario de variantes
        Schema::table('movimientos_inventario_variantes', function (Blueprint $table) {
            $table->dropForeign(['variante_id', 'usuario_id']);
        });
        
        // Movimientos de inventario
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropForeign(['producto_id', 'usuario_id']);
        });
        
        // Imágenes de variantes
        Schema::table('imagenes_variantes', function (Blueprint $table) {
            $table->dropForeign(['variante_id']);
        });
        
        // Variantes de producto
        Schema::table('variantes_producto', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
        });
        
        // Productos
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id', 'marca_id']);
        });
    }
};
