<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\InboxEntry;
use App\Models\LaporanPengawasan;
use App\Models\LaporanPengurus;
use App\Models\LaporanBendahara;
use App\Models\Pembiayaan;
use App\Models\Pesanan;
use App\Models\Simpanan;
use App\Models\TransaksiPembayaran;
use App\Models\User;
use Illuminate\View\View;

class KetuaDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'anggota' => User::where('role', User::ROLE_ANGGOTA)->count(),
            'anggota_aktif' => User::where('role', User::ROLE_ANGGOTA)->where('is_active', true)->count(),
            'total_simpanan' => (float) Simpanan::where('status', 'masuk')->sum('jumlah'),
            'pembiayaan_aktif' => (float) Pembiayaan::whereIn('status', ['disetujui', 'berjalan'])->sum('jumlah_pembiayaan'),
            'pengajuan_menunggu' => Pembiayaan::where('status', 'diajukan')->count(),
            'angsuran_tertunggak' => Angsuran::where('status', 'belum_bayar')->whereDate('jatuh_tempo', '<', today())->count(),
            'nilai_tunggakan' => (float) Angsuran::where('status', 'belum_bayar')->whereDate('jatuh_tempo', '<', today())->sum('jumlah_bayar'),
            'omzet_bulan_ini' => (float) Pesanan::where('status', 'selesai')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
        ];

        $anggota = [
            'baru_bulan_ini' => User::where('role', User::ROLE_ANGGOTA)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count(),
            'calon' => User::where('role', User::ROLE_ANGGOTA)->where('status', 'Calon')->count(),
            'nonaktif' => User::where('role', User::ROLE_ANGGOTA)->where('is_active', false)->count(),
            'terbaru' => User::where('role', User::ROLE_ANGGOTA)->latest('id')->limit(6)->get(),
        ];

        $keuangan = [
            'simpanan_pokok' => (float) Simpanan::where('status', 'masuk')->where('jenis', 'pokok')->sum('jumlah'),
            'simpanan_wajib' => (float) Simpanan::where('status', 'masuk')->where('jenis', 'wajib')->sum('jumlah'),
            'simpanan_sukarela' => (float) Simpanan::where('status', 'masuk')->where('jenis', 'sukarela')->sum('jumlah'),
            'pemasukan_bulan_ini' => (float) TransaksiPembayaran::whereIn('status', ['diverifikasi', 'selesai'])->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('jumlah'),
            'menunggu_verifikasi' => TransaksiPembayaran::where('status', 'menunggu_verifikasi')->count(),
            'transaksi_terbaru' => TransaksiPembayaran::with('user')->latest('id')->limit(6)->get(),
        ];

        $pembiayaan = [
            'total_pengajuan' => Pembiayaan::count(),
            'aktif' => Pembiayaan::whereIn('status', ['disetujui', 'berjalan'])->count(),
            'ditolak' => Pembiayaan::where('status', 'ditolak')->count(),
            'lunas' => Pembiayaan::where('status', 'lunas')->count(),
            'akad' => Pembiayaan::selectRaw('akad, COUNT(*) as total')->groupBy('akad')->pluck('total', 'akad'),
            'terbaru' => Pembiayaan::with('user')->latest('id')->limit(8)->get(),
        ];

        $tren = collect(range(5, 0))->map(function (int $month): array {
            $date = now()->subMonths($month);

            return [
                'label' => $date->locale('id')->translatedFormat('M'),
                'simpanan' => (float) Simpanan::where('status', 'masuk')->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->sum('jumlah'),
                'pembiayaan' => (float) Pembiayaan::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->sum('jumlah_pembiayaan'),
                'anggota' => User::where('role', User::ROLE_ANGGOTA)->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
            ];
        });

        $notifikasi = InboxEntry::where('user_id', auth()->id())->latest('id')->limit(8)->get();
        $notifCount = InboxEntry::where('user_id', auth()->id())->where('is_read', false)->count();
        $pembiayaanTerbaru = $pembiayaan['terbaru']->take(5);
        $laporanPengurus = LaporanPengurus::with(['pembuat', 'peninjau'])->where('status', '!=', 'draf')->latest('dikirim_pada')->get();
        $laporanBendahara = LaporanBendahara::with(['pembuat', 'peninjau'])->where('status', '!=', 'draf')->latest('dikirim_pada')->get();
        $laporanDps = LaporanPengawasan::with('dps')->where('status', 'Terbit')->latest('dikirim_pada')->get();

        return view('dashboard.ketua.index', compact('stats', 'anggota', 'keuangan', 'pembiayaan', 'tren', 'notifikasi', 'notifCount', 'pembiayaanTerbaru', 'laporanPengurus', 'laporanBendahara', 'laporanDps'));
    }
}
