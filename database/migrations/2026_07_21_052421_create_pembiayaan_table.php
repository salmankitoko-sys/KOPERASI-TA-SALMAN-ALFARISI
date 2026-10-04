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
        Schema::create('pembiayaan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('toko_id')->nullable()->constrained('tokos')->nullOnDelete();

            $table->string('kode')->unique();

            $table->enum('akad', [
                'murabahah',
                'mudharabah',
                'musyarakah',
                'ijarah'
            ]);

            $table->string('tujuan_pembiayaan')->default('kebutuhan_lainnya');

            $table->string('objek_pembiayaan')->nullable();
            $table->text('rencana_penggunaan_dana')->nullable();
            $table->decimal('estimasi_omzet_usaha', 15, 2)->nullable();

            $table->decimal('jumlah_pembiayaan', 15, 2);

            $table->integer('tenor');

            $table->decimal('angsuran_bulanan', 15, 2)->default(0);

            $table->date('tanggal_pengajuan');

            $table->date('tanggal_persetujuan')->nullable();

            $table->enum('status', [
                'diajukan',
                'disetujui',
                'ditolak',
                'berjalan',
                'lunas'
            ])->default('diajukan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembiayaan');
    }
};
