<?php

namespace App\Http\Controllers;

use App\Models\AuditTemuan;
use App\Models\LaporanPengawasan;
use App\Models\OpiniSyariah;
use App\Models\Pembiayaan;
use App\Models\Produk;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DpsDashboardController extends Controller
{
    /**
     * Display the DPS dashboard.
     */
    public function index(): View
    {
        Gate::authorize('dps.view-akad');

        $stats = [
            'akad_aktif' => Pembiayaan::whereIn('status', ['disetujui', 'berjalan'])->count(),
            'akad_menunggu_validasi' => Pembiayaan::where('status', 'diajukan')
                ->where('status_validasi_dps', 'menunggu')
                ->count(),
            'akad_perlu_perbaikan' => Pembiayaan::where('status_validasi_dps', 'perlu_perbaikan')->count(),
            'produk_menunggu_review' => Produk::where('status', 'pending')->count(),
            'temuan_terbuka' => AuditTemuan::whereIn('status', ['Dibuka', 'Ditindaklanjuti'])->count(),
            'temuan_selesai' => AuditTemuan::where('status', 'Ditutup')->count(),
            'opini_bulan_ini' => OpiniSyariah::whereMonth('tanggal_opini', now()->month)
                ->whereYear('tanggal_opini', now()->year)
                ->count(),
            'laporan_semester' => LaporanPengawasan::where('status', 'Terbit')->count(),
        ];

        $aktivitas = collect();

        // Temuan baru
        AuditTemuan::with('dibuatOleh')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->each(function ($t) use ($aktivitas) {
                $aktivitas->push([
                    'icon' => 'temuan',
                    'warna' => 'bg-red-100 text-red-700',
                    'teks' => 'Temuan audit baru: '.$t->nomor_temuan.' ('.$t->jenis_temuan.')',
                    'waktu' => $t->created_at->diffForHumans(),
                    'tanggal' => $t->tanggal_temuan?->toDateString(),
                ]);
            });

        // Produk baru (menunggu review)
        Produk::with('toko')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->each(function ($p) use ($aktivitas) {
                $aktivitas->push([
                    'icon' => 'produk',
                    'warna' => 'bg-amber-100 text-amber-700',
                    'teks' => 'Produk menunggu review: '.$p->nama,
                    'waktu' => $p->created_at->diffForHumans(),
                    'tanggal' => $p->created_at->toDateString(),
                ]);
            });

        // Akad baru
        Pembiayaan::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->each(function ($p) use ($aktivitas) {
                $aktivitas->push([
                    'icon' => 'akad',
                    'warna' => 'bg-indigo-100 text-indigo-700',
                    'teks' => 'Akad baru: '.$p->kode.' ('.ucfirst($p->akad).')',
                    'waktu' => $p->created_at->diffForHumans(),
                    'tanggal' => $p->created_at->toDateString(),
                ]);
            });

        // Validasi akad terbaru oleh DPS
        Pembiayaan::with(['user', 'validatorDps'])
            ->whereNotNull('tanggal_validasi_dps')
            ->orderByDesc('tanggal_validasi_dps')
            ->limit(5)
            ->get()
            ->each(function ($p) use ($aktivitas) {
                $hasil = [
                    'sesuai' => 'sesuai syariah',
                    'perlu_perbaikan' => 'perlu perbaikan',
                    'tidak_sesuai' => 'tidak sesuai syariah',
                ][$p->status_validasi_dps] ?? $p->status_validasi_dps;

                $aktivitas->push([
                    'icon' => 'validasi',
                    'warna' => $p->status_validasi_dps === 'sesuai'
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-orange-100 text-orange-700',
                    'teks' => 'Validasi akad '.$p->kode.': '.$hasil,
                    'waktu' => $p->tanggal_validasi_dps?->diffForHumans() ?? '-',
                    'tanggal' => $p->tanggal_validasi_dps?->toDateString(),
                ]);
            });

        // Opini terakhir
        OpiniSyariah::with('dps')
            ->orderBy('tanggal_opini', 'desc')
            ->limit(5)
            ->get()
            ->each(function ($o) use ($aktivitas) {
                $aktivitas->push([
                    'icon' => 'opini',
                    'warna' => 'bg-emerald-100 text-emerald-700',
                    'teks' => 'Opini syariah: '.$o->nomor_opini.' → '.$o->hasil,
                    'waktu' => $o->created_at->diffForHumans(),
                    'tanggal' => $o->tanggal_opini?->toDateString(),
                ]);
            });

        $aktivitas = $aktivitas->sortByDesc('tanggal')->values()->take(10);

        // Grafik: temuan per bulan (6 bulan terakhir)
        $temuanPerBulan = collect(range(5, 0))->map(function ($i) {
            $bulan = now()->subMonths($i);

            return [
                'label' => $bulan->translatedFormat('M'),
                'total' => AuditTemuan::whereYear('tanggal_temuan', $bulan->year)
                    ->whereMonth('tanggal_temuan', $bulan->month)
                    ->count(),
            ];
        });

        // Grafik: status tindak lanjut
        $statusTindakLanjut = [
            'Dibuka' => AuditTemuan::where('status', 'Dibuka')->count(),
            'Ditindaklanjuti' => AuditTemuan::where('status', 'Ditindaklanjuti')->count(),
            'Diverifikasi' => AuditTemuan::where('status', 'Diverifikasi')->count(),
            'Ditutup' => AuditTemuan::where('status', 'Ditutup')->count(),
        ];

        return view('dashboard.dps.index', compact(
            'stats',
            'aktivitas',
            'temuanPerBulan',
            'statusTindakLanjut'
        ));
    }
}
