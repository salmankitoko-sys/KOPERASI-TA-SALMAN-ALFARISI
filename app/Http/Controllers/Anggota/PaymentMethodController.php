<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Pembiayaan;
use App\Models\Payment;

class PaymentMethodController extends Controller
{
    /**
     * Show payment method selection for an angsuran.
     *
     * GET /anggota/angsuran/{id}/bayar
     */
    public function showAngsuranMethod(Angsuran $angsuran)
    {
        $angsuran->load('pembiayaan');

        // Authorization
        if ($angsuran->pembiayaan->user_id !== auth()->id()) {
            abort(403);
        }

        if ($angsuran->pembiayaan->status !== 'berjalan') {
            return back()->with('info', 'Pembayaran angsuran tersedia setelah pencairan pembiayaan selesai diverifikasi.');
        }

        if ($angsuran->status !== 'belum_bayar') {
            return back()->with('info', 'Angsuran ini sudah diproses pembayarannya.');
        }

        // Check if there's already a pending QRIS payment for this angsuran
        $existingPayment = Payment::where('type', Payment::TYPE_ANGSURAN)
            ->where('payable_id', $angsuran->id)
            ->where('status', Payment::STATUS_PENDING)
            ->where('expired_at', '>', now())
            ->first();

        return view('anggota.payments.method', compact('angsuran', 'existingPayment'));
    }
}