<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->string('payment_code', 50)->unique(); // INV-20260822-XXXX
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Relasi polymorphic sederhana (type + payable_id)
            $table->string('type', 30); // angsuran, simpanan, marketplace
            $table->unsignedBigInteger('payable_id'); // ID angsuran / setoran_simpanan / pesanan

            // Amount — SELALU dari server, bukan dari frontend
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);

            // Payment method & gateway
            $table->string('payment_method', 30)->default('qris'); // qris, transfer_manual
            $table->string('gateway', 30); // midtrans, xendit, doku
            $table->string('gateway_reference')->nullable(); // ID transaksi dari gateway
            $table->string('external_id', 100)->unique(); // Unique ID yang dikirim ke gateway

            // QRIS data
            $table->text('qr_string')->nullable(); // payload QRIS (decrypted)
            $table->string('qr_url')->nullable();   // URL gambar QR code

            // Status — hanya diubah oleh webhook yang tervalidasi
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();

            // Raw response dari gateway (JSON)
            $table->json('gateway_response')->nullable();

            // Webhook tracking
            $table->timestamp('webhook_received_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']); // Query pembayaran user
            $table->index(['type', 'payable_id']); // Cek duplikasi
            $table->index(['status', 'expired_at']); // Cleanup expired
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
