<?php
namespace App\Http\Controllers;
use App\Models\LaporanBendahara;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
class LaporanBendaharaPdfController extends Controller
{
    public function __invoke(LaporanBendahara $laporan): Response
    {
        $user = auth()->user();
        abort_unless(($user->isBendahara() && $laporan->dibuat_oleh === $user->id) || ($user->isKetua() && $laporan->status !== 'draf'), 403);
        $laporan->load(['pembuat', 'peninjau']);
        return Pdf::loadView('pdf.laporan-bendahara', compact('laporan'))->setPaper('a4')->download('laporan-keuangan-'.str($laporan->judul)->slug().'.pdf');
    }
}
