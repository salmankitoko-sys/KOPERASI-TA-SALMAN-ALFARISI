<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Idempotent: jika tabel sudah ada, turunkan dulu agar migrasi tidak gagal.
        Schema::dropIfExists('produk');

        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->nullable();
            $table->decimal('harga', 15, 2);
            $table->unsignedInteger('stok')->default(0);
            $table->enum('akad', ['murabahah', 'salam', 'istishna'])->default('murabahah');
            $table->enum('status', ['pending', 'aktif', 'nonaktif', 'habis'])->default('pending');
            $table->string('foto_url')->nullable();
            $table->timestamps();

            $table->index(['toko_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};