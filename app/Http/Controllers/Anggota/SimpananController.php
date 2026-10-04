<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Simpanan;

class SimpananController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Ambil semua riwayat simpanan milik user
        $riwayat = Simpanan::where('user_id', $userId)
            ->whereIn('jenis', ['pokok', 'wajib', 'sukarela'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Hitung total per jenis
        $totalPerJenis = Simpanan::where('user_id', $userId)
            ->where('status', 'masuk')
            ->whereIn('jenis', ['pokok', 'wajib', 'sukarela'])
            ->selectRaw('jenis, SUM(jumlah) as total')
            ->groupBy('jenis')
            ->pluck('total', 'jenis');

        $ringkasan = (object) [
            'pokok' => (float) ($totalPerJenis['pokok'] ?? 0),
            'wajib' => (float) ($totalPerJenis['wajib'] ?? 0),
            'sukarela' => (float) ($totalPerJenis['sukarela'] ?? 0),
            'total' => (float) (collect($totalPerJenis)->sum()),
        ];

        // Setoran menunggu verifikasi
        $menungguVerifikasi = \App\Models\SetoranSimpanan::where('user_id', $userId)
            ->where('status', 'menunggu_verifikasi')
            ->count();

        // Grafik simpanan 6 bulan terakhir
        $grafikBulanan = collect(range(5, 0))->map(function ($i) use ($userId) {
            $bulan = \Carbon\Carbon::now()->subMonths($i);
            return [
                'label' => $bulan->translatedFormat('M'),
                'total' => Simpanan::where('user_id', $userId)
                    ->where('status', 'masuk')
                    ->whereIn('jenis', ['pokok', 'wajib', 'sukarela'])
                    ->whereYear('tanggal', $bulan->year)
                    ->whereMonth('tanggal', $bulan->month)
                    ->sum('jumlah'),
            ];
        });

        // Setoran terakhir
        $setoranTerakhir = Simpanan::where('user_id', $userId)
            ->where('status', 'masuk')
            ->whereIn('jenis', ['pokok', 'wajib', 'sukarela'])
            ->latest('tanggal')
            ->first();

        return view('anggota.simpanan.index', compact(
            'riwayat', 'ringkasan', 'menungguVerifikasi', 'grafikBulanan', 'setoranTerakhir'
        ));
    }

}
