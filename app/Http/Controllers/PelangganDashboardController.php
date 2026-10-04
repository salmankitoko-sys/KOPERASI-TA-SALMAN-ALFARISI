<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $userId = auth()->id();

        $stats = [
            'aktif' => Pesanan::where('pembeli_id', $userId)
                ->whereIn('status', ['menunggu', 'dikemas', 'dikirim'])
                ->count(),
            'selesai' => Pesanan::where('pembeli_id', $userId)
                ->where('status', 'selesai')
                ->count(),
            'total_belanja' => Pesanan::where('pembeli_id', $userId)
                ->whereIn('status', ['menunggu', 'dikemas', 'dikirim', 'selesai'])
                ->sum('total'),
            'menunggu' => Pesanan::where('pembeli_id', $userId)->where('status', 'menunggu')->count(),
            'dikemas' => Pesanan::where('pembeli_id', $userId)->where('status', 'dikemas')->count(),
            'dikirim' => Pesanan::where('pembeli_id', $userId)->where('status', 'dikirim')->count(),
            'belum_bayar' => Pesanan::where('pembeli_id', $userId)
                ->whereIn('status_pembayaran', ['menunggu', 'menunggu_verifikasi'])
                ->count(),
        ];

        $pesananTerbaru = Pesanan::where('pembeli_id', $userId)
            ->with(['items', 'toko'])
            ->latest()
            ->take(5)
            ->get();

        $cartCount = collect($request->session()->get('cart', []))->sum();

        $kategoriList = Produk::where('status', 'aktif')
            ->whereNotNull('kategori')
            ->select('kategori')
            ->distinct()
            ->take(8)
            ->pluck('kategori');

        $recommendedProducts = Produk::with('toko')
            ->where('status', 'aktif')
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.pelanggan.index', compact(
            'stats',
            'pesananTerbaru',
            'cartCount',
            'kategoriList',
            'recommendedProducts',
        ));
    }
}
