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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('usuario_id');
            $table->string('nombre_usuario', 255);
            $table->string('correo_electronico')->unique();
            $table->string('contrasena');
            $table->string('telefono', 20);
            $table->string('foto_perfil')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
