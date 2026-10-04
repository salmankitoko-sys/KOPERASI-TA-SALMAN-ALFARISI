<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();

            $table->string('type', 30); // payment, disbursement
            $table->string('gateway', 30);
            $table->string('external_id', 100)->nullable();
            $table->string('order_id', 100)->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_valid')->default(true);
            $table->text('error_message')->nullable();

            $table->json('payload');
            $table->json('verified_data')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['external_id', 'type']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
