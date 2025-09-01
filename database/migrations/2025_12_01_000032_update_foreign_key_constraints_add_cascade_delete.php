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
        // Actualizar restricciones de clave foránea para detalles_pedido
        Schema::table('detalles_pedido', function (Blueprint $table) {
            // Verificar si la restricción existe antes de eliminarla
            $foreignKeys = $this->getForeignKeys('detalles_pedido');
            if (in_array('detalles_pedido_producto_id_foreign', $foreignKeys)) {
                $table->dropForeign(['producto_id']);
            }
            
            // Agregar la nueva restricción con eliminación en cascada
            $table->foreign('producto_id')
                  ->references('producto_id')
                  ->on('productos')
                  ->onDelete('cascade');
        });

        // Actualizar restricciones de clave foránea para imagenes_productos
        Schema::table('imagenes_productos', function (Blueprint $table) {
            // Verificar si la restricción existe antes de eliminarla
            $foreignKeys = $this->getForeignKeys('imagenes_productos');
            if (in_array('imagenes_productos_producto_id_foreign', $foreignKeys)) {
                $table->dropForeign(['producto_id']);
            }
            
            // Agregar la nueva restricción con eliminación en cascada
            $table->foreign('producto_id')
                  ->references('producto_id')
                  ->on('productos')
                  ->onDelete('cascade');
        });

        // Actualizar restricciones de clave foránea para resenas
        Schema::table('resenas', function (Blueprint $table) {
            // Verificar si la restricción existe antes de eliminarla
            $foreignKeys = $this->getForeignKeys('resenas');
            if (in_array('resenas_producto_id_foreign', $foreignKeys)) {
                $table->dropForeign(['producto_id']);
            }
            
            // Agregar la nueva restricción con eliminación en cascada
            $table->foreign('producto_id')
                  ->references('producto_id')
                  ->on('productos')
                  ->onDelete('cascade');
        });

        // Actualizar restricciones de clave foránea para movimientos_inventario
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            // Verificar si la restricción existe antes de eliminarla
            $foreignKeys = $this->getForeignKeys('movimientos_inventario');
            if (in_array('movimientos_inventario_producto_id_foreign', $foreignKeys)) {
                $table->dropForeign(['producto_id']);
            }
            
            // Agregar la nueva restricción con eliminación en cascada
            $table->foreign('producto_id')
                  ->references('producto_id')
                  ->on('productos')
                  ->onDelete('cascade');
        });
    }

    /**
     * Obtener las claves foráneas de una tabla
     */
    private function getForeignKeys($tableName)
    {
        $foreignKeys = [];
        $constraints = \DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$tableName]);
        
        foreach ($constraints as $constraint) {
            $foreignKeys[] = $constraint->CONSTRAINT_NAME;
        }
        
        return $foreignKeys;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir restricciones de clave foránea para detalles_pedido
        Schema::table('detalles_pedido', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            
            $table->foreign('producto_id')
                  ->references('producto_id')
                  ->on('productos');
        });

        // Revertir restricciones de clave foránea para imagenes_productos
        Schema::table('imagenes_productos', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            
            $table->foreign('producto_id')
                  ->references('producto_id')
                  ->on('productos');
        });

        // Revertir restricciones de clave foránea para resenas
        Schema::table('resenas', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            
            $table->foreign('producto_id')
                  ->references('producto_id')
                  ->on('productos');
        });

        // Revertir restricciones de clave foránea para movimientos_inventario
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            
            $table->foreign('producto_id')
                  ->references('producto_id')
                  ->on('productos');
        });
    }
}; 