{{-- ========== PANEL: MARKETPLACE ========== --}}
<div x-show="activeTab === 'marketplace'" x-cloak class="space-y-6">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
        <h4 class="font-bold text-gray-900 mb-1">Jelajahi Produk</h4>
        <p class="text-xs text-gray-500 mb-5">Produk dari sesama anggota koperasi.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pesanan Aktif</p>
                <p class="text-lg font-black text-gray-900 mt-1">{{ $marketplace->pesananAktif ?? 0 }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Wishlist</p>
                <p class="text-lg font-black text-gray-900 mt-1">{{ $marketplace->wishlistCount ?? 0 }}</p>
            </div>
        </div>

        <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
            <p class="text-sm text-gray-500">Katalog produk marketplace tersedia di halaman belanja publik.</p>
            <div class="mt-4 flex flex-col sm:flex-row justify-center gap-2">
                <a href="{{ route('marketplace') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 text-xs font-bold text-white hover:bg-indigo-700 transition">Buka Marketplace</a>
                <a href="{{ route('marketplace.cart') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">Lihat Keranjang</a>
            </div>
        </div>
    </div>
</div>
