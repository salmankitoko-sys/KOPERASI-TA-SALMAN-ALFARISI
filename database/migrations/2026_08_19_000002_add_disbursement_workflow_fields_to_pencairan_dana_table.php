<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pencairan_dana', function (Blueprint $table) {
            $table->foreignId('rekening_koperasi_id')
                ->nullable()
                ->after('pembiayaan_id')
                ->constrained('rekening_koperasi')
                ->nullOnDelete();
            $table->foreignId('disetujui_oleh')
                ->nullable()
                ->after('dicairkan_oleh')
                ->constrained('users')
                ->nullOnDelete();
            $table->date('tanggal_persetujuan')->nullable()->after('tanggal_pencairan');
            $table->string('nomor_referensi_transfer', 100)->nullable()->after('bukti_transfer');
            $table->date('tanggal_transfer')->nullable()->after('nomor_referensi_transfer');
            $table->foreignId('diverifikasi_oleh')
                ->nullable()
                ->after('disetujui_oleh')
                ->constrained('users')
                ->nullOnDelete();
            $table->date('tanggal_verifikasi')->nullable()->after('tanggal_transfer');
        });
    }

    public function down(): void
    {
        Schema::table('pencairan_dana', function (Blueprint $table) {
            $table->dropForeign(['rekening_koperasi_id']);
            $table->dropForeign(['disetujui_oleh']);
            $table->dropForeign(['diverifikasi_oleh']);
            $table->dropColumn([
                'rekening_koperasi_id',
                'disetujui_oleh',
                'tanggal_persetujuan',
                'nomor_referensi_transfer',
                'tanggal_transfer',
                'diverifikasi_oleh',
                'tanggal_verifikasi',
            ]);
        });
    }
};
