{{-- ========== PANEL: PRODUK & STOK ========== --}}
<div x-show="activeTab === 'produk'" x-cloak class="space-y-6">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
        @php
            $produkSummary = $produkSummary ?? (object) ['total' => 0, 'aktif' => 0, 'pending' => 0, 'nonaktif' => 0, 'habis' => 0, 'stok_menipis' => 0, 'stok_total' => 0];
            $statusProdukClass = [
                'aktif' => 'bg-emerald-100 text-emerald-700',
                'pending' => 'bg-amber-100 text-amber-700',
                'nonaktif' => 'bg-gray-100 text-gray-600',
                'habis' => 'bg-red-100 text-red-700',
            ];
            $akadLabel = [
                'murabahah' => 'Murabahah',
                'salam' => 'Salam',
                'istishna' => 'Istishna',
            ];
        @endphp

        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between mb-5">
            <div>
                <h4 class="font-bold text-gray-900 mb-1">Produk & Stok</h4>
                <p class="text-xs text-gray-500">Produk dapat diedit atau dihapus kapan saja, termasuk saat masih menunggu moderasi.</p>
            </div>
            @if(isset($lapak) && $lapak)
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('anggota.lapak.produk.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">Daftar Produk</a>
                    <a href="{{ route('anggota.lapak.produk.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 transition">Tambah Produk</a>
                </div>
            @endif
        </div>

        @if(!isset($lapak) || !$lapak)
            <div class="text-center py-10 bg-amber-50/40 rounded-xl border border-dashed border-amber-200">
                <p class="text-sm font-semibold text-amber-800">Buka toko terlebih dahulu.</p>
                <p class="text-xs text-gray-500 mt-1">Produk hanya dapat ditambahkan setelah profil toko dibuat.</p>
                <button type="button" @click="activeTab = 'toko'" class="mt-4 px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition">Buka Toko</button>
            </div>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-5">
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                    <p class="text-[10px] font-bold uppercase text-gray-400">Total</p>
                    <p class="text-lg font-black text-gray-900">{{ $produkSummary->total ?? 0 }}</p>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3">
                    <p class="text-[10px] font-bold uppercase text-emerald-600">Aktif</p>
                    <p class="text-lg font-black text-emerald-700">{{ $produkSummary->aktif ?? 0 }}</p>
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3">
                    <p class="text-[10px] font-bold uppercase text-amber-600">Moderasi</p>
                    <p class="text-lg font-black text-amber-700">{{ $produkSummary->pending ?? 0 }}</p>
                </div>
                <div class="bg-red-50 border border-red-100 rounded-xl p-3">
                    <p class="text-[10px] font-bold uppercase text-red-600">Stok Menipis</p>
                    <p class="text-lg font-black text-red-700">{{ $produkSummary->stok_menipis ?? 0 }}</p>
                </div>
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-3">
                    <p class="text-[10px] font-bold uppercase text-indigo-600">Unit Stok</p>
                    <p class="text-lg font-black text-indigo-700">{{ $produkSummary->stok_total ?? 0 }}</p>
                </div>
            </div>

            @if($produkList->isEmpty())
                <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-sm font-semibold text-gray-700">Belum ada produk yang diunggah.</p>
                    <p class="text-xs text-gray-500 mt-1">Tambahkan produk pertama agar toko masuk proses moderasi marketplace.</p>
                    @if($lapak->status === 'aktif')
                        <a href="{{ route('anggota.lapak.produk.create') }}" class="mt-4 inline-flex px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition">Tambah Produk Pertama</a>
                    @else
                        <p class="text-[11px] text-amber-700 mt-4">Toko masih menunggu verifikasi pengurus.</p>
                    @endif
                </div>
            @else
                <div class="space-y-3">
                @foreach($produkList as $prod)
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-gray-50 border border-gray-100 rounded-xl p-4 text-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 overflow-hidden flex items-center justify-center shrink-0">
                                @if($prod->foto_url)
                                    <img src="{{ $prod->foto_url }}" alt="{{ $prod->nama }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M6 7l1 13h10l1-13M9 7a3 3 0 116 0"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 truncate">{{ $prod->nama ?? 'Produk' }}</p>
                                <p class="text-gray-500 mt-0.5">Stok: {{ $prod->stok ?? 0 }} unit &middot; {{ $akadLabel[$prod->akad] ?? ucfirst($prod->akad ?? '-') }}</p>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusProdukClass[$prod->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $prod->status ?? 'pending' }}</span>
                                    @if(($prod->stok ?? 0) <= 5)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">Perlu Restok</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 sm:justify-end shrink-0">
                            <span class="font-bold text-gray-900 block">{{ $rupiah($prod->harga ?? 0) }}</span>
                            <a href="{{ route('anggota.lapak.produk.edit', $prod->id) }}" class="inline-flex rounded-lg border border-indigo-200 px-3 py-2 text-[11px] font-bold text-indigo-600 hover:bg-indigo-50">Edit</a>
                            <form method="POST" action="{{ route('anggota.lapak.produk.destroy', $prod->id) }}" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg border border-red-200 px-3 py-2 text-[11px] font-bold text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
                </div>
            @endif
        @endif
    </div>
</div>
