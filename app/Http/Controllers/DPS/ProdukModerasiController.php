<?php

namespace App\Http\Controllers\DPS;

use App\Http\Controllers\Controller;
use App\Models\OpiniSyariah;
use App\Models\Produk;
use App\Support\NotificationHelper;
use App\Support\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProdukModerasiController extends Controller
{
    /**
     * Daftar produk marketplace untuk moderasi DPS.
     */
    public function index(Request $request)
    {
        Gate::authorize('dps.moderasi-produk');

        $query = Produk::with(['toko.user']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search nama produk / toko
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhereHas('toko', function ($tq) use ($search) {
                        $tq->where('nama_toko', 'like', "%{$search}%");
                    });
            });
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
                'menunggu' => Produk::where('status', 'pending')->count(),
                'aktif'    => Produk::where('status', 'aktif')->count(),
                'nonaktif' => Produk::whereIn('status', ['nonaktif', 'habis'])->count(),
            ],
        ]);
    }

    /**
     * Detail produk.
     */
    public function show($id)
    {
        Gate::authorize('dps.moderasi-produk');

        $produk = Produk::with(['toko.user'])->findOrFail($id);

        $opini = OpiniSyariah::with('dps')
            ->where('jenis_objek', 'produk')
            ->where('objek_id', $produk->id)
            ->latest('tanggal_opini')
            ->first();

        return response()->json([
            'success' => true,
            'produk'  => $produk,
            'opini'   => $opini,
        ]);
    }

    /**
     * Review produk: setujui / minta revisi / tolak.
     * DPS tidak mengedit produk; hanya memberi opini & status.
     */
    public function review(Request $request, $id)
    {
        Gate::authorize('dps.moderasi-produk');

        $request->validate([
            'hasil'   => 'required|in:Disetujui,Perlu Revisi,Ditolak',
            'catatan' => 'nullable|string|max:2000',
        ]);

        $produk = Produk::findOrFail($id);

        // Hanya produk pending yang bisa di-review
        if ($produk->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Produk sudah pernah direview. Status saat ini: ' . $produk->status . '.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Buat opini syariah
            OpiniSyariah::create([
                'nomor_opini'         => 'OPN-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'jenis_objek'         => 'produk',
                'objek_id'            => $produk->id,
                'hasil'               => $request->hasil,
                'catatan'             => $request->catatan,
                'ditandatangani_oleh' => auth()->id(),
                'tanggal_opini'       => now()->toDateString(),
            ]);

            // Update status produk berdasarkan opini
            $statusProduk = match ($request->hasil) {
                'Disetujui'   => 'aktif',
                'Perlu Revisi' => 'pending', // tetap menunggu perbaikan admin
                'Ditolak'     => 'nonaktif',
            };

            $produk->update([
                'status' => $statusProduk,
            ]);

            DB::commit();

            // Notifikasi via NotificationService
            NotificationService::produkDireviewDps($produk, $request->hasil);

            return response()->json([
                'success' => true,
                'message' => 'Review produk "' . $produk->nama . '" selesai. Hasil: ' . $request->hasil . '.',
                'status'  => $statusProduk,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereview produk: ' . $e->getMessage(),
            ], 500);
        }
    }
}

