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
        Schema::create('productos', function (Blueprint $table) {
            $table->id('producto_id');
            $table->string('nombre_producto', 255);
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            $table->integer('stock');
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('marca_id');
            $table->string('imagen_url')->nullable();
            $table->timestamps();
            $table->foreign('categoria_id')->references('categoria_id')->on('categorias')->onDelete('cascade');
            $table->foreign('marca_id')->references('marca_id')->on('marcas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
