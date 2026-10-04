<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pembiayaan;
use App\Models\Angsuran;
use App\Models\Simpanan;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\InboxEntry;
use App\Models\TransaksiPembayaran;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PengurusDashboardController extends Controller
{
    /**
     * Display the pengurus dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_users'         => User::where('role', 'anggota')->count(),
            'anggota_baru'        => User::where('role', 'anggota')
                ->whereDate('created_at', today())
                ->count(),
            'pengajuan'           => Pembiayaan::whereDate('created_at', today())->count(),
            'angsuran'            => Angsuran::whereDate('created_at', today())->count(),
            'produk_baru'         => DB::table('produk')->whereDate('created_at', today())->count(),
            'total_simpanan'      => DB::table('simpanan')->sum('jumlah') ?? 0,
            'simpanan_pokok'      => DB::table('simpanan')->where('jenis', 'pokok')->sum('jumlah') ?? 0,
            'simpanan_wajib'      => DB::table('simpanan')->where('jenis', 'wajib')->sum('jumlah') ?? 0,
            'simpanan_sukarela'   => DB::table('simpanan')->where('jenis', 'sukarela')->sum('jumlah') ?? 0,
            'pembiayaan_aktif'    => Pembiayaan::whereIn('status', ['disetujui', 'berjalan'])
                ->sum('jumlah_pembiayaan'),
            'npf'                 => $this->calculateNpf(),
            'omzet_marketplace'   => DB::table('pesanan')->where('status', 'selesai')
                ->whereMonth('created_at', now()->month)
                ->sum('total') ?? 0,
            'penjual_aktif'       => DB::table('tokos')->where('status', 'aktif')->count(),
            'toko_pending'        => DB::table('tokos')->where('status', 'pending')->count(),
            'total_toko'          => DB::table('tokos')->count(),
            'transaksi_aktif'     => DB::table('pesanan')->whereIn('status', ['diproses', 'dikirim'])->count(),
        ];

        $menuSummary = [
            [
                'title' => 'Anggota',
                'tab' => 'anggota',
                'icon' => 'users',
                'accent' => 'indigo',
                'value' => (string) $stats['total_users'],
                'detail' => $stats['anggota_baru'] . ' baru hari ini',
                'caption' => 'Perkembangan anggota dan status',
            ],
            [
                'title' => 'Simpanan',
                'tab' => 'simpanan',
                'icon' => 'wallet',
                'accent' => 'emerald',
                'value' => $this->formatCurrency($stats['total_simpanan']),
                'detail' => 'Pokok ' . $this->formatCurrency($stats['simpanan_pokok']),
                'caption' => 'Saldo simpanan yang sudah masuk',
            ],
            [
                'title' => 'Pembiayaan',
                'tab' => 'pembiayaan',
                'icon' => 'cash',
                'accent' => 'amber',
                'value' => $this->formatCurrency($stats['pembiayaan_aktif']),
                'detail' => $stats['pengajuan'] . ' pengajuan hari ini',
                'caption' => 'Pembiayaan berjalan dan review',
            ],
            [
                'title' => 'Keuangan',
                'tab' => 'keuangan',
                'icon' => 'bank',
                'accent' => 'violet',
                'value' => $this->formatCurrency($stats['total_simpanan'] + Angsuran::where('status', 'dibayar')->sum('jumlah_bayar')),
                'detail' => 'Simpanan dan angsuran koperasi',
                'caption' => 'Pemasukan koperasi dari simpanan dan angsuran',
            ],
            [
                'title' => 'Marketplace',
                'tab' => 'marketplace',
                'icon' => 'shop',
                'accent' => 'sky',
                'value' => $this->formatCurrency($stats['omzet_marketplace']),
                'detail' => $stats['toko_pending'] . ' toko menunggu verifikasi',
                'caption' => 'Omzet bulan ini dan penjual aktif',
            ],
            [
                'title' => 'Laporan',
                'tab' => 'laporan',
                'icon' => 'chart',
                'accent' => 'violet',
                'value' => (string) $stats['transaksi_aktif'],
                'detail' => 'Transaksi aktif saat ini',
                'caption' => 'Data siap dipakai untuk laporan',
            ],
            [
                'title' => 'Notifikasi',
                'tab' => 'notifikasi',
                'icon' => 'bell',
                'accent' => 'rose',
                'value' => (string) max(0, Pembiayaan::where('status', 'diajukan')->count() + Produk::where('status', 'pending')->count()),
                'detail' => 'Butuh tindakan pengurus',
                'caption' => 'Review pembiayaan dan produk pending',
            ],
        ];

        $verifiedStatuses = ['diverifikasi', 'selesai'];
        $cashIn = (float) TransaksiPembayaran::whereIn('status', $verifiedStatuses)
            ->where('jenis_transaksi', '!=', 'pencairan')->sum('jumlah');
        $cashOut = (float) TransaksiPembayaran::where('jenis_transaksi', 'pencairan')
            ->where('status', 'selesai')->sum('jumlah');
        $monthIn = (float) TransaksiPembayaran::whereIn('status', $verifiedStatuses)
            ->where('jenis_transaksi', '!=', 'pencairan')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('jumlah');
        $monthOut = (float) TransaksiPembayaran::where('jenis_transaksi', 'pencairan')
            ->where('status', 'selesai')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('jumlah');

        $keuanganSummary = [
            'saldo_bersih' => $this->formatCurrency($cashIn - $cashOut),
            'kas_masuk' => $this->formatCurrency($cashIn),
            'kas_keluar' => $this->formatCurrency($cashOut),
            'menunggu_konfirmasi' => TransaksiPembayaran::where('status', 'menunggu_verifikasi')->count(),
        ];

        $keuanganTransactions = TransaksiPembayaran::with('user')
            ->where('jenis_transaksi', '!=', 'marketplace')->latest()->limit(16)->get()
            ->map(fn ($item) => [
                'type' => ucwords(str_replace('_', ' ', $item->jenis_transaksi)),
                'title' => $item->judul ?: ucwords(str_replace('_', ' ', $item->jenis_transaksi)),
                'name' => $item->user?->name ?? 'Pengguna',
                'amount' => $this->formatCurrency((float) $item->jumlah),
                'method' => $item->metode_pembayaran
                    ? ucwords(str_replace('_', ' ', $item->metode_pembayaran)) : '-',
                'status' => ucwords(str_replace('_', ' ', $item->status)),
                'date' => $item->created_at?->format('d M Y H:i') ?? '-',
                'reference' => $item->referensi ?: 'TRX-' . str_pad((string) $item->id, 6, '0', STR_PAD_LEFT),
            ]);

        $financialPosition = [
            ['label' => 'Saldo bersih koperasi', 'value' => $this->formatCurrency($cashIn - $cashOut), 'detail' => 'Kas masuk dikurangi kas keluar terverifikasi', 'type' => ($cashIn - $cashOut) >= 0 ? 'positive' : 'negative'],
            ['label' => 'Kas masuk bulan ini', 'value' => $this->formatCurrency($monthIn), 'detail' => now()->translatedFormat('F Y'), 'type' => 'positive'],
            ['label' => 'Kas keluar bulan ini', 'value' => $this->formatCurrency($monthOut), 'detail' => 'Pencairan yang sudah selesai', 'type' => 'negative'],
            ['label' => 'Perubahan kas bulan ini', 'value' => $this->formatCurrency($monthIn - $monthOut), 'detail' => 'Selisih kas masuk dan keluar periode berjalan', 'type' => ($monthIn - $monthOut) >= 0 ? 'positive' : 'negative'],
        ];

        $transactionStatuses = collect([
            'menunggu_verifikasi' => 'Menunggu verifikasi',
            'diverifikasi' => 'Diverifikasi',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ])->map(fn ($label, $status) => [
            'label' => $label,
            'count' => TransaksiPembayaran::where('status', $status)->count(),
            'amount' => $this->formatCurrency((float) TransaksiPembayaran::where('status', $status)->sum('jumlah')),
            'status' => $status,
        ])->values();

        $cashFlow = [
            ['label' => 'Total kas masuk terverifikasi', 'value' => $this->formatCurrency($cashIn), 'type' => 'inflow'],
            ['label' => 'Total pencairan selesai', 'value' => $this->formatCurrency($cashOut), 'type' => 'outflow'],
            ['label' => 'Kas masuk bulan berjalan', 'value' => $this->formatCurrency($monthIn), 'type' => 'inflow'],
            ['label' => 'Kas keluar bulan berjalan', 'value' => $this->formatCurrency($monthOut), 'type' => 'outflow'],
        ];

        $incomeComposition = TransaksiPembayaran::query()
            ->whereIn('status', $verifiedStatuses)
            ->where('jenis_transaksi', '!=', 'pencairan')
            ->where('jenis_transaksi', '!=', 'marketplace')
            ->select('jenis_transaksi', DB::raw('COUNT(*) as transaction_count'), DB::raw('SUM(jumlah) as total'))
            ->groupBy('jenis_transaksi')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($item) => [
                'label' => ucwords(str_replace('_', ' ', $item->jenis_transaksi)),
                'count' => (int) $item->transaction_count,
                'value' => $this->formatCurrency((float) $item->total),
                'percentage' => $cashIn > 0 ? round(((float) $item->total / $cashIn) * 100, 1) : 0,
            ]);

        $notifCount = Pembiayaan::where('status', 'diajukan')->count()
            + Produk::where('status', 'pending')->count()
            + $stats['toko_pending'];
        $chatCount = InboxEntry::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
        $inboxEntries = InboxEntry::where('user_id', auth()->id())
            ->latest('created_at')
            ->limit(6)
            ->get();

        $recentActivity = [
            'anggota' => User::where('role', 'anggota')
                ->latest('created_at')
                ->limit(4)
                ->get(),
            'simpanan' => Simpanan::with('user')
                ->latest('created_at')
                ->limit(4)
                ->get(),
            'pembiayaan' => Pembiayaan::with('user')
                ->latest('created_at')
                ->limit(4)
                ->get(),
            'marketplace' => Produk::with('toko.user')
                ->latest('created_at')
                ->limit(4)
                ->get(),
        ];

        $activityFeed = collect();

        foreach ($recentActivity['anggota'] as $item) {
            $activityFeed->push([
                'module' => 'anggota',
                'title' => ($item->name ?? 'Anggota baru') . ' bergabung',
                'description' => 'Profil anggota baru siap ditinjau.',
                'status' => 'Anggota baru',
                'tab' => 'anggota',
                'tone' => 'indigo',
                'time' => $item->created_at?->locale('id')->diffForHumans() ?? '-',
                'timestamp' => $item->created_at?->getTimestamp() ?? 0,
            ]);
        }

        foreach ($recentActivity['simpanan'] as $item) {
            $activityFeed->push([
                'module' => 'simpanan',
                'title' => 'Setoran ' . ucfirst($item->jenis ?? 'simpanan'),
                'description' => ($item->user?->name ?? 'Anggota') . ' · ' . $this->formatCurrency((float) $item->jumlah),
                'status' => ucfirst($item->status ?? 'masuk'),
                'tab' => 'simpanan',
                'tone' => 'emerald',
                'time' => $item->created_at?->locale('id')->diffForHumans() ?? '-',
                'timestamp' => $item->created_at?->getTimestamp() ?? 0,
            ]);
        }

        foreach ($recentActivity['pembiayaan'] as $item) {
            $activityFeed->push([
                'module' => 'pembiayaan',
                'title' => 'Pembiayaan ' . ucfirst($item->akad ?? 'syariah'),
                'description' => ($item->user?->name ?? 'Anggota') . ' · ' . $this->formatCurrency((float) $item->jumlah_pembiayaan),
                'status' => ucfirst($item->status ?? 'diajukan'),
                'tab' => 'pembiayaan',
                'tone' => 'amber',
                'time' => $item->created_at?->locale('id')->diffForHumans() ?? '-',
                'timestamp' => $item->created_at?->getTimestamp() ?? 0,
            ]);
        }

        foreach ($recentActivity['marketplace'] as $item) {
            $activityFeed->push([
                'module' => 'marketplace',
                'title' => $item->nama ?? $item->name ?? 'Produk marketplace',
                'description' => 'Produk dari ' . ($item->toko?->nama_toko ?? $item->toko?->user?->name ?? 'mitra koperasi'),
                'status' => ucfirst($item->status ?? 'pending'),
                'tab' => 'marketplace',
                'tone' => 'sky',
                'time' => $item->created_at?->locale('id')->diffForHumans() ?? '-',
                'timestamp' => $item->created_at?->getTimestamp() ?? 0,
            ]);
        }

        $activityFeed = $activityFeed
            ->sortByDesc('timestamp')
            ->take(12)
            ->values();

        $activityCounts = collect(['anggota', 'simpanan', 'pembiayaan', 'marketplace'])
            ->mapWithKeys(fn (string $module) => [$module => $activityFeed->where('module', $module)->count()]);

        $trendMonths = collect(range(5, 0))->map(
            fn (int $offset) => now()->startOfMonth()->subMonths($offset)
        );
        $trendStart = $trendMonths->first()->copy()->startOfMonth();

        $simpananByMonth = Simpanan::query()
            ->where('status', 'masuk')
            ->where('created_at', '>=', $trendStart)
            ->get(['jumlah', 'created_at'])
            ->groupBy(fn (Simpanan $item) => $item->created_at->format('Y-m'))
            ->map(fn ($items) => (float) $items->sum('jumlah'));

        $pembiayaanByMonth = Pembiayaan::query()
            ->whereIn('status', ['disetujui', 'berjalan', 'lunas'])
            ->where('created_at', '>=', $trendStart)
            ->get(['jumlah_pembiayaan', 'created_at'])
            ->groupBy(fn (Pembiayaan $item) => $item->created_at->format('Y-m'))
            ->map(fn ($items) => (float) $items->sum('jumlah_pembiayaan'));

        $marketplaceByMonth = DB::table('pesanan')
            ->where('status', 'selesai')
            ->where('created_at', '>=', $trendStart)
            ->get(['total', 'created_at'])
            ->groupBy(fn ($item) => Carbon::parse($item->created_at)->format('Y-m'))
            ->map(fn ($items) => (float) $items->sum('total'));

        $trendLabels = $trendMonths
            ->map(fn (Carbon $month) => $month->locale('id')->translatedFormat('M'))
            ->values();

        $trendData = [
            'simpanan' => [
                'label' => 'Simpanan masuk',
                'shortLabel' => 'Simpanan',
                'color' => '#10b981',
                'values' => $trendMonths->map(fn (Carbon $month) => $simpananByMonth->get($month->format('Y-m'), 0))->values(),
            ],
            'pembiayaan' => [
                'label' => 'Pembiayaan disetujui',
                'shortLabel' => 'Pembiayaan',
                'color' => '#6366f1',
                'values' => $trendMonths->map(fn (Carbon $month) => $pembiayaanByMonth->get($month->format('Y-m'), 0))->values(),
            ],
            'marketplace' => [
                'label' => 'Omzet marketplace',
                'shortLabel' => 'Marketplace',
                'color' => '#0ea5e9',
                'values' => $trendMonths->map(fn (Carbon $month) => $marketplaceByMonth->get($month->format('Y-m'), 0))->values(),
            ],
        ];

        $overdueInstallments = Angsuran::query()
            ->where('status', 'belum_bayar')
            ->whereDate('jatuh_tempo', '<', today())
            ->count();
        $pendingFinancing = Pembiayaan::where('status', 'diajukan')->count();
        $pendingSavings = Simpanan::where('status', 'pending')->count();
        $pendingProducts = Produk::where('status', 'pending')->count();

        $attentionItems = [
            [
                'label' => 'Pengajuan pembiayaan',
                'value' => $pendingFinancing,
                'description' => 'Menunggu keputusan pengurus',
                'tab' => 'pembiayaan',
                'tone' => $pendingFinancing > 0 ? 'amber' : 'emerald',
            ],
            [
                'label' => 'Setoran simpanan',
                'value' => $pendingSavings,
                'description' => 'Menunggu verifikasi masuk',
                'tab' => 'simpanan',
                'tone' => $pendingSavings > 0 ? 'sky' : 'emerald',
            ],
            [
                'label' => 'Angsuran terlambat',
                'value' => $overdueInstallments,
                'description' => 'Melewati tanggal jatuh tempo',
                'tab' => 'pembiayaan',
                'tone' => $overdueInstallments > 0 ? 'rose' : 'emerald',
            ],
            [
                'label' => 'Moderasi marketplace',
                'value' => $pendingProducts + $stats['toko_pending'],
                'description' => $pendingProducts . ' produk, ' . $stats['toko_pending'] . ' toko',
                'tab' => 'marketplace',
                'tone' => ($pendingProducts + $stats['toko_pending']) > 0 ? 'violet' : 'emerald',
            ],
        ];

        $attentionTotal = collect($attentionItems)->sum('value');

        // Add data for sub-panels
        $anggotaList = User::where('role', 'anggota')
            ->withSum(['simpanan as simpanan_pokok' => function ($q) {
                $q->where('jenis', 'pokok')->where('status', 'masuk');
            }], 'jumlah')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $simpananList = Simpanan::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pembiayaanList = Pembiayaan::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pembiayaanAll = Pembiayaan::with(['user', 'detail', 'angsuran'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $produkModerasi = Produk::with('toko.user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $tokoVerifikasi = Toko::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $tokoMarketplace = Toko::withCount(['produk', 'pesanan'])
            ->with('user')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'aktif' THEN 1 ELSE 2 END")
            ->latest('updated_at')
            ->limit(12)
            ->get();

        return view('dashboard.pengurus.index', compact(
            'stats', 'menuSummary', 'activityFeed', 'activityCounts', 'notifCount', 'chatCount', 'inboxEntries',
            'anggotaList', 'simpananList', 'pembiayaanList', 'pembiayaanAll', 'produkModerasi',
            'tokoVerifikasi', 'tokoMarketplace',
            'keuanganSummary', 'keuanganTransactions', 'financialPosition', 'transactionStatuses', 'cashFlow', 'incomeComposition',
            'trendLabels', 'trendData', 'attentionItems', 'attentionTotal'
        ));
    }

    /**
     * Calculate NPF (Non Performing Financing) ratio.
     */
    private function calculateNpf(): float
    {
        $total  = Pembiayaan::whereIn('status', ['disetujui', 'berjalan'])->sum('jumlah_pembiayaan');
        $macet  = Pembiayaan::where('status', 'ditolak')->sum('jumlah_pembiayaan');
        if ($total == 0) return 0;
        return round(($macet / $total) * 100, 2);
    }

    private function formatCurrency(float|int $value): string
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
