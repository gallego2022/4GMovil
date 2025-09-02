<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * MIGRACIÓN CONSOLIDADA FINAL - SISTEMA STRIPE Y SUSCRIPCIONES
     * 
     * Esta migración incluye todas las tablas del sistema de Stripe
     * que estaban fragmentadas en múltiples migraciones:
     * - Campos de Stripe en usuarios
     * - Suscripciones
     * - Items de suscripción
     * - Webhook events
     */
    public function up(): void
    {
        // ===== TABLA DE SUSCRIPCIONES =====
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id('subscription_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('name');
            $table->string('stripe_id')->unique();
            $table->string('stripe_status');
            $table->string('stripe_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            
            $table->foreign('usuario_id')
                  ->references('usuario_id')
                  ->on('usuarios')
                  ->onDelete('cascade');
                  
            $table->index(['usuario_id', 'stripe_status']);
            $table->index('stripe_id');
        });
        
        // ===== TABLA DE ITEMS DE SUSCRIPCIÓN =====
        Schema::create('subscription_items', function (Blueprint $table) {
            $table->id('subscription_item_id');
            $table->unsignedBigInteger('subscription_id');
            $table->string('stripe_id')->unique();
            $table->string('stripe_product')->nullable();
            $table->string('stripe_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
            
            $table->foreign('subscription_id')
                  ->references('subscription_id')
                  ->on('subscriptions')
                  ->onDelete('cascade');
                  
            $table->index('subscription_id');
            $table->index('stripe_id');
        });
        
        // ===== TABLA DE CUSTOMER COLUMNS (ya incluida en usuarios) =====
        // Los campos de Stripe ya están incluidos en la migración consolidada de usuarios
        // stripe_id, pm_type, pm_last_four, trial_ends_at
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('subscriptions');
    }
};
