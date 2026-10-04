<?php

namespace App\Http\Controllers;

use App\Models\InboxEntry;
use App\Models\PembayaranAngsuran;
use App\Models\PencairanDana;
use App\Models\RekeningKoperasi;
use App\Models\SetoranSimpanan;
use App\Models\TransaksiPembayaran;
use Illuminate\View\View;

class BendaharaDashboardController extends Controller
{
    public function index(): View
    {
        $verified = ['diverifikasi', 'selesai'];
        $cashIn = TransaksiPembayaran::whereIn('status', $verified)
            ->where('jenis_transaksi', '!=', 'pencairan')->sum('jumlah');
        $cashOut = TransaksiPembayaran::where('jenis_transaksi', 'pencairan')
            ->where('status', 'selesai')->sum('jumlah');

        $stats = [
            'siap_dicairkan' => PencairanDana::where('status', 'menunggu')
                ->whereHas('pembiayaan', fn ($q) => $q->where('status', 'disetujui'))->count(),
            'sedang_diproses' => PencairanDana::where('status', 'diproses')->count(),
            'pembayaran_pending' => PembayaranAngsuran::where('status', 'menunggu_verifikasi')->count(),
            'simpanan_pending' => SetoranSimpanan::where('status', 'menunggu_verifikasi')->count(),
            'mutasi_pending' => TransaksiPembayaran::where('status', 'menunggu_verifikasi')->count(),
            'rekening_aktif' => RekeningKoperasi::where('is_aktif', true)->count(),
            'kas_masuk' => $cashIn,
            'kas_keluar' => $cashOut,
            'saldo_bersih' => $cashIn - $cashOut,
            'masuk_hari_ini' => TransaksiPembayaran::whereIn('status', $verified)
                ->where('jenis_transaksi', '!=', 'pencairan')->whereDate('updated_at', today())->sum('jumlah'),
            'keluar_hari_ini' => TransaksiPembayaran::where('jenis_transaksi', 'pencairan')
                ->where('status', 'selesai')->whereDate('updated_at', today())->sum('jumlah'),
        ];

        $priorityDisbursements = PencairanDana::with(['pembiayaan.user', 'rekeningKoperasi'])
            ->whereIn('status', ['menunggu', 'diproses'])->oldest()->limit(5)->get();
        $recentMutations = TransaksiPembayaran::with(['user', 'rekening'])->latest()->limit(7)->get();
        $accounts = RekeningKoperasi::orderByDesc('is_aktif')->orderBy('nama_bank')->limit(5)->get();
        $notifications = InboxEntry::where('user_id', auth()->id())->latest()->limit(5)->get();

        return view('dashboard.bendahara.index', compact(
            'stats', 'priorityDisbursements', 'recentMutations', 'accounts', 'notifications'
        ));
    }
}
