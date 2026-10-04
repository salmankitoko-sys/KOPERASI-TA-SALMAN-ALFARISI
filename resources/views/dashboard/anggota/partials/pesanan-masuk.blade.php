{{-- ========== PANEL: PESANAN MASUK (LAPAK) ========== --}}
<div x-show="activeTab === 'pesanan-masuk'" x-cloak class="space-y-6">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-3">
                <div>
                    <h4 class="font-bold text-gray-900">Pesanan Masuk</h4>
                    <p class="text-xs text-gray-500">Daftar pesanan dari pembeli ke toko Anda.</p>
                </div>

            </div>
            @if(isset($lapak) && $lapak)
                <a href="{{ route('anggota.lapak.pesanan.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1">
                    Kelola Pesanan
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            @endif
        </div>

        @php
            $pesananMasuk = $pesananMasuk ?? collect();
            $statistikPesananMasuk = $statistikPesananMasuk ?? (object) ['menunggu' => 0, 'dikemas' => 0, 'dikirim' => 0, 'selesai' => 0];
            $tokoSummary = $tokoSummary ?? (object) ['omzet_selesai' => 0, 'omzet_bulan_ini' => 0, 'pesanan_aktif' => 0, 'pesanan_total' => 0];
            $statusPesananClass = [
                'menunggu' => 'bg-amber-100 text-amber-700',
                'dikemas' => 'bg-blue-100 text-blue-700',
                'dikirim' => 'bg-violet-100 text-violet-700',
                'selesai' => 'bg-emerald-100 text-emerald-700',
                'batal' => 'bg-red-100 text-red-700',
            ];
            $statusPesananLabel = [
                'menunggu' => 'Menunggu',
                'dikemas' => 'Dikemas',
                'dikirim' => 'Dikirim',
                'selesai' => 'Selesai',
                'batal' => 'Batal',
            ];
        @endphp

        @if(!isset($lapak) || !$lapak)
            <div class="flex flex-col items-center justify-center text-center py-10 px-4 bg-amber-50/40 border border-dashed border-amber-200 rounded-xl mt-4">
                <p class="text-sm text-amber-800 font-semibold">Anda belum memiliki toko.</p>
                <p class="text-[11px] text-gray-400 mt-1">Buka toko terlebih dahulu untuk mulai menerima pesanan.</p>
            </div>
        @elseif($pesananMasuk->isEmpty())
            <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200 mt-4">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                <p class="text-sm text-gray-500">Belum ada pesanan masuk.</p>
                <p class="text-xs text-gray-400 mt-1">Pesanan dari pembeli akan muncul di sini.</p>
            </div>
        @else
            {{-- Statistik --}}
            <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-5 mt-4">
                <div class="bg-amber-50 rounded-xl p-3 border border-amber-100 text-center">
                    <p class="text-[10px] font-bold text-amber-600 uppercase">Menunggu</p>
                    <p class="text-lg font-black text-amber-700">{{ $statistikPesananMasuk->menunggu ?? 0 }}</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-3 border border-blue-100 text-center">
                    <p class="text-[10px] font-bold text-blue-600 uppercase">Dikemas</p>
                    <p class="text-lg font-black text-blue-700">{{ $statistikPesananMasuk->dikemas ?? 0 }}</p>
                </div>
                <div class="bg-violet-50 rounded-xl p-3 border border-violet-100 text-center">
                    <p class="text-[10px] font-bold text-violet-600 uppercase">Dikirim</p>
                    <p class="text-lg font-black text-violet-700">{{ $statistikPesananMasuk->dikirim ?? 0 }}</p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-100 text-center">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase">Selesai</p>
                    <p class="text-lg font-black text-emerald-700">{{ $statistikPesananMasuk->selesai ?? 0 }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-center">
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Total</p>
                    <p class="text-lg font-black text-gray-900">{{ $tokoSummary->pesanan_total ?? 0 }}</p>
                </div>
                <div class="bg-indigo-50 rounded-xl p-3 border border-indigo-100 text-center">
                    <p class="text-[10px] font-bold text-indigo-600 uppercase">Omzet</p>
                    <p class="text-sm font-black text-indigo-700 mt-1">{{ $rupiah($tokoSummary->omzet_selesai ?? 0) }}</p>
                </div>
            </div>

            {{-- Daftar Pesanan --}}
            <div class="space-y-3">
                @foreach($pesananMasuk as $pm)
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-gray-50 border border-gray-100 rounded-xl p-4 text-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-indigo-600">
                                    {{ collect(explode(' ', $pm->pembeli->name ?? 'P'))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('') }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900">{{ $pm->pembeli->name ?? 'Pembeli' }}</p>
                                <p class="text-gray-500 mt-0.5">
                                    {{ $pm->items->count() }} item &middot; {{ $rupiah($pm->total ?? $pm->items->sum('subtotal')) }}
                                </p>
                                <p class="text-gray-500 mt-0.5 truncate">
                                    {{ $pm->items->pluck('nama_produk_snapshot')->filter()->take(2)->implode(', ') ?: 'Produk pesanan' }}
                                </p>
                                <p class="text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($pm->created_at)->translatedFormat('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="sm:text-right shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold inline-block {{ $statusPesananClass[$pm->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusPesananLabel[$pm->status] ?? ucfirst($pm->status) }}
                            </span>
                            <p class="text-[10px] text-gray-400 mt-1">{{ $pm->kode ?? '#' . $pm->id }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

