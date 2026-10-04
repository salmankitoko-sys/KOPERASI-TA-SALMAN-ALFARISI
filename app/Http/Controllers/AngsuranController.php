<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\Pembiayaan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AngsuranController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $pembiayaanQuery = Pembiayaan::where('user_id', $userId)
            ->whereIn('status', ['disetujui', 'berjalan', 'lunas'])
            ->whereHas('angsuran')
            ->with(['angsuran' => function ($query) {
                $query->orderBy('bulan_ke');
            }])
            ->orderByDesc('created_at')
            ->orderByDesc('id');
        $paginated = $pembiayaanQuery->paginate(10);
        $pembiayaanList = $paginated->getCollection()->map(function (Pembiayaan $pembiayaan) {
            return [
                'id' => $pembiayaan->id,
                'kode' => $pembiayaan->kode,
                'akad' => ucfirst($pembiayaan->akad),
                'status' => $pembiayaan->status,
                'dapat_dibayar' => $pembiayaan->status === 'berjalan',
                'angsuran' => $pembiayaan->angsuran->map(function (Angsuran $angsuran) {
                    $jatuhTempo = $angsuran->jatuh_tempo ? Carbon::parse($angsuran->jatuh_tempo) : null;
                    $tanggalBayar = $angsuran->tanggal_bayar ? Carbon::parse($angsuran->tanggal_bayar) : null;

                    return [
                        'id' => $angsuran->id,
                        'pembiayaan_id' => $angsuran->pembiayaan_id,
                        'bulan_ke' => $angsuran->bulan_ke,
                        'jumlah_bayar' => (float) $angsuran->jumlah_bayar,
                        'pokok' => (float) $angsuran->pokok,
                        'margin' => (float) $angsuran->margin,
                        'sisa_pokok' => (float) $angsuran->sisa_pokok,
                        'jatuh_tempo' => $jatuhTempo ? $jatuhTempo->toDateString() : null,
                        'status' => $angsuran->status,
                        'tanggal_bayar' => $tanggalBayar ? $tanggalBayar->toDateString() : null,
                    ];
                })->values(),
            ];
        })->values();

        $angsuranQuery = Angsuran::whereHas('pembiayaan', function ($query) use ($userId) {
            $query->where('user_id', $userId)->whereIn('status', ['disetujui', 'berjalan', 'lunas']);
        });
        $totalAngsuran = (clone $angsuranQuery)->count();
        $totalLunas = (clone $angsuranQuery)->where('status', 'dibayar')->count();
        $totalBelumBayar = (clone $angsuranQuery)->where('status', 'belum_bayar')->count();
        $totalTerlewat = (clone $angsuranQuery)
            ->where('status', 'belum_bayar')
            ->where('jatuh_tempo', '<', now()->toDateString())
            ->count();

        $pagination = [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem(),
            'total' => $paginated->total(),
            'prev_page_url' => $paginated->previousPageUrl(),
            'next_page_url' => $paginated->nextPageUrl(),
        ];

        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'pembiayaanList' => $pembiayaanList,
                'stats' => [
                    'total' => $totalAngsuran,
                    'lunas' => $totalLunas,
                    'belum' => $totalBelumBayar,
                    'terlewat' => $totalTerlewat,
                ],
                'pagination' => $pagination,
            ]);
        }

        return view('anggota.angsuran.index', compact(
            'pembiayaanList',
            'totalAngsuran',
            'totalLunas',
            'totalBelumBayar',
            'totalTerlewat',
            'pagination'
        ));
    }

    public function bayar($id)
    {
        $angsuran = Angsuran::with('pembiayaan')->findOrFail($id);

        // Pastikan milik user yang login
        if ($angsuran->pembiayaan->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($angsuran->pembiayaan->status !== 'berjalan') {
            return back()->with('info', 'Pembayaran angsuran tersedia setelah pencairan pembiayaan selesai diverifikasi.');
        }

        return redirect()
            ->route('anggota.transaksi.angsuran.create', [
                'pembiayaan_id' => $angsuran->pembiayaan_id,
                'angsuran_id' => $angsuran->id,
            ])
            ->with('info', 'Kirim bukti transfer melalui formulir pembayaran angsuran agar dapat diverifikasi pengurus.');
    }
}
