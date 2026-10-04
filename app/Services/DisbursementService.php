<?php

namespace App\Services;

use App\Models\PencairanDana;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DisbursementService
{
    protected bool $enabled;
    protected string $gateway;
    protected array $config;

    public function __construct()
    {
        $this->enabled = config('payment.disbursement.enabled', false);
        $this->gateway = config('payment.disbursement.gateway', 'xendit');
        $this->config = config("payment.disbursement.{$this->gateway}", []);
    }

    /**
     * Check if disbursement is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Create a disbursement/payout request to the gateway.
     *
     * @param  PencairanDana  $pencairan
     * @return array{reference: string, status: string, response: array}
     *
     * @throws \RuntimeException
     */
    public function createDisbursement(PencairanDana $pencairan): array
    {
        if (!$this->isEnabled()) {
            throw new \RuntimeException('Disbursement gateway tidak aktif. Gunakan transfer manual.');
        }

        return match ($this->gateway) {
            'xendit' => $this->createXenditDisbursement($pencairan),
            'midtrans' => $this->createMidtransDisbursement($pencairan),
            default => throw new \RuntimeException("Disbursement gateway tidak didukung: {$this->gateway}"),
        };
    }

    /**
     * Check disbursement status from gateway.
     */
    public function checkStatus(PencairanDana $pencairan): array
    {
        return match ($this->gateway) {
            'xendit' => $this->checkXenditDisbursementStatus($pencairan),
            'midtrans' => $this->checkMidtransDisbursementStatus($pencairan),
            default => throw new \RuntimeException("Status check tidak didukung untuk gateway: {$this->gateway}"),
        };
    }

    /**
     * Verify and process webhook for disbursement.
     */
    public function verifyWebhook(array $payload): array
    {
        return match ($this->gateway) {
            'xendit' => $this->verifyXenditDisbursementWebhook($payload),
            'midtrans' => $this->verifyMidtransDisbursementWebhook($payload),
            default => throw new \RuntimeException("Webhook tidak didukung untuk gateway: {$this->gateway}"),
        };
    }

    // ─── Xendit Disbursement ──────────────────────────────────

    protected function createXenditDisbursement(PencairanDana $pencairan): array
    {
        $secretKey = $this->config['secret_key'];
        $apiUrl = $this->config['api_url'];

        // Determine bank code for Xendit
        $bankCode = $this->mapBankToXenditCode($pencairan->bank_tujuan);

        $payload = [
            'external_id' => 'DISB-' . $pencairan->id . '-' . now()->timestamp,
            'amount' => (int) $pencairan->nominal_pencairan,
            'bank_code' => $bankCode,
            'account_number' => $pencairan->no_rekening_tujuan,
            'account_holder_name' => $pencairan->nama_pemilik_rekening,
            'description' => 'Pencairan pembiayaan #' . ($pencairan->pembiayaan->kode ?? $pencairan->id),
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
            'Content-Type' => 'application/json',
        ])->timeout(30)
          ->post("{$apiUrl}/disbursements", $payload);

        if ($response->failed()) {
            $body = $response->json();
            throw new \RuntimeException(
                $body['message'] ?? 'Xendit disbursement error: ' . $response->status()
            );
        }

        $data = $response->json();

        Log::info('DisbursementService: Xendit disbursement created', [
            'pencairan_id' => $pencairan->id,
            'reference' => $data['id'] ?? null,
            'amount' => $pencairan->nominal_pencairan,
        ]);

        return [
            'reference' => $data['id'] ?? null,
            'status' => $data['status'] ?? 'PENDING',
            'response' => $data,
        ];
    }

    protected function checkXenditDisbursementStatus(PencairanDana $pencairan): array
    {
        $secretKey = $this->config['secret_key'];
        $apiUrl = $this->config['api_url'];

        $reference = $pencairan->disbursement_reference;
        if (!$reference) {
            throw new \RuntimeException('Tidak ada disbursement reference untuk pencairan #' . $pencairan->id);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
        ])->timeout(15)
          ->get("{$apiUrl}/disbursements/{$reference}");

        if ($response->failed()) {
            throw new \RuntimeException('Xendit disbursement status check failed: ' . $response->status());
        }

        return $response->json();
    }

    protected function verifyXenditDisbursementWebhook(array $payload): array
    {
        $externalId = $payload['external_id'] ?? null;
        $status = $payload['status'] ?? null;

        if (!$externalId) {
            throw new \RuntimeException('Invalid Xendit disbursement webhook: missing external_id');
        }

        // Extract pencairan_id from external_id format: DISB-{id}-{timestamp}
        $parts = explode('-', $externalId);
        $pencairanId = $parts[1] ?? null;

        $mappedStatus = match ($status) {
            'SUCCEEDED' => 'success',
            'PENDING', 'INITIATED' => 'processing',
            'FAILED' => 'failed',
            default => 'processing',
        };

        return [
            'pencairan_id' => $pencairanId,
            'reference' => $externalId,
            'status' => $mappedStatus,
            'raw_status' => $status,
            'amount' => $payload['amount'] ?? null,
            'raw_payload' => $payload,
        ];
    }

    // ─── Midtrans Disbursement (Payout) ───────────────────────

    protected function createMidtransDisbursement(PencairanDana $pencairan): array
    {
        $serverKey = $this->config['server_key'];
        $apiUrl = $this->config['api_url'];

        $reference = 'DISB-' . $pencairan->id . '-' . now()->timestamp;

        $payload = [
            'bank' => strtolower($pencairan->bank_tujuan),
            'bank_account_number' => $pencairan->no_rekening_tujuan,
            'amount' => (int) $pencairan->nominal_pencairan,
            'recipient_name' => $pencairan->nama_pemilik_rekening,
            'notes' => 'Pencairan pembiayaan #' . ($pencairan->pembiayaan->kode ?? $pencairan->id),
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->timeout(30)
            ->post("{$apiUrl}/payment/v1/payouts", $payload);

        if ($response->failed()) {
            $body = $response->json();
            throw new \RuntimeException(
                $body['status_message'] ?? 'Midtrans payout error: ' . $response->status()
            );
        }

        $data = $response->json();

        return [
            'reference' => $data['payout']['id'] ?? $reference,
            'status' => $data['payout']['status'] ?? 'pending',
            'response' => $data,
        ];
    }

    protected function checkMidtransDisbursementStatus(PencairanDana $pencairan): array
    {
        $serverKey = $this->config['server_key'];
        $apiUrl = $this->config['api_url'];

        $reference = $pencairan->disbursement_reference;

        $response = Http::withBasicAuth($serverKey, '')
            ->timeout(15)
            ->get("{$apiUrl}/payment/v1/payouts/{$reference}");

        if ($response->failed()) {
            throw new \RuntimeException('Midtrans payout status check failed');
        }

        return $response->json();
    }

    protected function verifyMidtransDisbursementWebhook(array $payload): array
    {
        $payoutId = $payload['payout']['id'] ?? $payload['id'] ?? null;
        $status = $payload['payout']['status'] ?? $payload['status'] ?? null;

        if (!$payoutId) {
            throw new \RuntimeException('Invalid Midtrans payout webhook: missing payout id');
        }

        $parts = explode('-', $payoutId);
        $pencairanId = $parts[1] ?? null;

        $mappedStatus = match ($status) {
            'completed', 'COMPLETED' => 'success',
            'pending', 'PENDING', 'processing', 'PROCESSING' => 'processing',
            'failed', 'FAILED' => 'failed',
            default => 'processing',
        };

        return [
            'pencairan_id' => $pencairanId,
            'reference' => $payoutId,
            'status' => $mappedStatus,
            'raw_status' => $status,
            'amount' => $payload['payout']['amount'] ?? null,
            'raw_payload' => $payload,
        ];
    }

    // ─── Helpers ───────────────────────────────────────────────

    /**
     * Map bank name to Xendit bank code.
     */
    protected function mapBankToXenditCode(string $bankName): string
    {
        $mapping = [
            'bca' => 'BCA',
            'mandiri' => 'MANDIRI',
            'bni' => 'BNI',
            'bri' => 'BRI',
            'bsi' => 'BSI',
            'btn' => 'BTN',
            'cimb' => 'CIMB',
            'danamon' => 'DANAMON',
            'permata' => 'PERMATA',
            'mega' => 'MEGA',
            'panin' => 'PANIN',
            'maybank' => 'MAYBANK',
            'ocbc' => 'OCBC',
            'uob' => 'UOB',
            'bukopin' => 'BUKOPIN',
            'sinarmas' => 'SINARMAS',
            'bjb' => 'BJB',
            'dki' => 'DKI',
            'jatim' => 'JATIM',
            'jateng' => 'JATENG',
            'bpd' => 'BPD',
        ];

        $normalized = strtolower(trim($bankName));

        return $mapping[$normalized] ?? strtoupper($bankName);
    }
}
