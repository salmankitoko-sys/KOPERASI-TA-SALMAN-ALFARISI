<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-indigo-700 leading-tight">Pesanan Saya</h2>
                <p class="text-sm text-gray-500 mt-0.5">Pantau status belanja marketplace Anda.</p>
            </div>
            <a href="{{ route('marketplace') }}" class="hidden sm:inline-flex px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">
                Belanja Lagi
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
            'menunggu' => 'bg-gray-100 text-gray-700',
            'menunggu_verifikasi' => 'bg-amber-100 text-amber-700',
            'terverifikasi' => 'bg-emerald-100 text-emerald-700',
            'gagal' => 'bg-red-100 text-red-700',
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
                @if($pesananList->isEmpty())
                    <div class="text-center py-12 px-4">
                        <p class="text-sm font-semibold text-gray-700">Belum ada pesanan.</p>
                        <p class="text-xs text-gray-500 mt-1">Pesanan dari checkout marketplace akan muncul di sini.</p>
                        <a href="{{ route('marketplace') }}" class="inline-flex mt-4 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">
                            Mulai Belanja
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($pesananList as $pesanan)
                            <div class="p-4">
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $pesanan->nomor_pesanan }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $pesanan->toko->nama_toko ?? 'Toko Anggota' }} &middot;
                                            {{ $pesanan->created_at->translatedFormat('d M Y H:i') }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $pesanan->alamat_kirim ?: 'Alamat belum diisi' }}</p>
                                    </div>
                                    <div class="lg:text-right">
                                        <p class="font-black text-gray-900">{{ $rupiah($pesanan->total) }}</p>
                                        <div class="flex flex-wrap gap-2 lg:justify-end mt-2">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusClass[$pesanan->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ str_replace('_', ' ', $pesanan->status) }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $paymentClass[$pesanan->status_pembayaran ?? 'menunggu'] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ str_replace('_', ' ', $pesanan->status_pembayaran ?? 'menunggu') }}
                                            </span>
                                        </div>
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
                                    <div class="px-4 py-3 text-xs text-gray-500">
                                        Pengiriman: {{ str_replace('_', ' ', $pesanan->metode_pengiriman ?? '-') }} &middot;
                                        Pembayaran: {{ str_replace('_', ' ', $pesanan->metode_pembayaran ?? '-') }}
                                    </div>
                                </div>

                                {{-- Actions --}}
                                @if(in_array($pesanan->status, ['dikemas', 'dikirim'], true))
                                    <form method="POST" action="{{ route('marketplace.orders.received', $pesanan->id) }}" class="mt-4">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700">
                                            Konfirmasi Diterima
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($pesanan->status_pembayaran, ['menunggu', 'menunggu_verifikasi'], true) && $pesanan->metode_pembayaran === 'transfer_manual')
                                    <a href="{{ route('marketplace.payment', ['orders' => $pesanan->id]) }}" class="inline-flex mt-3 px-4 py-2 rounded-xl bg-amber-500 text-white text-xs font-bold hover:bg-amber-600">
                                        💰 Lihat Instruksi Pembayaran
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $pesananList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
