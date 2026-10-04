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
        Schema::create('opini_syariah', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_opini')->unique();
            $table->enum('jenis_objek', ['pembiayaan', 'produk']);
            $table->unsignedBigInteger('objek_id');
            $table->enum('hasil', ['Disetujui', 'Perlu Revisi', 'Ditolak']);

            $table->text('catatan')->nullable();

            $table->foreignId('ditandatangani_oleh')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('tanggal_opini')->default(now()->toDateString());

            $table->timestamps();

            // Objek morfisme sederhana: jenis_objek + objek_id
            $table->index(['jenis_objek', 'objek_id']);
            $table->index('hasil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opini_syariah');
    }
};

