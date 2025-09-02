<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * MIGRACIÓN CONSOLIDADA FINAL - TABLA PAGOS
     * 
     * Esta migración incluye:
     * - Todos los campos necesarios para el sistema de pagos
     * - Estructura completa en un solo archivo
     * - NOTA: Las claves foráneas se agregan en una migración separada
     */
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            // ===== CAMPOS PRINCIPALES =====
            $table->id('pago_id');                                    // ID único del pago
            $table->unsignedBigInteger('pedido_id');                   // ID del pedido asociado
            $table->decimal('monto', 10, 2);                          // Monto del pago (10 dígitos, 2 decimales)
            $table->unsignedBigInteger('metodo_id');                   // ID del método de pago
            $table->datetime('fecha_pago');                           // Fecha y hora del pago
            $table->string('estado')->default('pendiente');            // Estado del pago (pendiente, procesando, completado, cancelado)
            $table->string('referencia')->nullable();                  // Referencia opcional (ID de Stripe, número de factura, etc.)
            
            // ===== TIMESTAMPS =====
            $table->timestamps();                                      // created_at y updated_at
            
            // ===== ÍNDICES OPTIMIZADOS =====
            $table->index(['pedido_id', 'estado']);                    // Consultas por pedido y estado
            $table->index('fecha_pago');                               // Consultas por fecha
            $table->index('estado');                                   // Consultas por estado
            $table->index('metodo_id');                                // Consultas por método de pago
            
            // ===== DOCUMENTACIÓN =====
            $table->comment('Tabla consolidada de pagos - Sistema de e-commerce 4GMovil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
