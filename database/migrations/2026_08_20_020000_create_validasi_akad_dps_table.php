<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validasi_akad_dps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembiayaan_id')->constrained('pembiayaan')->cascadeOnDelete();
            $table->unsignedInteger('versi');
            $table->string('hasil', 30);
            $table->json('checklist');
            $table->json('snapshot_data');
            $table->char('snapshot_hash', 64);
            $table->json('referensi_fatwa')->nullable();
            $table->string('referensi_tambahan', 1000)->nullable();
            $table->text('kesimpulan');
            $table->text('catatan_perbaikan')->nullable();
            $table->unsignedInteger('jumlah_kriteria')->default(0);
            $table->unsignedInteger('jumlah_sesuai')->default(0);
            $table->unsignedInteger('jumlah_tidak_sesuai')->default(0);
            $table->foreignId('divalidasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('divalidasi_pada');
            $table->timestamps();

            $table->unique(['pembiayaan_id', 'versi']);
            $table->index(['pembiayaan_id', 'hasil']);
            $table->index('snapshot_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validasi_akad_dps');
    }
};
