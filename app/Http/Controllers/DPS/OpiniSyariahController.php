<?php

namespace App\Http\Controllers\DPS;

use App\Http\Controllers\Controller;
use App\Models\OpiniSyariah;
use App\Support\NotificationHelper;
use App\Support\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OpiniSyariahController extends Controller
{
    /**
     * Daftar opini syariah.
     */
    public function index(Request $request)
    {
        Gate::authorize('dps.kelola-opini');

        $query = OpiniSyariah::with(['dps']);

        if ($request->filled('hasil')) {
            $query->where('hasil', $request->hasil);
        }
        if ($request->filled('jenis_objek')) {
            $query->where('jenis_objek', $request->jenis_objek);
        }

        $list = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success'    => true,
            'data'       => $list->items(),
            'pagination' => [
                'current_page' => $list->currentPage(),
                'last_page'    => $list->lastPage(),
                'per_page'     => $list->perPage(),
                'total'        => $list->total(),
            ],
            'stats' => [
                'disetujui'   => OpiniSyariah::where('hasil', 'Disetujui')->count(),
                'perlu_revisi' => OpiniSyariah::where('hasil', 'Perlu Revisi')->count(),
                'ditolak'     => OpiniSyariah::where('hasil', 'Ditolak')->count(),
            ],
        ]);
    }

    /**
     * Detail opini syariah.
     */
    public function show($id)
    {
        Gate::authorize('dps.kelola-opini');

        $opini = OpiniSyariah::with(['dps', 'pembiayaan', 'produk'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $opini,
        ]);
    }

    /**
     * Buat opini syariah baru (review akad / produk).
     */
    public function store(Request $request)
    {
        Gate::authorize('dps.kelola-opini');

        $request->validate([
            'jenis_objek' => 'required|in:pembiayaan,produk',
            'objek_id'    => 'required|integer',
            'hasil'       => 'required|in:Disetujui,Perlu Revisi,Ditolak',
            'catatan'     => 'nullable|string|max:3000',
        ]);

        // Validasi objek eksis
        if ($request->jenis_objek === 'pembiayaan') {
            $exists = \App\Models\Pembiayaan::whereKey($request->objek_id)->exists();
        } else {
            $exists = \App\Models\Produk::whereKey($request->objek_id)->exists();
        }

        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Objek ' . $request->jenis_objek . ' tidak ditemukan.',
            ], 404);
        }

        $opini = OpiniSyariah::create([
            'nomor_opini'         => 'OPN-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
            'jenis_objek'         => $request->jenis_objek,
            'objek_id'            => $request->objek_id,
            'hasil'               => $request->hasil,
            'catatan'             => $request->catatan,
            'ditandatangani_oleh' => auth()->id(),
            'tanggal_opini'       => now()->toDateString(),
        ]);

        // Notifikasi via NotificationService
        NotificationService::opiniSyariahBaru($opini);

        return response()->json([
            'success' => true,
            'message' => 'Opini syariah ' . $opini->nomor_opini . ' berhasil diterbitkan.',
            'data'    => $opini->load(['dps', 'pembiayaan', 'produk']),
        ], 201);
    }
}

