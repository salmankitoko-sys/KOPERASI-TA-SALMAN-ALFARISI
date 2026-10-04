<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_akad', function (Blueprint $table) {

            $table->id();

            $table->foreignId('pembiayaan_id')
                  ->constrained('pembiayaan')
                  ->cascadeOnDelete();

            // Data Umum
            $table->string('objek')->nullable();
            $table->integer('tenor')->nullable();

            /*
            |-------------------------------------
            | Murabahah
            |-------------------------------------
            */
            $table->decimal('harga_beli',15,2)->nullable();
            $table->decimal('harga_jual',15,2)->nullable();
            $table->decimal('margin_persen',5,2)->nullable();
            $table->decimal('margin',15,2)->nullable();
            $table->decimal('dp',15,2)->nullable();
            $table->decimal('biaya_admin',15,2)->nullable();
            $table->decimal('angsuran_bulanan',15,2)->nullable();

            /*
            |-------------------------------------
            | Mudharabah
            |-------------------------------------
            */
            $table->decimal('modal',15,2)->nullable();
            $table->decimal('modal_anggota',15,2)->nullable();
            $table->decimal('porsi_modal_koperasi',5,2)->nullable();
            $table->decimal('porsi_modal_anggota',5,2)->nullable();
            $table->decimal('nisbah_koperasi',5,2)->nullable();
            $table->decimal('nisbah_anggota',5,2)->nullable();
            $table->decimal('estimasi_omzet',15,2)->nullable();
            $table->decimal('estimasi_biaya',15,2)->nullable();
            $table->decimal('estimasi_laba',15,2)->nullable();
            $table->decimal('bagi_hasil_koperasi',15,2)->nullable();
            $table->decimal('bagi_hasil_anggota',15,2)->nullable();

            /*
            |-------------------------------------
            | Ijarah
            |-------------------------------------
            */
            $table->decimal('nilai_aset',15,2)->nullable();
            $table->decimal('ujrah_bulanan',15,2)->nullable();
            $table->decimal('biaya_perawatan',15,2)->nullable();
            $table->decimal('opsi_beli',15,2)->nullable();
            $table->decimal('total_pembayaran',15,2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_akad');
    }
};
