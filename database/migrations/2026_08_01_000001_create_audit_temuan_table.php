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
        Schema::create('audit_temuan', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_temuan')->unique();
            $table->foreignId('pembiayaan_id')->nullable()
                ->constrained('pembiayaan')
                ->nullOnDelete();
            $table->foreignId('produk_id')->nullable()
                ->constrained('produk')
                ->nullOnDelete();

            $table->string('jenis_temuan');
            $table->string('kategori')->nullable();
            $table->enum('tingkat_resiko', ['Rendah', 'Sedang', 'Tinggi', 'Kritis'])
                ->default('Sedang');

            $table->text('deskripsi');
            $table->text('rekomendasi')->nullable();

            $table->enum('status', [
                'Dibuka',
                'Ditindaklanjuti',
                'Diverifikasi',
                'Ditutup',
            ])->default('Dibuka');

            $table->foreignId('dibuat_oleh')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('diverifikasi_oleh')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('tanggal_temuan')->default(now()->toDateString());
            $table->date('tanggal_penutupan')->nullable();

            $table->timestamps();

            $table->index(['status', 'tingkat_resiko']);
            $table->index(['pembiayaan_id', 'produk_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_temuan');
    }
};

