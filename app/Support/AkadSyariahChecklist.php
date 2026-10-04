<?php

namespace App\Support;

use App\Models\Pembiayaan;

class AkadSyariahChecklist
{
    public static function references(string $akad): array
    {
        return match (strtolower($akad)) {
            'murabahah' => [
                'Fatwa DSN-MUI No. 111/DSN-MUI/IX/2017 tentang Akad Jual Beli Murabahah',
                'Fatwa DSN-MUI No. 04/DSN-MUI/IV/2000 tentang Murabahah',
            ],
            'mudharabah' => [
                'Fatwa DSN-MUI No. 115/DSN-MUI/IX/2017 tentang Akad Mudharabah',
                'Fatwa DSN-MUI No. 07/DSN-MUI/IV/2000 tentang Pembiayaan Mudharabah',
            ],
            'musyarakah' => [
                'Fatwa DSN-MUI No. 114/DSN-MUI/IX/2017 tentang Akad Syirkah',
                'Fatwa DSN-MUI No. 08/DSN-MUI/IV/2000 tentang Pembiayaan Musyarakah',
            ],
            'ijarah' => [
                'Fatwa DSN-MUI No. 112/DSN-MUI/IX/2017 tentang Akad Ijarah',
                'Fatwa DSN-MUI No. 09/DSN-MUI/IV/2000 tentang Pembiayaan Ijarah',
            ],
            'qardh' => [
                'Fatwa DSN-MUI No. 02/DSN-MUI/I/2002 tentang Qardh (Pembiayaan Qardh)',
                'Fatwa DSN-MUI No. 76/DSN-MUI/VI/2011 tentang Pembiayaan Qardh',
                'Pedoman Umum Produk Pembiayaan Koperasi Syariah (KUAS)',
            ],
            default => [],
        };
    }

    public static function definitions(string $akad): array
    {
        $general = [
            self::item('para_pihak', 'Umum', 'Para pihak dan kewenangan', 'Identitas, kecakapan, kewenangan, serta persetujuan anggota dan koperasi jelas.'),
            self::item('objek_tujuan_halal', 'Umum', 'Objek dan tujuan halal', 'Objek, manfaat, atau kegiatan usaha jelas, dapat diserahterimakan, dan tidak bertentangan dengan syariah.'),
            self::item('rukun_syarat', 'Umum', 'Rukun dan syarat akad', 'Rukun, syarat, ijab-kabul, hak, serta kewajiban para pihak tertuang secara jelas.'),
            self::item('nilai_tenor_transparan', 'Umum', 'Nilai dan tenor transparan', 'Nominal, tenor, cara pembayaran, biaya, dan konsekuensi wanprestasi diketahui para pihak.'),
            self::item('konsistensi_dokumen', 'Umum', 'Konsistensi dokumen', 'Formulir, detail akad, bukti pendukung, dan jadwal pembayaran konsisten satu sama lain.'),
            self::item('kepatuhan_fatwa_sop', 'Umum', 'Kepatuhan fatwa dan SOP', 'Struktur serta urutan transaksi sesuai fatwa DSN-MUI dan pedoman operasional koperasi.'),
            self::item('bebas_unsur_terlarang', 'Umum', 'Bebas unsur terlarang', 'Tidak terdapat riba, gharar, maysir, tadlis, risywah, dharar, atau objek haram.'),
            self::item('denda_agunan_biaya', 'Umum', 'Denda, agunan, dan biaya', 'Denda, agunan, serta biaya tidak menjadi keuntungan yang bertentangan dengan prinsip syariah.'),
        ];

        $specific = match (strtolower($akad)) {
            'murabahah' => [
                self::item('murabahah_kepemilikan', 'Khusus Murabahah', 'Kepemilikan objek sebelum penjualan', 'Koperasi telah memiliki objek sebelum menjualnya; jika menggunakan wakalah, urutan kuasa dan jual beli dapat dibuktikan.'),
                self::item('murabahah_harga_margin', 'Khusus Murabahah', 'Harga perolehan dan margin', 'Harga beli, margin, harga jual, uang muka, serta biaya administrasi dinyatakan terbuka.'),
                self::item('murabahah_harga_tetap', 'Khusus Murabahah', 'Harga jual disepakati', 'Harga jual tidak berubah selama masa akad dan bukan bunga yang dikaitkan dengan waktu.'),
                self::item('murabahah_objek_spesifik', 'Khusus Murabahah', 'Objek jual beli spesifik', 'Jenis, spesifikasi, harga, pemasok, dan bukti pembelian objek dapat diidentifikasi.'),
                self::item('murabahah_jadwal', 'Khusus Murabahah', 'Jadwal pembayaran konsisten', 'Total pokok dan margin pada jadwal sesuai harga jual setelah memperhitungkan uang muka.'),
            ],
            'mudharabah' => [
                self::item('mudharabah_peran', 'Khusus Mudharabah', 'Peran shahibul maal dan mudharib', 'Koperasi menyediakan modal dan anggota mengelola usaha sesuai batas kewenangan yang disepakati.'),
                self::item('mudharabah_modal', 'Khusus Mudharabah', 'Modal jelas dan diserahkan', 'Jumlah, bentuk, waktu penyerahan, serta penggunaan modal dinyatakan jelas.'),
                self::item('mudharabah_nisbah', 'Khusus Mudharabah', 'Nisbah berupa persentase', 'Nisbah disepakati dalam persentase dan keuntungan tidak dijanjikan dalam nominal tetap.'),
                self::item('mudharabah_realisasi', 'Khusus Mudharabah', 'Bagi hasil berdasarkan realisasi', 'Bagi hasil dihitung dari hasil usaha yang benar-benar terealisasi dan dapat diverifikasi.'),
                self::item('mudharabah_risiko', 'Khusus Mudharabah', 'Kerugian dan jaminan', 'Kerugian usaha ditanggung pemilik modal, kecuali akibat kesengajaan, kelalaian, atau pelanggaran mudharib.'),
            ],
            'musyarakah' => [
                self::item('musyarakah_modal', 'Khusus Musyarakah', 'Kontribusi modal para pihak', 'Modal koperasi dan anggota, bentuk setoran, serta porsi kepemilikan masing-masing dinyatakan jelas.'),
                self::item('musyarakah_nisbah', 'Khusus Musyarakah', 'Nisbah keuntungan', 'Nisbah keuntungan disepakati dalam persentase dan tidak berupa keuntungan nominal tetap.'),
                self::item('musyarakah_kerugian', 'Khusus Musyarakah', 'Pembagian kerugian', 'Kerugian dibagi proporsional berdasarkan porsi modal, kecuali kerugian akibat pelanggaran salah satu pihak.'),
                self::item('musyarakah_pengelolaan', 'Khusus Musyarakah', 'Hak pengelolaan dan pengawasan', 'Kewenangan mengelola usaha, mengambil keputusan, dan mengakses laporan disepakati.'),
                self::item('musyarakah_jaminan', 'Khusus Musyarakah', 'Jaminan tidak menjamin keuntungan', 'Agunan hanya menjamin pelanggaran atau kelalaian, bukan menjamin modal dan keuntungan usaha normal.'),
            ],
            'ijarah' => [
                self::item('ijarah_manfaat', 'Khusus Ijarah', 'Manfaat dan objek sewa', 'Objek atau manfaat halal, dapat dinilai, dapat diserahkan, dan periode pemanfaatannya jelas.'),
                self::item('ijarah_ujrah', 'Khusus Ijarah', 'Ujrah disepakati', 'Besaran, waktu, dan cara pembayaran ujrah diketahui serta disepakati para pihak.'),
                self::item('ijarah_kepemilikan', 'Khusus Ijarah', 'Kepemilikan dan pemeliharaan', 'Risiko kepemilikan serta pemeliharaan pokok berada pada pemilik, sedangkan biaya operasional sesuai kesepakatan.'),
                self::item('ijarah_pemindahan', 'Khusus Ijarah', 'Pemindahan kepemilikan terpisah', 'Jika ada opsi pemindahan kepemilikan, janji atau akad pemindahannya dibuat terpisah dari akad ijarah.'),
                self::item('ijarah_total', 'Khusus Ijarah', 'Total kewajiban konsisten', 'Total ujrah, biaya yang diperbolehkan, dan opsi beli sesuai dengan tenor serta dokumen akad.'),
            ],
            'qardh' => [
                self::item('qardh_pinjaman_hasanah', 'Khusus Qardh', 'Pinjaman berupa manfaat (hasanah)', 'Pinjaman berupa manfaat atau barang, bukan uang dengan tambahan. Jika berupa uang, pengembalian sesuai pokok.'),
                self::item('qardh_tanpa_keuntungan', 'Khusus Qardh', 'Tanpa keuntungan dari pokok', 'Tidak terdapat margin, bunga, nisbah, bagi hasil, markup, atau keuntungan apapun dari pokok pinjaman.'),
                self::item('qardh_total_kewajiban', 'Khusus Qardh', 'Total kewajiban = pokok', 'Total kewajiban pengembalian anggota harus sama persis dengan pokok pembiayaan yang diterima.'),
                self::item('qardh_jadwal_konsisten', 'Khusus Qardh', 'Jadwal angsuran konsisten', 'Jumlah angsuran × tenor = pokok pembiayaan. Selisih pembulatan ditanggung di angsuran terakhir.'),
                self::item('qardh_bebas_ribawi', 'Khusus Qardh', 'Bebas unsur ribawi', 'Tidak ada denda keterlambatan yang bersifat ribawi, dan tidak ada ketentuan yang menguntungkan pemberi pinjaman.'),
            ],
            default => [],
        };

        return array_values(array_merge($general, $specific));
    }

    public static function snapshot(Pembiayaan $pembiayaan): array
    {
        $pembiayaan->loadMissing(['detail', 'dokumen', 'angsuran']);

        $pembiayaanFields = [
            'kode', 'akad', 'tujuan_pembiayaan', 'objek_pembiayaan',
            'rencana_penggunaan_dana', 'estimasi_omzet_usaha',
            'jumlah_pembiayaan', 'tenor', 'angsuran_bulanan', 'tanggal_pengajuan',
            'bank_tujuan', 'no_rekening_tujuan', 'nama_pemilik_rekening',
            'tanggal_pencairan_diharapkan',
        ];

        $detailFields = [
            'objek', 'tenor', 'harga_beli', 'harga_jual', 'margin_persen', 'margin',
            'dp', 'biaya_admin', 'angsuran_bulanan', 'modal', 'modal_anggota',
            'porsi_modal_koperasi', 'porsi_modal_anggota', 'nisbah_koperasi',
            'nisbah_anggota', 'estimasi_omzet', 'estimasi_biaya', 'estimasi_laba',
            'bagi_hasil_koperasi', 'bagi_hasil_anggota', 'nilai_aset',
            'ujrah_bulanan', 'biaya_perawatan', 'opsi_beli', 'total_pembayaran',
        ];

        return [
            'pembiayaan' => collect($pembiayaan->only($pembiayaanFields))->sortKeys()->all(),
            'detail_akad' => $pembiayaan->detail
                ? collect($pembiayaan->detail->only($detailFields))->sortKeys()->all()
                : null,
            'dokumen' => $pembiayaan->dokumen
                ->sortBy('id')
                ->map(fn ($dokumen) => [
                    'id' => $dokumen->id,
                    'jenis' => $dokumen->jenis,
                    'nama_asli' => $dokumen->nama_asli,
                    'path' => $dokumen->path,
                    'ukuran' => $dokumen->ukuran,
                    'updated_at' => $dokumen->updated_at?->toISOString(),
                ])->values()->all(),
            'jadwal_angsuran' => $pembiayaan->angsuran
                ->sortBy('bulan_ke')
                ->map(fn ($angsuran) => [
                    'bulan_ke' => $angsuran->bulan_ke,
                    'jatuh_tempo' => $angsuran->jatuh_tempo ? (string) $angsuran->jatuh_tempo : null,
                    'jumlah_bayar' => $angsuran->jumlah_bayar,
                    'pokok' => $angsuran->pokok,
                    'margin' => $angsuran->margin,
                    'sisa_pokok' => $angsuran->sisa_pokok,
                ])->values()->all(),
        ];
    }

    public static function hash(Pembiayaan $pembiayaan): string
    {
        return hash('sha256', json_encode(
            self::snapshot($pembiayaan),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION
        ));
    }

    public static function systemChecks(Pembiayaan $pembiayaan): array
    {
        $pembiayaan->loadMissing(['detail', 'dokumen', 'angsuran']);
        $detail = $pembiayaan->detail;
        $checks = [];

        $checks[] = self::check(
            'detail_akad',
            'Detail akad tersedia',
            (bool) $detail,
            'Detail akad belum tersedia sehingga struktur akad tidak dapat diperiksa.'
        );
        $checks[] = self::check(
            'formulir_pengajuan',
            'Formulir pengajuan tersedia',
            $pembiayaan->dokumen->contains('jenis', 'formulir_pengajuan'),
            'Formulir pengajuan wajib tersedia sebagai bukti pemeriksaan.'
        );
        $checks[] = self::check(
            'objek_pembiayaan',
            'Objek pembiayaan teridentifikasi',
            filled($pembiayaan->objek_pembiayaan) && filled($detail?->objek),
            'Objek pada pembiayaan dan detail akad harus diisi.'
        );
        $checks[] = self::check(
            'nilai_tenor',
            'Nilai dan tenor valid',
            (float) $pembiayaan->jumlah_pembiayaan > 0
                && (int) $pembiayaan->tenor > 0
                && (!$detail || (int) $detail->tenor === (int) $pembiayaan->tenor),
            'Nominal harus positif dan tenor pada pengajuan harus sama dengan detail akad.'
        );

        if (!$detail) {
            return $checks;
        }

        if ($pembiayaan->akad === 'murabahah') {
            $hargaBeli = (float) $detail->harga_beli;
            $margin = (float) $detail->margin;
            $hargaJual = (float) $detail->harga_jual;
            $dp = (float) $detail->dp;
            $totalJadwal = (float) $pembiayaan->angsuran->sum('jumlah_bayar');

            $checks[] = self::check('murabahah_nilai', 'Perhitungan harga jual konsisten', $hargaBeli > 0 && abs(($hargaBeli + $margin) - $hargaJual) < 1, 'Harga jual harus sama dengan harga beli ditambah margin.');
            $checks[] = self::check('murabahah_dp', 'Uang muka tidak melebihi harga beli', $dp >= 0 && $dp <= $hargaBeli, 'Uang muka tidak boleh melebihi harga beli.');
            $checks[] = self::check('murabahah_jadwal', 'Jadwal sesuai tenor dan harga jual', $pembiayaan->angsuran->count() === (int) $pembiayaan->tenor && abs($totalJadwal - ($hargaJual - $dp)) < 1, 'Jumlah jadwal harus sesuai tenor dan total harga jual setelah uang muka.');
        } elseif ($pembiayaan->akad === 'mudharabah') {
            $checks[] = self::check('mudharabah_modal', 'Modal mudharabah valid', (float) $detail->modal > 0, 'Modal mudharabah harus lebih besar dari nol.');
            $checks[] = self::check('mudharabah_nisbah', 'Total nisbah tepat 100%', abs(((float) $detail->nisbah_koperasi + (float) $detail->nisbah_anggota) - 100) < 0.01, 'Nisbah koperasi dan anggota harus berjumlah 100%.');
        } elseif ($pembiayaan->akad === 'musyarakah') {
            $modalTotal = (float) $detail->modal + (float) $detail->modal_anggota;
            $checks[] = self::check('musyarakah_modal', 'Kontribusi modal kedua pihak valid', (float) $detail->modal > 0 && (float) $detail->modal_anggota > 0 && $modalTotal > 0, 'Modal koperasi dan anggota harus lebih besar dari nol.');
            $checks[] = self::check('musyarakah_porsi', 'Porsi modal tepat 100%', abs(((float) $detail->porsi_modal_koperasi + (float) $detail->porsi_modal_anggota) - 100) < 0.01, 'Porsi modal koperasi dan anggota harus berjumlah 100%.');
            $checks[] = self::check('musyarakah_nisbah', 'Total nisbah tepat 100%', abs(((float) $detail->nisbah_koperasi + (float) $detail->nisbah_anggota) - 100) < 0.01, 'Nisbah koperasi dan anggota harus berjumlah 100%.');
        } elseif ($pembiayaan->akad === 'ijarah') {
            $expectedTotal = ((float) $detail->ujrah_bulanan + (float) $detail->biaya_perawatan) * (int) $detail->tenor + (float) $detail->opsi_beli;
            $checks[] = self::check('ijarah_objek', 'Nilai aset dan ujrah valid', (float) $detail->nilai_aset > 0 && (float) $detail->ujrah_bulanan > 0, 'Nilai aset dan ujrah harus lebih besar dari nol.');
            $checks[] = self::check('ijarah_total', 'Total pembayaran konsisten', abs($expectedTotal - (float) $detail->total_pembayaran) < 1, 'Total pembayaran harus sesuai ujrah, biaya, tenor, dan opsi beli.');
        } elseif ($pembiayaan->akad === 'qardh') {
            $pokok = (float) $pembiayaan->jumlah_pembiayaan;
            $totalJadwal = (float) $pembiayaan->angsuran->sum('jumlah_bayar');
            $checks[] = self::check('qardh_pokok_valid', 'Pokok pembiayaan valid', $pokok > 0, 'Pokok pembiayaan harus lebih besar dari nol.');
            $checks[] = self::check('qardh_tanpa_margin', 'Tidak ada margin/keuntungan', $pembiayaan->angsuran->sum('margin') == 0, 'Akad Qardh tidak boleh menghasilkan margin atau keuntungan.');
            $checks[] = self::check('qardh_total_sama_pokok', 'Total jadwal = pokok pembiayaan', abs($totalJadwal - $pokok) < 1, 'Total seluruh angsuran harus sama dengan pokok pembiayaan.');
            $checks[] = self::check('qardh_jadwal_lengkap', 'Jumlah jadwal sesuai tenor', $pembiayaan->angsuran->count() === (int) $pembiayaan->tenor, 'Jumlah jadwal angsuran harus sama dengan tenor.');
        }

        return $checks;
    }

    public static function blockingFindings(Pembiayaan $pembiayaan): array
    {
        return array_values(array_filter(
            self::systemChecks($pembiayaan),
            fn (array $check) => $check['status'] === 'gagal'
        ));
    }

    private static function item(string $kode, string $kelompok, string $label, string $panduan): array
    {
        return compact('kode', 'kelompok', 'label', 'panduan');
    }

    private static function check(string $kode, string $label, bool $passed, string $failureMessage): array
    {
        return [
            'kode' => $kode,
            'label' => $label,
            'status' => $passed ? 'lulus' : 'gagal',
            'pesan' => $passed ? 'Pemeriksaan data sistem terpenuhi.' : $failureMessage,
        ];
    }
}
