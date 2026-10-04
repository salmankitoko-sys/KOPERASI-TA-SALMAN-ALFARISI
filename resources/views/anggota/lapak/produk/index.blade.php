<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-indigo-700 leading-tight">Produk & Stok</h2>
                <p class="text-sm text-gray-500 mt-0.5">Kelola katalog produk {{ $lapak->nama_toko }}.</p>
            </div>
            <a href="{{ route('anggota.lapak.produk.create') }}" class="inline-flex px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">
                Tambah Produk
            </a>
        </div>
    </x-slot>

    @php
        $rupiah = fn ($v) => class_exists(\App\Helpers\RupiahHelper::class)
            ? \App\Helpers\RupiahHelper::format($v ?? 0)
            : 'Rp ' . number_format($v ?? 0, 0, ',', '.');
        $statusClass = [
            'aktif' => 'bg-emerald-100 text-emerald-700',
            'pending' => 'bg-amber-100 text-amber-700',
            'nonaktif' => 'bg-gray-100 text-gray-600',
            'habis' => 'bg-red-100 text-red-700',
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
            @if (session('success'))
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold px-4 py-3">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm font-semibold px-4 py-3">{{ session('error') }}</div>
            @endif

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                @if($produkList->isEmpty())
                    <div class="text-center py-12 px-4">
                        <p class="text-sm font-semibold text-gray-700">Belum ada produk.</p>
                        <p class="text-xs text-gray-500 mt-1">Tambahkan produk pertama untuk masuk antrean moderasi marketplace.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($produkList as $produk)
                            <div class="p-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-14 h-14 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden flex items-center justify-center shrink-0">
                                        @if($produk->foto_url)
                                            <img src="{{ $produk->foto_url }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M6 7l1 13h10l1-13M9 7a3 3 0 116 0"/></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 truncate">{{ $produk->nama }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $produk->kategori ?: 'Tanpa kategori' }} &middot; Stok {{ $produk->stok }}</p>
                                        <span class="inline-flex mt-2 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusClass[$produk->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $produk->status }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between gap-3 md:justify-end">
                                    <p class="font-black text-gray-900">{{ $rupiah($produk->harga) }}</p>
                                    <a href="{{ route('anggota.lapak.produk.edit', $produk->id) }}" class="px-3 py-2 rounded-lg border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50">Edit</a>
                                    <form method="POST" action="{{ route('anggota.lapak.produk.destroy', $produk->id) }}" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-2 rounded-lg border border-red-200 text-xs font-bold text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $produkList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
