<?php

use App\Models\LaporanPengurus;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$report = new LaporanPengurus([
    'judul' => 'Laporan Operasional Koperasi - Agustus 2026',
    'periode_mulai' => '2026-08-01',
    'periode_selesai' => '2026-08-31',
    'ringkasan' => 'Laporan dibuat otomatis dari data anggota, simpanan, pembiayaan, keuangan, dan marketplace pada periode berjalan.',
    'capaian' => "Anggota baru: 24\nSimpanan masuk: Rp 18.750.000\nPembiayaan berjalan: Rp 42.500.000\nOmzet marketplace: Rp 12.300.000",
    'kendala' => 'Sebagian anggota belum melengkapi pembayaran simpanan wajib pada periode berjalan.',
    'tindak_lanjut' => 'Pengurus akan melakukan pengingat bertahap dan pendampingan pembayaran sebelum penutupan periode berikutnya.',
    'status' => 'dikirim',
    'dikirim_pada' => '2026-08-26 10:00:00',
]);
$report->setRelation('pembuat', new User(['name' => 'Pengurus Koperasi']));
$report->setRelation('peninjau', new User(['name' => 'Ketua Koperasi']));

$output = dirname(__DIR__).'/output/pdf/contoh-laporan-pengurus.pdf';
if (! is_dir(dirname($output))) {
    mkdir(dirname($output), 0777, true);
}
Pdf::loadView('pdf.laporan-pengurus', ['laporan' => $report])->setPaper('a4')->save($output);
echo $output.PHP_EOL;
