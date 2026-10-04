{{--
    Panel Marketplace
    x-show="activeTab === 'marketplace'"
--}}
<section x-show="activeTab === 'marketplace'" x-cloak>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Marketplace</h3>
            <p class="text-sm text-gray-500">Verifikasi toko, moderasi produk, dan monitoring transaksi.</p>
        </div>
        <div class="flex gap-2">
            <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option>Semua Kategori</option>
                <option>Menunggu Moderasi</option>
                <option>Disetujui</option>
                <option>Ditolak</option>
            </select>
        </div>
    </div>

    {{-- Statistik Marketplace --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @php
            $mkCards = [
                ['label' => 'Toko Pending', 'value' => $stats['toko_pending'] ?? 0, 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
                ['label' => 'Omzet Bulan Ini', 'value' => $rupiah($stats['omzet_marketplace'] ?? 0), 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
                ['label' => 'Anggota Berjualan', 'value' => $stats['penjual_aktif'] ?? 0, 'color' => 'text-amber-600', 'bg' => 'bg-amber-50'],
                ['label' => 'Transaksi Aktif', 'value' => $stats['transaksi_aktif'] ?? 0, 'color' => 'text-sky-600', 'bg' => 'bg-sky-50'],
            ];
        @endphp
        @foreach ($mkCards as $c)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 mb-1">{{ $c['label'] }}</p>
                <p class="text-lg font-bold {{ $c['color'] }}">{{ $c['value'] }}</p>
            </div>
        @endforeach
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- Toko Menunggu Verifikasi --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="px-4 py-3 border-b border-gray-100 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h4 class="font-semibold text-gray-900">Toko Menunggu Verifikasi</h4>
                <p class="text-xs text-gray-500">Pengurus menyetujui toko sebelum anggota dapat menambah produk.</p>
            </div>
            <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-100 px-3 py-1 rounded-full">
                {{ ($tokoVerifikasi ?? collect())->count() }} pending
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Toko</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Pemilik</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Kategori</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Diajukan</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tokoVerifikasi ?? [] as $toko)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $toko->nama_toko }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 max-w-md truncate">{{ $toko->deskripsi ?: 'Deskripsi belum diisi' }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <p class="font-medium text-gray-900">{{ $toko->user->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $toko->user->email ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $toko->kategori ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $toko->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <form method="POST" action="{{ route('pengurus.marketplace.toko.approve', $toko->id) }}">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700 transition font-medium">
                                            Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('pengurus.marketplace.toko.reject', $toko->id) }}" onsubmit="return confirm('Tandai toko ini tidak aktif?')">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-red-500 text-white text-xs rounded-lg hover:bg-red-600 transition font-medium">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l1.5-4h15L21 7M3 7v12a1 1 0 001 1h16a1 1 0 001-1V7" />
                                </svg>
                                <p class="font-medium">Tidak ada toko menunggu verifikasi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Semua Toko Marketplace --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        @php
            $statusTokoClass = [
                'pending' => 'bg-amber-100 text-amber-700',
                'aktif' => 'bg-emerald-100 text-emerald-700',
                'nonaktif' => 'bg-gray-100 text-gray-600',
            ];
        @endphp
        <div class="px-4 py-3 border-b border-gray-100">
            <h4 class="font-semibold text-gray-900">Daftar Toko Marketplace</h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 p-4">
            @forelse ($tokoMarketplace ?? [] as $toko)
                <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/60">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $toko->nama_toko }}</p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $toko->user->name ?? 'N/A' }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $statusTokoClass[$toko->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $toko->status }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-4 text-xs">
                        <div>
                            <p class="text-gray-500">Produk</p>
                            <p class="font-bold text-gray-900">{{ $toko->produk_count ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Pesanan</p>
                            <p class="font-bold text-gray-900">{{ $toko->pesanan_count ?? 0 }}</p>
                        </div>
                    </div>
                    @if($toko->status !== 'aktif')
                        <form method="POST" action="{{ route('pengurus.marketplace.toko.approve', $toko->id) }}" class="mt-4">
                            @csrf
                            <button class="w-full px-3 py-2 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700 transition font-medium">
                                Aktifkan Toko
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('pengurus.marketplace.toko.reject', $toko->id) }}" class="mt-4" onsubmit="return confirm('Nonaktifkan toko ini?')">
                            @csrf
                            <button class="w-full px-3 py-2 border border-red-200 text-red-700 text-xs rounded-lg hover:bg-red-50 transition font-medium">
                                Nonaktifkan
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="md:col-span-2 xl:col-span-3 text-center py-8 text-gray-500">
                    <p class="font-medium">Belum ada toko marketplace</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Produk Menunggu Moderasi --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h4 class="font-semibold text-gray-900">Produk Menunggu Moderasi</h4>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Produk</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Penjual</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Kategori</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-600">Harga</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Tgl Upload</th>
                    <th class="text-center px-4 py-3 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($produkModerasi ?? [] as $produk)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $produk->nama }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $produk->toko->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $produk->kategori ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $rupiah($produk->harga) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $produk->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form method="POST" action="{{ route('pengurus.marketplace.produk.approve', $produk->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700 transition font-medium" onclick="return confirm('Setujui produk ini?')">
                                        Setujui
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('pengurus.marketplace.produk.reject', $produk->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-red-500 text-white text-xs rounded-lg hover:bg-red-600 transition font-medium" onclick="return confirm('Tolak produk ini?')">
                                        Tolak
                                    </button>
                                </form>
                                <a href="{{ route('pengurus.marketplace.produk.show', $produk->id) }}" class="p-1.5 rounded-lg hover:bg-gray-100" title="Detail">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p class="font-medium">Tidak ada produk menunggu moderasi</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
