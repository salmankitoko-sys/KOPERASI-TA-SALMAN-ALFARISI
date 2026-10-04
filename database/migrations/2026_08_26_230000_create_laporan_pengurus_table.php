<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_pengurus', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->text('ringkasan');
            $table->text('capaian')->nullable();
            $table->text('kendala')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->enum('status', ['draf', 'dikirim', 'diterima', 'perlu_tindak_lanjut'])->default('draf');
            $table->foreignId('dibuat_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamp('dikirim_pada')->nullable();
            $table->foreignId('ditinjau_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ditinjau_pada')->nullable();
            $table->text('catatan_ketua')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_pengurus');
    }
};
