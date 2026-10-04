<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembiayaan', function (Blueprint $table) {
            $table->string('status_validasi_dps', 30)
                ->default('menunggu')
                ->after('status');
            $table->text('catatan_validasi_dps')
                ->nullable()
                ->after('status_validasi_dps');
            $table->foreignId('divalidasi_oleh_dps')
                ->nullable()
                ->after('catatan_validasi_dps')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('tanggal_validasi_dps')
                ->nullable()
                ->after('divalidasi_oleh_dps');

            $table->index('status_validasi_dps');
        });
    }

    public function down(): void
    {
        Schema::table('pembiayaan', function (Blueprint $table) {
            $table->dropForeign(['divalidasi_oleh_dps']);
            $table->dropIndex(['status_validasi_dps']);
            $table->dropColumn([
                'status_validasi_dps',
                'catatan_validasi_dps',
                'divalidasi_oleh_dps',
                'tanggal_validasi_dps',
            ]);
        });
    }
};
