<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekening_koperasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank');
            $table->string('nomor_rekening');
            $table->string('atas_nama');
            $table->string('kode_rekening')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('pencairan_dana', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembiayaan_id')->constrained('pembiayaan')->cascadeOnDelete();
            $table->decimal('nominal_pencairan', 15, 2);
            $table->string('bank_tujuan');
            $table->string('no_rekening_tujuan');
            $table->string('nama_pemilik_rekening');
            $table->string('bukti_transfer')->nullable();
            $table->date('tanggal_pencairan');
            $table->foreignId('dicairkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('pembayaran_angsuran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembiayaan_id')->constrained('pembiayaan')->cascadeOnDelete();
            $table->foreignId('angsuran_id')->constrained('angsuran')->cascadeOnDelete();
            $table->decimal('jumlah_dibayar', 15, 2);
            $table->date('tanggal_bayar');
            $table->string('bukti_transfer')->nullable();
            $table->enum('status', ['menunggu_verifikasi', 'diverifikasi', 'ditolak'])->default('menunggu_verifikasi');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_verifikasi')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('setoran_simpanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis_simpanan', ['pokok', 'wajib', 'sukarela', 'mudharabah'])->default('sukarela');
            $table->decimal('nominal', 15, 2);
            $table->string('bukti_transfer')->nullable();
            $table->date('tanggal_setor');
            $table->enum('status', ['menunggu_verifikasi', 'diverifikasi', 'ditolak'])->default('menunggu_verifikasi');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('saldo_setelah', 15, 2)->default(0);
            $table->text('catatan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_simpanan');
        Schema::dropIfExists('pembayaran_angsuran');
        Schema::dropIfExists('pencairan_dana');
        Schema::dropIfExists('rekening_koperasi');
    }
};
