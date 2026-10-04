<?php

namespace App\Http\Controllers;

use App\Models\LaporanPengawasan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class LaporanPengawasanPdfController extends Controller
{
    public function __invoke(LaporanPengawasan $laporan): Response
    {
        $user = auth()->user();
        abort_unless(in_array($user->role, [\App\Models\User::ROLE_DPS, \App\Models\User::ROLE_KETUA], true), 403);
        abort_unless($laporan->status === 'Terbit' || $user->isDps(), 403);

        $laporan->load('dps');
        $filename = 'laporan-pengawasan-dps-'.$laporan->id.'.pdf';

        return Pdf::loadView('pdf.laporan-pengawasan-dps', compact('laporan'))
            ->setPaper('a4')
            ->download($filename);
    }
}