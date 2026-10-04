<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Pembiayaan;
use App\Models\PencairanDana;
use App\Support\AkadSyariahChecklist;
use App\Support\NotificationHelper;
use App\Support\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PembiayaanController extends Controller
{
    /**
     * Daftar semua pembiayaan (dengan filter).
     */
    public function index(Request $request)
    {
        $query = Pembiayaan::with(['user', 'dokumen', 'toko', 'validatorDps']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by member name or kode
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by akad type
        if ($request->filled('akad')) {
            $query->where('akad', $request->akad);
        }

        // Filter by date range
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $pembiayaanList = $query->orderBy('created_at', 'desc')->paginate(15);
        $pembiayaanList->getCollection()->transform(function (Pembiayaan $pembiayaan) {
            $pembiayaan->tujuan_label = $this->tujuanLabel($pembiayaan->tujuan_pembiayaan);

            return $pembiayaan;
        });

        // Stats for the view
        $stats = [
            'total_pembiayaan' => Pembiayaan::sum('jumlah_pembiayaan'),
            'pengajuan_baru' => Pembiayaan::where('status', 'diajukan')->count(),
            'menunggu_validasi_dps' => Pembiayaan::where('status', 'diajukan')
                ->where('status_validasi_dps', 'menunggu')
                ->count(),
            'pembiayaan_aktif' => Pembiayaan::whereIn('status', ['disetujui', 'berjalan'])->sum('jumlah_pembiayaan'),
            'angsuran_hari_ini' => Angsuran::whereDate('created_at', today())->count(),
            'total_disetujui' => Pembiayaan::where('status', 'disetujui')->count(),
            'total_ditolak' => Pembiayaan::where('status', 'ditolak')->count(),
            'total_berjalan' => Pembiayaan::where('status', 'berjalan')->count(),
            'total_lunas' => Pembiayaan::where('status', 'lunas')->count(),
            'npf' => $this->calculateNpf(),
        ];

        // Koleksi untuk dropdown filter
        $statusList = ['diajukan', 'disetujui', 'ditolak', 'berjalan', 'lunas'];
        $akadList = ['murabahah', 'mudharabah', 'musyarakah', 'ijarah', 'qardh'];

        return response()->json([
            'pembiayaanList' => $pembiayaanList,
            'stats' => $stats,
            'statusList' => $statusList,
            'akadList' => $akadList,
        ]);
    }

    /**
     * Detail pembiayaan + detail akad.
     */
    public function show($id)
    {
        $pembiayaan = Pembiayaan::with(['user', 'toko', 'detail', 'dokumen', 'validatorDps', 'angsuran' => function ($q) {
            $q->orderBy('bulan_ke');
        }])->findOrFail($id);

        // Label status
        $statusLabels = [
            'diajukan' => 'Diajukan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'berjalan' => 'Berjalan',
            'lunas' => 'Lunas',
        ];

        // Label akad
        $akadLabels = [
            'murabahah' => 'Murabahah',
            'mudharabah' => 'Mudharabah',
            'musyarakah' => 'Musyarakah',
            'ijarah' => 'Ijarah',
            'qardh' => 'Qardh',
        ];

        $detailAkad = $pembiayaan->detail;

        // Data spesifik berdasarkan akad
        $detailData = [];
        if ($detailAkad) {
            if ($pembiayaan->akad === 'murabahah') {
                $detailData = [
                    'Harga Beli' => $detailAkad->harga_beli,
                    'Margin (%)' => $detailAkad->margin_persen.'%',
                    'Total Margin' => $detailAkad->margin,
                    'Harga Jual' => $detailAkad->harga_jual,
                    'Uang Muka (DP)' => $detailAkad->dp,
                    'Biaya Admin' => $detailAkad->biaya_admin,
                    'Angsuran/Bulan' => $detailAkad->angsuran_bulanan,
                    'Objek' => $detailAkad->objek,
                ];
            } elseif ($pembiayaan->akad === 'mudharabah') {
                $detailData = [
                    'Modal' => $detailAkad->modal,
                    'Nisbah Koperasi (%)' => $detailAkad->nisbah_koperasi.'%',
                    'Nisbah Anggota (%)' => $detailAkad->nisbah_anggota.'%',
                    'Estimasi Omzet' => $detailAkad->estimasi_omzet,
                    'Estimasi Biaya' => $detailAkad->estimasi_biaya,
                    'Estimasi Laba' => $detailAkad->estimasi_laba,
                    'Bagi Hasil Koperasi' => $detailAkad->bagi_hasil_koperasi,
                    'Bagi Hasil Anggota' => $detailAkad->bagi_hasil_anggota,
                ];
            } elseif ($pembiayaan->akad === 'musyarakah') {
                $detailData = [
                    'Modal Koperasi' => $detailAkad->modal,
                    'Modal Anggota' => $detailAkad->modal_anggota,
                    'Porsi Modal Koperasi' => $detailAkad->porsi_modal_koperasi.'%',
                    'Porsi Modal Anggota' => $detailAkad->porsi_modal_anggota.'%',
                    'Nisbah Koperasi (%)' => $detailAkad->nisbah_koperasi.'%',
                    'Nisbah Anggota (%)' => $detailAkad->nisbah_anggota.'%',
                    'Estimasi Omzet' => $detailAkad->estimasi_omzet,
                    'Estimasi Biaya' => $detailAkad->estimasi_biaya,
                    'Estimasi Laba' => $detailAkad->estimasi_laba,
                    'Bagi Hasil Koperasi' => $detailAkad->bagi_hasil_koperasi,
                    'Bagi Hasil Anggota' => $detailAkad->bagi_hasil_anggota,
                    'Objek' => $detailAkad->objek,
                ];
            } elseif ($pembiayaan->akad === 'ijarah') {
                $detailData = [
                    'Nilai Aset' => $detailAkad->nilai_aset,
                    'Ujrah/Bulan' => $detailAkad->ujrah_bulanan,
                    'Biaya Perawatan' => $detailAkad->biaya_perawatan,
                    'Opsi Beli' => $detailAkad->opsi_beli,
                    'Total Pembayaran' => $detailAkad->total_pembayaran,
                    'Objek' => $detailAkad->objek,
                ];
            } elseif ($pembiayaan->akad === 'qardh') {
                $detailData = [
                    'Pokok Pembiayaan' => $pembiayaan->jumlah_pembiayaan,
                    'Angsuran/Bulan' => $detailAkad->angsuran_bulanan ?? $pembiayaan->angsuran_bulanan,
                    'Objek' => $detailAkad->objek ?? '-',
                ];
            }
        }

        // Hitung progress angsuran
        $totalAngsuran = $pembiayaan->angsuran->count();
        $sudahDibayar = $pembiayaan->angsuran->where('status', 'dibayar')->count();
        $sisaAngsuran = $totalAngsuran - $sudahDibayar;
        $progressPct = $totalAngsuran > 0 ? round(($sudahDibayar / $totalAngsuran) * 100, 1) : 0;

        return response()->json([
            'pembiayaan' => $pembiayaan,
            'statusLabel' => $statusLabels[$pembiayaan->status] ?? $pembiayaan->status,
            'akadLabel' => $akadLabels[$pembiayaan->akad] ?? $pembiayaan->akad,
            'tujuanLabel' => $this->tujuanLabel($pembiayaan->tujuan_pembiayaan),
            'detailAkad' => $detailAkad,
            'detailData' => $detailData,
            'totalAngsuran' => $totalAngsuran,
            'sudahDibayar' => $sudahDibayar,
            'sisaAngsuran' => $sisaAngsuran,
            'progressPct' => $progressPct,
            'validasiDpsLabel' => $this->validasiDpsLabel($pembiayaan->status_validasi_dps),
        ]);
    }

    /**
     * Menyetujui pembiayaan.
     */
    public function approve($id)
    {
        $pencairanBaru = null;
        DB::beginTransaction();
        try {
            $pembiayaan = Pembiayaan::with(['detail', 'dokumen', 'angsuran'])
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pembiayaan->status !== 'diajukan') {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Pembiayaan sudah diproses sebelumnya (status: '.$pembiayaan->status.').',
                ], 400);
            }

            $validasiTerbaru = $pembiayaan->riwayatValidasiDps()->first();
            if ($pembiayaan->status_validasi_dps !== 'sesuai' || !$validasiTerbaru || $validasiTerbaru->hasil !== 'sesuai') {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Pembiayaan belum dapat disetujui. Akad harus diperiksa lengkap dan dinyatakan Sesuai Syariah oleh DPS.',
                ], 422);
            }

            if (!hash_equals($validasiTerbaru->snapshot_hash, AkadSyariahChecklist::hash($pembiayaan))) {
                $pembiayaan->update([
                    'status_validasi_dps' => 'menunggu',
                    'catatan_validasi_dps' => 'Data akad berubah setelah validasi DPS. Pemeriksaan ulang diperlukan.',
                ]);
                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Data akad berubah setelah validasi terakhir. DPS harus melakukan validasi ulang sebelum pembiayaan dapat disetujui.',
                ], 422);
            }

            $pembiayaan->update([
                'status' => 'disetujui',
                'tanggal_persetujuan' => today()->toDateString(),
            ]);

            // Notify via NotificationService
            NotificationService::pembiayaanDisetujui($pembiayaan);

            // Generate jadwal angsuran jika belum ada
            $existingAngsuran = Angsuran::where('pembiayaan_id', $pembiayaan->id)->count();
            if ($existingAngsuran === 0) {
                $this->generateAngsuran($pembiayaan);
            }

            $rekeningLengkap = $pembiayaan->bank_tujuan
                && $pembiayaan->no_rekening_tujuan
                && $pembiayaan->nama_pemilik_rekening;

            if ($rekeningLengkap) {
                $pencairan = PencairanDana::firstOrCreate(
                    ['pembiayaan_id' => $pembiayaan->id],
                    [
                        'nominal_pencairan' => $pembiayaan->jumlah_pembiayaan,
                        'bank_tujuan' => $pembiayaan->bank_tujuan,
                        'no_rekening_tujuan' => $pembiayaan->no_rekening_tujuan,
                        'nama_pemilik_rekening' => $pembiayaan->nama_pemilik_rekening,
                        'tanggal_pencairan' => $pembiayaan->tanggal_pencairan_diharapkan?->toDateString() ?? today()->toDateString(),
                        'status' => 'menunggu',
                        'catatan' => 'Dibuat otomatis setelah persetujuan Pengurus.',
                    ]
                );

                if ($pencairan->wasRecentlyCreated) {
                    $pencairanBaru = $pencairan;
                }
            }

            DB::commit();

            if ($pencairanBaru) {
                NotificationService::pencairanDiajukan($pencairanBaru);
            }

            $message = 'Pembiayaan '.$pembiayaan->kode.' berhasil disetujui. Jadwal angsuran telah dibuat.';
            $message .= $pencairanBaru
                ? ' Permintaan pencairan otomatis telah dikirim ke Bendahara.'
                : ' Data rekening tujuan belum tersedia; anggota perlu mengajukan pencairan secara manual.';

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui pembiayaan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menolak pembiayaan.
     */
    public function tolak(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'alasan_tolak' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pembiayaan = Pembiayaan::findOrFail($id);

        if ($pembiayaan->status !== 'diajukan') {
            return response()->json([
                'success' => false,
                'message' => 'Pembiayaan sudah diproses sebelumnya (status: '.$pembiayaan->status.').',
            ], 400);
        }            $pembiayaan->update([
                'status' => 'ditolak',
                'tanggal_persetujuan' => today()->toDateString(),
            ]);

            // Notify anggota via NotificationService
            NotificationService::pembiayaanDitolak($pembiayaan, $request->alasan_tolak);

            return response()->json([
                'success' => true,
                'message' => 'Pembiayaan '.$pembiayaan->kode.' ditolak.',
            ]);
    }

    /**
     * Jadwal angsuran suatu pembiayaan.
     */
    public function angsuran($id)
    {
        $pembiayaan = Pembiayaan::findOrFail($id);

        $angsuranList = Angsuran::where('pembiayaan_id', $id)
            ->orderBy('bulan_ke')
            ->get()
            ->map(function ($a) {
                $statusLabel = $a->status === 'dibayar' ? 'Dibayar' : 'Belum Dibayar';
                $terlambat = ($a->status === 'belum_bayar' && Carbon::parse($a->jatuh_tempo)->isPast());

                return [
                    'id' => $a->id,
                    'bulan_ke' => $a->bulan_ke,
                    'jatuh_tempo' => $a->jatuh_tempo,
                    'jumlah_bayar' => $a->jumlah_bayar,
                    'pokok' => $a->pokok,
                    'margin' => $a->margin,
                    'sisa_pokok' => $a->sisa_pokok,
                    'status' => $a->status,
                    'status_label' => $statusLabel,
                    'tanggal_bayar' => $a->tanggal_bayar,
                    'terlambat' => $terlambat,
                ];
            });

        $totalAngsuran = $angsuranList->count();
        $sudahDibayar = $angsuranList->where('status', 'dibayar')->count();
        $belumDibayar = $totalAngsuran - $sudahDibayar;
        $totalTerlambat = $angsuranList->where('terlambat', true)->count();

        return response()->json([
            'pembiayaan' => $pembiayaan,
            'angsuranList' => $angsuranList,
            'totalAngsuran' => $totalAngsuran,
            'sudahDibayar' => $sudahDibayar,
            'belumDibayar' => $belumDibayar,
            'totalTerlambat' => $totalTerlambat,
        ]);
    }

    /**
     * Riwayat pembayaran angsuran.
     */
    public function riwayatBayar($id)
    {
        $pembiayaan = Pembiayaan::findOrFail($id);

        $riwayat = Angsuran::where('pembiayaan_id', $id)
            ->where('status', 'dibayar')
            ->whereNotNull('tanggal_bayar')
            ->orderBy('tanggal_bayar', 'desc')
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'bulan_ke' => $a->bulan_ke,
                    'jumlah_bayar' => $a->jumlah_bayar,
                    'pokok' => $a->pokok,
                    'margin' => $a->margin,
                    'sisa_pokok' => $a->sisa_pokok,
                    'tanggal_bayar' => $a->tanggal_bayar,
                ];
            });

        $totalTelahDibayar = $riwayat->sum('jumlah_bayar');

        return response()->json([
            'pembiayaan' => $pembiayaan,
            'riwayat' => $riwayat,
            'totalRiwayat' => $riwayat->count(),
            'totalTelahDibayar' => $totalTelahDibayar,
        ]);
    }

    /**
     * Generate jadwal angsuran otomatis saat approve.
     */
    private function generateAngsuran(Pembiayaan $pembiayaan)
    {
        $detail = $pembiayaan->detail;
        $tenor = $pembiayaan->tenor;

        if ($pembiayaan->akad === 'murabahah') {
            // Untuk Murabahah: harga beli dan margin dibagi rata tanpa DP.
            $pokokTotal = $detail->harga_beli ?? $pembiayaan->jumlah_pembiayaan;
            $marginTotal = $detail->margin ?? 0;
            $totalBayar = $pokokTotal + $marginTotal;
            $angsuranPokok = $tenor > 0 ? floor($pokokTotal / $tenor) : 0;
            $angsuranMargin = $tenor > 0 ? floor($marginTotal / $tenor) : 0;
            $angsuranBulanan = $totalBayar > 0 && $tenor > 0 ? ceil($totalBayar / $tenor) : 0;

            $sisaPokok = $pokokTotal;
            $sisaMargin = $marginTotal;

            $jatuhTempo = Carbon::parse($pembiayaan->tanggal_persetujuan ?? now())->addMonth();

            for ($bulan = 1; $bulan <= $tenor; $bulan++) {
                $isLast = $bulan === $tenor;
                $pokok = $isLast ? $sisaPokok : min($angsuranPokok, $sisaPokok);
                $margin = $isLast ? $sisaMargin : min($angsuranMargin, $sisaMargin);
                $jumlahBayar = $pokok + $margin;
                $sisaPokok = max(0, $sisaPokok - $pokok);
                $sisaMargin = max(0, $sisaMargin - $margin);

                Angsuran::create([
                    'pembiayaan_id' => $pembiayaan->id,
                    'bulan_ke' => $bulan,
                    'jatuh_tempo' => $jatuhTempo->toDateString(),
                    'jumlah_bayar' => $jumlahBayar,
                    'pokok' => $pokok,
                    'margin' => $margin,
                    'sisa_pokok' => $sisaPokok,
                    'status' => 'belum_bayar',
                ]);

                $jatuhTempo->addMonth();
            }
        } elseif ($pembiayaan->akad === 'mudharabah' || $pembiayaan->akad === 'musyarakah') {
            // Untuk akad bagi hasil: estimasi bagi hasil per bulan dari detail akad.
            $bagiHasilKoperasi = $detail->bagi_hasil_koperasi ?? 0;
            $sisaPokok = $detail->modal ?? $pembiayaan->jumlah_pembiayaan;
            $angsuranPerBulan = $bagiHasilKoperasi;

            $jatuhTempo = Carbon::parse($pembiayaan->tanggal_persetujuan ?? now())->addMonth();

            for ($bulan = 1; $bulan <= $tenor; $bulan++) {
                $isLast = $bulan === $tenor;
                $bayar = $isLast ? ($angsuranPerBulan + $sisaPokok) : $angsuranPerBulan;
                $pokok = $isLast ? $sisaPokok : 0;
                $sisaPokok = $isLast ? 0 : $sisaPokok;

                Angsuran::create([
                    'pembiayaan_id' => $pembiayaan->id,
                    'bulan_ke' => $bulan,
                    'jatuh_tempo' => $jatuhTempo->toDateString(),
                    'jumlah_bayar' => $bayar,
                    'pokok' => $pokok,
                    'margin' => $bagiHasilKoperasi,
                    'sisa_pokok' => $sisaPokok,
                    'status' => 'belum_bayar',
                ]);

                $jatuhTempo->addMonth();
            }
        } elseif ($pembiayaan->akad === 'ijarah') {
            // Untuk Ijarah: ujrah + biaya perawatan per bulan
            $ujrah = $detail->ujrah_bulanan ?? 0;
            $rawat = $detail->biaya_perawatan ?? 0;
            $bayarPerBulan = $ujrah + $rawat;
            $sisaPokok = $detail->opsi_beli ?? 0;

            $jatuhTempo = Carbon::parse($pembiayaan->tanggal_persetujuan ?? now())->addMonth();

            for ($bulan = 1; $bulan <= $tenor; $bulan++) {
                $isLast = $bulan === $tenor;
                $bayar = $isLast ? ($bayarPerBulan + $sisaPokok) : $bayarPerBulan;
                $pokok = $isLast ? $sisaPokok : 0;
                $sisaPokok = $isLast ? 0 : $sisaPokok;

                Angsuran::create([
                    'pembiayaan_id' => $pembiayaan->id,
                    'bulan_ke' => $bulan,
                    'jatuh_tempo' => $jatuhTempo->toDateString(),
                    'jumlah_bayar' => $bayar,
                    'pokok' => $pokok,
                    'margin' => $ujrah,
                    'sisa_pokok' => $sisaPokok,
                    'status' => 'belum_bayar',
                ]);

                $jatuhTempo->addMonth();
            }
        } elseif ($pembiayaan->akad === 'qardh') {
            // Untuk Qardh: pokok dibagi rata, tidak ada margin
            $pokokTotal = $pembiayaan->jumlah_pembiayaan;
            $angsuranPerBulan = $tenor > 0 ? (int) floor($pokokTotal / $tenor) : 0;
            $sisaPokok = $pokokTotal;

            $jatuhTempo = Carbon::parse($pembiayaan->tanggal_persetujuan ?? now())->addMonth();

            for ($bulan = 1; $bulan <= $tenor; $bulan++) {
                $isLast = $bulan === $tenor;
                $bayar = $isLast ? $sisaPokok : min($angsuranPerBulan, $sisaPokok);
                $sisaPokok = max(0, $sisaPokok - $bayar);

                Angsuran::create([
                    'pembiayaan_id' => $pembiayaan->id,
                    'bulan_ke' => $bulan,
                    'jatuh_tempo' => $jatuhTempo->toDateString(),
                    'jumlah_bayar' => $bayar,
                    'pokok' => $bayar,
                    'margin' => 0,
                    'sisa_pokok' => $sisaPokok,
                    'status' => 'belum_bayar',
                ]);

                $jatuhTempo->addMonth();
            }
        }
    }

    /**
     * Hitung NPF (Non Performing Financing).
     */
    private function calculateNpf(): float
    {
        $total = Pembiayaan::whereIn('status', ['disetujui', 'berjalan'])->sum('jumlah_pembiayaan');
        $macet = Pembiayaan::where('status', 'ditolak')->sum('jumlah_pembiayaan');
        if ($total == 0) {
            return 0;
        }

        return round(($macet / $total) * 100, 2);
    }

    private function tujuanLabel(?string $tujuan): string
    {
        return [
            'modal_usaha' => 'Modal Usaha',
            'pembelian_barang' => 'Pembelian Barang',
            'pendidikan' => 'Pendidikan',
            'renovasi' => 'Renovasi',
            'kebutuhan_lainnya' => 'Kebutuhan Lainnya',
        ][$tujuan ?? ''] ?? 'Kebutuhan Lainnya';
    }

    private function validasiDpsLabel(?string $status): string
    {
        return [
            'menunggu' => 'Menunggu Validasi DPS',
            'sesuai' => 'Sesuai Syariah',
            'perlu_perbaikan' => 'Perlu Perbaikan',
            'tidak_sesuai' => 'Tidak Sesuai Syariah',
        ][$status ?? 'menunggu'] ?? 'Menunggu Validasi DPS';
    }
}
