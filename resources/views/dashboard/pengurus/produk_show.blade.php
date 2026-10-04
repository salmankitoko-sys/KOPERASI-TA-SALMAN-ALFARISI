@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start gap-6">
            <div class="w-64 h-44 bg-gray-100 rounded-lg overflow-hidden bg-center bg-cover" style="background-image: url('{{ $produk->foto_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=900&q=80' }}')"></div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900">{{ $produk->nama }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $produk->kategori ?? '-' }}</p>
                <p class="text-lg font-extrabold text-emerald-600 mt-3">Rp {{ number_format((float) $produk->harga, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-600 mt-4">{{ $produk->deskripsi ?: 'Tidak ada deskripsi.' }}</p>

                <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500">Toko</p>
                        <p class="font-medium text-gray-900">{{ $produk->toko?->nama_toko ?? 'Toko Anggota' }}</p>
                        <p class="text-xs text-gray-500">{{ $produk->toko?->user?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Stok</p>
                        <p class="font-medium text-gray-900">{{ $produk->stok ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Status: <span class="font-bold">{{ ucfirst($produk->status ?? '-') }}</span></p>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <form method="POST" action="{{ route('pengurus.marketplace.produk.approve', $produk->id) }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-emerald-600 text-white rounded-lg font-semibold" onclick="return confirm('Setujui produk ini?')">Setujui</button>
                    </form>
                    <form method="POST" action="{{ route('pengurus.marketplace.produk.reject', $produk->id) }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 bg-red-500 text-white rounded-lg font-semibold" onclick="return confirm('Tolak produk ini?')">Tolak</button>
                    </form>
                    <a href="{{ route('pengurus.dashboard') }}" class="px-3 py-2 border border-gray-200 rounded-lg">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
