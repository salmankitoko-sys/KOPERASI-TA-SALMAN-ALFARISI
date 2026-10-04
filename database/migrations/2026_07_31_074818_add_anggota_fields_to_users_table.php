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
        Schema::table('users', function (Blueprint $table) {
            $table->string('pekerjaan', 100)->nullable()->after('tanggal_lahir');
            $table->decimal('penghasilan', 15, 2)->nullable()->after('pekerjaan');
            $table->enum('status', [
                'Aktif',
                'Calon',
                'Non-Aktif'
            ])->default('Aktif')->after('penghasilan');
            $table->date('tgl_gabung')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'pekerjaan',
                'penghasilan',
                'status',
                'tgl_gabung',
            ]);
        });
    }
};
