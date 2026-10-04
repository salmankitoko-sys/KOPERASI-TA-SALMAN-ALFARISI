<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angsuran', function (Blueprint $table) {

            $table->id();

            $table->foreignId('pembiayaan_id')
                  ->constrained('pembiayaan')
                  ->cascadeOnDelete();

            $table->integer('bulan_ke');

            $table->date('jatuh_tempo');

            $table->decimal('jumlah_bayar',15,2);

            $table->decimal('pokok',15,2)->nullable();

            $table->decimal('margin',15,2)->nullable();

            $table->decimal('sisa_pokok',15,2)->nullable();

            $table->enum('status',[
                'belum_bayar',
                'dibayar'
            ])->default('belum_bayar');

            $table->date('tanggal_bayar')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angsuran');
    }
};