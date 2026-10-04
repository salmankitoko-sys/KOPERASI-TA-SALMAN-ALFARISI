<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\LaporanPengurus;
use App\Support\NotificationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LaporanPengurusController extends Controller
{
    public function review(Request $request, LaporanPengurus $laporan): RedirectResponse
    {
        abort_unless($laporan->status === 'dikirim', 422);
        $data = $request->validate(['keputusan' => 'required|in:diterima,perlu_tindak_lanjut', 'catatan_ketua' => 'nullable|string|required_if:keputusan,perlu_tindak_lanjut']);
        $laporan->update(['status' => $data['keputusan'], 'catatan_ketua' => $data['catatan_ketua'] ?? null, 'ditinjau_oleh' => auth()->id(), 'ditinjau_pada' => now()]);
        NotificationHelper::sendToUser($laporan->pembuat, 'Hasil Peninjauan Laporan', "Laporan {$laporan->judul} berstatus ".str_replace('_', ' ', $data['keputusan']).'. File laporan tersedia untuk diunduh.', ['laporan_pengurus_id' => $laporan->id, 'tab' => 'laporan', 'pdf_url' => route('laporan-pengurus.pdf', $laporan)]);

        return redirect()->route('ketua.dashboard', ['tab' => 'laporan'])->with('success', 'Hasil peninjauan laporan berhasil disimpan.');
    }
}
