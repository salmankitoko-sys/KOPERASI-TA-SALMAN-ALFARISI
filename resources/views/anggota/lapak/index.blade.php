<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-indigo-700 leading-tight">Toko Saya</h2>
                <p class="text-sm text-gray-500 mt-0.5">Kelola profil toko dan performa marketplace Anda.</p>
            </div>
            <a href="{{ route('anggota.dashboard', ['tab' => 'toko']) }}" class="hidden sm:inline-flex px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50">
                Kembali ke Dashboard
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

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 text-red-800 text-sm font-semibold px-4 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!$lapak)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                        <div class="lg:col-span-2 rounded-xl bg-amber-50 border border-amber-100 p-5">
                            <h3 class="font-bold text-amber-900">Buka toko marketplace</h3>
                            <p class="text-sm text-amber-800/80 mt-2 leading-relaxed">Profil toko akan dikirim ke pengurus untuk verifikasi. Setelah aktif, Anda dapat menambah produk dan menerima pesanan.</p>
                        </div>

                        <form method="POST" action="{{ route('anggota.lapak.store') }}" class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Nama Toko</label>
                                <input name="nama_toko" value="{{ old('nama_toko') }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Kategori</label>
                                <input name="kategori" value="{{ old('kategori') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 mb-1">Deskripsi</label>
                                <textarea name="deskripsi" rows="4" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi') }}</textarea>
                            </div>
                            <div class="sm:col-span-2 flex justify-end">
                                <button class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700">Simpan Toko</button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm lg:col-span-2">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $lapak->nama_toko }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $lapak->kategori ?: 'Kategori belum diisi' }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusClass[$lapak->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $lapak->status }}</span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed mt-4">{{ $lapak->deskripsi ?: 'Deskripsi toko belum diisi.' }}</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase">Produk</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ $totalProduk ?? 0 }}</p>
                        <a href="{{ route('anggota.lapak.produk.index') }}" class="inline-flex mt-3 text-xs font-bold text-indigo-600 hover:text-indigo-700">Kelola Produk</a>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                        <p class="text-xs font-bold text-gray-400 uppercase">Pesanan Aktif</p>
                        <p class="text-2xl font-black text-gray-900 mt-1">{{ $totalPesananMasuk ?? 0 }}</p>
                        <a href="{{ route('anggota.lapak.pesanan.index') }}" class="inline-flex mt-3 text-xs font-bold text-indigo-600 hover:text-indigo-700">Lihat Pesanan</a>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Total Penjualan Selesai</p>
                            <p class="text-2xl font-black text-gray-900 mt-1">{{ $rupiah($totalPenjualan ?? 0) }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('anggota.lapak.produk.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-700">Tambah Produk</a>
                            <a href="{{ route('anggota.pembiayaan.ajukan', ['tujuan' => 'modal_usaha']) }}" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50">Ajukan Modal</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
