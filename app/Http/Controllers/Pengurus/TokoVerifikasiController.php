<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use App\Support\NotificationService;
use Illuminate\Http\RedirectResponse;

class TokoVerifikasiController extends Controller
{
    public function approve(int $id): RedirectResponse
    {
        $toko = Toko::with('user')->findOrFail($id);

        if ($toko->status !== 'aktif') {
            $toko->update([
                'status' => 'aktif',
            ]);

            NotificationService::tokoDiverifikasi($toko, true);
        }

        return redirect()
            ->route('pengurus.dashboard', ['tab' => 'marketplace'])
            ->with('success', 'Toko "' . $toko->nama_toko . '" berhasil diverifikasi dan diaktifkan.');
    }

    public function reject(int $id): RedirectResponse
    {
        $toko = Toko::with('user')->findOrFail($id);

        if ($toko->status !== 'nonaktif') {
            $toko->update([
                'status' => 'nonaktif',
            ]);

            NotificationService::tokoDiverifikasi($toko, false);
        }

        return redirect()
            ->route('pengurus.dashboard', ['tab' => 'marketplace'])
            ->with('success', 'Toko "' . $toko->nama_toko . '" ditandai tidak aktif.');
    }
}
