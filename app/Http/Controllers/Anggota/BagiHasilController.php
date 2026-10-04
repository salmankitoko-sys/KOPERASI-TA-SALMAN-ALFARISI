<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\BagiHasil;
use Carbon\Carbon;

class BagiHasilController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Riwayat bagi hasil
        $riwayat = BagiHasil::where('user_id', $userId)
            ->orderBy('periode', 'desc')
            ->paginate(12);

        // Estimasi terbaru
        $estimasiTerbaru = BagiHasil::where('user_id', $userId)
            ->latest('periode')
            ->first();

        return view('anggota.bagi-hasil.index', compact('riwayat', 'estimasiTerbaru'));
    }
}
