<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only attempt to alter the table if it already exists. The create migration may run after this file
        // depending on filename ordering in the migrations directory.
        if (Schema::hasTable('transaksi_pembayaran')) {
            Schema::table('transaksi_pembayaran', function (Blueprint $table) {
                if (! Schema::hasColumn('transaksi_pembayaran', 'rekening_koperasi_id')) {
                    $table->foreignId('rekening_koperasi_id')->nullable()->constrained('rekening_koperasi')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transaksi_pembayaran') && Schema::hasColumn('transaksi_pembayaran', 'rekening_koperasi_id')) {
            Schema::table('transaksi_pembayaran', function (Blueprint $table) {
                $table->dropForeign(['rekening_koperasi_id']);
                $table->dropColumn('rekening_koperasi_id');
            });
        }
    }
};
