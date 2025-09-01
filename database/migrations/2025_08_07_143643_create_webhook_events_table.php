<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_event_id')->unique();
            $table->string('event_type');
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->json('payload');
            $table->enum('status', ['pending', 'processing', 'processed', 'failed'])->default('pending');
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('pedido_id')->references('pedido_id')->on('pedidos')->onDelete('cascade');
            $table->index(['status', 'attempts']);
            $table->index('stripe_event_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('webhook_events');
    }
};
