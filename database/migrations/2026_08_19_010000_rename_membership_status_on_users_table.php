<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'jenis_keanggotaan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('jenis_keanggotaan');
            });
        }

        if (Schema::hasColumn('users', 'status_keanggotaan')) {
            if (! Schema::hasColumn('users', 'status')) {
                if (DB::getDriverName() === 'mysql') {
                    DB::statement("ALTER TABLE users CHANGE status_keanggotaan status ENUM('Aktif','Calon','Non-Aktif') NOT NULL DEFAULT 'Aktif'");
                } else {
                    Schema::table('users', function (Blueprint $table) {
                        $table->renameColumn('status_keanggotaan', 'status');
                    });
                }
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('status_keanggotaan');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'status') && ! Schema::hasColumn('users', 'status_keanggotaan')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE users CHANGE status status_keanggotaan ENUM('Aktif','Calon','Non-Aktif') NOT NULL DEFAULT 'Aktif'");
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->renameColumn('status', 'status_keanggotaan');
                });
            }
        }

        if (! Schema::hasColumn('users', 'jenis_keanggotaan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('jenis_keanggotaan', [
                    'Simpan Pinjam',
                    'Konsumen',
                    'Produsen',
                    'Jasa',
                    'Pemasaran',
                ])->default('Simpan Pinjam')->after('penghasilan');
            });
        }
    }
};
