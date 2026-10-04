<?php

namespace App\Http\Controllers\DPS;

use App\Http\Controllers\Controller;
use App\Models\LaporanPengawasan;
use App\Models\ValidasiAkadDps;
use App\Support\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class LaporanPengawasanController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('dps.kelola-laporan');
        $query = LaporanPengawasan::with('dps');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('periode')) $query->where('periode', $request->periode);
        $list = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $list->items(),
            'pagination' => [
                'current_page' => $list->currentPage(), 'last_page' => $list->lastPage(),
                'per_page' => $list->perPage(), 'total' => $list->total(),
            ],
            'stats' => [
                'total' => LaporanPengawasan::count(),
                'draf' => LaporanPengawasan::where('status', 'Draf')->count(),
                'terbit' => LaporanPengawasan::where('status', 'Terbit')->count(),
            ],
        ]);
    }

    public function show($id)
    {
        Gate::authorize('dps.kelola-laporan');
        return response()->json(['success' => true, 'data' => LaporanPengawasan::with('dps')->findOrFail($id)]);
    }

    public function generate(Request $request)
    {
        Gate::authorize('dps.kelola-laporan');
        $data = $request->validate([
            'periode' => 'required|in:Semester,Tahunan',
            'semester' => 'nullable|string|max:50',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'rekomendasi' => 'nullable|string|max:5000',
        ]);

        $validasi = ValidasiAkadDps::with(['pembiayaan.user', 'validator'])
            ->whereBetween('divalidasi_pada', [$data['periode_mulai'].' 00:00:00', $data['periode_selesai'].' 23:59:59'])
            ->orderBy('divalidasi_pada')
            ->get();

        $statistik = [
            'total' => $validasi->count(),
            'sesuai' => $validasi->where('hasil', 'sesuai')->count(),
            'perlu_perbaikan' => $validasi->where('hasil', 'perlu_perbaikan')->count(),
            'tidak_sesuai' => $validasi->where('hasil', 'tidak_sesuai')->count(),
        ];
        $statistik['persentase_sesuai'] = $statistik['total'] > 0
            ? round(($statistik['sesuai'] / $statistik['total']) * 100, 1) : 0;

        $snapshot = [
            'statistik' => $statistik,
            'akad' => $validasi->groupBy(fn ($item) => $item->pembiayaan?->akad ?? 'tidak diketahui')->map->count()->all(),
            'isu' => $validasi->whereIn('hasil', ['perlu_perbaikan', 'tidak_sesuai'])
                ->map(fn ($item) => $item->catatan_perbaikan ?: $item->kesimpulan)
                ->filter()->unique()->values()->all(),
            'rincian' => $validasi->map(fn ($item) => [
                'kode' => $item->pembiayaan?->kode ?? '-',
                'anggota' => $item->pembiayaan?->user?->name ?? '-',
                'akad' => $item->pembiayaan?->akad ?? '-',
                'hasil' => $item->hasil,
                'tanggal' => $item->divalidasi_pada?->format('d/m/Y') ?? '-',
                'validator' => $item->validator?->name ?? '-',
            ])->values()->all(),
        ];

        $ringkasan = "Pada periode {$data['periode_mulai']} sampai {$data['periode_selesai']}, DPS melakukan {$statistik['total']} validasi akad. "
            ."Sebanyak {$statistik['sesuai']} akad sesuai syariah, {$statistik['perlu_perbaikan']} perlu perbaikan, dan {$statistik['tidak_sesuai']} tidak sesuai.";
        $rekomendasi = ($data['rekomendasi'] ?? null) ?: ($statistik['perlu_perbaikan'] + $statistik['tidak_sesuai'] > 0
            ? 'Ketua agar memastikan pengurus menindaklanjuti seluruh catatan perbaikan sebelum proses pembiayaan dilanjutkan.'
            : 'Mempertahankan kelengkapan dokumen dan konsistensi pelaksanaan akad sesuai prinsip syariah.');

        $laporan = LaporanPengawasan::create([
            'periode' => $data['periode'], 'semester' => $data['semester'] ?? null,
            'periode_mulai' => $data['periode_mulai'], 'periode_selesai' => $data['periode_selesai'],
            'ringkasan' => $ringkasan, 'rekomendasi' => $rekomendasi, 'data_laporan' => $snapshot,
            'tanggal_publikasi' => today(), 'dikirim_pada' => now(), 'status' => 'Terbit', 'dibuat_oleh' => auth()->id(),
        ]);

        $path = 'laporan-pengawasan/laporan-dps-'.$laporan->id.'.pdf';
        Storage::disk('public')->put($path, Pdf::loadView('pdf.laporan-pengawasan-dps', ['laporan' => $laporan->load('dps')])->setPaper('a4')->output());
        $laporan->update(['file_laporan' => $path]);
        NotificationService::laporanDiterbitkan($laporan);

        return response()->json([
            'success' => true,
            'message' => 'Laporan otomatis berhasil dicetak dan dikirim kepada Ketua.',
            'data' => $laporan->fresh('dps'),
            'download_url' => route('laporan-pengawasan.pdf', $laporan),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('dps.kelola-laporan');
        $laporan = LaporanPengawasan::findOrFail($id);
        $data = $request->validate([
            'ringkasan' => 'nullable|string', 'rekomendasi' => 'nullable|string',
            'status' => 'nullable|in:Draf,Terbit',
        ]);
        $laporan->update($data);
        return response()->json(['success' => true, 'message' => 'Laporan berhasil diperbarui.', 'data' => $laporan->load('dps')]);
    }
}