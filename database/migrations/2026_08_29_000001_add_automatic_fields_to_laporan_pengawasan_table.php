<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_pengawasan', function (Blueprint $table) {
            $table->date('periode_mulai')->nullable()->after('semester');
            $table->date('periode_selesai')->nullable()->after('periode_mulai');
            $table->json('data_laporan')->nullable()->after('rekomendasi');
            $table->timestamp('dikirim_pada')->nullable()->after('tanggal_publikasi');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_pengawasan', function (Blueprint $table) {
            $table->dropColumn(['periode_mulai', 'periode_selesai', 'data_laporan', 'dikirim_pada']);
        });
    }
};