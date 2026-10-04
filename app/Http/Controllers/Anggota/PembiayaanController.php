<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\DetailAkad;
use App\Models\Pembiayaan;
use App\Models\PembiayaanDokumen;
use App\Models\SkorKredit;
use App\Models\Toko;
use App\Support\NotificationHelper;
use App\Support\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PembiayaanController extends Controller
{
    public function index()
    {
        $pembiayaanAktif = Pembiayaan::where('user_id', auth()->id())
            ->whereIn('status', ['diajukan', 'disetujui', 'berjalan'])
            ->with(['detail', 'angsuran', 'dokumen', 'toko'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($p) {
                // Cari angsuran pertama yang belum dibayar.
                // sisa_pokok adalah saldo setelah satu baris angsuran, jadi tidak boleh dijumlahkan.
                $angsuranBelum = $p->angsuran
                    ->where('status', 'belum_bayar')
                    ->sortBy('bulan_ke');

                $angsuranTerbayar = $p->angsuran
                    ->where('status', 'dibayar');

                $angsuranNext = $angsuranBelum->first();
                $pokokTerbayar = $this->hitungPokokTerbayar($p, $angsuranTerbayar);
                $sisaPokok = max((float) $p->jumlah_pembiayaan - $pokokTerbayar, 0);

                $p->jatuhTempo = $angsuranNext?->jatuh_tempo;
                $p->tagihanBerikutnya = (float) ($angsuranNext?->jumlah_bayar ?? 0);
                $p->sisaTagihan = $sisaPokok;
                $p->sisaAngsuran = $sisaPokok;
                $p->sisaPokok = $sisaPokok;
                $p->totalTerbayar = (float) $angsuranTerbayar->sum('jumlah_bayar');
                $p->angsuranLunas = $angsuranTerbayar->count();

                // Label status dalam bahasa Indonesia
                $statusLabels = [
                    'diajukan' => 'Diajukan',
                    'disetujui' => 'Disetujui',
                    'ditolak' => 'Ditolak',
                    'berjalan' => 'Berjalan',
                    'lunas' => 'Lunas',
                ];
                $p->status_label = $statusLabels[$p->status] ?? $p->status;

                // Label akad
                $akadLabels = [
                    'murabahah' => 'Murabahah',
                    'mudharabah' => 'Mudharabah',
                    'musyarakah' => 'Musyarakah',
                    'ijarah' => 'Ijarah',
                    'qardh' => 'Qardh',
                ];
                $p->akad_label = $akadLabels[$p->akad] ?? $p->akad;
                $p->tujuan_label = $this->tujuanLabel($p->tujuan_pembiayaan);

                return $p;
            });

        // Notifikasi angsuran yang jatuh tempo dalam 7 hari ke depan
        $notifikasiAngsuran = Angsuran::whereHas('pembiayaan', function ($q) {
            $q->where('user_id', auth()->id())
                ->whereIn('status', ['disetujui', 'berjalan']);
        })
            ->where('status', 'belum_bayar')
            ->whereBetween('jatuh_tempo', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->with('pembiayaan')
            ->orderBy('jatuh_tempo')
            ->get()
            ->map(function ($a) {
                return (object) [
                    'pesan' => 'Angsuran ke-'.$a->bulan_ke.' ('.($a->pembiayaan->kode ?? '-').') jatuh tempo '.Carbon::parse($a->jatuh_tempo)->translatedFormat('d F Y').' — Rp '.number_format($a->jumlah_bayar, 0, ',', '.'),
                    'jatuh_tempo' => $a->jatuh_tempo,
                    'jumlah' => $a->jumlah_bayar,
                ];
            });

        $rupiah = fn ($v) => 'Rp '.number_format($v ?? 0, 0, ',', '.');
        $lapak = Toko::where('user_id', auth()->id())->first();
        $skorKredit = SkorKredit::where('user_id', auth()->id())
            ->terbaru()
            ->first();

        return view('anggota.pembiayaan.index', [
            'title' => 'Pembiayaan & Angsuran',
            'description' => 'Status pembiayaan aktif, jadwal angsuran, dan akad berjalan.',
            'pembiayaanAktif' => $pembiayaanAktif,
            'notifikasiAngsuran' => $notifikasiAngsuran,
            'rupiah' => $rupiah,
            'lapak' => $lapak,
            'skorKredit' => $skorKredit,
            'tujuanOptions' => $this->tujuanOptions(),
        ]);
    }

    public function create()
    {
        $response = $this->index();

        return $response->with([
            'title' => 'Ajukan Pembiayaan',
            'description' => 'Form pengajuan pembiayaan online.',
            'openPengajuan' => true,
        ]);
    }

    public function downloadFormulir()
    {
        $candidates = [
            public_path('templates/Formulir_Pengajuan_Pembiayaan.pdf'),
            public_path('templates/Formulir_Pengajuan_Pembiayaan.docx'),
            public_path('templates/Formulir_Pengajuan_Pembiayaan.doc'),
            public_path('templates/formulir-pengajuan-pembiayaan.docx'),
            public_path('templates/formulir-pengajuan-pembiayaan.pdf'),
            public_path('templates/formulir-pengajuan-pembiayaan.doc'),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return response()->download($path);
            }
        }

        return redirect()
            ->route('anggota.pembiayaan.index')
            ->with('error', 'Template formulir belum tersedia. Letakkan file formulir di public/templates/formulir-pengajuan-pembiayaan.docx atau .pdf.');
    }

    public function store(Request $request)
    {
        $this->normalizePembiayaanInput($request);

        $validator = Validator::make($request->all(), [
            'jenis_akad' => 'required|string|in:Murabahah,Mudharabah,Musyarakah,Ijarah,Qardh',
            'tujuan_pembiayaan' => 'required|string|in:'.implode(',', array_keys($this->tujuanOptions())),
            'tenor_final' => 'required|integer|min:1',
            'total_kewajiban' => 'required|numeric|min:0',
            'nilai_utama' => 'required|numeric|min:0',
            'rencana_penggunaan_dana' => 'nullable|string|max:2000',
            'estimasi_omzet_usaha' => 'nullable|numeric|min:0|max:99999999999999',
            'bank_tujuan' => 'required|string|max:255',
            'no_rekening_tujuan' => 'required|string|max:100',
            'nama_pemilik_rekening' => 'required|string|max:255',
            'tanggal_pencairan_diharapkan' => 'required|date|after_or_equal:today',
            // Murabahah
            'm_harga_beli' => 'nullable|numeric|min:0',
            'm_margin_persen' => 'nullable|numeric|min:0',
            'm_tenor' => 'nullable|integer|min:1|max:240',
            'm_objek' => 'nullable|string|max:255',
            // Mudharabah
            'd_modal' => 'nullable|numeric|min:0',
            'd_nisbah_koperasi' => 'nullable|numeric|min:0|max:100',
            'd_nisbah_anggota' => 'nullable|numeric|min:0|max:100',
            'd_omzet' => 'nullable|numeric|min:0',
            'd_biaya' => 'nullable|numeric|min:0',
            // Musyarakah
            's_modal_koperasi' => 'nullable|numeric|min:0',
            's_modal_anggota' => 'nullable|numeric|min:0',
            's_nisbah_koperasi' => 'nullable|numeric|min:0|max:100',
            's_nisbah_anggota' => 'nullable|numeric|min:0|max:100',
            's_omzet' => 'nullable|numeric|min:0',
            's_biaya' => 'nullable|numeric|min:0',
            's_usaha' => 'nullable|string|max:255',
            // Ijarah
            'i_nilai_aset' => 'nullable|numeric|min:0',
            'i_ujrah' => 'nullable|numeric|min:0',
            'i_rawat' => 'nullable|numeric|min:0',
            'i_opsi_persen' => 'nullable|numeric|min:0',
            'i_objek' => 'nullable|string|max:255',
            // Dokumen kelengkapan
            'formulir_pengajuan' => 'required|file|mimes:pdf,doc,docx|max:8192',
        ]);

        $validator->after(function ($validator) use ($request) {
            $akad = $request->input('jenis_akad');
            $tujuan = $request->input('tujuan_pembiayaan');

            if ($akad === 'Murabahah') {
                if ((float) $request->input('m_harga_beli', 0) <= 0) {
                    $validator->errors()->add('m_harga_beli', 'Harga beli barang wajib diisi untuk akad Murabahah.');
                }
                if ((int) $request->input('tenor_final', 0) > 240) {
                    $validator->errors()->add('m_tenor', 'Jangka waktu Murabahah maksimal 240 bulan.');
                }
                if (! $request->filled('m_objek')) {
                    $validator->errors()->add('m_objek', 'Objek pembiayaan wajib diisi untuk akad Murabahah.');
                }
            }

            if ($akad === 'Mudharabah') {
                $this->validateNisbah($validator, $request, 'd_nisbah_koperasi', 'd_nisbah_anggota');
                if ((float) $request->input('d_modal', 0) <= 0) {
                    $validator->errors()->add('d_modal', 'Modal koperasi wajib diisi untuk akad Mudharabah.');
                }
                if ((float) $request->input('d_biaya', 0) > (float) $request->input('d_omzet', 0)) {
                    $validator->errors()->add('d_biaya', 'Estimasi biaya tidak boleh melebihi estimasi omzet.');
                }
            }

            if ($akad === 'Musyarakah') {
                $this->validateNisbah($validator, $request, 's_nisbah_koperasi', 's_nisbah_anggota');
                if ((float) $request->input('s_modal_koperasi', 0) <= 0) {
                    $validator->errors()->add('s_modal_koperasi', 'Porsi modal koperasi wajib diisi untuk akad Musyarakah.');
                }
                if ((float) $request->input('s_modal_anggota', 0) <= 0) {
                    $validator->errors()->add('s_modal_anggota', 'Porsi modal anggota wajib diisi untuk akad Musyarakah.');
                }
                if ((float) $request->input('s_biaya', 0) > (float) $request->input('s_omzet', 0)) {
                    $validator->errors()->add('s_biaya', 'Estimasi biaya tidak boleh melebihi estimasi omzet.');
                }
                if (! $request->filled('s_usaha')) {
                    $validator->errors()->add('s_usaha', 'Nama/jenis usaha wajib diisi untuk akad Musyarakah.');
                }
            }

            if ($akad === 'Ijarah') {
                if ((float) $request->input('i_nilai_aset', 0) <= 0) {
                    $validator->errors()->add('i_nilai_aset', 'Nilai aset wajib diisi untuk akad Ijarah.');
                }
                if ((float) $request->input('i_ujrah', 0) <= 0) {
                    $validator->errors()->add('i_ujrah', 'Ujrah bulanan wajib diisi untuk akad Ijarah.');
                }
                if (! $request->filled('i_objek')) {
                    $validator->errors()->add('i_objek', 'Objek sewa wajib diisi untuk akad Ijarah.');
                }
            }

            if ($akad === 'Qardh') {
                if ((float) $request->input('q_pokok', 0) <= 0) {
                    $validator->errors()->add('q_pokok', 'Pokok pembiayaan wajib diisi untuk akad Qardh.');
                }
                if ((int) $request->input('q_tenor', 0) <= 0) {
                    $validator->errors()->add('q_tenor', 'Tenor wajib diisi untuk akad Qardh.');
                }
            }

            if ($tujuan === 'modal_usaha') {
                $lapak = Toko::where('user_id', auth()->id())->first();

                if (! $lapak) {
                    $validator->errors()->add('tujuan_pembiayaan', 'Modal usaha hanya bisa diajukan setelah anggota memiliki toko.');
                } elseif ($lapak->status !== 'aktif') {
                    $validator->errors()->add('tujuan_pembiayaan', 'Toko harus aktif sebelum mengajukan modal usaha.');
                }

                if (! $request->filled('rencana_penggunaan_dana')) {
                    $validator->errors()->add('rencana_penggunaan_dana', 'Rencana penggunaan dana wajib diisi untuk modal usaha.');
                }

                if ((float) $request->input('estimasi_omzet_usaha', 0) <= 0) {
                    $validator->errors()->add('estimasi_omzet_usaha', 'Estimasi omzet usaha wajib diisi untuk modal usaha.');
                }

                $pengajuanAktif = Pembiayaan::where('user_id', auth()->id())
                    ->where('tujuan_pembiayaan', 'modal_usaha')
                    ->whereIn('status', ['diajukan', 'disetujui', 'berjalan'])
                    ->exists();

                if ($pengajuanAktif) {
                    $validator->errors()->add('tujuan_pembiayaan', 'Masih ada pembiayaan modal usaha yang aktif atau menunggu proses.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->route('anggota.pembiayaan.index')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $akad = $data['jenis_akad'];
        $tujuan = $data['tujuan_pembiayaan'];
        $tenor = (int) $data['tenor_final'];
        $tanggal = now();
        $lapak = $tujuan === 'modal_usaha'
            ? Toko::where('user_id', auth()->id())->where('status', 'aktif')->first()
            : null;

        // --- Generate kode otomatis ---
        $todayCount = Pembiayaan::whereDate('created_at', today())->count();
        $kode = 'PBJ-'.now()->format('Ymd').'-'.str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);

        // --- Tentukan jumlah_pembiayaan & angsuran_bulanan ---
        $jumlahPembiayaan = 0;
        $angsuranBulanan = (int) ($data['nilai_utama'] ?? 0);
        $objekPembiayaan = '';

        if ($akad === 'Murabahah') {
            $jumlahPembiayaan = (int) ($data['m_harga_beli'] ?? 0);
            $objekPembiayaan = $data['m_objek'] ?? '';
        } elseif ($akad === 'Mudharabah') {
            $jumlahPembiayaan = (int) ($data['d_modal'] ?? 0);
            $objekPembiayaan = 'Modal usaha mudharabah';
        } elseif ($akad === 'Musyarakah') {
            $jumlahPembiayaan = (int) ($data['s_modal_koperasi'] ?? 0);
            $objekPembiayaan = $data['s_usaha'] ?? 'Pembiayaan usaha musyarakah';
        } elseif ($akad === 'Ijarah') {
            $jumlahPembiayaan = (int) ($data['i_nilai_aset'] ?? 0);
            $objekPembiayaan = $data['i_objek'] ?? '';
        } elseif ($akad === 'Qardh') {
            $jumlahPembiayaan = (int) ($data['q_pokok'] ?? 0);
            $objekPembiayaan = $data['q_objek'] ?? 'Pinjaman qardh';
        }

        $storedPaths = [];

        DB::beginTransaction();
        try {
            // 1. Simpan ke tabel pembiayaan
            $pembiayaan = Pembiayaan::create([
                'user_id' => auth()->id(),
                'toko_id' => $lapak?->id,
                'kode' => $kode,
                'akad' => strtolower($akad),
                'tujuan_pembiayaan' => $tujuan,
                'objek_pembiayaan' => $objekPembiayaan,
                'rencana_penggunaan_dana' => $data['rencana_penggunaan_dana'] ?? null,
                'estimasi_omzet_usaha' => $tujuan === 'modal_usaha' ? ($data['estimasi_omzet_usaha'] ?? null) : null,
                'jumlah_pembiayaan' => $jumlahPembiayaan,
                'tenor' => $tenor,
                'angsuran_bulanan' => $angsuranBulanan,
                'tanggal_pengajuan' => $tanggal->toDateString(),
                'bank_tujuan' => $data['bank_tujuan'],
                'no_rekening_tujuan' => $data['no_rekening_tujuan'],
                'nama_pemilik_rekening' => $data['nama_pemilik_rekening'],
                'tanggal_pencairan_diharapkan' => $data['tanggal_pencairan_diharapkan'],
                'status' => 'diajukan',
            ]);

            // 2. Simpan ke tabel detail_akad
            $detailData = ['pembiayaan_id' => $pembiayaan->id];

            if ($akad === 'Murabahah') {
                $beli = (int) ($data['m_harga_beli'] ?? 0);
                $marginPct = (float) ($data['m_margin_persen'] ?? 0);
                $margin = (int) round($beli * $marginPct / 100);

                $detailData = array_merge($detailData, [
                    'objek' => $data['m_objek'] ?? '',
                    'tenor' => $tenor,
                    'harga_beli' => $beli,
                    'harga_jual' => $beli + $margin,
                    'margin_persen' => $marginPct,
                    'margin' => $margin,
                    'dp' => 0,
                    'biaya_admin' => 0,
                    'angsuran_bulanan' => $angsuranBulanan,
                ]);
            } elseif ($akad === 'Mudharabah') {
                $modal = (int) ($data['d_modal'] ?? 0);
                $nk = (float) ($data['d_nisbah_koperasi'] ?? 0);
                $na = (float) ($data['d_nisbah_anggota'] ?? 0);
                $omzet = (int) ($data['d_omzet'] ?? 0);
                $biaya = (int) ($data['d_biaya'] ?? 0);
                $laba = $omzet - $biaya;

                $detailData = array_merge($detailData, [
                    'objek' => 'Modal usaha mudharabah',
                    'tenor' => $tenor,
                    'modal' => $modal,
                    'nisbah_koperasi' => $nk,
                    'nisbah_anggota' => $na,
                    'estimasi_omzet' => $omzet,
                    'estimasi_biaya' => $biaya,
                    'estimasi_laba' => max(0, $laba),
                    'bagi_hasil_koperasi' => (int) round(max(0, $laba) * $nk / 100),
                    'bagi_hasil_anggota' => (int) round(max(0, $laba) * $na / 100),
                ]);
            } elseif ($akad === 'Musyarakah') {
                $modalKoperasi = (int) ($data['s_modal_koperasi'] ?? 0);
                $modalAnggota = (int) ($data['s_modal_anggota'] ?? 0);
                $totalModal = $modalKoperasi + $modalAnggota;
                $nk = (float) ($data['s_nisbah_koperasi'] ?? 0);
                $na = (float) ($data['s_nisbah_anggota'] ?? 0);
                $omzet = (int) ($data['s_omzet'] ?? 0);
                $biaya = (int) ($data['s_biaya'] ?? 0);
                $laba = $omzet - $biaya;

                $detailData = array_merge($detailData, [
                    'objek' => $data['s_usaha'] ?? 'Usaha musyarakah',
                    'tenor' => $tenor,
                    'modal' => $modalKoperasi,
                    'modal_anggota' => $modalAnggota,
                    'porsi_modal_koperasi' => $totalModal > 0 ? round($modalKoperasi / $totalModal * 100, 2) : 0,
                    'porsi_modal_anggota' => $totalModal > 0 ? round($modalAnggota / $totalModal * 100, 2) : 0,
                    'nisbah_koperasi' => $nk,
                    'nisbah_anggota' => $na,
                    'estimasi_omzet' => $omzet,
                    'estimasi_biaya' => $biaya,
                    'estimasi_laba' => max(0, $laba),
                    'bagi_hasil_koperasi' => (int) round(max(0, $laba) * $nk / 100),
                    'bagi_hasil_anggota' => (int) round(max(0, $laba) * $na / 100),
                ]);
            } elseif ($akad === 'Ijarah') {
                $aset = (int) ($data['i_nilai_aset'] ?? 0);
                $ujrah = (int) ($data['i_ujrah'] ?? 0);
                $rawat = (int) ($data['i_rawat'] ?? 0);
                $opsiPct = (float) ($data['i_opsi_persen'] ?? 0);
                $opsiBeli = (int) round($aset * $opsiPct / 100);
                $total = ($ujrah * $tenor) + ($rawat * $tenor) + $opsiBeli;

                $detailData = array_merge($detailData, [
                    'objek' => $data['i_objek'] ?? '',
                    'tenor' => $tenor,
                    'nilai_aset' => $aset,
                    'ujrah_bulanan' => $ujrah,
                    'biaya_perawatan' => $rawat,
                    'opsi_beli' => $opsiBeli,
                    'total_pembayaran' => $total,
                ]);
            } elseif ($akad === 'Qardh') {
                $pokok = (int) ($data['q_pokok'] ?? 0);
                $angsuranPerBulan = $tenor > 0 ? (int) ceil($pokok / $tenor) : 0;

                $detailData = array_merge($detailData, [
                    'objek' => $data['q_objek'] ?? 'Pinjaman qardh',
                    'tenor' => $tenor,
                    'angsuran_bulanan' => $angsuranPerBulan,
                ]);
            }

            DetailAkad::create($detailData);
            $storedPaths = $this->storeDokumenPengajuan($request, $pembiayaan);

            // 3. Simpan jadwal angsuran berdasarkan nilai yang telah divalidasi server
            if ($akad === 'Murabahah') {
                $jadwal = $this->buildJadwalMurabahah(
                    (int) ($data['m_harga_beli'] ?? 0),
                    (float) ($data['m_margin_persen'] ?? 0),
                    $tenor
                );
                $this->storeJadwalMurabahah($pembiayaan, $jadwal, $tanggal);
            } elseif ($akad === 'Qardh') {
                $jadwal = $this->buildJadwalQardh(
                    (int) ($data['q_pokok'] ?? 0),
                    $tenor
                );
                $this->storeJadwalQardh($pembiayaan, $jadwal, $tanggal);
            }

            DB::commit();

            NotificationService::pembiayaanDiajukan($pembiayaan);

            return redirect()
                ->route('anggota.dashboard')
                ->with('success', 'Pengajuan pembiayaan '.$kode.' ('.$akad.') berhasil dikirim. Menunggu validasi DPS dan review pengurus.');

        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return redirect()
                ->route('anggota.dashboard')
                ->withInput()
                ->with('error', 'Gagal mengirim pengajuan: '.$e->getMessage());
        }
    }

    private function normalizePembiayaanInput(Request $request): void
    {
        $akadLabels = [
            'murabahah' => 'Murabahah',
            'mudharabah' => 'Mudharabah',
            'musyarakah' => 'Musyarakah',
            'ijarah' => 'Ijarah',
            'qardh' => 'Qardh',
        ];

        $akad = $akadLabels[strtolower((string) $request->input('jenis_akad', 'murabahah'))] ?? 'Murabahah';
        $tenor = (int) $request->input('tenor_final', 0);
        $nilaiUtama = (float) $request->input('nilai_utama', 0);
        $total = (float) $request->input('total_kewajiban', 0);

        if ($akad === 'Murabahah') {
            $beli = (float) $request->input('m_harga_beli', 0);
            $marginPct = (float) $request->input('m_margin_persen', 0);
            $tenor = max($tenor, (int) $request->input('m_tenor', 0));

            $margin = round($beli * $marginPct / 100);
            $totalAngsuran = $beli + $margin;
            $nilaiUtama = $tenor > 0 ? ceil($totalAngsuran / $tenor) : $nilaiUtama;
            $total = $totalAngsuran;
        } elseif ($akad === 'Mudharabah') {
            $tenor = max($tenor, (int) $request->input('d_tenor', 0));
            $laba = max(0, (float) $request->input('d_omzet', 0) - (float) $request->input('d_biaya', 0));
            $nilaiUtama = round($laba * (float) $request->input('d_nisbah_koperasi', 0) / 100);
            $total = $nilaiUtama * $tenor;
        } elseif ($akad === 'Musyarakah') {
            $tenor = max($tenor, (int) $request->input('s_tenor', 0));
            $laba = max(0, (float) $request->input('s_omzet', 0) - (float) $request->input('s_biaya', 0));
            $nilaiUtama = round($laba * (float) $request->input('s_nisbah_koperasi', 0) / 100);
            $total = $nilaiUtama * $tenor;
        } elseif ($akad === 'Ijarah') {
            $tenor = max($tenor, (int) $request->input('i_tenor', 0));
            $ujrah = (float) $request->input('i_ujrah', 0);
            $rawat = (float) $request->input('i_rawat', 0);
            $aset = (float) $request->input('i_nilai_aset', 0);
            $opsi = round($aset * (float) $request->input('i_opsi_persen', 0) / 100);
            $nilaiUtama = $ujrah + $rawat;
            $total = ($nilaiUtama * $tenor) + $opsi;
        } elseif ($akad === 'Qardh') {
            $tenor = max($tenor, (int) $request->input('q_tenor', 0));
            $pokok = (float) $request->input('q_pokok', 0);
            // Qardh: total kewajiban = pokok, tidak ada margin
            $nilaiUtama = $tenor > 0 ? ceil($pokok / $tenor) : 0;
            $total = $pokok;
        }

        $request->merge([
            'jenis_akad' => $akad,
            'tujuan_pembiayaan' => $request->input('tujuan_pembiayaan', 'kebutuhan_lainnya'),
            'tenor_final' => $tenor,
            'nilai_utama' => max(0, $nilaiUtama),
            'total_kewajiban' => max(0, $total),
        ]);
    }

    private function buildJadwalMurabahah(int $hargaBeli, float $marginPersen, int $tenor): array
    {
        if ($tenor <= 0) {
            return [];
        }

        $pokok = $hargaBeli;
        $margin = (int) round($hargaBeli * $marginPersen / 100);
        $pokokBulanan = (int) floor($pokok / $tenor);
        $marginBulanan = (int) floor($margin / $tenor);
        $sisaPokok = $pokok;
        $sisaMargin = $margin;
        $jadwal = [];

        for ($bulan = 1; $bulan <= min($tenor, 240); $bulan++) {
            $isLast = $bulan === $tenor;
            $pokokBayar = $isLast ? $sisaPokok : min($pokokBulanan, $sisaPokok);
            $marginBayar = $isLast ? $sisaMargin : min($marginBulanan, $sisaMargin);
            $sisaPokok = max(0, $sisaPokok - $pokokBayar);
            $sisaMargin = max(0, $sisaMargin - $marginBayar);

            $jadwal[] = [$bulan, $pokokBayar, $marginBayar, $pokokBayar + $marginBayar, $sisaPokok];
        }

        return $jadwal;
    }

    private function storeJadwalMurabahah(Pembiayaan $pembiayaan, array $jadwal, Carbon $tanggal): void
    {
        $jatuhTempo = $tanggal->copy()->addMonth();

        foreach ($jadwal as $item) {
            if (! is_array($item) || count($item) < 4) {
                continue;
            }

            Angsuran::create([
                'pembiayaan_id' => $pembiayaan->id,
                'bulan_ke' => (int) $item[0],
                'jatuh_tempo' => $jatuhTempo->toDateString(),
                'jumlah_bayar' => (int) $item[3],
                'pokok' => (int) $item[1],
                'margin' => (int) $item[2],
                'sisa_pokok' => (int) ($item[4] ?? 0),
                'status' => 'belum_bayar',
            ]);

            $jatuhTempo->addMonth();
        }
    }

    /**
     * Build jadwal angsuran Qardh (tanpa margin/keuntungan).
     * Total angsuran = pokok pembiayaan.
     */
    private function buildJadwalQardh(int $pokok, int $tenor): array
    {
        if ($tenor <= 0 || $pokok <= 0) {
            return [];
        }

        $angsuranPerBulan = (int) floor($pokok / $tenor);
        $sisaPokok = $pokok;
        $jadwal = [];

        for ($bulan = 1; $bulan <= min($tenor, 240); $bulan++) {
            $isLast = $bulan === $tenor;
            $bayar = $isLast ? $sisaPokok : min($angsuranPerBulan, $sisaPokok);
            $sisaPokok = max(0, $sisaPokok - $bayar);

            // [bulan_ke, pokok, margin=0, total, sisa_pokok]
            $jadwal[] = [$bulan, $bayar, 0, $bayar, $sisaPokok];
        }

        return $jadwal;
    }

    /**
     * Simpan jadwal angsuran Qardh ke database.
     */
    private function storeJadwalQardh(Pembiayaan $pembiayaan, array $jadwal, Carbon $tanggal): void
    {
        $jatuhTempo = $tanggal->copy()->addMonth();

        foreach ($jadwal as $item) {
            if (! is_array($item) || count($item) < 4) {
                continue;
            }

            Angsuran::create([
                'pembiayaan_id' => $pembiayaan->id,
                'bulan_ke' => (int) $item[0],
                'jatuh_tempo' => $jatuhTempo->toDateString(),
                'jumlah_bayar' => (int) $item[3],
                'pokok' => (int) $item[1],
                'margin' => 0,
                'sisa_pokok' => (int) ($item[4] ?? 0),
                'status' => 'belum_bayar',
            ]);

            $jatuhTempo->addMonth();
        }
    }

    private function validateNisbah($validator, Request $request, string $koperasiField, string $anggotaField): void
    {
        $koperasi = (float) $request->input($koperasiField, 0);
        $anggota = (float) $request->input($anggotaField, 0);

        if (round($koperasi + $anggota, 2) !== 100.0) {
            $validator->errors()->add($koperasiField, 'Total nisbah koperasi dan anggota harus tepat 100%.');
        }
    }

    private function storeDokumenPengajuan(Request $request, Pembiayaan $pembiayaan): array
    {
        $fields = [
            'formulir_pengajuan' => 'formulir_pengajuan',
        ];

        $storedPaths = [];

        foreach ($fields as $field => $jenis) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            $path = $file->store('pembiayaan/'.$pembiayaan->kode, 'public');
            $storedPaths[] = $path;

            PembiayaanDokumen::create([
                'pembiayaan_id' => $pembiayaan->id,
                'jenis' => $jenis,
                'nama_asli' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'ukuran' => $file->getSize() ?: 0,
            ]);
        }

        return $storedPaths;
    }

    private function hitungPokokTerbayar(Pembiayaan $pembiayaan, $angsuranTerbayar): float
    {
        $pokokTerbayar = (float) $angsuranTerbayar->sum('pokok');

        if ($pokokTerbayar > 0 || $angsuranTerbayar->isEmpty()) {
            return min($pokokTerbayar, (float) $pembiayaan->jumlah_pembiayaan);
        }

        $tenor = max((int) $pembiayaan->tenor, 1);
        $pokokPerAngsuran = (float) $pembiayaan->jumlah_pembiayaan / $tenor;

        return min($pokokPerAngsuran * $angsuranTerbayar->count(), (float) $pembiayaan->jumlah_pembiayaan);
    }
    private function tujuanOptions(): array
    {
        return [
            'modal_usaha' => 'Modal Usaha',
            'pembelian_barang' => 'Pembelian Barang',
            'pendidikan' => 'Pendidikan',
            'renovasi' => 'Renovasi',
            'kebutuhan_lainnya' => 'Kebutuhan Lainnya',
        ];
    }

    private function tujuanLabel(?string $tujuan): string
    {
        return $this->tujuanOptions()[$tujuan ?? ''] ?? 'Kebutuhan Lainnya';
    }
}
