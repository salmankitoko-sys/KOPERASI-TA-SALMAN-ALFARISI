<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bagi_hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('periode')->comment('Tanggal 1 di bulan yang bersangkutan, contoh 2026-06-01');
            $table->decimal('saldo_rata_rata', 15, 2);
            $table->decimal('nisbah_persen', 5, 2);
            $table->decimal('jumlah_diterima', 15, 2);
            $table->enum('status', ['proses', 'diterima'])->default('proses');
            $table->timestamps();

            $table->unique(['user_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bagi_hasil');
    }
};