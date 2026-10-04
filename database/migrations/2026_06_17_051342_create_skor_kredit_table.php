<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skor_kredit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('skor')->comment('Skor gabungan 0-100');
            $table->unsignedTinyInteger('faktor_simpanan')->comment('0-100, riwayat simpanan');
            $table->unsignedTinyInteger('faktor_omzet')->comment('0-100, omzet lapak');
            $table->unsignedTinyInteger('faktor_ketepatan')->comment('0-100, ketepatan angsuran');
            $table->unsignedTinyInteger('faktor_lama_anggota')->comment('0-100, lama keanggotaan');
            $table->timestamp('dihitung_pada');
            $table->timestamps();

            $table->index(['user_id', 'dihitung_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skor_kredit');
    }
};