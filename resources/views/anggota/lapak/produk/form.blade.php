@php
    $produk = $produk ?? null;
@endphp

<div class="sm:col-span-2">
    <label class="block text-xs font-bold text-gray-600 mb-1">Nama Produk</label>
    <input name="nama" value="{{ old('nama', $produk->nama ?? '') }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
</div>

<div>
    <label class="block text-xs font-bold text-gray-600 mb-1">Harga</label>
    <input name="harga" type="number" min="100" value="{{ old('harga', $produk->harga ?? '') }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
</div>

<div>
    <label class="block text-xs font-bold text-gray-600 mb-1">Stok</label>
    <input name="stok" type="number" min="0" value="{{ old('stok', $produk->stok ?? 0) }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
</div>

<div>
    <label class="block text-xs font-bold text-gray-600 mb-1">Kategori</label>
    <input name="kategori" value="{{ old('kategori', $produk->kategori ?? '') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
</div>

<div class="sm:col-span-2">
    <label class="block text-xs font-bold text-gray-600 mb-1">URL Foto Produk</label>
    <input name="foto_url" value="{{ old('foto_url', $produk->foto_url ?? '') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://...">
</div>

<div class="sm:col-span-2">
    <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi</label>
    <textarea name="deskripsi" rows="4" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea>
</div>

<div class="sm:col-span-2 flex justify-between gap-3">
    <a href="{{ route('anggota.lapak.produk.index') }}" class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50">Batal</a>
    <button class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700">{{ $submitLabel ?? 'Simpan' }}</button>
</div>
