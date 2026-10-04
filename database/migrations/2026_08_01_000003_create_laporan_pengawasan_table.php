<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_pengawasan', function (Blueprint $table) {
            $table->id();

            $table->enum('periode', ['Semester', 'Tahunan'])->default('Semester');
            $table->string('semester')->nullable(); // mis. "2026-1" atau "2026-SMT2"
            $table->text('ringkasan');
            $table->text('rekomendasi')->nullable();
            $table->string('file_laporan')->nullable();
            $table->date('tanggal_publikasi')->nullable();
            $table->enum('status', ['Draf', 'Terbit'])->default('Draf');

            $table->foreignId('dibuat_oleh')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['periode', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pengawasan');
    }
};

