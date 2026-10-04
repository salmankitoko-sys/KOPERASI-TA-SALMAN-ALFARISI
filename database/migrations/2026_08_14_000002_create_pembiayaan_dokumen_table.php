<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembiayaan_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembiayaan_id')->constrained('pembiayaan')->cascadeOnDelete();
            $table->string('jenis', 50);
            $table->string('nama_asli');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('ukuran')->default(0);
            $table->timestamps();

            $table->index(['pembiayaan_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembiayaan_dokumen');
    }
};
