<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Crea la tabla de configuraciones de localización para usuarios.
     * Almacena preferencias de idioma, moneda, país y formato de fecha/hora.
     */
    public function up(): void
    {
        Schema::create('localization_configs', function (Blueprint $table) {
            // Clave primaria
            $table->id();
            
            // Referencia al usuario (clave foránea)
            $table->unsignedBigInteger('usuario_id');
            
            // Configuración de localización
            $table->string('country_code', 2)->default('CO')->comment('Código del país (CO, US, BR, ES)');
            $table->string('language_code', 5)->default('es')->comment('Código del idioma (es, en, pt)');
            $table->string('currency_code', 3)->default('COP')->comment('Código de la moneda (COP, USD, BRL, EUR)');
            $table->string('timezone')->default('America/Bogota')->comment('Zona horaria del usuario');
            $table->string('date_format')->default('d/m/Y')->comment('Formato de fecha preferido');
            $table->string('time_format')->default('H:i')->comment('Formato de hora preferido');
            
            // Timestamps
            $table->timestamps();
            
            // Clave foránea hacia la tabla usuarios
            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('cascade');
            
            // Índice único para evitar múltiples configuraciones por usuario
            $table->unique('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Elimina la tabla de configuraciones de localización.
     * Se eliminarán automáticamente las claves foráneas y restricciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('localization_configs');
    }
};
