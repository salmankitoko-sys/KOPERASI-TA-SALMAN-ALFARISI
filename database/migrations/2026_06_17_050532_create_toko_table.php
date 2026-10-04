<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_toko');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->nullable();
            $table->enum('status', ['pending', 'aktif', 'nonaktif'])->default('pending');
            $table->decimal('rating_rata', 3, 2)->default(0);
            $table->unsignedInteger('jumlah_ulasan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokos');
    }
};