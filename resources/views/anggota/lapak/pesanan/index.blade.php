<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-indigo-700 leading-tight">Pesanan Masuk</h2>
                <p class="text-sm text-gray-500 mt-0.5">Pantau pesanan pembeli untuk toko {{ $lapak->nama_toko }}.</p>
            </div>
            <a href="{{ route('anggota.dashboard') }}" class="hidden sm:inline-flex px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50">
                Dashboard
            </a>
        </div>
    </x-slot>

    @php
        $rupiah = fn ($v) => class_exists(\App\Helpers\RupiahHelper::class)
            ? \App\Helpers\RupiahHelper::format($v ?? 0)
            : 'Rp ' . number_format($v ?? 0, 0, ',', '.');
        $statusClass = [
            'menunggu' => 'bg-amber-100 text-amber-700',
            'dikemas' => 'bg-blue-100 text-blue-700',
            'dikirim' => 'bg-violet-100 text-violet-700',
            'selesai' => 'bg-emerald-100 text-emerald-700',
            'batal' => 'bg-red-100 text-red-700',
        ];
        $paymentClass = [
            'menunggu' => 'bg-gray-100 text-gray-600',
            'menunggu_verifikasi' => 'bg-amber-100 text-amber-700',
            'terverifikasi' => 'bg-emerald-100 text-emerald-700',
            'lunas' => 'bg-emerald-100 text-emerald-700',
            'gagal' => 'bg-red-100 text-red-700',
        ];
        $paymentLabel = [
            'menunggu' => 'Menunggu Pembayaran',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'terverifikasi' => 'Terverifikasi',
            'lunas' => 'Lunas',
            'gagal' => 'Gagal',
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">
            @if (session('success'))
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold px-4 py-3">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-white border border-amber-100 rounded-2xl p-4 shadow-sm">
                    <p class="text-[10px] font-bold text-amber-600 uppercase">Menunggu</p>
                    <p class="text-2xl font-black text-amber-700">{{ $statistik->menunggu ?? 0 }}</p>
                </div>
                <div class="bg-white border border-blue-100 rounded-2xl p-4 shadow-sm">
                    <p class="text-[10px] font-bold text-blue-600 uppercase">Dikemas</p>
                    <p class="text-2xl font-black text-blue-700">{{ $statistik->dikemas ?? 0 }}</p>
                </div>
                <div class="bg-white border border-violet-100 rounded-2xl p-4 shadow-sm">
                    <p class="text-[10px] font-bold text-violet-600 uppercase">Dikirim</p>
                    <p class="text-2xl font-black text-violet-700">{{ $statistik->dikirim ?? 0 }}</p>
                </div>
                <div class="bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase">Selesai</p>
                    <p class="text-2xl font-black text-emerald-700">{{ $statistik->selesai ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                @if($pesananMasuk->isEmpty())
                    <div class="text-center py-12 px-4">
                        <p class="text-sm font-semibold text-gray-700">Belum ada pesanan masuk.</p>
                        <p class="text-xs text-gray-500 mt-1">Pesanan dari pembeli akan muncul di halaman ini.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($pesananMasuk as $pesanan)
                            <div class="p-4">
                                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $pesanan->nomor_pesanan }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Pembeli: {{ $pesanan->pembeli->name ?? 'Pembeli' }} &middot; {{ $pesanan->created_at->translatedFormat('d M Y H:i') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $pesanan->alamat_kirim ?: 'Alamat kirim belum diisi' }}</p>
                                    </div>
                                    <div class="md:text-right">
                                        <p class="font-black text-gray-900">{{ $rupiah($pesanan->total) }}</p>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusClass[$pesanan->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $pesanan->status }}</span>
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $paymentClass[$pesanan->status_pembayaran ?? 'menunggu'] ?? 'bg-gray-100 text-gray-600' }}">
                                                💰 {{ $paymentLabel[$pesanan->status_pembayaran ?? 'menunggu'] ?? $pesanan->status_pembayaran }}
                                            </span>
                                        </div>
                                        <form method="POST" action="{{ route('anggota.lapak.pesanan.status', $pesanan->id) }}" class="mt-3 flex gap-2 md:justify-end">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="rounded-lg border-gray-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                                @foreach(['menunggu' => 'Menunggu', 'dikemas' => 'Dikemas', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $value => $label)
                                                    <option value="{{ $value }}" @selected($pesanan->status === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">Update</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden">
                                    @foreach($pesanan->items as $item)
                                        <div class="px-4 py-3 flex items-center justify-between gap-4 text-xs border-b border-gray-100 last:border-b-0">
                                            <div>
                                                <p class="font-bold text-gray-800">{{ $item->nama_produk_snapshot }}</p>
                                                <p class="text-gray-500 mt-0.5">{{ $item->qty }} x {{ $rupiah($item->harga_satuan) }}</p>
                                            </div>
                                            <p class="font-bold text-gray-900">{{ $rupiah($item->subtotal) }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Bukti Transfer & Konfirmasi Pembayaran --}}
                                @if($pesanan->metode_pembayaran === 'transfer_manual')
                                    <div class="mt-3 rounded-xl bg-amber-50 border border-amber-200 p-4">
                                        <p class="text-xs font-bold text-amber-700 mb-2">💰 Transfer Manual</p>
                                        @if($pesanan->bukti_transfer)
                                            <p class="text-xs text-green-700 font-semibold mb-2">✅ Bukti transfer sudah diupload oleh pembeli.</p>
                                            <a href="{{ Storage::url($pesanan->bukti_transfer) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                                🖼️ Lihat Bukti Transfer
                                            </a>
                                            @if($pesanan->status_pembayaran !== 'terverifikasi')
                                                <form method="POST" action="{{ route('anggota.lapak.pesanan.confirmPayment', $pesanan->id) }}" class="mt-3 inline-flex">
                                                    @csrf
                                                    <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700">
                                                        ✅ Konfirmasi Pembayaran
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <p class="text-xs text-amber-600">⏳ Menunggu bukti transfer dari pembeli.</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $pesananMasuk->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
