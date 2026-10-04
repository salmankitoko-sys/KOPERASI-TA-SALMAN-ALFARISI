<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\Payment;
use App\Models\Pembiayaan;
use App\Models\SetoranSimpanan;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();
        $totals = Simpanan::where('user_id', $userId)
            ->where('status', 'masuk')
            ->selectRaw("COALESCE(SUM(CASE WHEN jenis = 'pokok' THEN jumlah ELSE 0 END), 0) pokok, COALESCE(SUM(CASE WHEN jenis = 'wajib' THEN jumlah ELSE 0 END), 0) wajib, COALESCE(SUM(CASE WHEN jenis = 'sukarela' THEN jumlah ELSE 0 END), 0) sukarela")
            ->first();

        $pendingQuery = Payment::where('user_id', $userId)
            ->where('type', Payment::TYPE_SIMPANAN)
            ->where('status', Payment::STATUS_PENDING)
            ->where('expired_at', '>', now());
        $pembayaranSimpananPending = (clone $pendingQuery)->latest()->first();

        $simpanan = (object) [
            'pokok' => (float) ($totals->pokok ?? 0),
            'wajib' => (float) ($totals->wajib ?? 0),
            'sukarela' => (float) ($totals->sukarela ?? 0),
            'total' => (float) (($totals->pokok ?? 0) + ($totals->wajib ?? 0) + ($totals->sukarela ?? 0)),
            'pending' => (float) $pendingQuery->sum('amount'),
        ];

        $riwayatSimpanan = SetoranSimpanan::where('user_id', $userId)
            ->whereIn('jenis_simpanan', ['pokok', 'wajib', 'sukarela'])
            ->latest()->take(8)->get()->map(fn ($item) => (object) [
                'jenis' => $item->jenis_simpanan,
                'jumlah' => $item->nominal,
                'tanggal' => $item->tanggal_setor,
                'status' => match ($item->status) {
                    'diverifikasi' => 'masuk',
                    'ditolak' => 'ditolak',
                    default => 'pending',
                },
            ]);

        $pembiayaanAktif = Pembiayaan::where('user_id', $userId)
            ->whereIn('status', ['diajukan', 'disetujui', 'berjalan'])
            ->with(['detail', 'angsuran', 'dokumen', 'toko'])
            ->latest()
            ->get()
            ->map(function (Pembiayaan $pembiayaan) {
                $angsuranBelum = $pembiayaan->angsuran
                    ->where('status', 'belum_bayar')
                    ->sortBy('bulan_ke');
                $angsuranTerbayar = $pembiayaan->angsuran
                    ->where('status', 'dibayar');
                $pokokTerbayar = (float) $angsuranTerbayar->sum('pokok');

                if ($pokokTerbayar <= 0 && $angsuranTerbayar->isNotEmpty()) {
                    $pokokTerbayar = (float) $pembiayaan->jumlah_pembiayaan
                        / max((int) $pembiayaan->tenor, 1)
                        * $angsuranTerbayar->count();
                }

                $sisaPokok = max((float) $pembiayaan->jumlah_pembiayaan - $pokokTerbayar, 0);
                $pembiayaan->jatuhTempo = $angsuranBelum->first()?->jatuh_tempo;
                $pembiayaan->tagihanBerikutnya = (float) ($angsuranBelum->first()?->jumlah_bayar ?? 0);
                $pembiayaan->sisaTagihan = $sisaPokok;
                $pembiayaan->sisaAngsuran = $sisaPokok;
                $pembiayaan->sisaPokok = $sisaPokok;
                $pembiayaan->totalTerbayar = (float) $angsuranTerbayar->sum('jumlah_bayar');
                $pembiayaan->angsuranLunas = $angsuranTerbayar->count();
                $pembiayaan->progressPersen = (int) $pembiayaan->tenor > 0
                    ? round($pembiayaan->angsuranLunas / (int) $pembiayaan->tenor * 100, 1)
                    : 0;
                $pembiayaan->status_label = [
                    'diajukan' => 'Diajukan',
                    'disetujui' => 'Disetujui',
                    'berjalan' => 'Berjalan',
                ][$pembiayaan->status] ?? $pembiayaan->status;
                $pembiayaan->akad_label = [
                    'murabahah' => 'Murabahah',
                    'mudharabah' => 'Mudharabah',
                    'musyarakah' => 'Musyarakah',
                    'ijarah' => 'Ijarah',
                    'qardh' => 'Qardh',
                ][$pembiayaan->akad] ?? ucfirst($pembiayaan->akad);
                $pembiayaan->tujuan_label = [
                    'modal_usaha' => 'Modal Usaha',
                    'pembelian_barang' => 'Pembelian Barang',
                    'pendidikan' => 'Pendidikan',
                    'renovasi' => 'Renovasi',
                    'kebutuhan_lainnya' => 'Kebutuhan Lainnya',
                ][$pembiayaan->tujuan_pembiayaan] ?? 'Kebutuhan Lainnya';

                return $pembiayaan;
            });

        $pembiayaanBerjalan = $pembiayaanAktif->whereIn('status', ['disetujui', 'berjalan']);
        $pembiayaanSummary = (object) [
            'total_aktif' => $pembiayaanBerjalan->count(),
            'total_plafon' => (float) $pembiayaanBerjalan->sum('jumlah_pembiayaan'),
            'total_sisa_pokok' => (float) $pembiayaanBerjalan->sum('sisaPokok'),
            'total_angsuran_bulanan' => (float) $pembiayaanBerjalan->sum('angsuran_bulanan'),
            'total_tagihan_mendatang' => (float) $pembiayaanBerjalan->sum('tagihanBerikutnya'),
            'jatuh_tempo_terdekat' => $pembiayaanBerjalan
                ->pluck('jatuhTempo')
                ->filter()
                ->sort()
                ->first(),
        ];

        $notifikasiAngsuran = Angsuran::whereHas('pembiayaan', function ($query) use ($userId) {
            $query->where('user_id', $userId)->whereIn('status', ['disetujui', 'berjalan']);
        })
            ->where('status', 'belum_bayar')
            ->whereBetween('jatuh_tempo', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->with('pembiayaan')
            ->orderBy('jatuh_tempo')
            ->get()
            ->map(fn (Angsuran $angsuran) => (object) [
                'pesan' => 'Angsuran ke-'.$angsuran->bulan_ke.' ('.($angsuran->pembiayaan->kode ?? '-').') jatuh tempo '.
                    Carbon::parse($angsuran->jatuh_tempo)->translatedFormat('d F Y').' — Rp '.number_format((float) $angsuran->jumlah_bayar, 0, ',', '.'),
            ]);

        return view('dashboard.anggota.index', [
            'simpanan' => $simpanan,
            'riwayatSimpanan' => $riwayatSimpanan,
            'pembayaranSimpananPending' => $pembayaranSimpananPending,
            'pembiayaanAktif' => $pembiayaanAktif,
            'pembiayaanSummary' => $pembiayaanSummary,
            'bagiHasil' => (object) ['estimasi' => 0, 'periode' => null],
            'notifikasiAngsuran' => $notifikasiAngsuran,
            'lapak' => null,
            'pembiayaanUsahaAktif' => null,
            'skorKredit' => null,
            'inbox' => collect(),
            'produkList' => collect(),
            'produkSummary' => (object) ['total' => 0, 'aktif' => 0, 'stok_menipis' => 0],
            'tokoSummary' => (object) ['total_produk' => 0, 'total_pesanan' => 0, 'omzet_selesai' => 0],
            'pesananMasuk' => collect(),
            'statistikPesananMasuk' => (object) ['total' => 0, 'baru' => 0, 'diproses' => 0, 'selesai' => 0],
            'jumlahNotif' => 0,
            'alurStatus' => 'belum_verifikasi',
            'angsuranList' => collect(),
            'userId' => $userId,
            'rupiah' => fn ($value) => 'Rp '.number_format((float) ($value ?? 0), 0, ',', '.'),
        ]);
    }
}
