<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = $app->make('db');

try {
    $anggotaPending = $db->table('users')
        ->where('role', 'anggota')
        ->where(function ($q) {
            $q->where('status_keanggotaan', 'pending')
              ->orWhereNull('email_verified_at');
        })->count();

    $pengajuanPembiayaan = $db->table('pembiayaan')->where('status', 'diajukan')->count();

    $angsuranJatuhTempo = $db->table('angsuran')
        ->whereRaw('DATE(jatuh_tempo) = CURDATE()')
        ->where(function ($q) {
            $q->where('status', 'belum_bayar')->orWhereNull('status');
        })->count();

    $produkModerasi = $db->table('produk')->where('status', 'pending')->count();

    $recentInbox = $db->table('inbox_entries')
        ->select('id', 'title', 'message', 'created_at', 'is_read')
        ->orderBy('created_at', 'desc')
        ->limit(6)
        ->get();

    $out = [
        'success' => true,
        'data' => [
            'anggotaPending' => $anggotaPending,
            'pengajuanPembiayaan' => $pengajuanPembiayaan,
            'angsuranJatuhTempo' => $angsuranJatuhTempo,
            'produkModerasi' => $produkModerasi,
            'recentInbox' => $recentInbox,
        ],
    ];
    echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
