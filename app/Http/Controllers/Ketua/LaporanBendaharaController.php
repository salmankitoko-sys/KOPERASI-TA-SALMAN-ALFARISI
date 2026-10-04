<?php
namespace App\Http\Controllers\Ketua;
use App\Http\Controllers\Controller;
use App\Models\LaporanBendahara;
use App\Support\NotificationHelper;
use Illuminate\Http\{RedirectResponse, Request};
class LaporanBendaharaController extends Controller
{
    public function review(Request $request, LaporanBendahara $laporan): RedirectResponse
    {
        abort_unless($laporan->status === 'dikirim', 422);
        $data = $request->validate(['keputusan' => 'required|in:diterima,perlu_tindak_lanjut', 'catatan_ketua' => 'nullable|string|required_if:keputusan,perlu_tindak_lanjut']);
        $laporan->update(['status' => $data['keputusan'], 'catatan_ketua' => $data['catatan_ketua'] ?? null, 'ditinjau_oleh' => auth()->id(), 'ditinjau_pada' => now()]);
        NotificationHelper::sendToUser($laporan->pembuat, 'Hasil Peninjauan Laporan Keuangan', $laporan->judul.' berstatus '.str_replace('_', ' ', $data['keputusan']).'.');
        return redirect()->route('ketua.dashboard', ['tab' => 'laporan'])->with('success', 'Hasil peninjauan laporan Bendahara berhasil disimpan.');
    }
}
