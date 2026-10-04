<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembeli_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('toko_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_pesanan')->unique();
            $table->enum('status', ['menunggu', 'dikemas', 'dikirim', 'selesai', 'batal'])->default('menunggu');
            $table->enum('status_pembayaran', ['menunggu', 'menunggu_verifikasi', 'terverifikasi', 'gagal'])->default('menunggu');
            $table->enum('akad', ['murabahah', 'salam', 'istishna'])->default('murabahah');
            $table->decimal('total', 15, 2);
            $table->decimal('biaya_pengiriman', 15, 2)->default(0);
            $table->string('metode_pengiriman')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->text('alamat_kirim')->nullable();
            $table->text('catatan_pembeli')->nullable();
            $table->timestamps();

            $table->index(['pembeli_id', 'status']);
            $table->index(['toko_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
