<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * MIGRACIÓN CONSOLIDADA - TABLA ESPECIFICACIONES_CATEGORIA
     * 
     * Esta migración incluye la estructura completa de la tabla
     * especificaciones_categoria con todos los campos necesarios:
     * - Campos básicos (nombre_campo, etiqueta, tipo_campo)
     * - Configuración de opciones y unidades
     * - Sistema de orden y estado
     * - Campo descripción agregado
     * 
     * NOTA: Esta migración reemplaza la estructura anterior
     * y agrega la columna descripcion que faltaba
     */
    public function up(): void
    {
        // Verificar si la tabla ya existe
        if (Schema::hasTable('especificaciones_categoria')) {
            // Si existe, solo agregar la columna descripcion si no existe
            if (!Schema::hasColumn('especificaciones_categoria', 'descripcion')) {
                Schema::table('especificaciones_categoria', function (Blueprint $table) {
                    $table->text('descripcion')->nullable()->after('unidad');
                });
            }
        } else {
            // Si no existe, crear la tabla completa
            Schema::create('especificaciones_categoria', function (Blueprint $table) {
                $table->id('especificacion_id');
                $table->unsignedBigInteger('categoria_id');
                $table->string('nombre_campo', 100);
                $table->string('etiqueta', 100);
                $table->enum('tipo_campo', [
                    'texto', 'numero', 'select', 'checkbox', 'radio'
                ]);
                $table->text('opciones')->nullable();
                $table->string('unidad', 50)->nullable();
                $table->text('descripcion')->nullable();
                $table->boolean('requerido')->default(false);
                $table->integer('orden')->default(0);
                $table->boolean('estado')->default(true);
                $table->timestamps();
                
                // Índices
                $table->index(['categoria_id', 'estado']);
                $table->index('orden');
                $table->index('nombre_campo');
                
                // Comentario
                $table->comment('Tabla de especificaciones por categoría - Sistema de e-commerce 4GMovil');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Solo eliminar la columna descripcion si existe
        if (Schema::hasColumn('especificaciones_categoria', 'descripcion')) {
            Schema::table('especificaciones_categoria', function (Blueprint $table) {
                $table->dropColumn('descripcion');
            });
        }
    }
};
