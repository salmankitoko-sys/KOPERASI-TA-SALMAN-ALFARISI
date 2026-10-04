<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pesanan', 'status_pembayaran')) {
                $table->enum('status_pembayaran', ['menunggu', 'menunggu_verifikasi', 'terverifikasi', 'gagal'])
                    ->default('menunggu')
                    ->after('status');
            }

            if (!Schema::hasColumn('pesanan', 'biaya_pengiriman')) {
                $table->decimal('biaya_pengiriman', 15, 2)
                    ->default(0)
                    ->after('total');
            }

            if (!Schema::hasColumn('pesanan', 'metode_pengiriman')) {
                $table->string('metode_pengiriman')
                    ->nullable()
                    ->after('biaya_pengiriman');
            }

            if (!Schema::hasColumn('pesanan', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')
                    ->nullable()
                    ->after('metode_pengiriman');
            }

            if (!Schema::hasColumn('pesanan', 'catatan_pembeli')) {
                $table->text('catatan_pembeli')
                    ->nullable()
                    ->after('alamat_kirim');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            foreach (['catatan_pembeli', 'metode_pembayaran', 'metode_pengiriman', 'biaya_pengiriman', 'status_pembayaran'] as $column) {
                if (Schema::hasColumn('pesanan', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
