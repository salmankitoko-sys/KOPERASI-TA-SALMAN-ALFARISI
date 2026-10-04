<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Toko;
use App\Support\NotificationService;
use Illuminate\Http\Request;

class LapakPesananController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $lapak = Toko::where('user_id', $userId)->first();

        if (!$lapak) {
            return redirect()->route('anggota.lapak.index')
                ->with('error', 'Anda harus memiliki toko terlebih dahulu.');
        }

        $pesananMasuk = Pesanan::where('toko_id', $lapak->id)
            ->with(['items', 'pembeli'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Statistik
        $statistik = (object) [
            'menunggu' => Pesanan::where('toko_id', $lapak->id)->where('status', 'menunggu')->count(),
            'dikemas'  => Pesanan::where('toko_id', $lapak->id)->where('status', 'dikemas')->count(),
            'dikirim'  => Pesanan::where('toko_id', $lapak->id)->where('status', 'dikirim')->count(),
            'selesai'  => Pesanan::where('toko_id', $lapak->id)->where('status', 'selesai')->count(),
        ];

        return view('anggota.lapak.pesanan.index', compact('pesananMasuk', 'lapak', 'statistik'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,dikemas,dikirim,selesai,batal',
        ]);

        $lapak = Toko::where('user_id', auth()->id())->firstOrFail();

        $pesanan = Pesanan::where('toko_id', $lapak->id)->findOrFail($id);
        $statusBaru = $request->status;
        $statusLama = $pesanan->status;

        // Penjual bebas mengubah status pesanan tanpa perlu verifikasi pengurus
        $pesanan->update([
            'status' => $statusBaru,
        ]);

        // Notify pembeli about status change
        NotificationService::pesananStatusDiubah($pesanan, $statusLama, $statusBaru);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Penjual mengonfirmasi pembayaran transfer manual.
     *
     * POST /anggota/lapak/pesanan/{id}/konfirmasi-pembayaran
     */
    public function confirmPayment($id)
    {
        $lapak = Toko::where('user_id', auth()->id())->firstOrFail();

        $pesanan = Pesanan::where('toko_id', $lapak->id)->findOrFail($id);

        // Hanya untuk metode transfer_manual yang belum terverifikasi
        if ($pesanan->metode_pembayaran !== 'transfer_manual') {
            return back()->with('error', 'Konfirmasi pembayaran hanya tersedia untuk transfer manual.');
        }

        if (in_array($pesanan->status_pembayaran, ['terverifikasi', 'lunas'], true)) {
            return back()->with('info', 'Pembayaran sudah terverifikasi.');
        }

        $pesanan->update([
            'status_pembayaran' => 'terverifikasi',
        ]);

        // Notify pembeli bahwa pembayaran terverifikasi
        NotificationService::pembayaranDikonfirmasi($pesanan);

        return back()->with('success', 'Pembayaran telah dikonfirmasi. Pesanan siap diproses.');
    }
}
