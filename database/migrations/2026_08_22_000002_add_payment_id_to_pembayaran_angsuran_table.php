<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_angsuran', function (Blueprint $table) {
            $table->foreignId('payment_id')
                ->nullable()
                ->after('angsuran_id')
                ->constrained('payments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_angsuran', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');
        });
    }
};
