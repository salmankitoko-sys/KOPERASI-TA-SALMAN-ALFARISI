<section x-show="activeTab === 'keuangan'" x-cloak>
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Ringkasan Keuangan Koperasi</h3>
            <p class="text-sm text-gray-500">Pantau posisi kas dan transaksi aktual yang sudah tercatat oleh Bendahara.</p>
        </div>
        <div class="inline-flex items-center rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">
            Data ledger aktual
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-gray-500">Saldo bersih</p>
            <p class="mt-2 text-xl font-semibold text-emerald-700">{{ $keuanganSummary['saldo_bersih'] ?? 'Rp 0' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-gray-500">Total kas masuk</p>
            <p class="mt-2 text-xl font-semibold text-gray-900">{{ $keuanganSummary['kas_masuk'] ?? 'Rp 0' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-gray-500">Total kas keluar</p>
            <p class="mt-2 text-xl font-semibold text-rose-600">{{ $keuanganSummary['kas_keluar'] ?? 'Rp 0' }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-gray-500">Menunggu konfirmasi</p>
            <p class="mt-2 text-xl font-semibold text-amber-600">{{ $keuanganSummary['menunggu_konfirmasi'] ?? 0 }}</p>
        </div>
    </div>

    <section class="mb-6 border-y border-gray-200 bg-white">
        <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h4 class="text-base font-semibold text-gray-900">Pengawasan Keuangan</h4>
                <p class="mt-1 text-sm text-gray-500">Akses proses pencairan, verifikasi mutasi, dan rekening sumber koperasi.</p>
            </div>
            <span class="w-fit rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Dilaksanakan Bendahara</span>
        </div>
        <div class="grid divide-y divide-gray-100 md:grid-cols-3 md:divide-x md:divide-y-0">
            <a href="{{ '#' }}" class="group flex items-center gap-3 px-5 py-4 transition hover:bg-gray-50">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M7 8h10M7 16h10M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />
                    </svg>
                </span>
                <span>
                    <span class="block text-sm font-semibold text-gray-900 group-hover:text-indigo-700">Pencairan Dana</span>
                    <span class="mt-0.5 block text-xs text-gray-500">Pantau pencairan yang dijalankan Bendahara.</span>
                </span>
            </a>
            <a href="{{ '#' }}" class="group flex items-center gap-3 px-5 py-4 transition hover:bg-gray-50">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M7 3v4m10-4v4M5 11h14v8H5z" />
                    </svg>
                </span>
                <span>
                    <span class="block text-sm font-semibold text-gray-900 group-hover:text-indigo-700">Mutasi Kas</span>
                    <span class="mt-0.5 block text-xs text-gray-500">Pantau jurnal yang diverifikasi Bendahara.</span>
                </span>
            </a>
            <a href="{{ '#' }}" class="group flex items-center gap-3 px-5 py-4 transition hover:bg-gray-50">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M5 10V8l7-4 7 4v2M6 10v8m4-8v8m4-8v8m4-8v8M3 20h18" />
                    </svg>
                </span>
                <span>
                    <span class="block text-sm font-semibold text-gray-900 group-hover:text-indigo-700">Rekening Koperasi</span>
                    <span class="mt-0.5 block text-xs text-gray-500">Pantau rekening yang dikelola Bendahara.</span>
                </span>
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-base font-semibold text-gray-900">Posisi Kas Aktual</h4>
                    <p class="mt-1 text-sm text-gray-500">Perbandingan saldo dan arus kas berdasarkan transaksi terverifikasi.</p>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($financialPosition as $item)
                    <div class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $item['label'] }}</p>
                        <p class="mt-2 text-lg font-bold {{ $item['type'] === 'positive' ? 'text-emerald-700' : 'text-rose-600' }}">{{ $item['value'] }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ $item['detail'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
            <h4 class="text-base font-semibold text-gray-900">Status Transaksi</h4>
            <p class="mt-1 text-sm text-gray-500">Jumlah dan nominal transaksi pada setiap tahap verifikasi.</p>

            <div class="mt-4 space-y-3">
                @foreach ($transactionStatuses as $item)
                    <div class="rounded-2xl border border-gray-200 bg-gray-50/70 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $item['label'] }}</p>
                            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-gray-700">{{ $item['count'] }}</span>
                        </div>
                        <p class="mt-1 text-sm font-medium text-gray-600">{{ $item['amount'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        <div class="rounded-3xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-4 py-4">
                <h4 class="text-base font-semibold text-gray-900">Arus kas</h4>
                <p class="text-sm text-gray-500">Ringkasan masuk dan keluar kas dari sumber utama.</p>
            </div>
            <div class="p-4 space-y-3">
                @foreach ($cashFlow as $item)
                    <div class="flex items-center justify-between rounded-2xl border border-gray-200 bg-gray-50/70 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $item['label'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold {{ $item['type'] === 'inflow' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $item['value'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-4 py-4">
                <h4 class="text-base font-semibold text-gray-900">Komposisi Kas Masuk</h4>
                <p class="text-sm text-gray-500">Sumber penerimaan yang sudah diverifikasi berdasarkan kategori transaksi.</p>
            </div>
            <div class="p-4 space-y-4">
                @forelse ($incomeComposition as $item)
                    <div>
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $item['label'] }}</p>
                                <p class="text-xs text-gray-500">{{ $item['count'] }} transaksi</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">{{ $item['value'] }}</p>
                                <p class="text-xs text-gray-500">{{ $item['percentage'] }}%</p>
                            </div>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-indigo-500" style="width: {{ min(100, $item['percentage']) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-gray-500">Belum ada kas masuk terverifikasi.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-4">
            <div>
                <h4 class="text-base font-semibold text-gray-900">Riwayat transaksi keuangan</h4>
                <p class="text-sm text-gray-500">Daftar transaksi terbaru dari simpanan, angsuran, dan marketplace.</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Live data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Pengguna</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Referensi</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Metode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($keuanganTransactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600">{{ $transaction['date'] }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                    {{ $transaction['type'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-900">{{ $transaction['name'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $transaction['reference'] }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $transaction['method'] }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    {{ $transaction['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ $transaction['amount'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada transaksi keuangan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
