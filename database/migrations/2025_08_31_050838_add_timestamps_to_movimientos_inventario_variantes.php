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
        Schema::table('movimientos_inventario_variantes', function (Blueprint $table) {
            if (!Schema::hasColumn('movimientos_inventario_variantes', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_inventario_variantes', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
