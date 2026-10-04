{{-- ========== PANEL: TOKO SAYA ========== --}}
<div x-show="activeTab === 'toko'" x-cloak class="space-y-6">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
        @php
            $produkSummary = $produkSummary ?? (object) ['total' => 0, 'aktif' => 0, 'pending' => 0, 'stok_menipis' => 0];
            $tokoSummary = $tokoSummary ?? (object) ['omzet_selesai' => 0, 'omzet_bulan_ini' => 0, 'pesanan_aktif' => 0, 'nilai_stok' => 0];
            $statusTokoClass = [
                'aktif' => 'bg-emerald-100 text-emerald-700',
                'pending' => 'bg-amber-100 text-amber-700',
                'nonaktif' => 'bg-gray-100 text-gray-600',
            ];
        @endphp

        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between mb-5">
            <div>
                <h4 class="font-bold text-gray-900 mb-1">Toko Saya</h4>
                <p class="text-xs text-gray-500">Profil, status verifikasi, dan performa marketplace Anda.</p>
            </div>
            @if(!is_null($lapak))
                <a href="{{ route('anggota.lapak.index') }}" class="inline-flex items-center justify-center gap-1 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700 transition">
                    Kelola Toko
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            @endif
        </div>

        @if(is_null($lapak))
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
                <div class="lg:col-span-2 flex flex-col justify-center rounded-xl border border-dashed border-amber-200 bg-amber-50/50 p-5">
                    <p class="text-sm text-amber-800 font-semibold">Anda belum membuka toko.</p>
                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">Isi profil toko terlebih dahulu. Setelah toko dibuat, pengurus akan melakukan verifikasi sebelum produk dapat ditayangkan.</p>
                </div>

                <form method="POST" action="{{ route('anggota.lapak.store') }}" class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Nama Toko</label>
                        <input name="nama_toko" value="{{ old('nama_toko') }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Warung Berkah">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Kategori</label>
                        <input name="kategori" value="{{ old('kategori') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Sembako, fashion, jasa">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="3" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ceritakan produk utama dan area layanan toko Anda.">{{ old('deskripsi') }}</textarea>
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition">Buka Toko Sekarang</button>
                    </div>
                </form>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Nama Toko</p>
                    <p class="text-sm font-bold text-gray-900 mt-1">{{ $lapak->nama_toko }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">{{ $lapak->kategori ?: 'Kategori belum diisi' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Status Toko</p>
                    <span class="inline-flex mt-2 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusTokoClass[$lapak->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $lapak->status }}</span>
                    <p class="text-[11px] text-gray-500 mt-2">{{ $lapak->status === 'aktif' ? 'Siap menerima produk dan pesanan.' : 'Menunggu verifikasi pengurus.' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Omzet Selesai</p>
                    <p class="text-sm font-bold text-gray-900 mt-1">{{ $rupiah($tokoSummary->omzet_selesai ?? 0) }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">Bulan ini {{ $rupiah($tokoSummary->omzet_bulan_ini ?? 0) }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Rating Toko</p>
                    <p class="text-sm font-bold text-gray-900 mt-1">{{ number_format((float) ($lapak->rating_rata ?? 0), 1, ',', '.') }} / 5</p>
                    <p class="text-[11px] text-gray-500 mt-1">{{ $lapak->jumlah_ulasan ?? 0 }} ulasan pembeli</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-5">
                <div class="lg:col-span-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs font-bold text-gray-700 mb-2">Profil Toko</p>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $lapak->deskripsi ?: 'Deskripsi toko belum diisi. Lengkapi deskripsi agar pembeli memahami produk, layanan, dan keunggulan toko Anda.' }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs font-bold text-gray-700 mb-3">Ringkasan Operasional</p>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between"><span class="text-gray-500">Produk aktif</span><span class="font-bold text-gray-900">{{ $produkSummary->aktif ?? 0 }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Menunggu moderasi</span><span class="font-bold text-amber-700">{{ $produkSummary->pending ?? 0 }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Pesanan aktif</span><span class="font-bold text-indigo-700">{{ $tokoSummary->pesanan_aktif ?? 0 }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Nilai stok</span><span class="font-bold text-gray-900">{{ $rupiah($tokoSummary->nilai_stok ?? 0) }}</span></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
