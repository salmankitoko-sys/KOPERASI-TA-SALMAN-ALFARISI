{{-- ========== PANEL: PESANAN SAYA ========== --}}
<div x-show="activeTab === 'pesanan'" x-cloak class="space-y-6">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-3">
                <div>
                    <h4 class="font-bold text-gray-900">Pesanan Saya</h4>
                    <p class="text-xs text-gray-500">Riwayat & status pesanan belanja Anda.</p>
                </div>

            </div>
        </div>

        @if($pesananList->isEmpty())
            <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                <p class="text-sm text-gray-500">Belum ada pesanan.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($pesananList as $ps)
                    <div class="flex items-center justify-between bg-gray-50 border border-gray-100 rounded-xl p-4 text-xs">
                        <div>
                            <p class="font-bold text-gray-900">{{ $ps->nomor_pesanan ?? 'Pesanan' }}</p>
                            <p class="text-gray-500 mt-0.5">
                                {{ $ps->items->pluck('nama_produk_snapshot')->filter()->take(2)->implode(', ') ?: 'Produk pesanan' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 font-bold rounded text-[10px] uppercase">{{ $ps->status ?? 'Diproses' }}</span>
                            <a href="{{ route('anggota.pesanan.index') }}" class="block mt-2 font-bold text-indigo-600 hover:text-indigo-700">Detail</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
