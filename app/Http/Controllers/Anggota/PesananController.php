<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Support\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $pesananList = Pesanan::where('pembeli_id', $userId)
            ->with(['items', 'toko'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('anggota.pesanan.index', compact('pesananList'));
    }

    public function markReceived($id): RedirectResponse
    {
        $pesanan = Pesanan::where('pembeli_id', auth()->id())->findOrFail($id);

        if (!in_array($pesanan->status, ['dikirim', 'dikemas'], true)) {
            return back()->with('error', 'Pesanan belum dapat dikonfirmasi diterima.');
        }

        $pesanan->update([
            'status' => 'selesai',
            'status_pembayaran' => $pesanan->status_pembayaran === 'menunggu'
                ? 'terverifikasi'
                : $pesanan->status_pembayaran,
        ]);

        // Notify penjual + pengurus that pesanan is received
        NotificationService::pesananDiterima($pesanan);

        return back()->with('success', 'Pesanan berhasil dikonfirmasi diterima.');
    }

    /**
     * AJAX: Check payment status of an order.
     *
     * GET /pesanan-saya/{id}/payment-status
     */
    public function paymentStatus($id): JsonResponse
    {
        $pesanan = Pesanan::where('pembeli_id', auth()->id())->findOrFail($id);

        return response()->json([
            'status' => $pesanan->status,
            'status_pembayaran' => $pesanan->status_pembayaran,
        ]);
    }

    /**
     * Upload bukti transfer untuk pembayaran transfer manual.
     *
     * POST /pesanan-saya/{id}/upload-bukti
     */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'bukti_transfer.required' => 'Pilih bukti transfer yang akan diupload.',
            'bukti_transfer.image' => 'File harus berupa gambar.',
            'bukti_transfer.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'bukti_transfer.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $pesanan = Pesanan::where('pembeli_id', auth()->id())->findOrFail($id);

        // Hanya untuk metode transfer_manual
        if ($pesanan->metode_pembayaran !== 'transfer_manual') {
            return back()->with('error', 'Upload bukti transfer hanya tersedia untuk metode transfer manual.');
        }

        // Simpan bukti transfer
        $path = $request->file('bukti_transfer')->store('bukti-transfer', 'public');
        $pesanan->update(['bukti_transfer' => $path]);

        // Notify penjual bahwa bukti transfer diterima
        NotificationService::buktiTransferDiterima($pesanan);

        return back()->with('success', 'Bukti transfer berhasil diupload. Penjual akan segera memverifikasi.');
    }
}
