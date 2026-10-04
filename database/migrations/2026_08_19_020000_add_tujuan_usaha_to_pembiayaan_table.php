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
        Schema::table('pembiayaan', function (Blueprint $table) {
            if (! Schema::hasColumn('pembiayaan', 'toko_id')) {
                $table->foreignId('toko_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('tokos')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('pembiayaan', 'tujuan_pembiayaan')) {
                $table->string('tujuan_pembiayaan')
                    ->default('kebutuhan_lainnya')
                    ->after('akad');
            }

            if (! Schema::hasColumn('pembiayaan', 'rencana_penggunaan_dana')) {
                $table->text('rencana_penggunaan_dana')
                    ->nullable()
                    ->after('objek_pembiayaan');
            }

            if (! Schema::hasColumn('pembiayaan', 'estimasi_omzet_usaha')) {
                $table->decimal('estimasi_omzet_usaha', 15, 2)
                    ->nullable()
                    ->after('rencana_penggunaan_dana');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembiayaan', function (Blueprint $table) {
            if (Schema::hasColumn('pembiayaan', 'toko_id')) {
                $table->dropConstrainedForeignId('toko_id');
            }

            $columns = array_filter([
                Schema::hasColumn('pembiayaan', 'tujuan_pembiayaan') ? 'tujuan_pembiayaan' : null,
                Schema::hasColumn('pembiayaan', 'rencana_penggunaan_dana') ? 'rencana_penggunaan_dana' : null,
                Schema::hasColumn('pembiayaan', 'estimasi_omzet_usaha') ? 'estimasi_omzet_usaha' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
