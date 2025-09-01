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
        Schema::create('estados_pedido', function (Blueprint $table) {
            $table->id('estado_id');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('color', 7)->default('#3b82f6');
            $table->integer('orden')->default(1);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados_pedido');
    }
};
