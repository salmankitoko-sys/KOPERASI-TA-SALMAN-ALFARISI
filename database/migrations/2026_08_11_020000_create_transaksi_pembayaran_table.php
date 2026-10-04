<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('jenis_transaksi');
            $table->string('judul')->nullable();
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->string('metode_pembayaran')->nullable();
            $table->enum('status', ['menunggu_verifikasi', 'diverifikasi', 'selesai', 'ditolak'])->default('menunggu_verifikasi');
            $table->string('referensi')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('rekening_koperasi_id')->nullable()->constrained('rekening_koperasi')->nullOnDelete();
            $table->string('bukti_transfer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_pembayaran');
    }
};
