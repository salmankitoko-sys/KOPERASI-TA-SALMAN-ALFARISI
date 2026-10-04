<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Toko;

class MarketplaceController extends Controller
{
    public function index()
    {
        // Ambil semua produk aktif beserta informasi toko
        $produkList = Produk::with(['toko' => function ($q) {
                $q->where('status', 'aktif');
            }])
            ->where('status', 'aktif')
            ->latest()
            ->paginate(12);

        // Kategori unik untuk filter
        $kategoriList = Produk::where('status', 'aktif')
            ->whereNotNull('kategori')
            ->select('kategori')
            ->distinct()
            ->pluck('kategori');

        return view('anggota.marketplace.index', compact('produkList', 'kategoriList'));
    }
}
