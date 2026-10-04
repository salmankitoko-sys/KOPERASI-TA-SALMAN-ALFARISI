<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Toko;
use App\Support\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class LapakProdukController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $lapak = Toko::where('user_id', $userId)->first();

        if (!$lapak) {
            return redirect()->route('anggota.lapak.index')
                ->with('error', 'Anda harus memiliki toko terlebih dahulu.');
        }

        $produkList = Produk::where('toko_id', $lapak->id)
            ->latest()
            ->paginate(20);

        return view('anggota.lapak.produk.index', compact('produkList', 'lapak'));
    }

    public function create()
    {
        $userId = auth()->id();
        $lapak = Toko::where('user_id', $userId)->first();

        if (!$lapak) {
            return redirect()->route('anggota.lapak.index')
                ->with('error', 'Anda harus memiliki toko terlebih dahulu.');
        }

        if ($lapak->status !== 'aktif') {
            return redirect()->route('anggota.lapak.index')
                ->with('error', 'Toko Anda belum aktif. Harap tunggu verifikasi dari pengurus.');
        }

        return view('anggota.lapak.produk.create', compact('lapak'));
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        $lapak = Toko::where('user_id', $userId)->first();

        if (!$lapak) {
            return redirect()->route('anggota.lapak.index')
                ->with('error', 'Anda harus memiliki toko terlebih dahulu.');
        }

        $validator = Validator::make($request->all(), [
            'nama'       => 'required|string|max:255',
            'harga'      => 'required|numeric|min:100',
            'stok'       => 'required|integer|min:0',
            'kategori'   => 'nullable|string|max:100',
            'deskripsi'  => 'nullable|string|max:5000',
            'foto_url'   => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $produk = Produk::create([
            'toko_id'    => $lapak->id,
            'nama'       => $request->nama,
            'slug'       => Str::slug($request->nama) . '-' . Str::random(6),
            'deskripsi'  => $request->deskripsi,
            'kategori'   => $request->kategori,
            'harga'      => $request->harga,
            'stok'       => $request->stok,
            'foto_url'   => $request->foto_url,
            'status'     => 'pending', // Langsung masuk moderasi
        ]);

        // Notify pengurus + DPS + admin about new produk
        NotificationService::produkDibuat($produk);

        return redirect()->route('anggota.lapak.produk.index')
            ->with('success', 'Produk "' . $produk->nama . '" berhasil diunggah, menunggu moderasi pengurus.');
    }

    public function edit($id)
    {
        $userId = auth()->id();
        $lapak = Toko::where('user_id', $userId)->first();
        $produk = Produk::where('toko_id', $lapak?->id)->findOrFail($id);

        return view('anggota.lapak.produk.edit', compact('produk', 'lapak'));
    }

    public function update(Request $request, $id)
    {
        $userId = auth()->id();
        $lapak = Toko::where('user_id', $userId)->first();
        $produk = Produk::where('toko_id', $lapak?->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama'       => 'required|string|max:255',
            'harga'      => 'required|numeric|min:100',
            'stok'       => 'required|integer|min:0',
            'kategori'   => 'nullable|string|max:100',
            'deskripsi'  => 'nullable|string|max:5000',
            'foto_url'   => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Jika nama berubah, update slug
        if ($produk->nama !== $request->nama) {
            $produk->slug = Str::slug($request->nama) . '-' . Str::random(6);
        }

        $produk->update([
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
            'kategori'  => $request->kategori,
            'harga'     => $request->harga,
            'stok'      => $request->stok,
            'foto_url'  => $request->foto_url,
            'status'    => 'pending', // Kembali ke moderasi jika ada perubahan
        ]);

        // Notify pengurus + DPS about produk update needing re-moderation
        NotificationService::produkDiUpdate($produk);

        return redirect()->route('anggota.lapak.produk.index')
            ->with('success', 'Produk "' . $produk->nama . '" berhasil diperbarui, menunggu moderasi ulang.');
    }

    public function destroy($id)
    {
        $userId = auth()->id();
        $lapak = Toko::where('user_id', $userId)->first();
        $produk = Produk::where('toko_id', $lapak?->id)->findOrFail($id);

        $produk->delete();

        return redirect()->route('anggota.lapak.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
