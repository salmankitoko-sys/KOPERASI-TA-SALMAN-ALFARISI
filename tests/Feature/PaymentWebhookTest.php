<?php

namespace Tests\Feature;

use App\Models\Angsuran;
use App\Models\Payment;
use App\Models\PembayaranAngsuran;
use App\Models\Pembiayaan;
use App\Models\TransaksiPembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Pembiayaan $pembiayaan;
    protected Angsuran $angsuran;
    protected Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'anggota']);

        // Create pembiayaan manually (no factory)
        $this->pembiayaan = Pembiayaan::create([
            'user_id' => $this->user->id,
            'kode' => 'PBJ-TEST001',
            'akad' => 'murabahah',
            'jumlah_pembiayaan' => 5000000,
            'tenor' => 10,
            'angsuran_bulanan' => 500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'status' => 'berjalan',
        ]);

        // Create angsuran manually
        $this->angsuran = Angsuran::create([
            'pembiayaan_id' => $this->pembiayaan->id,
            'bulan_ke' => 1,
            'jatuh_tempo' => now()->addMonth()->toDateString(),
            'jumlah_bayar' => 500000,
            'pokok' => 500000,
            'margin' => 0,
            'sisa_pokok' => 4500000,
            'status' => 'belum_bayar',
        ]);

        // Create payment record
        $this->payment = Payment::create([
            'payment_code' => 'INV-TEST-0001',
            'user_id' => $this->user->id,
            'type' => Payment::TYPE_ANGSURAN,
            'payable_id' => $this->angsuran->id,
            'amount' => 500000,
            'total_amount' => 500000,
            'payment_method' => 'qris',
            'gateway' => 'midtrans',
            'external_id' => 'PAY-TEST123',
            'status' => Payment::STATUS_PENDING,
            'expired_at' => now()->addMinutes(30),
        ]);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $response = $this->postJson(route('payment.webhook'), [
            'order_id' => 'PAY-TEST123',
            'status_code' => '200',
            'gross_amount' => '500000',
            'signature_key' => 'invalid_signature',
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
        ]);

        $response->assertStatus(400);
    }

    public function test_webhook_handles_valid_payment(): void
    {
        $serverKey = config('payment.midtrans.server_key');
        $orderId = 'PAY-TEST123';
        $statusCode = '200';
        $grossAmount = '500000';
        $signature = hash_hmac('sha512', $orderId . $statusCode . $grossAmount . $serverKey, $serverKey);

        $response = $this->postJson(route('payment.webhook'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
            'payment_type' => 'qris',
        ]);

        $response->assertOk();

        $this->payment->refresh();
        $this->assertEquals(Payment::STATUS_PAID, $this->payment->status);
        $this->assertNotNull($this->payment->paid_at);

        $this->angsuran->refresh();
        $this->assertEquals('dibayar', $this->angsuran->status);

        $this->assertDatabaseHas('pembayaran_angsuran', [
            'pembiayaan_id' => $this->pembiayaan->id,
            'angsuran_id' => $this->angsuran->id,
            'payment_id' => $this->payment->id,
            'status' => 'diverifikasi',
        ]);

        $this->assertDatabaseHas('transaksi_pembayaran', [
            'user_id' => $this->user->id,
            'jenis_transaksi' => 'angsuran',
            'metode_pembayaran' => 'qris',
            'status' => 'diverifikasi',
        ]);
    }

    public function test_webhook_is_idempotent(): void
    {
        $serverKey = config('payment.midtrans.server_key');
        $orderId = 'PAY-TEST123';
        $statusCode = '200';
        $grossAmount = '500000';
        $signature = hash_hmac('sha512', $orderId . $statusCode . $grossAmount . $serverKey, $serverKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
            'payment_type' => 'qris',
        ];

        // First webhook
        $this->postJson(route('payment.webhook'), $payload)->assertOk();

        // Second webhook (duplicate)
        $this->postJson(route('payment.webhook'), $payload)->assertOk();

        // Verify only one PembayaranAngsuran created
        $this->assertEquals(1, PembayaranAngsuran::where('angsuran_id', $this->angsuran->id)->count());
    }

    public function test_webhook_rejects_amount_mismatch(): void
    {
        $serverKey = config('payment.midtrans.server_key');
        $orderId = 'PAY-TEST123';
        $statusCode = '200';
        $grossAmount = '999999';
        $signature = hash_hmac('sha512', $orderId . $statusCode . $grossAmount . $serverKey, $serverKey);

        $response = $this->postJson(route('payment.webhook'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
        ]);

        $response->assertStatus(400);

        $this->payment->refresh();
        $this->assertEquals(Payment::STATUS_PENDING, $this->payment->status);
    }

    public function test_webhook_handles_expired_payment(): void
    {
        $serverKey = config('payment.midtrans.server_key');
        $orderId = 'PAY-TEST123';
        $statusCode = '200';
        $grossAmount = '500000';
        $signature = hash_hmac('sha512', $orderId . $statusCode . $grossAmount . $serverKey, $serverKey);

        $response = $this->postJson(route('payment.webhook'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'expire',
            'payment_type' => 'qris',
        ]);

        $response->assertOk();

        $this->payment->refresh();
        $this->assertEquals(Payment::STATUS_EXPIRED, $this->payment->status);

        $this->angsuran->refresh();
        $this->assertEquals('belum_bayar', $this->angsuran->status);
    }

    public function test_webhook_logs_invalid_callback(): void
    {
        $response = $this->postJson(route('payment.webhook'), [
            'order_id' => 'UNKNOWN',
            'status_code' => '200',
            'gross_amount' => '500000',
            'signature_key' => 'invalid',
            'transaction_status' => 'capture',
        ]);

        $response->assertStatus(400);

        $this->assertDatabaseHas('webhook_logs', [
            'type' => 'payment',
            'is_valid' => false,
        ]);
    }

    public function test_pending_qris_page_has_modal_button_and_direct_fallback(): void
    {
        $this->payment->update([
            'qr_string' => 'snap-token-test',
            'qr_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/test',
        ]);

        $this->actingAs($this->user)
            ->get(route('anggota.payments.show', $this->payment->payment_code))
            ->assertOk()
            ->assertSee('Tampilkan QR Code QRIS')
            ->assertSee('QR tidak muncul? Buka halaman pembayaran langsung')
            ->assertSee($this->payment->qr_url, false);
    }}
