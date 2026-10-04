<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Payment;
use App\Models\Pembiayaan;
use App\Models\SetoranSimpanan;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $paymentGateway,
    ) {}

    /**
     * Initiate QRIS payment for a specific angsuran.
     *
     * POST /anggota/payments/qris-angsuran
     */
    public function createQrisAngsuran(Request $request)
    {
        $request->validate([
            'pembiayaan_id' => 'required|exists:pembiayaan,id',
            'angsuran_id' => 'required|exists:angsuran,id',
        ]);

        $pembiayaan = Pembiayaan::findOrFail($request->pembiayaan_id);
        $angsuran = Angsuran::findOrFail($request->angsuran_id);

        // Authorization: must own the pembiayaan
        if ($pembiayaan->user_id !== auth()->id()) {
            abort(403);
        }

        // Pembiayaan must be active
        if ($pembiayaan->status !== 'berjalan') {
            return back()->with('error', 'Pembayaran angsuran hanya tersedia untuk pembiayaan berjalan.');
        }

        // Angsuran must belong to this pembiayaan
        if ($angsuran->pembiayaan_id !== $pembiayaan->id) {
            abort(403);
        }

        // Angsuran must be unpaid
        if ($angsuran->status !== 'belum_bayar') {
            return back()->with('info', 'Angsuran ini sudah diproses pembayarannya.');
        }

        try {
            $result = $this->paymentGateway->createQrisPayment([
                'user_id' => auth()->id(),
                'type' => Payment::TYPE_ANGSURAN,
                'payable_id' => $angsuran->id,
                'amount' => $angsuran->jumlah_bayar,
                'notes' => 'Pembayaran angsuran ke-' . $angsuran->bulan_ke . ' - ' . ($pembiayaan->kode ?? ''),
            ]);

            return redirect()->route('anggota.payments.show', $result['payment']->payment_code)
                ->with('success', 'QRIS berhasil dibuat. Silakan scan QR untuk pembayaran.');

        } catch (\Exception $e) {
            Log::error('PaymentController: Failed to create QRIS angsuran', [
                'user_id' => auth()->id(),
                'angsuran_id' => $angsuran->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal membuat QRIS: ' . $e->getMessage());
        }
    }
    /**
     * Show QRIS simpanan form (select type + amount).
     *
     * GET /anggota/payments/simpanan/qris
     */
    public function simpananForm()
    {
        return view('anggota.payments.simpanan-form');
    }

    /**
     * Initiate QRIS payment for simpanan.
     *
     * POST /anggota/payments/qris-simpanan
     */
    public function createQrisSimpanan(Request $request)
    {
        $validated = $request->validate([
            'jenis_simpanan' => 'required|in:pokok,wajib,sukarela',
            'nominal' => 'required|numeric|min:1000',
        ], [
            'jenis_simpanan.required' => 'Pilih jenis simpanan.',
            'nominal.required' => 'Masukkan jumlah setoran.',
            'nominal.min' => 'Jumlah setoran minimal Rp 1.000.',
        ]);

        try {
            // 1. Check for existing pending SetoranSimpanan with same type
            $existingSetoran = SetoranSimpanan::where('user_id', auth()->id())
                ->where('jenis_simpanan', $validated['jenis_simpanan'])
                ->where('status', 'menunggu_verifikasi')
                ->exists();

            if ($existingSetoran) {
                return back()->with('error', 'Anda sudah memiliki setoran ' . ucfirst($validated['jenis_simpanan']) . ' yang sedang menunggu pembayaran. Silakan selesaikan atau batalkan pembayaran yang sudah ada.');
            }

            // 2. Create SetoranSimpanan record first (status: menunggu_verifikasi)
            $setoran = SetoranSimpanan::create([
                'user_id' => auth()->id(),
                'jenis_simpanan' => $validated['jenis_simpanan'],
                'nominal' => $validated['nominal'],
                'tanggal_setor' => now()->toDateString(),
                'status' => 'menunggu_verifikasi',
            ]);

            // 2. Create QRIS payment
            $result = $this->paymentGateway->createQrisPayment([
                'user_id' => auth()->id(),
                'type' => Payment::TYPE_SIMPANAN,
                'payable_id' => $setoran->id,
                'amount' => $validated['nominal'],
                'notes' => 'Setoran ' . ucfirst($validated['jenis_simpanan']) . ' via QRIS',
            ]);

            return redirect()->route('anggota.payments.show', $result['payment']->payment_code)
                ->with('success', 'QRIS berhasil dibuat. Silakan scan QR untuk pembayaran.');

        } catch (\Exception $e) {
            Log::error('PaymentController: Failed to create QRIS simpanan', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal membuat QRIS: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show payment page with QR code and status.
     *
     * GET /anggota/payments/{paymentCode}
     */
    public function show(string $paymentCode)
    {
        $payment = Payment::where('payment_code', $paymentCode)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Load related data based on type
        $payable = match ($payment->type) {
            Payment::TYPE_ANGSURAN => Angsuran::with('pembiayaan')->find($payment->payable_id),
            Payment::TYPE_SIMPANAN => \App\Models\SetoranSimpanan::find($payment->payable_id),
            Payment::TYPE_MARKETPLACE => \App\Models\Pesanan::with('items')->find($payment->payable_id),
            default => null,
        };

        return view('anggota.payments.show', compact('payment', 'payable'));
    }

    /**
     * AJAX: Check payment status.
     *
     * GET /anggota/payments/{paymentCode}/status
     */
    public function status(string $paymentCode)
    {
        $payment = Payment::where('payment_code', $paymentCode)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // If still pending, try to check from gateway (fallback if webhook failed)
        if ($payment->status === Payment::STATUS_PENDING) {
            try {
                $gatewayStatus = $this->paymentGateway->checkStatus($payment);

                if (isset($gatewayStatus['transaction_status'])) {
                    $mappedStatus = match ($gatewayStatus['transaction_status']) {
                        'capture', 'settlement' => Payment::STATUS_PAID,
                        'pending' => Payment::STATUS_PENDING,
                        'expire' => Payment::STATUS_EXPIRED,
                        'deny', 'cancel', 'failure' => Payment::STATUS_FAILED,
                        default => null,
                    };

                    // If gateway says PAID but our record is still pending, process it
                    // This is the fallback when webhook delivery fails
                    if ($mappedStatus === Payment::STATUS_PAID && $payment->status === Payment::STATUS_PENDING) {
                        Log::info('PaymentController: Gateway says PAID, processing via status check fallback', [
                            'payment_id' => $payment->id,
                        ]);

                        $payment->update([
                            'status' => Payment::STATUS_PAID,
                            'paid_at' => now(),
                            'gateway_response' => $gatewayStatus,
                            'webhook_received_at' => now(),
                            'notes' => trim(($payment->notes ?? '') . ' [processed via status check]'),
                        ]);

                        // Process business logic (same as webhook)
                        try {
                            $webhookController = app(\App\Http\Controllers\PaymentWebhookController::class);
                            $webhookController->processPaidPayment($payment);
                        } catch (\Exception $e) {
                            Log::error('PaymentController: Failed to process payment via fallback', [
                                'payment_id' => $payment->id,
                                'error' => $e->getMessage(),
                            ]);
                        }

                        // Reload to get updated data
                        $payment = $payment->fresh();
                    }
                }
            } catch (\Exception $e) {
                Log::warning('PaymentController: Status check failed', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'status' => $payment->status,
            'status_label' => $payment->status_label,
            'paid_at' => $payment->paid_at?->toIso8601String(),
            'expired_at' => $payment->expired_at?->toIso8601String(),
            'is_expired' => $payment->expired_at && $payment->expired_at->isPast(),
            'is_payable' => $payment->isPayable(),
        ]);
    }

    /**
     * Cancel a pending payment.
     *
     * POST /anggota/payments/{paymentCode}/cancel
     */
    public function cancel(string $paymentCode)
    {
        $payment = DB::transaction(function () use ($paymentCode) {
            $payment = Payment::where('payment_code', $paymentCode)
                ->where('user_id', auth()->id())
                ->where('status', Payment::STATUS_PENDING)
                ->lockForUpdate()
                ->firstOrFail();

            $payment->update([
                'status' => Payment::STATUS_CANCELLED,
                'notes' => 'Dibatalkan oleh pengguna',
            ]);

            if ($payment->type === Payment::TYPE_SIMPANAN) {
                SetoranSimpanan::whereKey($payment->payable_id)
                    ->where('user_id', $payment->user_id)
                    ->where('status', 'menunggu_verifikasi')
                    ->update([
                        'status' => 'ditolak',
                        'catatan_penolakan' => 'Pembayaran QRIS dibatalkan oleh pengguna.',
                    ]);
            }

            return $payment;
        });

        return redirect()->route('anggota.dashboard', ['tab' => 'simpanan'])
            ->with('info', 'Pembayaran ' . $payment->payment_code . ' telah dibatalkan.');
    }

    /**
     * Show payment history for the logged-in user.
     *
     * GET /anggota/payments/riwayat
     */
    public function history()
    {
        $payments = Payment::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('anggota.payments.history', compact('payments'));
    }
}

