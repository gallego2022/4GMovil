<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('metodos_pago')) {
            Schema::table('metodos_pago', function (Blueprint $table) {
                $table->unique('nombre');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('metodos_pago')) {
            Schema::table('metodos_pago', function (Blueprint $table) {
                $table->dropUnique(['nombre']);
            });
        }
    }
};

