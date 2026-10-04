<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-xl text-indigo-700 leading-tight">Status Transaksi</h2>
                <p class="text-sm text-gray-500 mt-0.5">Pantau proses pencairan, pembayaran angsuran, dan setoran simpanan Anda.</p>
            </div>
            <a href="{{ route('anggota.dashboard', ['tab' => 'keuangan']) }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Keuangan
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="font-semibold text-gray-900">Transaksi Berjalan</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-3 py-2 text-left">Jenis</th>
                                <th class="px-3 py-2 text-left">Referensi</th>
                                <th class="px-3 py-2 text-left">Nominal</th>
                                <th class="px-3 py-2 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($menungguVerifikasi as $item)
                                @php
                                    $isPencairan = $item instanceof \App\Models\PencairanDana;
                                    $statusLabel = $isPencairan
                                        ? ($item->status === 'menunggu'
                                            ? 'Menunggu persetujuan pengurus'
                                            : ($item->bukti_transfer ? 'Menunggu verifikasi mutasi' : 'Menunggu transfer pengurus'))
                                        : str_replace('_', ' ', $item->status);
                                @endphp
                                <tr>
                                    <td class="px-3 py-2">{{ $item instanceof \App\Models\PembayaranAngsuran ? 'Pembayaran Angsuran' : ($item instanceof \App\Models\SetoranSimpanan ? 'Setoran Simpanan' : 'Pencairan Dana') }}</td>
                                    <td class="px-3 py-2">{{ $item->pembiayaan->kode ?? ($item->jenis_simpanan ?? '-') }}</td>
                                    <td class="px-3 py-2">Rp {{ number_format($item->nominal ?? $item->jumlah_dibayar ?? $item->nominal_pencairan ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-amber-600">{{ $statusLabel }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-gray-500">Tidak ada transaksi yang sedang diproses.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
