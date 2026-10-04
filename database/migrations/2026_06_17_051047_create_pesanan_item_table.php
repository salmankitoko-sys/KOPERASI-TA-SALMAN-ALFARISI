<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Idempotent: jika tabel sudah ada, turunkan dulu agar migrasi tidak gagal.
        Schema::dropIfExists('pesanan_item');

        Schema::create('pesanan_item', function (Blueprint $table) {
            $table->id();

            // Pastikan referensi FK memakai tipe & parent table yang benar.
            // Laravel default: foreignId -> unsignedBigInteger, sehingga pesanan.id harus bigIncrements (id()).
            $table->foreignId('pesanan_id')
                ->constrained('pesanan')
                ->cascadeOnDelete();

            $table->foreignId('produk_id')
                ->nullable()
                ->constrained('produk')
                ->nullOnDelete();

            $table->string('nama_produk_snapshot')->comment('Salinan nama produk saat dibeli, agar histori tetap valid jika produk diubah/dihapus');
            $table->decimal('harga_satuan', 15, 2);
            $table->unsignedInteger('qty');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_item');
    }
};