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
        Schema::create('variantes_producto', function (Blueprint $table) {
            $table->id('variante_id');
            $table->unsignedBigInteger('producto_id');
            $table->string('nombre', 100); // Ej: "Negro", "Blanco", "Azul"
            $table->string('codigo_color', 7)->nullable(); // Código hexadecimal del color
            $table->integer('stock')->default(0);
            $table->boolean('disponible')->default(true);
            $table->decimal('precio_adicional', 10, 2)->default(0); // Precio adicional por esta variante
            $table->text('descripcion')->nullable(); // Descripción específica del color
            $table->integer('orden')->default(0); // Para ordenar las variantes
            $table->timestamps();
            
            $table->foreign('producto_id')->references('producto_id')->on('productos')->onDelete('cascade');
            $table->index(['producto_id', 'disponible']);
            $table->index(['producto_id', 'stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variantes_producto');
    }
};
