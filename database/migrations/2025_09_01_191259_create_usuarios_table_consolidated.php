<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * MIGRACIÓN CONSOLIDADA FINAL - SISTEMA DE USUARIOS COMPLETO
     * 
     * Esta migración incluye:
     * - Tabla usuarios (compatible con Laravel)
     * - Tabla password_reset_tokens (Laravel por defecto)
     * - Tabla sessions (Laravel por defecto)
     * - Tabla otp_codes (códigos de verificación)
     * - Todos los campos necesarios para OAuth y Stripe
     */
    public function up(): void
    {
        // ===== TABLA PRINCIPAL DE USUARIOS =====
        Schema::create('usuarios', function (Blueprint $table) {
            // ===== CAMPOS BÁSICOS =====
            $table->id('usuario_id');
            $table->string('nombre_usuario', 255);
            $table->string('correo_electronico')->unique();
            $table->string('contrasena')->nullable();                  // Nullable para OAuth
            $table->string('telefono', 20)->nullable();               // Nullable para flexibilidad
            $table->string('foto_perfil')->nullable();
            
            // ===== CAMPOS DE ESTADO Y ROL =====
            $table->boolean('estado')->default(true);                  // Usuario activo/inactivo
            $table->string('rol')->default('cliente');                 // Rol del usuario
            
            // ===== VERIFICACIÓN DE EMAIL =====
            $table->timestamp('email_verified_at')->nullable();        // Fecha de verificación de email
            $table->timestamp('fecha_registro')->useCurrent();         // Fecha de registro original
            
            // ===== INTEGRACIÓN GOOGLE OAUTH =====
            $table->string('google_id')->nullable();                   // ID de Google para OAuth
            $table->index('google_id');                                // Índice para búsquedas OAuth
            
            // ===== INTEGRACIÓN STRIPE =====
            $table->string('stripe_id')->nullable()->index();          // ID de cliente en Stripe
            $table->string('pm_type')->nullable();                     // Tipo de método de pago
            $table->string('pm_last_four', 4)->nullable();            // Últimos 4 dígitos de la tarjeta
            $table->timestamp('trial_ends_at')->nullable();            // Fin del período de prueba
            
            // ===== AUTENTICACIÓN =====
            $table->rememberToken();                                   // Token para "recordarme"
            
            // ===== TIMESTAMPS =====
            $table->timestamps();                                      // created_at y updated_at
            
            // ===== ÍNDICES ADICIONALES =====
            $table->index(['estado', 'rol']);
            $table->index('correo_electronico');
            $table->index('fecha_registro');
        });
        
        // ===== TABLA DE PASSWORD RESET TOKENS (Laravel por defecto) =====
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        
        // ===== TABLA DE SESSIONS (Laravel por defecto) =====
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();  // Laravel espera user_id
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
            
            // Clave foránea a la tabla usuarios (usando user_id para compatibilidad con Laravel)
            $table->foreign('user_id')
                  ->references('usuario_id')
                  ->on('usuarios')
                  ->onDelete('cascade');
        });

        // ===== TABLA DE CÓDIGOS OTP =====
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id('otp_id');
            $table->unsignedBigInteger('usuario_id');
            $table->string('codigo', 10);
            $table->string('tipo', 50); // 'verificacion', 'reset_password', 'login', etc.
            $table->timestamp('fecha_expiracion');
            $table->boolean('usado')->default(false);
            $table->timestamps();
            
            // Índices
            $table->index(['usuario_id', 'tipo']);
            $table->index(['codigo', 'tipo']);
            $table->index('fecha_expiracion');
            $table->index('usado');
            
            // Clave foránea
            $table->foreign('usuario_id')
                  ->references('usuario_id')
                  ->on('usuarios')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
    }
};
