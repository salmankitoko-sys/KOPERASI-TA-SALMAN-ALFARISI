<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pembiayaan MODIFY akad ENUM('murabahah','mudharabah','musyarakah','ijarah') NOT NULL");
        }

        Schema::table('detail_akad', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_akad', 'modal_anggota')) {
                $table->decimal('modal_anggota', 15, 2)->nullable()->after('modal');
            }
            if (!Schema::hasColumn('detail_akad', 'porsi_modal_koperasi')) {
                $table->decimal('porsi_modal_koperasi', 5, 2)->nullable()->after('modal_anggota');
            }
            if (!Schema::hasColumn('detail_akad', 'porsi_modal_anggota')) {
                $table->decimal('porsi_modal_anggota', 5, 2)->nullable()->after('porsi_modal_koperasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detail_akad', function (Blueprint $table) {
            if (Schema::hasColumn('detail_akad', 'porsi_modal_anggota')) {
                $table->dropColumn('porsi_modal_anggota');
            }
            if (Schema::hasColumn('detail_akad', 'porsi_modal_koperasi')) {
                $table->dropColumn('porsi_modal_koperasi');
            }
            if (Schema::hasColumn('detail_akad', 'modal_anggota')) {
                $table->dropColumn('modal_anggota');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pembiayaan MODIFY akad ENUM('murabahah','mudharabah','ijarah') NOT NULL");
        }
    }
};
