<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_modal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('toko_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tujuan');
            $table->decimal('jumlah_diajukan', 15, 2);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'diproses', 'disetujui', 'ditolak', 'lunas'])->default('draft');
            $table->date('tanggal_pengajuan');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_modal');
    }
};