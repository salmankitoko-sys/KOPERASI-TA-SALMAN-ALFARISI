<?php

namespace App\Http\Controllers\DPS;

use App\Http\Controllers\Controller;
use App\Models\Pembiayaan;
use App\Models\SetoranSimpanan;
use App\Models\Simpanan;
use App\Models\TransaksiPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PengawasanController extends Controller
{
    /**
     * Ringkasan pengawasan operasional DPS. Seluruh data pada endpoint ini
     * bersifat baca-saja; tindak lanjut dicatat melalui temuan atau opini.
     */
    public function index(Request $request)
    {
        Gate::authorize('dps.view-operasional');

        $query = TransaksiPembayaran::with(['user', 'rekening'])
            ->latest('created_at');

        if ($request->filled('q')) {
            $search = $request->string('q')->trim();
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('judul', 'like', "%{$search}%")
                    ->orWhere('referensi', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_transaksi', $request->string('jenis'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $mutasi = $query->paginate(15)->withQueryString();

        $simpanan = Simpanan::query()
            ->where('status', 'masuk')
            ->selectRaw('jenis, COUNT(*) as total_transaksi, COALESCE(SUM(jumlah), 0) as nominal')
            ->groupBy('jenis')
            ->orderBy('jenis')
            ->get();

        $pembiayaan = Pembiayaan::query()
            ->selectRaw("akad, COUNT(*) as total_akad, COALESCE(SUM(jumlah_pembiayaan), 0) as nominal, SUM(CASE WHEN status_validasi_dps = 'sesuai' THEN 1 ELSE 0 END) as sesuai, SUM(CASE WHEN status_validasi_dps IN ('perlu_perbaikan', 'tidak_sesuai') THEN 1 ELSE 0 END) as perlu_tindak_lanjut")
            ->groupBy('akad')
            ->orderBy('akad')
            ->get();

        $layanan = TransaksiPembayaran::query()
            ->selectRaw('jenis_transaksi, COUNT(*) as total_transaksi, COALESCE(SUM(jumlah), 0) as nominal')
            ->groupBy('jenis_transaksi')
            ->orderBy('jenis_transaksi')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => [
                'saldo_simpanan' => (float) Simpanan::where('status', 'masuk')->sum('jumlah'),
                'setoran_menunggu' => SetoranSimpanan::where('status', 'menunggu_verifikasi')->count(),
                'pembiayaan_aktif' => Pembiayaan::whereIn('status', ['disetujui', 'berjalan'])->count(),
                'akad_menunggu_dps' => Pembiayaan::where('status_validasi_dps', 'menunggu')->count(),
                'perlu_tindak_lanjut' => Pembiayaan::whereIn('status_validasi_dps', ['perlu_perbaikan', 'tidak_sesuai'])->count(),
                'transaksi_menunggu' => TransaksiPembayaran::where('status', 'menunggu_verifikasi')->count(),
            ],
            'matriks' => [
                'simpanan' => $simpanan,
                'pembiayaan' => $pembiayaan,
                'layanan' => $layanan,
            ],
            'jenis_layanan' => TransaksiPembayaran::query()
                ->whereNotNull('jenis_transaksi')
                ->distinct()
                ->orderBy('jenis_transaksi')
                ->pluck('jenis_transaksi'),
            'data' => $mutasi->items(),
            'pagination' => [
                'current_page' => $mutasi->currentPage(),
                'last_page' => $mutasi->lastPage(),
                'per_page' => $mutasi->perPage(),
                'total' => $mutasi->total(),
                'from' => $mutasi->firstItem() ?? 0,
                'to' => $mutasi->lastItem() ?? 0,
            ],
        ]);
    }
}
