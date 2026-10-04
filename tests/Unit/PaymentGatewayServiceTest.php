<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentGatewayServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentGatewayService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PaymentGatewayService::class);
    }

    public function test_generates_unique_payment_code(): void
    {
        $user = \App\Models\User::factory()->create();

        $code1 = Payment::generatePaymentCode();

        // Create a payment with that code
        Payment::create([
            'payment_code' => $code1,
            'user_id' => $user->id,
            'type' => 'angsuran',
            'payable_id' => 1,
            'amount' => 100000,
            'total_amount' => 100000,
            'gateway' => 'midtrans',
            'external_id' => 'PAY-UNIQUE-001',
            'status' => 'pending',
            'expired_at' => now()->addMinutes(30),
        ]);

        $code2 = Payment::generatePaymentCode();

        // Code should increment after a payment is created
        $this->assertNotEquals($code1, $code2);
        $this->assertMatchesRegularExpression('/^INV-\d{8}-\d{4}$/', $code1);
    }

    public function test_generates_unique_external_id(): void
    {
        $id1 = Payment::generateExternalId();
        $id2 = Payment::generateExternalId();

        $this->assertNotEquals($id1, $id2);
        $this->assertStringStartsWith('PAY-', $id1);
    }

    public function test_payment_model_status_constants(): void
    {
        $this->assertEquals('pending', Payment::STATUS_PENDING);
        $this->assertEquals('paid', Payment::STATUS_PAID);
        $this->assertEquals('failed', Payment::STATUS_FAILED);
        $this->assertEquals('expired', Payment::STATUS_EXPIRED);
        $this->assertEquals('cancelled', Payment::STATUS_CANCELLED);
    }

    public function test_payment_model_type_constants(): void
    {
        $this->assertEquals('angsuran', Payment::TYPE_ANGSURAN);
        $this->assertEquals('simpanan', Payment::TYPE_SIMPANAN);
        $this->assertEquals('marketplace', Payment::TYPE_MARKETPLACE);
    }

    public function test_payment_is_payable(): void
    {
        $payment = new Payment([
            'status' => Payment::STATUS_PENDING,
            'expired_at' => now()->addMinutes(30),
        ]);

        $this->assertTrue($payment->isPayable());
    }

    public function test_payment_is_not_payable_when_expired(): void
    {
        $payment = new Payment([
            'status' => Payment::STATUS_PENDING,
            'expired_at' => now()->subMinutes(5),
        ]);

        $this->assertFalse($payment->isPayable());
    }

    public function test_payment_is_not_payable_when_paid(): void
    {
        $payment = new Payment([
            'status' => Payment::STATUS_PAID,
            'expired_at' => now()->addMinutes(30),
        ]);

        $this->assertFalse($payment->isPayable());
    }

    public function test_payment_status_label(): void
    {
        $payment = new Payment(['status' => 'pending']);
        $this->assertEquals('Menunggu Pembayaran', $payment->status_label);

        $payment = new Payment(['status' => 'paid']);
        $this->assertEquals('Lunas', $payment->status_label);

        $payment = new Payment(['status' => 'expired']);
        $this->assertEquals('Kedaluwarsa', $payment->status_label);
    }

    public function test_payment_type_label(): void
    {
        $payment = new Payment(['type' => 'angsuran']);
        $this->assertEquals('Angsuran Pembiayaan', $payment->type_label);

        $payment = new Payment(['type' => 'simpanan']);
        $this->assertEquals('Setoran Simpanan', $payment->type_label);

        $payment = new Payment(['type' => 'marketplace']);
        $this->assertEquals('Pembelian Marketplace', $payment->type_label);
    }

    public function test_payment_code_increments(): void
    {
        $user = \App\Models\User::factory()->create();

        // Create first payment
        Payment::create([
            'payment_code' => 'INV-'.now()->format('Ymd').'-0001',
            'user_id' => $user->id,
            'type' => 'angsuran',
            'payable_id' => 1,
            'amount' => 100000,
            'total_amount' => 100000,
            'gateway' => 'midtrans',
            'external_id' => 'PAY-001',
            'status' => 'pending',
            'expired_at' => now()->addMinutes(30),
        ]);

        $nextCode = Payment::generatePaymentCode();

        // Should increment
        $this->assertEquals('INV-'.now()->format('Ymd').'-0002', $nextCode);
    }
}
