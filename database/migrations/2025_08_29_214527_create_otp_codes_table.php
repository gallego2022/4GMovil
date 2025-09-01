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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id');
            $table->string('codigo', 6); // Código OTP de 6 dígitos
            $table->string('tipo'); // 'email_verification', 'password_reset', etc.
            $table->timestamp('expires_at');
            $table->boolean('usado')->default(false);
            $table->timestamps();

            $table->foreign('usuario_id')->references('usuario_id')->on('usuarios')->onDelete('cascade');
            $table->index(['usuario_id', 'tipo', 'usado']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
