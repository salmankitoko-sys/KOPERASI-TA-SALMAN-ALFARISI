<?php

namespace App\Http\Controllers\DPS;

use App\Http\Controllers\Controller;
use App\Models\AuditTemuan;
use App\Models\Pembiayaan;
use App\Support\NotificationHelper;
use App\Support\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TemuanController extends Controller
{
    /**
     * Daftar temuan audit.
     */
    public function index(Request $request)
    {
        Gate::authorize('dps.kelola-temuan');

        $query = AuditTemuan::with(['pembiayaan', 'produk', 'dibuatOleh', 'diverifikasiOleh']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tingkat_resiko')) {
            $query->where('tingkat_resiko', $request->tingkat_resiko);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_temuan', 'like', "%{$search}%")
                    ->orWhere('jenis_temuan', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        $list = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success'    => true,
            'data'       => $list->items(),
            'pagination' => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
            'stats' => [
                'dibuka'          => AuditTemuan::where('status', 'Dibuka')->count(),
                'ditindaklanjuti' => AuditTemuan::where('status', 'Ditindaklanjuti')->count(),
                'diverifikasi'    => AuditTemuan::where('status', 'Diverifikasi')->count(),
                'ditutup'         => AuditTemuan::where('status', 'Ditutup')->count(),
            ],
        ]);
    }

    /**
     * Autocomplete search for pembiayaan.
     */
    public function searchPembiayaan(Request $request)
    {
        Gate::authorize('dps.kelola-temuan');

        $search = $request->string('q')->trim();
        $results = Pembiayaan::with('user')
            ->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            })
            ->limit(10)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'label' => $p->kode . ' — ' . ($p->user?->name ?? '-') . ' (' . ucfirst($p->akad) . ')',
                'kode' => $p->kode,
                'nama' => $p->user?->name,
            ]);

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * Autocomplete search for produk.
     */
    public function searchProduk(Request $request)
    {
        Gate::authorize('dps.kelola-temuan');

        $search = $request->string('q')->trim();
        $results = \App\Models\Produk::with('toko')
            ->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhereHas('toko', fn ($tq) => $tq->where('nama_toko', 'like', "%{$search}%"));
            })
            ->limit(10)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'label' => $p->nama . ' — ' . ($p->toko?->nama_toko ?? '-'),
                'nama' => $p->nama,
            ]);

        return response()->json(['success' => true, 'data' => $results]);
    }

    /**
     * Detail temuan.
     */
    public function show($id)
    {
        Gate::authorize('dps.kelola-temuan');

        $temuan = AuditTemuan::with(['pembiayaan.user', 'produk.toko', 'dibuatOleh', 'diverifikasiOleh'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $temuan,
        ]);
    }

    /**
     * Buat temuan baru.
     */
    public function store(Request $request)
    {
        Gate::authorize('dps.kelola-temuan');

        $request->validate([
            'jenis_temuan'   => 'required|string|max:150',
            'kategori'       => 'nullable|string|max:150',
            'tingkat_resiko' => 'required|in:Rendah,Sedang,Tinggi,Kritis',
            'deskripsi'      => 'required|string',
            'rekomendasi'    => 'nullable|string',
            'pembiayaan_id'  => 'nullable|integer|exists:pembiayaan,id',
            'produk_id'      => 'nullable|integer|exists:produk,id',
        ]);

        // Generate nomor temuan
        $nomor = 'TMN-' . now()->format('Ymd') . '-' . strtoupper(uniqid());

        $temuan = AuditTemuan::create([
            'nomor_temuan'   => $nomor,
            'pembiayaan_id'  => $request->pembiayaan_id,
            'produk_id'      => $request->produk_id,
            'jenis_temuan'   => $request->jenis_temuan,
            'kategori'       => $request->kategori,
            'tingkat_resiko' => $request->tingkat_resiko,
            'deskripsi'      => $request->deskripsi,
            'rekomendasi'    => $request->rekomendasi,
            'status'         => 'Dibuka',
            'dibuat_oleh'    => auth()->id(),
            'tanggal_temuan' => now()->toDateString(),
        ]);

        // Notifikasi via NotificationService
        NotificationService::temuanAuditBaru($temuan);

        return response()->json([
            'success' => true,
            'message' => 'Temuan audit ' . $nomor . ' berhasil dibuat.',
            'data'    => $temuan->load(['pembiayaan', 'produk', 'dibuatOleh']),
        ], 201);
    }

    /**
     * Update temuan (hanya ubah status verifikasi, tanpa hapus).
     */
    public function update(Request $request, $id)
    {
        Gate::authorize('dps.kelola-temuan');

        $temuan = AuditTemuan::findOrFail($id);

        $request->validate([
            'status'       => 'required|in:Dibuka,Ditindaklanjuti,Diverifikasi,Ditutup',
            'rekomendasi'  => 'nullable|string',
            'deskripsi'    => 'nullable|string',
        ]);

        $data = [
            'status'          => $request->status,
            'rekomendasi'     => $request->rekomendasi ?? $temuan->rekomendasi,
            'deskripsi'       => $request->deskripsi ?? $temuan->deskripsi,
        ];

        // Jika diverifikasi / ditutup, catat verifikator
        if (in_array($request->status, ['Diverifikasi', 'Ditutup'])) {
            $data['diverifikasi_oleh'] = auth()->id();
        }

        // Jika ditutup, catat tanggal penutupan
        if ($request->status === 'Ditutup') {
            $data['tanggal_penutupan'] = now()->toDateString();
        }

        $statusLama = $temuan->status;
        $temuan->update($data);

        // Notifikasi perubahan status
        NotificationService::temuanStatusDiubah($temuan, $statusLama, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Status temuan ' . $temuan->nomor_temuan . ' menjadi ' . $request->status . '.',
            'data'    => $temuan->fresh(['pembiayaan', 'produk', 'dibuatOleh', 'diverifikasiOleh']),
        ]);
    }
}

