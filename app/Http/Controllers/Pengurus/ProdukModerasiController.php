<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Support\NotificationService;
use Illuminate\Http\Request;

class ProdukModerasiController extends Controller
{
    /**
     * Tampilkan detail produk untuk pengurus.
     */
    public function show($id)
    {
        $produk = Produk::with(['toko.user'])->findOrFail($id);

        return view('dashboard.pengurus.produk_show', compact('produk'));
    }

    /**
     * Setujui dan aktifkan produk.
     */
    public function approve(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->status === 'aktif') {
            return redirect()->back()->with('success', 'Produk sudah berstatus aktif.');
        }

        $produk->status = 'aktif';
        $produk->save();

        NotificationService::produkDimoderasi($produk, true);

        return redirect()->back()->with('success', 'Produk berhasil disetujui dan diaktifkan.');
    }

    /**
     * Tolak dan nonaktifkan produk.
     */
    public function reject(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->status === 'nonaktif') {
            return redirect()->back()->with('success', 'Produk sudah berstatus nonaktif.');
        }

        $produk->status = 'nonaktif';
        $produk->save();

        NotificationService::produkDimoderasi($produk, false);

        return redirect()->back()->with('success', 'Produk berhasil ditolak dan dinonaktifkan.');
    }
}
