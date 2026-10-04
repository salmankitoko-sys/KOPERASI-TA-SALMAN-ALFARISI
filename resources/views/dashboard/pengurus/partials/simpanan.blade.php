<section x-show="activeTab === 'simpanan'" x-cloak>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <span class="dashboard-section-icon bg-emerald-50 text-emerald-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7m-5 5h2" />
                </svg>
            </span>
            <div>
                <h3 class="text-lg font-bold text-gray-900">Simpanan anggota</h3>
                <p class="text-sm text-gray-500">Pantau setoran dan periksa bukti transfer yang dikirim anggota.</p>
            </div>
        </div>
        <a href="{{ '#' }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5 hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10" />
            </svg>
            Verifikasi oleh Bendahara
        </a>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Total Simpanan', 'value' => $rupiah($stats['total_simpanan'] ?? 0), 'color' => 'text-indigo-600', 'bar' => 'bg-indigo-500'],
                ['label' => 'Simpanan Pokok', 'value' => $rupiah($stats['simpanan_pokok'] ?? 0), 'color' => 'text-emerald-600', 'bar' => 'bg-emerald-500'],
                ['label' => 'Simpanan Wajib', 'value' => $rupiah($stats['simpanan_wajib'] ?? 0), 'color' => 'text-amber-600', 'bar' => 'bg-amber-500'],
                ['label' => 'Simpanan Sukarela', 'value' => $rupiah($stats['simpanan_sukarela'] ?? 0), 'color' => 'text-sky-600', 'bar' => 'bg-sky-500'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="mb-3 h-1.5 w-10 rounded-full {{ $card['bar'] }}"></div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $card['label'] }}</p>
                <p class="mt-2 text-lg font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h4 class="font-bold text-gray-900">Riwayat transaksi simpanan</h4>
                <p class="mt-0.5 text-xs text-gray-500">Setoran anggota beserta bukti transfer dan status verifikasinya.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700">
                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                Verifikasi melalui mutasi kas
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/80">
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Anggota</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Jenis</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Jumlah</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Bukti Transfer</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($simpananList ?? [] as $item)
                        <tr class="transition hover:bg-gray-50/80">
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $item->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $item->jenis === 'pokok' ? 'bg-indigo-100 text-indigo-700' : ($item->jenis === 'wajib' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700') }}">
                                    {{ ucfirst($item->jenis) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-bold text-gray-900">{{ $rupiah($item->jumlah) }}</td>
                            <td class="px-4 py-3">
                                @if ($item->bukti_transfer)
                                    <a href="{{ asset('storage/'.$item->bukti_transfer) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs font-bold text-indigo-700 transition hover:bg-indigo-100">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.5-4.5a2.1 2.1 0 00-3-3L8 11a4 4 0 105.7 5.7l6.8-6.8" />
                                        </svg>
                                        Lihat bukti
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">Tidak tersedia</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $item->status === 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $item->status === 'masuk' ? 'Tervalidasi' : ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                <svg class="mx-auto mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 11h.01" />
                                </svg>
                                <p class="font-medium">Belum ada transaksi simpanan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
