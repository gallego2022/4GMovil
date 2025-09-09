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
        Schema::create('localization_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('country_code', 2)->default('CO');
            $table->string('language_code', 5)->default('es');
            $table->string('currency_code', 3)->default('COP');
            $table->string('timezone')->default('America/Bogota');
            $table->string('date_format')->default('d/m/Y');
            $table->string('time_format')->default('H:i');
            $table->timestamps();
            
            $table->index(['user_id', 'language_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localization_configs');
    }
};
