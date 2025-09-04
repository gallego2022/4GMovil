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
        Schema::table('webhook_events', function (Blueprint $table) {
            // Agregar foreign key para pedido_id
            $table->foreign('pedido_id')
                  ->references('pedido_id')
                  ->on('pedidos')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webhook_events', function (Blueprint $table) {
            // Eliminar foreign key
            $table->dropForeign(['pedido_id']);
        });
    }
};
