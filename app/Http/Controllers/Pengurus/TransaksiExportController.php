<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransaksiExportController extends Controller
{
    public function export(Request $request)
    {
        $query = \App\Models\TransaksiPembayaran::with('user', 'rekening')->orderBy('created_at', 'desc');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")->orWhere('referensi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_transaksi', $request->input('jenis'));
        }

        if ($request->filled('rekening_id')) {
            $query->where('rekening_koperasi_id', $request->input('rekening_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $fileName = 'mutasi_' . now()->format('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, ['Waktu', 'Jenis', 'Judul', 'Jumlah', 'Rekening', 'Status', 'Referensi', 'User', 'Keterangan']);

            $query->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $r) {
                    fputcsv($handle, [
                        $r->created_at->toDateTimeString(),
                        $r->jenis_transaksi,
                        $r->judul,
                        number_format($r->jumlah, 2, '.', ''),
                        $r->rekening?->nama_bank . ' ' . $r->rekening?->nomor_rekening,
                        $r->status,
                        $r->referensi,
                        $r->user?->name,
                        $r->keterangan,
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
}
