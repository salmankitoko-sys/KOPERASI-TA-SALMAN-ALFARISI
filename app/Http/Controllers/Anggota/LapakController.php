<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use App\Models\Produk;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use App\Support\NotificationService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class LapakController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $lapak = Toko::where('user_id', $userId)->first();

        if (!$lapak) {
            return view('anggota.lapak.index', ['lapak' => null]);
        }

        // Statistik toko
        $totalProduk = Produk::where('toko_id', $lapak->id)->count();
        $totalPesananMasuk = Pesanan::where('toko_id', $lapak->id)
            ->whereIn('status', ['menunggu', 'dikemas'])
            ->count();
        $totalPenjualan = Pesanan::where('toko_id', $lapak->id)
            ->where('status', 'selesai')
            ->sum('total');

        return view('anggota.lapak.index', compact(
            'lapak',
            'totalProduk',
            'totalPesananMasuk',
            'totalPenjualan'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_toko' => 'required|string|max:255',
            'kategori'  => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $userId = auth()->id();

        // Cek apakah sudah punya toko
        $existing = Toko::where('user_id', $userId)->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah memiliki toko: ' . $existing->nama_toko);
        }

        $lapak = Toko::create([
            'user_id'   => $userId,
            'nama_toko' => $request->nama_toko,
            'slug'      => Str::slug($request->nama_toko) . '-' . $userId,
            'kategori'  => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'status'    => 'pending',
        ]);

        // Notify pengurus + admin about new toko
        NotificationService::tokoDibuat($lapak);

        return redirect()->route('anggota.lapak.index')
            ->with('success', 'Toko "' . $lapak->nama_toko . '" berhasil dibuat, menunggu verifikasi pengurus.');
    }
}
