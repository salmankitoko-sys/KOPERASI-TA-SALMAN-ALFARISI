<x-bendahara-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-indigo-700">{{ request()->routeIs('bendahara.laporan.index') ? 'Laporan Keuangan' : (request()->routeIs('bendahara.keuangan.index') ? 'Ringkasan Keuangan' : 'Mutasi / Jurnal Kas') }}</h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ request()->routeIs('bendahara.laporan.index') ? 'Filter, tinjau, dan ekspor laporan transaksi koperasi.' : 'Verifikasi terpusat untuk semua transaksi masuk/keluar.' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('bendahara.transaksi.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:border-indigo-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Pencairan
                </a>
                <a href="{{ route('bendahara.transaksi.rekening.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:border-indigo-300">Rekening Koperasi</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-sm" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            @if(session('info'))
                <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('info') }}
                    </div>
                </div>
            @endif

            {{-- Filter Panel --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50/80 to-white px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Filter & Pencarian</h3>
                            <p class="mt-0.5 text-sm text-gray-500">Gunakan filter untuk menemukan transaksi tertentu.</p>
                        </div>
                    </div>
                </div>

                <form method="GET" action="{{ route('bendahara.transaksi.mutasi.index') }}" class="p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Cari Judul / Referensi</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik judul atau referensi..." class="w-full rounded-lg border-gray-300 py-2.5 pl-10 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Jenis Transaksi</label>
                            <select name="jenis" class="w-full rounded-lg border-gray-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Jenis</option>
                                <option value="pencairan" {{ request('jenis') == 'pencairan' ? 'selected' : '' }}>Pencairan</option>
                                <option value="angsuran" {{ request('jenis') == 'angsuran' ? 'selected' : '' }}>Angsuran</option>
                                <option value="setoran_simpanan" {{ request('jenis') == 'setoran_simpanan' ? 'selected' : '' }}>Setoran Simpanan</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Rekening</label>
                            <select name="rekening_id" class="w-full rounded-lg border-gray-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Rekening</option>
                                @foreach($rekeningList as $r)
                                    <option value="{{ $r->id }}" {{ request('rekening_id') == $r->id ? 'selected' : '' }}>{{ $r->nama_bank }} - {{ $r->nomor_rekening }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Dari Tanggal</label>
                            <input type="date" name="from" value="{{ request('from') }}" class="w-full rounded-lg border-gray-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-500">Sampai Tanggal</label>
                            <input type="date" name="to" value="{{ request('to') }}" class="w-full rounded-lg border-gray-300 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-gray-100 pt-5">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 hover:shadow-md">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Terapkan Filter
                        </button>
                        <a href="{{ route('bendahara.transaksi.mutasi.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Reset
                        </a>
                        <a href="{{ route('bendahara.transaksi.mutasi.export') . '?' . http_build_query(request()->query()) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:border-indigo-300">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export CSV
                        </a>
                    </div>
                </form>
            </div>

            {{-- Results Table --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100">
                                <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Daftar Mutasi / Jurnal Kas</h3>
                                <p class="mt-0.5 text-sm text-gray-500">{{ $mutasi->total() }} transaksi ditemukan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50/80 text-xs uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-3.5 text-left font-bold">Waktu</th>
                                <th class="px-6 py-3.5 text-left font-bold">Jenis</th>
                                <th class="px-6 py-3.5 text-left font-bold">Judul</th>
                                <th class="px-6 py-3.5 text-right font-bold">Jumlah</th>
                                <th class="px-6 py-3.5 text-left font-bold">Rekening</th>
                                <th class="px-6 py-3.5 text-left font-bold">Status</th>
                                <th class="px-6 py-3.5 text-left font-bold">Referensi</th>
                                <th class="px-6 py-3.5 text-left font-bold">Bukti</th>
                                <th class="px-6 py-3.5 text-left font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($mutasi as $m)
                                <tr class="transition hover:bg-gray-50/50">
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600">{{ $m->created_at->format('d M Y') }}</span>
                                        <span class="ml-1 text-xs text-gray-400">{{ $m->created_at->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($m->jenis_transaksi === 'pencairan')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                Pencairan
                                            </span>
                                        @elseif($m->jenis_transaksi === 'angsuran')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-3 py-1 text-xs font-bold text-violet-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                Angsuran
                                            </span>
                                        @elseif($m->jenis_transaksi === 'setoran_simpanan')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                Simpanan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600">{{ $m->jenis_transaksi }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-900">{{ $m->judul }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-base font-bold text-gray-900">Rp {{ number_format($m->jumlah,0,',','.') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($m->rekening)
                                            <div>
                                                <p class="text-sm font-medium text-gray-800">{{ $m->rekening->nama_bank }}</p>
                                                <p class="text-xs text-gray-400">{{ $m->rekening->nomor_rekening }}</p>
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($m->status === 'menunggu_verifikasi')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Menunggu
                                            </span>
                                        @elseif($m->status === 'diverifikasi')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                Diverifikasi
                                            </span>
                                        @elseif($m->status === 'selesai')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Selesai
                                            </span>
                                        @elseif($m->status === 'ditolak')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600">{{ $m->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($m->referensi)
                                            <span class="inline-flex rounded-lg bg-gray-100 px-2.5 py-1 font-mono text-xs text-gray-600">{{ $m->referensi }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($m->bukti_transfer)
                                            <a href="{{ asset('storage/' . $m->bukti_transfer) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 transition hover:bg-indigo-100">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Lihat Bukti
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($m->status === 'menunggu_verifikasi')
                                            <div class="flex flex-col gap-2">
                                                <form method="POST" action="{{ route('bendahara.transaksi.mutasi.approve', $m->id) }}" class="flex flex-wrap items-center gap-2">
                                                    @csrf
                                                    @if($m->jenis_transaksi !== 'pencairan')
                                                        <select name="rekening_koperasi_id" class="rounded-lg border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <option value="">Rekening (opsional)</option>
                                                            @foreach($rekeningList as $r)
                                                                <option value="{{ $r->id }}">{{ $r->nama_bank }} - {{ $r->nomor_rekening }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                        {{ $m->jenis_transaksi === 'pencairan' ? 'Verifikasi' : 'Approve' }}
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('bendahara.transaksi.mutasi.tolak', $m->id) }}" class="flex items-center gap-2">
                                                    @csrf
                                                    <input type="text" name="catatan" placeholder="Alasan tolak" class="w-32 rounded-lg border-gray-300 text-xs shadow-sm focus:border-red-400 focus:ring-red-400">
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50 hover:border-red-300">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                                                <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            </div>
                                            <p class="mt-4 text-sm font-medium text-gray-500">Tidak ada data mutasi ditemukan</p>
                                            <p class="mt-1 text-xs text-gray-400">Coba ubah filter pencarian atau reset filter</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($mutasi->hasPages())
                    <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                        {{ $mutasi->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-bendahara-layout>
