<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembiayaan', function (Blueprint $table) {
            $table->string('bank_tujuan')->nullable()->after('tanggal_persetujuan');
            $table->string('no_rekening_tujuan')->nullable()->after('bank_tujuan');
            $table->string('nama_pemilik_rekening')->nullable()->after('no_rekening_tujuan');
            $table->date('tanggal_pencairan_diharapkan')->nullable()->after('nama_pemilik_rekening');
        });
    }

    public function down(): void
    {
        Schema::table('pembiayaan', function (Blueprint $table) {
            $table->dropColumn([
                'bank_tujuan',
                'no_rekening_tujuan',
                'nama_pemilik_rekening',
                'tanggal_pencairan_diharapkan',
            ]);
        });
    }
};