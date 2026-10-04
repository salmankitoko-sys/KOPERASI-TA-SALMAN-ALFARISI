<?php

namespace App\Http\Controllers;

use App\Models\LaporanPengurus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class LaporanPengurusPdfController extends Controller
{
    public function __invoke(LaporanPengurus $laporan): Response
    {
        $user = auth()->user();
        $allowed = ($user->isPengurus() && $laporan->dibuat_oleh === $user->id)
            || ($user->isKetua() && $laporan->status !== 'draf');
        abort_unless($allowed, 403);

        $laporan->load(['pembuat', 'peninjau']);
        $filename = 'laporan-pengurus-'.str($laporan->judul)->slug().'.pdf';

        return Pdf::loadView('pdf.laporan-pengurus', compact('laporan'))
            ->setPaper('a4')
            ->download($filename);
    }
}
