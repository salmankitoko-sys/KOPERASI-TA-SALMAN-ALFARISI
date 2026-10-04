<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\Payment;
use App\Models\PembayaranAngsuran;
use App\Models\PencairanDana;
use App\Models\Pembiayaan;
use App\Models\Pesanan;
use App\Models\PesananItem;
use App\Models\Produk;
use App\Models\SetoranSimpanan;
use App\Models\Simpanan;
use App\Models\TransaksiPembayaran;
use App\Models\WebhookLog;
use App\Services\DisbursementService;
use App\Services\PaymentGatewayService;
use App\Support\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentGateway,
    ) {}

    /**
     * Handle QRIS payment webhook/callback from gateway.
     *
     * POST /payments/webhook
     *
     * This endpoint must be:
     * - Idempotent (safe to receive same callback multiple times)
     * - Not protected by CSRF (excluded in VerifyCsrfToken)
     * - Not protected by auth middleware
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $ip = $request->ip();

        Log::info('Webhook: Received callback', [
            'ip' => $ip,
            'order_id' => $payload['order_id'] ?? $payload['external_id'] ?? null,
        ]);

        // 0. IP whitelist check (if configured)
        $whitelist = config('payment.webhook.ip_whitelist', []);
        if (! empty($whitelist) && ! in_array($ip, $whitelist)) {
            Log::warning('Webhook: IP not in whitelist', ['ip' => $ip]);
            WebhookLog::create([
                'type' => 'payment',
                'gateway' => config('payment.default'),
                'ip_address' => $ip,
                'is_valid' => false,
                'error_message' => 'IP not in whitelist',
                'payload' => $payload,
            ]);
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        // 1. Verify webhook signature via gateway service
        try {
            $verified = $this->paymentGateway->verifyWebhook($payload);
        } catch (\Exception $e) {
            Log::warning('Webhook: Invalid callback', [
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);
            WebhookLog::create([
                'type' => 'payment',
                'gateway' => config('payment.default'),
                'ip_address' => $ip,
                'is_valid' => false,
                'error_message' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return response()->json(['status' => 'error', 'message' => 'Invalid webhook'], 400);
        }

        // 2. Find payment by external_id
        $externalId = $verified['external_id'] ?? null;
        if (!$externalId) {
            return response()->json(['status' => 'error', 'message' => 'Missing external_id'], 400);
        }

        $payment = Payment::where('external_id', $externalId)->first();
        if (!$payment) {
            Log::warning('Webhook: Payment not found', ['external_id' => $externalId]);
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }

        // 3. Idempotency: if already processed, skip
        if ($payment->status === Payment::STATUS_PAID) {
            Log::info('Webhook: Payment already paid, skipping', ['payment_id' => $payment->id]);
            return response()->json(['status' => 'ok', 'message' => 'Already processed']);
        }

        // 4. Verify amount matches (security check)
        $webhookAmount = (int) ($verified['gross_amount'] ?? 0);
        if ($webhookAmount > 0 && $webhookAmount !== (int) $payment->total_amount) {
            Log::critical('Webhook: Amount mismatch!', [
                'payment_id' => $payment->id,
                'expected' => $payment->total_amount,
                'received' => $webhookAmount,
            ]);

            return response()->json(['status' => 'error', 'message' => 'Amount mismatch'], 400);
        }

        // 5. Update payment status
        $newStatus = $verified['status'];

        if ($newStatus === $payment->status) {
            return response()->json(['status' => 'ok', 'message' => 'Status unchanged']);
        }

        $payment->update([
            'status' => $newStatus,
            'gateway_response' => $verified['raw_payload'] ?? null,
            'webhook_received_at' => now(),
            'paid_at' => $newStatus === Payment::STATUS_PAID ? now() : null,
        ]);

        Log::info('Webhook: Payment status updated', [
            'payment_id' => $payment->id,
            'new_status' => $newStatus,
        ]);

        // 6. If payment is PAID, process business logic
        if ($newStatus === Payment::STATUS_PAID) {
            $this->processPaidPayment($payment);
        }

        // 7. Log webhook
        WebhookLog::create([
            'type' => 'payment',
            'gateway' => config('payment.default'),
            'external_id' => $externalId,
            'order_id' => $payment->gateway_reference,
            'ip_address' => $ip,
            'is_valid' => true,
            'payload' => $payload,
            'verified_data' => $verified,
        ]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Process business logic after payment is confirmed PAID.
     * Wrapped in DB transaction for data consistency.
     * Public so it can be called from PaymentController as fallback.
     */
    public function processPaidPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            match ($payment->type) {
                Payment::TYPE_ANGSURAN => $this->processAngsuranPayment($payment),
                Payment::TYPE_SIMPANAN => $this->processSimpananPayment($payment),
                Payment::TYPE_MARKETPLACE => $this->processMarketplacePayment($payment),
                default => Log::warning('Webhook: Unknown payment type', ['type' => $payment->type]),
            };
        });
    }

    /**
     * Process paid angsuran payment:
     * 1. Create PembayaranAngsuran (status: diverifikasi)
     * 2. Update Angsuran status â†’ dibayar
     * 3. Create TransaksiPembayaran as journal
     */
    protected function processAngsuranPayment(Payment $payment): void
    {
        $angsuran = Angsuran::lockForUpdate()->find($payment->payable_id);
        if (!$angsuran) {
            Log::error('Webhook: Angsuran not found', ['payable_id' => $payment->payable_id]);
            return;
        }

        // Double-check: don't process if already paid (idempotency)
        if ($angsuran->status === 'dibayar') {
            Log::info('Webhook: Angsuran already paid, skipping', ['angsuran_id' => $angsuran->id]);
            return;
        }

        // Create PembayaranAngsuran record
        $pembayaran = PembayaranAngsuran::create([
            'pembiayaan_id' => $angsuran->pembiayaan_id,
            'angsuran_id' => $angsuran->id,
            'payment_id' => $payment->id,
            'jumlah_dibayar' => $payment->amount,
            'tanggal_bayar' => now()->toDateString(),
            'status' => 'diverifikasi',
        ]);

        // Update Angsuran status
        $angsuran->update([
            'status' => 'dibayar',
            'tanggal_bayar' => now()->toDateString(),
        ]);

        // Create journal entry
        $pembiayaan = $angsuran->pembiayaan;
        TransaksiPembayaran::create([
            'user_id' => $payment->user_id,
            'jenis_transaksi' => 'angsuran',
            'judul' => 'Pembayaran angsuran ' . ($pembiayaan->kode ?? '') . ' - Angsuran ke-' . $angsuran->bulan_ke . ' (QRIS)',
            'jumlah' => $payment->amount,
            'metode_pembayaran' => 'qris',
            'status' => 'diverifikasi',
            'referensi' => 'pembayaran_angsuran:' . $pembayaran->id,
            'keterangan' => 'Pembayaran via QRIS. Payment code: ' . $payment->payment_code,
        ]);

        NotificationHelper::sendToRole('bendahara', 'Pemasukan QRIS: Angsuran',
            'Pembayaran angsuran ' . ($pembiayaan->kode ?? '-') . ' sebesar Rp ' . number_format($payment->amount, 0, ',', '.') . ' telah berhasil dan dicatat di Mutasi Kas.',
            ['payment_id' => $payment->id, 'tab' => 'transaksi']);

        Log::info('Webhook: Angsuran payment processed', [
            'angsuran_id' => $angsuran->id,
            'pembayaran_id' => $pembayaran->id,
            'amount' => $payment->amount,
        ]);

        // Check if all angsuran are paid → update pembiayaan to 'lunas'
        $totalAngsuran = Angsuran::where('pembiayaan_id', $angsuran->pembiayaan_id)->count();
        $paidAngsuran = Angsuran::where('pembiayaan_id', $angsuran->pembiayaan_id)
            ->where('status', 'dibayar')
            ->count();

        if ($paidAngsuran >= $totalAngsuran) {
            $pembiayaan->update(['status' => 'lunas']);
            Log::info('Webhook: Pembiayaan fully paid', ['pembiayaan_id' => $pembiayaan->id]);
        }
    }
    /**
     * Process paid simpanan payment:
     * 1. Update SetoranSimpanan status â†’ diverifikasi
     * 2. Create Simpanan record (status: masuk)
     * 3. Create TransaksiPembayaran as journal
     */
    protected function processSimpananPayment(Payment $payment): void
    {
        $setoran = SetoranSimpanan::lockForUpdate()->find($payment->payable_id);
        if (!$setoran) {
            Log::error('Webhook: SetoranSimpanan not found', ['payable_id' => $payment->payable_id]);
            return;
        }

        // Idempotency check
        if ($setoran->status === 'diverifikasi') {
            Log::info('Webhook: SetoranSimpanan already verified, skipping');
            return;
        }

        // Calculate cumulative saldo (sum of all verified simpanan for this user + this one)
        $saldoSebelumnya = \App\Models\Simpanan::where('user_id', $setoran->user_id)
            ->where('status', 'masuk')
            ->sum('jumlah');
        $saldoSetelah = $saldoSebelumnya + $setoran->nominal;

        // Update SetoranSimpanan
        $setoran->update([
            'status' => 'diverifikasi',
            'payment_id' => $payment->id,
            'saldo_setelah' => $saldoSetelah,
        ]);

        // Create Simpanan record
        Simpanan::create([
            'user_id' => $setoran->user_id,
            'jenis' => $setoran->jenis_simpanan,
            'jumlah' => $setoran->nominal,
            'keterangan' => 'Setoran via QRIS. Payment code: ' . $payment->payment_code,
            'bukti_transfer' => null,
            'status' => 'masuk',
            'tanggal' => $setoran->tanggal_setor,
        ]);

        // Create journal entry
        TransaksiPembayaran::create([
            'user_id' => $payment->user_id,
            'jenis_transaksi' => 'setoran_simpanan',
            'judul' => 'Setoran ' . ucfirst($setoran->jenis_simpanan) . ' (QRIS)',
            'jumlah' => $setoran->nominal,
            'metode_pembayaran' => 'qris',
            'status' => 'diverifikasi',
            'referensi' => 'setoran_simpanan:' . $setoran->id,
            'keterangan' => 'Pembayaran via QRIS. Payment code: ' . $payment->payment_code,
        ]);

        NotificationHelper::sendToRole('bendahara', 'Pemasukan QRIS: Simpanan',
            'Setoran ' . ucfirst($setoran->jenis_simpanan) . ' sebesar Rp ' . number_format($payment->amount, 0, ',', '.') . ' telah berhasil dan dicatat di Mutasi Kas.',
            ['payment_id' => $payment->id, 'tab' => 'transaksi']);

        Log::info('Webhook: Simpanan payment processed', [
            'setoran_id' => $setoran->id,
            'amount' => $payment->amount,
        ]);
    }

    /**
     * Process paid marketplace payment:
     * 1. Update Pesanan status_pembayaran â†’ lunas
     * 2. Update Pesanan status â†’ diproses
     * 3. Create TransaksiPembayaran as journal
     * 4. (Stock already reduced at checkout)
     */
    protected function processMarketplacePayment(Payment $payment): void
    {
        $pesanan = Pesanan::with('toko')->lockForUpdate()->find($payment->payable_id);
        if (!$pesanan) {
            Log::error('Webhook: Pesanan not found', ['payable_id' => $payment->payable_id]);
            return;
        }

        // Idempotency check
        if ($pesanan->status_pembayaran === 'terverifikasi') {
            Log::info('Webhook: Pesanan already paid, skipping');
            return;
        }

        // Update Pesanan
        $pesanan->update([
            'status_pembayaran' => 'terverifikasi',
        ]);

        // Create journal entry
        TransaksiPembayaran::create([
            'user_id' => $payment->user_id,
            'jenis_transaksi' => 'marketplace',
            'judul' => 'Pemasukan Marketplace â€” ' . ($pesanan->toko?->nama_toko ?? 'Toko tidak diketahui') . ' | Pesanan ' . $pesanan->nomor_pesanan . ' (QRIS)',
            'jumlah' => $payment->amount,
            'metode_pembayaran' => 'qris',
            'status' => 'diverifikasi',
            'referensi' => 'pesanan:' . $pesanan->id,
            'keterangan' => 'Pemasukan toko ' . ($pesanan->toko?->nama_toko ?? '-') . ' via QRIS. Payment code: ' . $payment->payment_code,
        ]);

        NotificationHelper::sendToRole('bendahara', 'Pemasukan QRIS: Marketplace',
            'Pemasukan toko ' . ($pesanan->toko?->nama_toko ?? '-') . ' dari pesanan ' . $pesanan->nomor_pesanan . ' sebesar Rp ' . number_format($payment->amount, 0, ',', '.') . ' telah berhasil dan dicatat di Mutasi Kas.',
            ['payment_id' => $payment->id, 'tab' => 'transaksi']);

        Log::info('Webhook: Marketplace payment processed', [
            'pesanan_id' => $pesanan->id,
            'nomor_pesanan' => $pesanan->nomor_pesanan,
            'amount' => $payment->amount,
        ]);
    }

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    // DISBURSEMENT WEBHOOK
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    /**
     * Handle disbursement webhook/callback from gateway.
     *
     * POST /payments/disbursement-webhook
     */
    public function handleDisbursement(Request $request)
    {
        $payload = $request->all();
        $ip = $request->ip();

        Log::info('Webhook: Received disbursement callback', [
            'ip' => $ip,
            'external_id' => $payload['external_id'] ?? null,
        ]);

        $disbursementService = app(DisbursementService::class);

        try {
            $verified = $disbursementService->verifyWebhook($payload);
        } catch (\Exception $e) {
            Log::warning('Webhook: Invalid disbursement callback', [
                'error' => $e->getMessage(),
                'ip' => $ip,
            ]);
            WebhookLog::create([
                'type' => 'disbursement',
                'gateway' => config('payment.disbursement.gateway'),
                'ip_address' => $ip,
                'is_valid' => false,
                'error_message' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return response()->json(['status' => 'error', 'message' => 'Invalid webhook'], 400);
        }

        $pencairanId = $verified['pencairan_id'] ?? null;
        if (!$pencairanId) {
            return response()->json(['status' => 'error', 'message' => 'Missing pencairan_id'], 400);
        }

        $pencairan = PencairanDana::find($pencairanId);
        if (!$pencairan) {
            Log::warning('Webhook: Pencairan not found', ['pencairan_id' => $pencairanId]);
            return response()->json(['status' => 'error', 'message' => 'Pencairan not found'], 404);
        }

        // Idempotency: skip if already processed
        if ($pencairan->disbursement_status === $verified['status']) {
            return response()->json(['status' => 'ok', 'message' => 'Already processed']);
        }

        DB::transaction(function () use ($pencairan, $verified) {
            // Update disbursement status
            $pencairan->update([
                'disbursement_status' => $verified['status'],
                'disbursement_response' => $verified['raw_payload'] ?? null,
            ]);

            if ($verified['status'] === 'success') {
                // Update pencairan to selesai
                $pencairan->update([
                    'status' => 'selesai',
                    'tanggal_pencairan' => now()->toDateString(),
                    'tanggal_transfer' => now()->toDateString(),
                    'nomor_referensi_transfer' => $verified['reference'] ?? $pencairan->disbursement_reference,
                ]);

                // Check if all pencairan for this pembiayaan are complete
                $totalDicairkan = PencairanDana::where('pembiayaan_id', $pencairan->pembiayaan_id)
                    ->where('status', 'selesai')
                    ->sum('nominal_pencairan');

                $pembiayaan = Pembiayaan::whereKey($pencairan->pembiayaan_id)->first();
                if ($pembiayaan && $totalDicairkan >= $pembiayaan->jumlah_pembiayaan) {
                    $pembiayaan->update(['status' => 'berjalan']);
                }

                // Update journal entry
                $referensi = 'pencairan:' . $pencairan->id;
                $journal = TransaksiPembayaran::where('referensi', $referensi)->first();
                if ($journal) {
                    $journal->update(['status' => 'selesai']);
                }

                Log::info('Webhook: Disbursement completed', [
                    'pencairan_id' => $pencairan->id,
                    'reference' => $verified['reference'] ?? null,
                ]);

            } elseif ($verified['status'] === 'failed') {
                $pencairan->update([
                    'catatan' => trim(($pencairan->catatan ?? '') . '\nDisbursement gagal: ' . ($verified['raw_status'] ?? 'unknown')),
                ]);

                Log::warning('Webhook: Disbursement failed', [
                    'pencairan_id' => $pencairan->id,
                    'raw_status' => $verified['raw_status'] ?? null,
                ]);
            }
        });

        // Log disbursement webhook
        WebhookLog::create([
            'type' => 'disbursement',
            'gateway' => config('payment.disbursement.gateway'),
            'external_id' => $verified['reference'] ?? null,
            'order_id' => $verified['reference'] ?? null,
            'ip_address' => $ip,
            'is_valid' => true,
            'payload' => $payload,
            'verified_data' => $verified,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
