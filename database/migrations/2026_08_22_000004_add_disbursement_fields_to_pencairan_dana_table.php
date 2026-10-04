<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pencairan_dana', function (Blueprint $table) {
            $table->string('metode_pencairan', 20)
                ->default('manual')
                ->after('status'); // manual, disbursement_api

            $table->string('disbursement_reference')
                ->nullable()
                ->after('metode_pencairan'); // ID transaksi dari gateway

            $table->enum('disbursement_status', ['pending', 'processing', 'success', 'failed'])
                ->nullable()
                ->after('disbursement_reference');

            $table->timestamp('disbursement_at')
                ->nullable()
                ->after('disbursement_status'); // Waktu request disbursement

            $table->json('disbursement_response')
                ->nullable()
                ->after('disbursement_at'); // Raw response dari gateway
        });
    }

    public function down(): void
    {
        Schema::table('pencairan_dana', function (Blueprint $table) {
            $table->dropColumn([
                'metode_pencairan',
                'disbursement_reference',
                'disbursement_status',
                'disbursement_at',
                'disbursement_response',
            ]);
        });
    }
};
