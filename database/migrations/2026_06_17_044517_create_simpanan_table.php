<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simpanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis', ['pokok', 'wajib', 'sukarela', 'mudharabah']);
            $table->decimal('jumlah', 15, 2);
            $table->string('keterangan')->nullable();
            $table->enum('status', ['masuk', 'pending'])->default('masuk');
            $table->date('tanggal');
            $table->timestamps();

            $table->index(['user_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simpanan');
    }
};