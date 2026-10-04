<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    protected string $gateway;
    protected array $config;

    public function __construct()
    {
        $this->gateway = config('payment.default', 'midtrans');
        $this->config = config("payment.{$this->gateway}", []);
    }

    /**
     * Create a QRIS payment transaction.
     *
     * @param  array  $data  Must contain: user_id, type, payable_id, amount, notes
     * @return array{payment: Payment, qr_url: string, qr_string: string|null, redirect_url: string|null}
     *
     * @throws \RuntimeException
     */
    public function createQrisPayment(array $data): array
    {
        // 1. Validate & sanitize amount from server side
        $amount = (int) $data['amount'];
        if ($amount <= 0) {
            throw new \RuntimeException('Nominal pembayaran harus lebih dari 0.');
        }

        // 2. Idempotency check — prevent duplicate pending payment for same payable
        $existing = Payment::where('type', $data['type'])
            ->where('payable_id', $data['payable_id'])
            ->where('status', Payment::STATUS_PENDING)
            ->where('expired_at', '>', now())
            ->first();

        if ($existing) {
            return [
                'payment' => $existing,
                'qr_url' => $existing->qr_url,
                'qr_string' => $existing->qr_string,
                'redirect_url' => null,
            ];
        }

        // 3. Create payment record (status: pending)
        $paymentCode = Payment::generatePaymentCode();
        $externalId = Payment::generateExternalId();
        $expiryMinutes = config('payment.qr_expiry_minutes', 30);

        $payment = Payment::create([
            'payment_code' => $paymentCode,
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'payable_id' => $data['payable_id'],
            'amount' => $amount,
            'fee' => $data['fee'] ?? 0,
            'total_amount' => $amount + ($data['fee'] ?? 0),
            'payment_method' => 'qris',
            'gateway' => $this->gateway,
            'external_id' => $externalId,
            'status' => Payment::STATUS_PENDING,
            'expired_at' => now()->addMinutes($expiryMinutes),
            'notes' => $data['notes'] ?? null,
        ]);

        // 4. Call gateway API to create QRIS
        try {
            $gatewayResponse = $this->callCreateQrisApi($payment);

            $payment->update([
                'gateway_reference' => $gatewayResponse['order_id'] ?? null,
                'qr_string' => $gatewayResponse['qr_string'] ?? $gatewayResponse['qr_code'] ?? null,
                'qr_url' => $gatewayResponse['qr_url'] ?? $gatewayResponse['redirect_url'] ?? null,
                'gateway_response' => $gatewayResponse,
            ]);

            Log::info('PaymentGateway: QRIS created', [
                'payment_id' => $payment->id,
                'payment_code' => $paymentCode,
                'type' => $data['type'],
                'amount' => $amount,
            ]);

            return [
                'payment' => $payment->fresh(),
                'qr_url' => $payment->qr_url,
                'qr_string' => $payment->qr_string,
                'redirect_url' => $gatewayResponse['redirect_url'] ?? null,
            ];
        } catch (\Exception $e) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'gateway_response' => ['error' => $e->getMessage()],
            ]);

            Log::error('PaymentGateway: Failed to create QRIS', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Gagal membuat QRIS: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Check payment status from gateway.
     */
    public function checkStatus(Payment $payment): array
    {
        return match ($this->gateway) {
            'midtrans' => $this->checkMidtransStatus($payment),
            'xendit' => $this->checkXenditStatus($payment),
            default => throw new \RuntimeException("Status check tidak didukung untuk gateway: {$this->gateway}"),
        };
    }

    /**
     * Verify and process webhook/callback from gateway.
     * Returns the verified status string.
     *
     * @throws \RuntimeException If webhook is invalid
     */
    public function verifyWebhook(array $payload): array
    {
        return match ($this->gateway) {
            'midtrans' => $this->verifyMidtransWebhook($payload),
            'xendit' => $this->verifyXenditWebhook($payload),
            default => throw new \RuntimeException("Webhook tidak didukung untuk gateway: {$this->gateway}"),
        };
    }

    // ─── Midtrans Implementation ──────────────────────────────

    protected function callCreateQrisApi(Payment $payment): array
    {
        $serverKey = $this->config['server_key'];
        $snapUrl = $this->config['snap_url'];

        $payload = [
            'transaction_details' => [
                'order_id' => $payment->external_id,
                'gross_amount' => (int) $payment->total_amount,
            ],
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->timeout(30)
            ->post($snapUrl, $payload);

        if ($response->failed()) {
            $body = $response->json();
            throw new \RuntimeException(
                $body['error_messages'][0] ?? $body['status_message'] ?? 'Midtrans API error: ' . $response->status()
            );
        }

        $data = $response->json();

        return [
            'order_id' => $data['order_id'] ?? $payment->external_id,
            'token' => $data['token'] ?? null,
            'redirect_url' => $data['redirect_url'] ?? null,
            'qr_code' => $data['qr_code'] ?? null,
            'qr_string' => $data['token'] ?? null,
            'qr_url' => $data['redirect_url'] ?? null,
            'status' => $data['transaction_status'] ?? 'pending',
        ];
    }

    protected function checkMidtransStatus(Payment $payment): array
    {
        $serverKey = $this->config['server_key'];
        $apiUrl = $this->config['api_url'];

        $orderId = $payment->gateway_reference ?? $payment->external_id;

        $response = Http::withBasicAuth($serverKey, '')
            ->timeout(15)
            ->get("{$apiUrl}/v2/{$orderId}/status");

        if ($response->failed()) {
            throw new \RuntimeException('Midtrans status check failed: ' . $response->status());
        }

        $data = $response->json();

        return [
            'order_id' => $data['order_id'] ?? null,
            'transaction_status' => $data['transaction_status'] ?? null,
            'status_code' => $data['status_code'] ?? null,
            'gross_amount' => $data['gross_amount'] ?? null,
            'payment_type' => $data['payment_type'] ?? null,
            'transaction_time' => $data['transaction_time'] ?? null,
            'settlement_time' => $data['settlement_time'] ?? null,
        ];
    }

    protected function verifyMidtransWebhook(array $payload): array
    {
        $serverKey = $this->config['server_key'];
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;

        if (!$orderId || !$signatureKey) {
            throw new \RuntimeException('Invalid Midtrans webhook: missing required fields');
        }

        // Verify signature
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if (!hash_equals($expectedSignature, $signatureKey)) {
            Log::warning('PaymentGateway: Invalid webhook signature', [
                'order_id' => $orderId,
            ]);
            throw new \RuntimeException('Invalid webhook signature');
        }

        // Map Midtrans status to our status
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';

        $mappedStatus = match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => Payment::STATUS_PAID,
            $transactionStatus === 'settlement' => Payment::STATUS_PAID,
            $transactionStatus === 'pending' => Payment::STATUS_PENDING,
            $transactionStatus === 'deny' => Payment::STATUS_FAILED,
            $transactionStatus === 'cancel' => Payment::STATUS_CANCELLED,
            $transactionStatus === 'expire' => Payment::STATUS_EXPIRED,
            $transactionStatus === 'failure' => Payment::STATUS_FAILED,
            default => Payment::STATUS_PENDING,
        };

        return [
            'external_id' => $orderId,
            'status' => $mappedStatus,
            'raw_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'gross_amount' => $grossAmount,
            'payment_type' => $payload['payment_type'] ?? null,
            'settlement_time' => $payload['settlement_time'] ?? null,
            'raw_payload' => $payload,
        ];
    }

    // ─── Xendit Implementation (Skeleton) ─────────────────────

    protected function callCreateQrisXenditApi(Payment $payment): array
    {
        $secretKey = $this->config['secret_key'] ?? config('payment.xendit.secret_key');
        $apiUrl = config('payment.xendit.api_url', 'https://api.xendit.co');

        $payload = [
            'external_id' => $payment->external_id,
            'amount' => (int) $payment->total_amount,
            'currency' => 'IDR',
            'qr_code' => [
                'callback_url' => route('payment.webhook'),
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
        ])->timeout(30)
          ->post("{$apiUrl}/qr_codes", $payload);

        if ($response->failed()) {
            $body = $response->json();
            throw new \RuntimeException(
                $body['message'] ?? 'Xendit API error: ' . $response->status()
            );
        }

        $data = $response->json();

        return [
            'order_id' => $data['id'] ?? $payment->external_id,
            'qr_string' => $data['qr_string'] ?? null,
            'qr_code' => $data['qr_code_image_url'] ?? null,
            'redirect_url' => $data['web_embed_url'] ?? null,
            'status' => $data['status'] ?? 'PENDING',
        ];
    }

    protected function checkXenditStatus(Payment $payment): array
    {
        $secretKey = config('payment.xendit.secret_key');
        $apiUrl = config('payment.xendit.api_url', 'https://api.xendit.co');

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
        ])->timeout(15)
          ->get("{$apiUrl}/qr_codes/{$payment->gateway_reference}");

        if ($response->failed()) {
            throw new \RuntimeException('Xendit status check failed: ' . $response->status());
        }

        return $response->json();
    }

    protected function verifyXenditWebhook(array $payload): array
    {
        $callbackToken = config('payment.xendit.callback_token');

        // Xendit sends X- CALLBACK-TOKEN header for verification
        // The token should be verified at middleware level, but we also check here
        $headerToken = request()->header('X-CALLBACK-TOKEN', '');
        if ($callbackToken && $headerToken && !hash_equals($callbackToken, $headerToken)) {
            throw new \RuntimeException('Invalid Xendit callback token');
        }

        $externalId = $payload['external_id'] ?? null;
        $status = $payload['status'] ?? null;

        if (!$externalId) {
            throw new \RuntimeException('Invalid Xendit webhook: missing external_id');
        }

        $mappedStatus = match ($status) {
            'SUCCEEDED' => Payment::STATUS_PAID,
            'PENDING' => Payment::STATUS_PENDING,
            'FAILED' => Payment::STATUS_FAILED,
            'EXPIRED' => Payment::STATUS_EXPIRED,
            default => Payment::STATUS_PENDING,
        };

        return [
            'external_id' => $externalId,
            'status' => $mappedStatus,
            'raw_status' => $status,
            'gross_amount' => $payload['amount'] ?? null,
            'raw_payload' => $payload,
        ];
    }
}
