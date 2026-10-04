<?php

namespace App\Support;

use App\Models\Angsuran;
use App\Models\AuditTemuan;
use App\Models\LaporanPengawasan;
use App\Models\Pembiayaan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Simpanan;
use App\Models\Toko;
use App\Models\User;
use App\Models\SetoranSimpanan;
use App\Models\PembayaranAngsuran;
use App\Models\PencairanDana;
use App\Models\ValidasiAkadDps;
use App\Models\OpiniSyariah;
use Illuminate\Support\Facades\Log;

/**
 * Comprehensive Notification Service for Koperasi Syariah.
 *
 * Covers all state-change events between roles:
 * - Anggota ↔ Pengurus (simpanan, pembiayaan, angsuran, pencairan, transaksi)
 * - Anggota ↔ DPS (akad validation, temuan audit)
 * - Anggota ↔ Pelanggan (marketplace orders)
 * - DPS ↔ Pengurus/Admin (audit findings, opinions, reports)
 * - Pengurus ↔ Admin (operational alerts)
 * - Public → Toko Owner (new orders)
 */
class NotificationService
{
    // ═══════════════════════════════════════════════════════════════════════
    // PEMBIAYAAN FLOW: Anggota → DPS → Pengurus → Anggota
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Anggota mengajukan pembiayaan → notifikasi ke DPS + Pengurus + Admin
     */
    public static function pembiayaanDiajukan(Pembiayaan $pembiayaan): void
    {
        $nama = $pembiayaan->user?->name ?? 'Anggota';
        $kode = $pembiayaan->kode;
        $akad = ucfirst($pembiayaan->akad ?? '');
        $nominal = 'Rp ' . number_format($pembiayaan->jumlah_pembiayaan ?? 0, 0, ',', '.');
        $extra = ['pembiayaan_id' => $pembiayaan->id, 'tab' => 'audit'];

        // DPS: perlu validasi akad
        NotificationHelper::sendToRole('dps', 'Akad Baru Menunggu Validasi',
            "{$nama} mengajukan pembiayaan {$kode} ({$akad}) sebesar {$nominal}. Segera lakukan pemeriksaan kepatuhan syariah.", $extra);

        // Pengurus: ada pengajuan baru
        NotificationHelper::sendToRole('pengurus', 'Pengajuan Pembiayaan Baru',
            "{$nama} mengajukan pembiayaan {$kode} ({$akad}) sebesar {$nominal}. Menunggu review dan persetujuan.", $extra);

        // Admin: info operasional
        NotificationHelper::sendToRole('admin', 'Pengajuan Pembiayaan Baru',
            "Pengajuan {$kode} dari {$nama} sebesar {$nominal} masuk ke antrian review.", $extra);
    }

    /**
     * DPS memvalidasi akad → notifikasi ke Pengurus + Admin + Anggota (jika perlu perbaikan/tidak sesuai)
     */
    public static function akadDivalidasiDps(ValidasiAkadDps $validasi, Pembiayaan $pembiayaan): void
    {
        $kode = $pembiayaan->kode;
        $hasil = $validasi->hasil;
        $hasilLabel = [
            'sesuai' => 'Sesuai Syariah',
            'perlu_perbaikan' => 'Perlu Perbaikan',
            'tidak_sesuai' => 'Tidak Sesuai Syariah',
        ][$hasil] ?? $hasil;
        $validator = $validasi->validator?->name ?? 'DPS';
        $extra = ['pembiayaan_id' => $pembiayaan->id, 'tab' => 'audit'];

        // Pengurus: tahu hasil validasi
        NotificationHelper::sendToRole('pengurus', 'Hasil Validasi Akad ' . $kode,
            "Akad {$kode} dinyatakan {$hasilLabel} oleh {$validator}. " .
            ($hasil === 'sesuai' ? 'Pembiayaan siap diproses persetujuan.' : 'Tindak lanjut diperlukan.'), $extra);

        // Admin: info validasi
        NotificationHelper::sendToRole('admin', 'Validasi DPS: ' . $hasilLabel,
            "Validasi akad {$kode}: {$hasilLabel} oleh {$validator}.", $extra);

        // Anggota: notifikasi hasil validasi
        if ($pembiayaan->user) {
            if ($hasil === 'sesuai') {
                NotificationHelper::sendToUser($pembiayaan->user, 'Akad Dinyatakan Sesuai Syariah',
                    "Pengajuan {$kode} telah dinyatakan sesuai syariah oleh DPS. Menunggu persetujuan pengurus.", $extra);
            } else {
                NotificationHelper::sendToUser($pembiayaan->user, 'Akad ' . $hasilLabel,
                    "Pengajuan {$kode} {$hasilLabel}. " . ($validasi->catatan_perbaikan ?? 'Silakan periksa catatan DPS.'),
                    $extra);
            }
        }
    }

    /**
     * Pengurus menyetujui pembiayaan → notifikasi ke Anggota + DPS + Admin
     */
    public static function pembiayaanDisetujui(Pembiayaan $pembiayaan): void
    {
        $kode = $pembiayaan->kode;
        $akad = ucfirst($pembiayaan->akad ?? '');
        $nominal = 'Rp ' . number_format($pembiayaan->jumlah_pembiayaan ?? 0, 0, ',', '.');
        $extra = ['pembiayaan_id' => $pembiayaan->id, 'tab' => 'pembiayaan'];

        // Anggota: pembiayaan disetujui
        if ($pembiayaan->user) {
            NotificationHelper::sendToUser($pembiayaan->user, 'Pembiayaan Disetujui! 🎉',
                "Pengajuan {$kode} ({$akad}) sebesar {$nominal} telah disetujui. Silakan ajukan pencairan dana melalui menu Transaksi.",
                $extra);
        }

        // DPS: informasi bahwa pembiayaan sudah disetujui
        NotificationHelper::sendToRole('dps', 'Pembiayaan Disetujui Pengurus',
            "Pembiayaan {$kode} telah disetujui pengurus setelah validasi DPS.", $extra);

        // Admin: info operasional
        NotificationHelper::sendToRole('admin', 'Pembiayaan Disetujui',
            "Pembiayaan {$kode} ({$akad}) sebesar {$nominal} disetujui pengurus.", $extra);
    }

    /**
     * Pengurus menolak pembiayaan → notifikasi ke Anggota + Admin
     */
    public static function pembiayaanDitolak(Pembiayaan $pembiayaan, ?string $alasan = null): void
    {
        $kode = $pembiayaan->kode;
        $akad = ucfirst($pembiayaan->akad ?? '');
        $extra = ['pembiayaan_id' => $pembiayaan->id, 'tab' => 'pembiayaan'];

        // Anggota: pembiayaan ditolak
        if ($pembiayaan->user) {
            NotificationHelper::sendToUser($pembiayaan->user, 'Pembiayaan Ditolak',
                "Pengajuan {$kode} ({$akad}) ditolak oleh pengurus." .
                ($alasan ? " Alasan: {$alasan}" : ' Silakan hubungi pengurus untuk informasi lebih lanjut.'),
                $extra);
        }

        // Admin: info penolakan
        NotificationHelper::sendToRole('admin', 'Pembiayaan Ditolak',
            "Pembiayaan {$kode} ({$akad}) ditolak." . ($alasan ? " Alasan: {$alasan}" : ''), $extra);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PENCAIRAN FLOW: Anggota → Pengurus → Anggota
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Anggota mengajukan pencairan → notifikasi ke Pengurus
     */
    public static function pencairanDiajukan(PencairanDana $pencairan): void
    {
        $pembiayaan = $pencairan->pembiayaan;
        $nama = $pembiayaan?->user?->name ?? 'Anggota';
        $kode = $pembiayaan?->kode ?? '-';
        $nominal = 'Rp ' . number_format($pencairan->nominal_pencairan ?? 0, 0, ',', '.');
        $extra = ['pencairan_id' => $pencairan->id, 'pembiayaan_id' => $pencairan->pembiayaan_id, 'tab' => 'transaksi'];

        NotificationHelper::sendToRole('bendahara', 'Permintaan Pencairan Dana Baru',
            "{$nama} mengajukan pencairan {$nominal} dari pembiayaan {$kode}. Rekening tujuan: {$pencairan->bank_tujuan} ({$pencairan->no_rekening_tujuan}).",
            $extra);
    }

    /**
     * Pengurus menyetujui pencairan → notifikasi ke Anggota
     */
    public static function pencairanDisetujui(PencairanDana $pencairan): void
    {
        $pembiayaan = $pencairan->pembiayaan;
        $nominal = 'Rp ' . number_format($pencairan->nominal_pencairan ?? 0, 0, ',', '.');
        $extra = ['pencairan_id' => $pencairan->id, 'pembiayaan_id' => $pencairan->pembiayaan_id, 'tab' => 'transaksi'];

        if ($pembiayaan?->user) {
            NotificationHelper::sendToUser($pembiayaan->user, 'Pencairan Dana Disetujui',
                "Permintaan pencairan {$nominal} telah disetujui. Transfer akan segera diproses oleh pengurus.",
                $extra);
        }
    }

    /**
     * Pengurus menolak pencairan → notifikasi ke Anggota
     */
    public static function pencairanDitolak(PencairanDana $pencairan): void
    {
        $pembiayaan = $pencairan->pembiayaan;
        $nominal = 'Rp ' . number_format($pencairan->nominal_pencairan ?? 0, 0, ',', '.');
        $extra = ['pencairan_id' => $pencairan->id, 'pembiayaan_id' => $pencairan->pembiayaan_id, 'tab' => 'transaksi'];

        if ($pembiayaan?->user) {
            NotificationHelper::sendToUser($pembiayaan->user, 'Pencairan Dana Ditolak',
                "Permintaan pencairan {$nominal} ditolak." .
                ($pencairan->catatan ? " Catatan: {$pencairan->catatan}" : ' Silakan hubungi pengurus.'),
                $extra);
        }
    }

    /**
     * Pengurus mentransfer dana pencairan → notifikasi ke Anggota
     */
    public static function pencairanDitransfer(PencairanDana $pencairan): void
    {
        $pembiayaan = $pencairan->pembiayaan;
        $nominal = 'Rp ' . number_format($pencairan->nominal_pencairan ?? 0, 0, ',', '.');
        $extra = ['pencairan_id' => $pencairan->id, 'pembiayaan_id' => $pencairan->pembiayaan_id, 'tab' => 'transaksi'];

        if ($pembiayaan?->user) {
            NotificationHelper::sendToUser($pembiayaan->user, 'Dana Pencairan Telah Dikirim',
                "Pencairan {$nominal} telah ditransfer ke rekening Anda ({$pencairan->bank_tujuan} {$pencairan->no_rekening_tujuan}). " .
                "Referensi: {$pencairan->nomor_referensi_transfer}.",
                $extra);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // SIMPANAN FLOW: Anggota → Pengurus → Anggota
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Anggota menyetor simpanan → notifikasi ke Pengurus
     */
    public static function simpananDisetor(SetoranSimpanan $setoran): void
    {
        $nama = $setoran->user?->name ?? 'Anggota';
        $jenis = ucfirst($setoran->jenis_simpanan ?? 'simpanan');
        $nominal = 'Rp ' . number_format($setoran->nominal ?? 0, 0, ',', '.');
        $extra = ['setoran_id' => $setoran->id, 'tab' => 'simpanan'];

        NotificationHelper::sendToRole('bendahara', 'Setoran Simpanan Baru',
            "{$nama} menyetor simpanan {$jenis} sebesar {$nominal}. Menunggu verifikasi.",
            $extra);
    }

    /**
     * Setoran simpanan diverifikasi → notifikasi ke Anggota
     */
    public static function simpananDiverifikasi(SetoranSimpanan $setoran): void
    {
        $jenis = ucfirst($setoran->jenis_simpanan ?? 'simpanan');
        $nominal = 'Rp ' . number_format($setoran->nominal ?? 0, 0, ',', '.');
        $extra = ['setoran_id' => $setoran->id, 'tab' => 'simpanan'];

        if ($setoran->user) {
            NotificationHelper::sendToUser($setoran->user, 'Setoran Simpanan Terverifikasi ✓',
                "Setoran simpanan {$jenis} sebesar {$nominal} telah diverifikasi dan masuk ke saldo Anda.",
                $extra);
        }
    }

    /**
     * Setoran simpanan ditolak → notifikasi ke Anggota
     */
    public static function simpananDitolak(SetoranSimpanan $setoran): void
    {
        $jenis = ucfirst($setoran->jenis_simpanan ?? 'simpanan');
        $nominal = 'Rp ' . number_format($setoran->nominal ?? 0, 0, ',', '.');
        $extra = ['setoran_id' => $setoran->id, 'tab' => 'simpanan'];

        if ($setoran->user) {
            NotificationHelper::sendToUser($setoran->user, 'Setoran Simpanan Ditolak',
                "Setoran simpanan {$jenis} sebesar {$nominal} ditolak." .
                ($setoran->catatan_penolakan ? " Alasan: {$setoran->catatan_penolakan}" : ''),
                $extra);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ANGSURAN FLOW: Anggota → Pengurus → Anggota
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Anggota membayar angsuran → notifikasi ke Pengurus
     */
    public static function angsuranDibayar(PembayaranAngsuran $pembayaran): void
    {
        $angsuran = $pembayaran->angsuran;
        $pembiayaan = $pembayaran->pembiayaan;
        $nama = $pembiayaan?->user?->name ?? 'Anggota';
        $kode = $pembiayaan?->kode ?? '-';
        $ke = $angsuran?->bulan_ke ?? '-';
        $nominal = 'Rp ' . number_format($pembayaran->jumlah_dibayar ?? 0, 0, ',', '.');
        $extra = ['angsuran_id' => $pembayaran->angsuran_id, 'pembiayaan_id' => $pembayaran->pembiayaan_id, 'tab' => 'transaksi'];

        NotificationHelper::sendToRole('bendahara', 'Pembayaran Angsuran Baru',
            "{$nama} membayar angsuran ke-{$ke} ({$kode}) sebesar {$nominal}. Menunggu verifikasi.",
            $extra);
    }

    /**
     * Angsuran diverifikasi → notifikasi ke Anggota
     */
    public static function angsuranDiverifikasi(PembayaranAngsuran $pembayaran): void
    {
        $angsuran = $pembayaran->angsuran;
        $pembiayaan = $pembayaran->pembiayaan;
        $ke = $angsuran?->bulan_ke ?? '-';
        $kode = $pembiayaan?->kode ?? '-';
        $nominal = 'Rp ' . number_format($pembayaran->jumlah_dibayar ?? 0, 0, ',', '.');
        $extra = ['angsuran_id' => $pembayaran->angsuran_id, 'pembiayaan_id' => $pembayaran->pembiayaan_id, 'tab' => 'angsuran'];

        if ($pembiayaan?->user) {
            NotificationHelper::sendToUser($pembiayaan->user, 'Angsuran Terverifikasi ✓',
                "Pembayaran angsuran ke-{$ke} ({$kode}) sebesar {$nominal} telah diverifikasi. Terima kasih!",
                $extra);
        }
    }

    /**
     * Angsuran ditolak → notifikasi ke Anggota
     */
    public static function angsuranDitolak(PembayaranAngsuran $pembayaran): void
    {
        $pembiayaan = $pembayaran->pembiayaan;
        $angsuran = $pembayaran->angsuran;
        $ke = $angsuran?->bulan_ke ?? '-';
        $nominal = 'Rp ' . number_format($pembayaran->jumlah_dibayar ?? 0, 0, ',', '.');
        $extra = ['angsuran_id' => $pembayaran->angsuran_id, 'pembiayaan_id' => $pembayaran->pembiayaan_id, 'tab' => 'transaksi'];

        if ($pembiayaan?->user) {
            NotificationHelper::sendToUser($pembiayaan->user, 'Pembayaran Angsuran Ditolak',
                "Pembayaran angsuran ke-{$ke} sebesar {$nominal} ditolak." .
                ($pembayaran->catatan_penolakan ? " Alasan: {$pembayaran->catatan_penolakan}" : ''),
                $extra);
        }
    }

    /**
     * Angsuran jatuh tempo → notifikasi ke Anggota (reminder)
     */
    public static function angsuranJatuhTempo(Angsuran $angsuran): void
    {
        $pembiayaan = $angsuran->pembiayaan;
        $ke = $angsuran->bulan_ke;
        $kode = $pembiayaan?->kode ?? '-';
        $nominal = 'Rp ' . number_format($angsuran->jumlah_bayar ?? 0, 0, ',', '.');
        $jatuh = \Carbon\Carbon::parse($angsuran->jatuh_tempo)->translatedFormat('d F Y');
        $extra = ['angsuran_id' => $angsuran->id, 'pembiayaan_id' => $angsuran->pembiayaan_id, 'tab' => 'angsuran'];

        if ($pembiayaan?->user) {
            NotificationHelper::sendToUser($pembiayaan->user, '⏰ Reminder: Angsuran Jatuh Tempo',
                "Angsuran ke-{$ke} ({$kode}) sebesar {$nominal} jatuh tempo pada {$jatuh}. Silakan lakukan pembayaran.",
                $extra);
        }
    }

    /**
     * Angsuran terlambat → notifikasi ke Anggota + Pengurus
     */
    public static function angsuranTerlambat(Angsuran $angsuran): void
    {
        $pembiayaan = $angsuran->pembiayaan;
        $nama = $pembiayaan?->user?->name ?? 'Anggota';
        $ke = $angsuran->bulan_ke;
        $kode = $pembiayaan?->kode ?? '-';
        $nominal = 'Rp ' . number_format($angsuran->jumlah_bayar ?? 0, 0, ',', '.');
        $extra = ['angsuran_id' => $angsuran->id, 'pembiayaan_id' => $angsuran->pembiayaan_id, 'tab' => 'pembiayaan'];

        // Anggota: peringatan
        if ($pembiayaan?->user) {
            NotificationHelper::sendToUser($pembiayaan->user, '⚠️ Angsuran Terlambat!',
                "Angsuran ke-{$ke} ({$kode}) sebesar {$nominal} melewati tanggal jatuh tempo. Segera lakukan pembayaran untuk menghindari denda.",
                $extra);
        }

        // Pengurus: info tunggakan
        NotificationHelper::sendToRole('bendahara', 'Angsuran Terlambat',
            "{$nama} memiliki tunggakan angsuran ke-{$ke} ({$kode}) sebesar {$nominal}.",
            $extra);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // MARKETPLACE FLOW: Anggota/Pelanggan → Toko Owner → Pembeli
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Toko baru dibuat → notifikasi ke Pengurus + Admin
     */
    public static function tokoDibuat(Toko $toko): void
    {
        $nama = $toko->user?->name ?? 'Anggota';
        $namaToko = $toko->nama_toko;
        $extra = ['toko_id' => $toko->id, 'tab' => 'marketplace'];

        NotificationHelper::sendToRole('pengurus', 'Toko Baru Menunggu Verifikasi',
            "{$nama} membuka toko \"{$namaToko}\". Menunggu verifikasi dan persetujuan pengurus.",
            $extra);

        NotificationHelper::sendToRole('admin', 'Toko Baru Terdaftar',
            "Toko \"{$namaToko}\" dari {$nama} terdaftar dan menunggu verifikasi.",
            $extra);
    }

    /**
     * Toko diverifikasi → notifikasi ke pemilik toko
     */
    public static function tokoDiverifikasi(Toko $toko, bool $disetujui): void
    {
        $namaToko = $toko->nama_toko;
        $extra = ['toko_id' => $toko->id, 'tab' => 'marketplace'];

        if ($toko->user) {
            if ($disetujui) {
                NotificationHelper::sendToUser($toko->user, 'Toko Disetujui! 🎉',
                    "Toko \"{$namaToko}\" telah diverifikasi dan diaktifkan. Anda dapat mulai mengunggah produk.",
                    $extra);
            } else {
                NotificationHelper::sendToUser($toko->user, 'Toko Tidak Disetujui',
                    "Toko \"{$namaToko}\" tidak disetujui oleh pengurus. Silakan periksa persyaratan dan ajukan ulang.",
                    $extra);
            }
        }
    }

    /**
     * Produk baru diunggah → notifikasi ke Pengurus + DPS + Admin
     */
    public static function produkDibuat(Produk $produk): void
    {
        $namaToko = $produk->toko?->nama_toko ?? 'Toko';
        $nama = $produk->toko?->user?->name ?? 'Anggota';
        $namaProduk = $produk->nama;
        $extra = ['produk_id' => $produk->id, 'tab' => 'marketplace'];

        NotificationHelper::sendToRole('pengurus', 'Produk Baru Menunggu Moderasi',
            "{$nama} mengunggah produk \"{$namaProduk}\" dari toko \"{$namaToko}\". Menunggu moderasi.",
            $extra);

        NotificationHelper::sendToRole('dps', 'Produk Baru untuk Review Syariah',
            "Produk \"{$namaProduk}\" dari toko \"{$namaToko}\" menunggu review kepatuhan syariah.",
            $extra);

        NotificationHelper::sendToRole('admin', 'Produk Baru Terdaftar',
            "Produk \"{$namaProduk}\" dari toko \"{$namaToko}\" masuk antrian moderasi.",
            $extra);
    }

    /**
     * Produk di-update → notifikasi ke Pengurus + DPS untuk re-moderasi
     */
    public static function produkDiUpdate(Produk $produk): void
    {
        $namaProduk = $produk->nama;
        $nama = $produk->toko?->user?->name ?? 'Anggota';
        $extra = ['produk_id' => $produk->id, 'tab' => 'marketplace'];

        NotificationHelper::sendToRole('pengurus', 'Produk Perlu Re-Moderasi',
            "{$nama} memperbarui produk \"{$namaProduk}\". Produk kembali ke status pending untuk moderasi ulang.",
            $extra);

        NotificationHelper::sendToRole('dps', 'Produk Perlu Review Ulang',
            "Produk \"{$namaProduk}\" diperbarui dan perlu review syariah ulang.",
            $extra);
    }

    /**
     * Produk disetujui/ditolak oleh Pengurus → notifikasi ke pemilik produk
     */
    public static function produkDimoderasi(Produk $produk, bool $disetujui): void
    {
        $namaProduk = $produk->nama;
        $extra = ['produk_id' => $produk->id, 'tab' => 'marketplace'];

        if ($produk->toko?->user) {
            if ($disetujui) {
                NotificationHelper::sendToUser($produk->toko->user, 'Produk Disetujui! 🎉',
                    "Produk \"{$namaProduk}\" telah disetujui dan sekarang aktif di marketplace.",
                    $extra);
            } else {
                NotificationHelper::sendToUser($produk->toko->user, 'Produk Ditolak',
                    "Produk \"{$namaProduk}\" ditolak oleh pengurus. Produk dinonaktifkan.",
                    $extra);
            }
        }
    }

    /**
     * Produk direview oleh DPS → notifikasi ke pemilik produk + Admin
     */
    public static function produkDireviewDps(Produk $produk, string $hasil): void
    {
        $namaProduk = $produk->nama;
        $extra = ['produk_id' => $produk->id, 'tab' => 'marketplace'];

        if ($produk->toko?->user) {
            NotificationHelper::sendToUser($produk->toko->user, 'Hasil Review DPS: ' . $hasil,
                "Produk \"{$namaProduk}\" direview oleh DPS dengan hasil: {$hasil}.",
                $extra);
        }

        NotificationHelper::sendToRole('admin', 'Review DPS Selesai',
            "Produk \"{$namaProduk}\" telah direview DPS: {$hasil}.",
            $extra);
    }

    /**
     * Pesanan baru masuk → notifikasi ke pembeli + pemilik toko + Pengurus
     */
    public static function pesananBaru(Pesanan $pesanan): void
    {
        $pembeli = $pesanan->pembeli?->name ?? 'Pembeli';
        $noPesanan = $pesanan->nomor_pesanan ?? $pesanan->id;
        $total = 'Rp ' . number_format($pesanan->total ?? 0, 0, ',', '.');
        $namaToko = $pesanan->toko?->nama_toko ?? '-';
        $extra = ['pesanan_id' => $pesanan->id, 'tab' => 'marketplace'];

        // Pembeli: pesanan berhasil dibuat
        if ($pesanan->pembeli) {
            NotificationHelper::sendToUser($pesanan->pembeli, '🛒 Pesanan Berhasil Dibuat!',
                "Pesanan #{$noPesanan} ke toko \"{$namaToko}\" sebesar {$total} berhasil dibuat. Silakan lakukan pembayaran.",
                $extra);
        }

        // Toko owner: pesanan masuk
        if ($pesanan->toko?->user) {
            NotificationHelper::sendToUser($pesanan->toko->user, '🛒 Pesanan Baru Masuk!',
                "{$pembeli} memesan pesanan #{$noPesanan} sebesar {$total}. Segera proses.",
                $extra);
        }

        // Pengurus: info transaksi marketplace
        NotificationHelper::sendToRole('pengurus', 'Pesanan Marketplace Baru',
            "Pesanan #{$noPesanan} dari {$pembeli} ke toko \"{$namaToko}\" sebesar {$total}.",
            $extra);
    }

    /**
     * Bukti transfer diterima → notifikasi ke penjual
     */
    public static function buktiTransferDiterima(Pesanan $pesanan): void
    {
        $pembeli = $pesanan->pembeli?->name ?? 'Pembeli';
        $noPesanan = $pesanan->nomor_pesanan ?? $pesanan->id;
        $total = 'Rp ' . number_format($pesanan->total ?? 0, 0, ',', '.');
        $extra = ['pesanan_id' => $pesanan->id, 'tab' => 'marketplace'];

        // Toko owner: bukti transfer diterima
        if ($pesanan->toko?->user) {
            NotificationHelper::sendToUser($pesanan->toko->user, '📎 Bukti Transfer Diterima',
                "{$pembeli} telah mengupload bukti transfer untuk pesanan #{$noPesanan} sebesar {$total}. Silakan verifikasi dan konfirmasi pembayaran.",
                $extra);
        }
    }

    /**
     * Pembayaran dikonfirmasi penjual → notifikasi ke pembeli
     */
    public static function pembayaranDikonfirmasi(Pesanan $pesanan): void
    {
        $noPesanan = $pesanan->nomor_pesanan ?? $pesanan->id;
        $namaToko = $pesanan->toko?->nama_toko ?? 'Toko';
        $total = 'Rp ' . number_format($pesanan->total ?? 0, 0, ',', '.');
        $extra = ['pesanan_id' => $pesanan->id, 'tab' => 'pesanan'];

        // Pembeli: pembayaran dikonfirmasi
        if ($pesanan->pembeli) {
            NotificationHelper::sendToUser($pesanan->pembeli, '✅ Pembayaran Terverifikasi!',
                "Pembayaran pesanan #{$noPesanan} ke toko \"{$namaToko}\" sebesar {$total} telah dikonfirmasi oleh penjual. Pesanan akan segera diproses.",
                $extra);
        }
    }

    /**
     * Status pesanan diubah oleh penjual → notifikasi ke pembeli
     */
    public static function pesananStatusDiubah(Pesanan $pesanan, string $statusLama, string $statusBaru): void
    {
        $noPesanan = $pesanan->nomor_pesanan ?? $pesanan->id;
        $namaToko = $pesanan->toko?->nama_toko ?? 'Toko';
        $statusLabel = [
            'menunggu' => 'Menunggu Diproses',
            'dikemas' => 'Sedang Dikemas',
            'dikirim' => 'Sedang Dikirim',
            'selesai' => 'Selesai',
            'batal' => 'Dibatalkan',
        ][$statusBaru] ?? $statusBaru;
        $extra = ['pesanan_id' => $pesanan->id, 'tab' => 'pesanan'];

        // Pembeli: status pesanan berubah
        if ($pesanan->pembeli) {
            NotificationHelper::sendToUser($pesanan->pembeli, 'Status Pesanan Diperbarui',
                "Pesanan #{$noPesanan} dari toko \"{$namaToko}\" status: {$statusLabel}.",
                $extra);
        }

        // Pengurus: info perubahan status
        NotificationHelper::sendToRole('pengurus', 'Status Pesanan Berubah',
            "Pesanan #{$noPesanan}: {$statusLama} → {$statusBaru}.",
            ['pesanan_id' => $pesanan->id, 'tab' => 'marketplace']);
    }

    /**
     * Pembeli mengkonfirmasi penerimaan → notifikasi ke penjual + Pengurus
     */
    public static function pesananDiterima(Pesanan $pesanan): void
    {
        $noPesanan = $pesanan->nomor_pesanan ?? $pesanan->id;
        $pembeli = $pesanan->pembeli?->name ?? 'Pembeli';
        $total = 'Rp ' . number_format($pesanan->total ?? 0, 0, ',', '.');
        $extra = ['pesanan_id' => $pesanan->id, 'tab' => 'marketplace'];

        if ($pesanan->toko?->user) {
            NotificationHelper::sendToUser($pesanan->toko->user, 'Pesanan Selesai ✓',
                "{$pembeli} telah mengkonfirmasi penerimaan pesanan #{$noPesanan} sebesar {$total}.",
                $extra);
        }

        NotificationHelper::sendToRole('pengurus', 'Pesanan Selesai',
            "Pesanan #{$noPesanan} dari {$pembeli} telah selesai.",
            $extra);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // AUDIT FLOW: DPS → Admin + Pengurus
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Temuan audit baru → notifikasi ke Admin + Pengurus
     */
    public static function temuanAuditBaru(AuditTemuan $temuan): void
    {
        $nomor = $temuan->nomor_temuan;
        $jenis = $temuan->jenis_temuan;
        $resiko = $temuan->tingkat_resiko;
        $extra = ['temuan_id' => $temuan->id, 'tab' => 'audit'];

        NotificationHelper::sendToRole('admin', 'Temuan Audit Baru',
            "Temuan {$nomor}: {$jenis} (Resiko: {$resiko}). Menunggu tindak lanjut.",
            $extra);

        NotificationHelper::sendToRole('pengurus', 'Temuan Audit DPS',
            "DPS menemukan temuan {$nomor}: {$jenis}. Perlu ditindaklanjuti.",
            $extra);
    }

    /**
     * Status temuan berubah → notifikasi ke Admin + DPS
     */
    public static function temuanStatusDiubah(AuditTemuan $temuan, string $statusLama, string $statusBaru): void
    {
        $nomor = $temuan->nomor_temuan;
        $extra = ['temuan_id' => $temuan->id, 'tab' => 'audit'];

        NotificationHelper::sendToRole('admin', 'Status Temuan: ' . $statusBaru,
            "Temuan {$nomor} status berubah: {$statusLama} → {$statusBaru}.",
            $extra);

        // DPS juga tahu
        NotificationHelper::sendToRole('dps', 'Status Temuan: ' . $statusBaru,
            "Temuan {$nomor} status: {$statusBaru}.",
            $extra);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // OPINI & LAPORAN: DPS → Admin
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Opini syariah baru → notifikasi ke Admin + Pengurus
     */
    public static function opiniSyariahBaru(OpiniSyariah $opini): void
    {
        $nomor = $opini->nomor_opini;
        $hasil = $opini->hasil;
        $extra = ['opini_id' => $opini->id, 'tab' => 'audit'];

        NotificationHelper::sendToRole('admin', 'Opini Syariah Baru',
            "{$nomor} — Hasil: {$hasil}.",
            $extra);

        NotificationHelper::sendToRole('pengurus', 'Opini Syariah DPS',
            "{$nomor}: {$hasil}.",
            $extra);
    }

    /**
     * Laporan pengawasan diterbitkan → notifikasi ke Admin + Pengurus
     */
    public static function laporanDiterbitkan(LaporanPengawasan $laporan): void
    {
        $periode = $laporan->periode ?? 'Semester';
        $extra = [
            'laporan_id' => $laporan->id,
            'tab' => 'laporan',
            'pdf_url' => route('laporan-pengawasan.pdf', $laporan),
        ];

        NotificationHelper::sendToRole('ketua', 'Laporan Pengawasan DPS',
            "Laporan pengawasan {$periode} telah dicetak otomatis dan dikirim oleh DPS.",
            $extra);
        NotificationHelper::sendToRole('admin', 'Laporan Pengawasan Diterbitkan',
            "Laporan pengawasan {$periode} telah diterbitkan oleh DPS.",
            $extra);

        NotificationHelper::sendToRole('pengurus', 'Laporan Pengawasan Baru',
            "Laporan pengawasan {$periode} dari DPS telah tersedia.",
            $extra);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // MUTASI / KAS: Pengurus → Anggota + Admin
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Transaksi mutasi ditolak → notifikasi ke Anggota terkait
     */
    public static function mutasiDitolak($referensi, ?string $catatan = null): void
    {
        if (!$referensi) return;

        [$type, $refId] = explode(':', $referensi) + [null, null];

        $extra = ['mutasi_ref' => $referensi, 'tab' => 'transaksi'];

        match ($type) {
            'pembayaran_angsuran' => self::mutasiAngsuranDitolak((int) $refId, $catatan, $extra),
            'setoran_simpanan' => self::mutasiSetoranDitolak((int) $refId, $catatan, $extra),
            'pencairan' => self::pencairanDitolak((int) $refId, $catatan, $extra),
            default => null,
        };
    }

    private static function mutasiAngsuranDitolak(int $refId, ?string $catatan, array $extra): void
    {
        $pembayaran = PembayaranAngsuran::with('pembiayaan.user', 'angsuran')->find($refId);
        if ($pembayaran?->pembiayaan?->user) {
            $ke = $pembayaran->angsuran?->bulan_ke ?? '-';
            $nominal = 'Rp ' . number_format($pembayaran->jumlah_dibayar ?? 0, 0, ',', '.');
            NotificationHelper::sendToUser($pembayaran->pembiayaan->user, 'Pembayaran Angsuran Ditolak',
                "Pembayaran angsuran ke-{$ke} sebesar {$nominal} ditolak." .
                ($catatan ? " Catatan: {$catatan}" : ''), $extra);
        }
    }

    private static function mutasiSetoranDitolak(int $refId, ?string $catatan, array $extra): void
    {
        $setoran = SetoranSimpanan::with('user')->find($refId);
        if ($setoran?->user) {
            $jenis = ucfirst($setoran->jenis_simpanan ?? 'simpanan');
            $nominal = 'Rp ' . number_format($setoran->nominal ?? 0, 0, ',', '.');
            NotificationHelper::sendToUser($setoran->user, 'Setoran Simpanan Ditolak',
                "Setoran {$jenis} sebesar {$nominal} ditolak." .
                ($catatan ? " Catatan: {$catatan}" : ''), $extra);
        }
    }

    private static function pencairanDitolakByRef(int $refId, ?string $catatan, array $extra): void
    {
        $pencairan = PencairanDana::with('pembiayaan.user')->find($refId);
        if ($pencairan) {
            self::pencairanDitolak($pencairan);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // USER MANAGEMENT: Admin → Pengurus
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * User baru terdaftar → notifikasi ke Admin
     */
    public static function userBaruTerdaftar(User $user): void
    {
        $extra = ['user_id' => $user->id, 'tab' => 'anggota'];

        NotificationHelper::sendToRole('admin', 'User Baru Terdaftar',
            "{$user->name} ({$user->email}) terdaftar sebagai {$user->role}.",
            $extra);

        if ($user->role === 'anggota') {
            NotificationHelper::sendToRole('pengurus', 'Anggota Baru Bergabung',
                "{$user->name} telah bergabung sebagai anggota koperasi.",
                $extra);
        }
    }

    /**
     * User dihapus → notifikasi ke Admin
     */
    public static function userDihapus(User $user): void
    {
        NotificationHelper::sendToRole('admin', 'User Dihapus',
            "Akun {$user->name} ({$user->email}) telah dihapus dari sistem.",
            []);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // WISHLIST: Pelanggan/Anggota → Toko Owner
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Produk ditambahkan ke wishlist oleh banyak orang → notifikasi ke toko owner
     */
    public static function produkPopulerDiWishlist(Produk $produk, int $count): void
    {
        if ($count >= 5 && $count % 5 === 0 && $produk->toko?->user) {
            $namaProduk = $produk->nama;
            NotificationHelper::sendToUser($produk->toko->user, 'Produk Populer di Wishlist! ❤️',
                "Produk \"{$namaProduk}\" telah ditambahkan ke wishlist oleh {$count} pembeli.",
                ['produk_id' => $produk->id]);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // BAGI HASIL: Sistem → Anggota
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Bagi hasil dihitung → notifikasi ke Anggota
     */
    public static function bagiHasilDihitung(User $user, string $periode, float $jumlah): void
    {
        $nominal = 'Rp ' . number_format($jumlah, 0, ',', '.');
        NotificationHelper::sendToUser($user, 'Bagi Hasil Dihitung 💰',
            "Bagi hasil periode {$periode} sebesar {$nominal} telah dihitung dan akan segera dicairkan.",
            ['tab' => 'bagihasil']);
    }
}
