<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\LaporanPengurus;
use App\Support\NotificationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPengurusController extends Controller
{
    public function index(): View
    {
        return view('pengurus.laporan.index', ['laporan' => LaporanPengurus::with('peninjau')->where('dibuat_oleh', auth()->id())->latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['judul' => 'required|string|max:150', 'periode_mulai' => 'required|date', 'periode_selesai' => 'required|date|after_or_equal:periode_mulai', 'ringkasan' => 'required|string', 'capaian' => 'nullable|string', 'kendala' => 'nullable|string', 'tindak_lanjut' => 'nullable|string']);
        $data['dibuat_oleh'] = auth()->id();
        LaporanPengurus::create($data);

        return back()->with('success', 'Draf laporan berhasil dibuat.');
    }

    public function update(Request $request, LaporanPengurus $laporan): RedirectResponse
    {
        abort_unless($laporan->dibuat_oleh === auth()->id() && in_array($laporan->status, ['draf', 'perlu_tindak_lanjut'], true), 403);
        $laporan->update($request->validate(['judul' => 'required|string|max:150', 'periode_mulai' => 'required|date', 'periode_selesai' => 'required|date|after_or_equal:periode_mulai', 'ringkasan' => 'required|string', 'capaian' => 'nullable|string', 'kendala' => 'nullable|string', 'tindak_lanjut' => 'nullable|string']));

        return back()->with('success', 'Laporan berhasil diperbarui.');
    }

    public function send(LaporanPengurus $laporan): RedirectResponse
    {
        abort_unless($laporan->dibuat_oleh === auth()->id() && in_array($laporan->status, ['draf', 'perlu_tindak_lanjut'], true), 403);
        $laporan->update(['status' => 'dikirim', 'dikirim_pada' => now(), 'ditinjau_oleh' => null, 'ditinjau_pada' => null]);
        NotificationHelper::sendToRole('ketua', 'Laporan Pengurus Dikirim', "{$laporan->judul} telah dikirim untuk ditinjau.", ['laporan_pengurus_id' => $laporan->id, 'tab' => 'laporan', 'pdf_url' => route('laporan-pengurus.pdf', $laporan)]);

        return back()->with('success', 'Laporan berhasil dikirim kepada Ketua Koperasi.');
    }
}
