<x-bendahara-layout>
    @php
        $section = request()->route('section', 'pencairan');
        $pageTitle = match($section) {
            'angsuran' => 'Verifikasi Angsuran',
            'simpanan' => 'Verifikasi Simpanan',
            default => 'Pencairan Dana',
        };
        $pageDescription = match($section) {
            'angsuran' => 'Periksa pembayaran angsuran anggota dan lanjutkan verifikasi melalui Mutasi Kas.',
            'simpanan' => 'Periksa setoran simpanan anggota dan lanjutkan verifikasi melalui Mutasi Kas.',
            default => 'Setujui pencairan, catat transfer, lalu selesaikan melalui Mutasi Kas.',
        };
    @endphp
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-indigo-700">{{ $pageTitle }}</h2>
                <p class="mt-0.5 text-sm text-gray-500">{{ $pageDescription }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('bendahara.dashboard', ['tab' => 'keuangan']) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:border-indigo-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Keuangan
                </a>
                <a href="{{ route('bendahara.transaksi.mutasi.index') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 shadow-sm">Mutasi Kas</a>
                <a href="{{ route('bendahara.transaksi.rekening.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:border-indigo-300">Rekening Koperasi</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-sm" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            @if (session('info'))
                <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('info') }}
                    </div>
                </div>
            @endif
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @if($section === 'pencairan')
                <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">Menunggu</p>
                            <p class="text-2xl font-bold text-amber-700">{{ $pencairan->count() }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if($section === 'pencairan')
                <div class="rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Siap Transfer</p>
                            <p class="text-2xl font-bold text-blue-700">{{ $pencairanSiapTransfer->count() }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if($section === 'angsuran')
                <div class="rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-50 to-white p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-100">
                            <svg class="h-6 w-6 text-violet-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-violet-600">Angsuran</p>
                            <p class="text-2xl font-bold text-violet-700">{{ $pembayaran->count() }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @if($section === 'simpanan')
                <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100">
                            <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Setoran</p>
                            <p class="text-2xl font-bold text-emerald-700">{{ $setoran->count() }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Section 1: Cairkan Dana (Gabungan Setujui + Transfer) --}}
            @if($section === 'pencairan')
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gradient-to-r from-emerald-50/80 to-white px-6 py-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100">
                                <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h3 id="pencairan" class="scroll-mt-6 text-base font-bold text-gray-900">Cairkan Dana Pembiayaan</h3>
                                <p class="mt-0.5 text-sm text-gray-500">Pilih rekening sumber, tentukan metode pencairan, lalu cairkan dalam satu langkah.</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 w-fit rounded-full bg-emerald-100 px-3.5 py-1.5 text-xs font-bold text-emerald-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $pencairan->count() }} menunggu
                        </span>
                    </div>
                </div>

                @if ($pencairan->isNotEmpty() && $rekeningAktif->isEmpty())
                    <div class="flex flex-col gap-3 border-b border-amber-200 bg-amber-50/60 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <p class="text-sm text-amber-900">Tambahkan atau aktifkan rekening koperasi terlebih dahulu agar pencairan dapat diproses.</p>
                        </div>
                        <a href="{{ route('bendahara.transaksi.rekening.index') }}" class="w-fit shrink-0 rounded-lg bg-amber-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-amber-700">Kelola Rekening</a>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50/80 text-xs uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-6 py-3.5 text-left font-bold">Anggota / Akad</th>
                                <th class="px-6 py-3.5 text-left font-bold">Rekening Tujuan</th>
                                <th class="px-6 py-3.5 text-right font-bold">Nominal</th>
                                <th class="px-6 py-3.5 text-left font-bold">Jadwal</th>
                                <th class="px-6 py-3.5 text-left font-bold min-w-[420px]">Cairkan Dana</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($pencairan as $item)
                                <tr class="align-top transition hover:bg-gray-50/50" x-data="{ metode_{{ $item->id }}: @js(config('payment.disbursement.enabled') ? 'api' : 'manual') }">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">
                                                {{ strtoupper(substr($item->pembiayaan->user->name ?? 'A', 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $item->pembiayaan->user->name ?? '-' }}</p>
                                                <p class="mt-0.5 text-xs text-gray-400">{{ $item->pembiayaan->kode ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-800">{{ $item->bank_tujuan }} - {{ $item->no_rekening_tujuan }}</p>
                                        <p class="mt-0.5 text-xs text-gray-400">a.n. {{ $item->nama_pemilik_rekening }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-base font-bold text-gray-900">Rp {{ number_format($item->nominal_pencairan, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                            {{ $item->tanggal_pencairan?->format('d M Y') ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($rekeningAktif->isEmpty())
                                            <a href="{{ route('bendahara.transaksi.rekening.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                Tambah rekening sumber
                                            </a>
                                        @else
                                            <form method="POST" action="{{ route('bendahara.transaksi.pencairan.cairkan', $item->id) }}" enctype="multipart/form-data" class="rounded-xl border border-gray-200 bg-gray-50/50 p-4">
                                                @csrf

                                                {{-- Pilih Rekening Sumber --}}
                                                <div class="mb-3">
                                                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-gray-500">Rekening Sumber</label>
                                                    <select name="rekening_koperasi_id" required class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                                        <option value="">Pilih rekening koperasi</option>
                                                        @foreach ($rekeningAktif as $rekening)
                                                            <option value="{{ $rekening->id }}">{{ $rekening->nama_bank }} - {{ $rekening->nomor_rekening }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Pilih Metode --}}
                                                <div class="mb-3">
                                                    <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-gray-500">Metode Pencairan</label>
                                                    <div class="flex gap-3">
                                                        @if (config('payment.disbursement.enabled'))
                                                            <label class="flex items-center gap-2 cursor-pointer rounded-lg border px-3 py-2 text-xs font-bold transition"
                                                                :class="metode_{{ $item->id }} === 'api' ? 'border-emerald-300 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-white text-gray-500'">
                                                                <input type="radio" name="metode" value="api" x-model="metode_{{ $item->id }}" class="hidden" @checked(config('payment.disbursement.enabled'))>
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                                Disbursement API
                                                            </label>
                                                        @endif
                                                        <label class="flex items-center gap-2 cursor-pointer rounded-lg border px-3 py-2 text-xs font-bold transition"
                                                            :class="metode_{{ $item->id }} === 'manual' ? 'border-blue-300 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-500'">
                                                            <input type="radio" name="metode" value="manual" x-model="metode_{{ $item->id }}" class="hidden" @checked(! config('payment.disbursement.enabled'))>
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                            Transfer Manual
                                                        </label>
                                                    </div>
                                                </div>

                                                {{-- Form Manual (muncul jika metode=manual) --}}
                                                <div x-show="metode_{{ $item->id }} === 'manual'" x-collapse class="space-y-3">
                                                    <div class="grid gap-3 sm:grid-cols-2">
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-gray-500">Nomor Referensi</label>
                                                            <input type="text" name="nomor_referensi_transfer" placeholder="Contoh: TRF-20250101-001" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-gray-500">Tanggal Transfer</label>
                                                            <input type="date" name="tanggal_transfer" value="{{ now()->toDateString() }}" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="mb-1 block text-xs font-semibold text-gray-500">Bukti Transfer</label>
                                                        <input type="file" name="bukti_transfer" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                                                    </div>
                                                </div>

                                                {{-- Form API (info) --}}
                                                <div x-show="metode_{{ $item->id }} === 'api'" x-collapse>
                                                    <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-xs text-emerald-800">
                                                        <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Dana akan dikirim otomatis ke rekening tujuan via payment gateway. Tidak perlu upload bukti.
                                                    </div>
                                                </div>

                                                {{-- Tombol Cairkan --}}
                                                <div class="mt-4 flex justify-end">
                                                    <button type="submit" onclick="return confirm('Cairkan dana pencairan ini?')"
                                                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 hover:shadow-md">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                        Cairkan Dana
                                                    </button>
                                                </div>
                                            </form>
                                        @endif

                                        {{-- Tolak --}}
                                        <form method="POST" action="{{ route('bendahara.transaksi.pencairan.tolak', $item->id) }}" class="mt-2 flex flex-wrap items-center gap-2">
                                            @csrf
                                            <input type="text" name="catatan" placeholder="Alasan penolakan (opsional)" class="min-w-48 rounded-lg border-gray-300 text-xs shadow-sm focus:border-red-400 focus:ring-red-400">
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-600 transition hover:bg-red-50 hover:border-red-300">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                Tolak
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
                                                <svg class="h-7 w-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <p class="mt-3 text-sm font-medium text-gray-500">Tidak ada permintaan pencairan</p>
                                            <p class="mt-1 text-xs text-gray-400">Semua permintaan sudah diproses</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Section 2: Pencairan Dalam Proses --}}
            @if ($pencairanSiapTransfer->isNotEmpty())
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-blue-50/80 to-white px-6 py-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">Pencairan Dalam Proses</h3>
                                    <p class="mt-0.5 text-sm text-gray-500">Lanjutkan transfer manual atau verifikasi mutasi kas untuk menyelesaikan pencairan.</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 w-fit rounded-full bg-blue-100 px-3.5 py-1.5 text-xs font-bold text-blue-700">
                                {{ $pencairanSiapTransfer->count() }} diproses
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50/80 text-xs uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-6 py-3.5 text-left font-bold">Anggota / Pembiayaan</th>
                                    <th class="px-6 py-3.5 text-left font-bold">Rekening Sumber</th>
                                    <th class="px-6 py-3.5 text-right font-bold">Nominal</th>
                                    <th class="px-6 py-3.5 text-left font-bold">Status Lanjutan</th>
                                    <th class="px-6 py-3.5 text-left font-bold min-w-[360px]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($pencairanSiapTransfer as $item)
                                    @php
                                        $referensi = 'pencairan:' . $item->id;
                                        $mutasi = $mutasiPencairan->get($referensi);
                                    @endphp
                                    <tr class="align-top transition hover:bg-gray-50/50">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-900">{{ $item->pembiayaan->user->name ?? '-' }}</p>
                                            <p class="mt-0.5 text-xs text-gray-400">{{ $item->pembiayaan->kode ?? '-' }} - {{ ucfirst($item->pembiayaan->akad ?? '-') }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-gray-800">{{ $item->rekeningKoperasi->nama_bank ?? '-' }}</p>
                                            <p class="mt-0.5 text-xs text-gray-400">{{ $item->rekeningKoperasi->nomor_rekening ?? '-' }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-base font-bold text-gray-900">Rp {{ number_format($item->nominal_pencairan, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($mutasi && $mutasi->status === 'menunggu_verifikasi')
                                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">Menunggu verifikasi mutasi</span>
                                            @elseif ($item->tanggal_transfer && $item->bukti_transfer)
                                                <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">Transfer tercatat</span>
                                            @elseif ($item->disbursement_status === 'processing')
                                                <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700">API memproses</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-600">Menunggu transfer</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($mutasi && $mutasi->status === 'menunggu_verifikasi')
                                                <a href="{{ route('bendahara.transaksi.mutasi.index', ['q' => $referensi]) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-amber-700">
                                                    Verifikasi di Mutasi Kas
                                                </a>
                                            @elseif ($item->disbursement_status === 'processing')
                                                <span class="inline-flex rounded-lg bg-indigo-50 px-4 py-2 text-xs font-bold text-indigo-700">Menunggu callback gateway</span>
                                            @elseif (! $item->tanggal_transfer || ! $item->bukti_transfer)
                                                <form method="POST" action="{{ route('bendahara.transaksi.pencairan.transfer', $item->id) }}" enctype="multipart/form-data" class="space-y-3 rounded-xl border border-gray-200 bg-gray-50/50 p-4">
                                                    @csrf
                                                    <div class="grid gap-3 sm:grid-cols-2">
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-gray-500">Nomor Referensi</label>
                                                            <input type="text" name="nomor_referensi_transfer" required placeholder="Contoh: TRF-20250101-001" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-xs font-semibold text-gray-500">Tanggal Transfer</label>
                                                            <input type="date" name="tanggal_transfer" required value="{{ now()->toDateString() }}" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="mb-1 block text-xs font-semibold text-gray-500">Bukti Transfer</label>
                                                        <input type="file" name="bukti_transfer" required accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                                                    </div>
                                                    <button type="submit" onclick="return confirm('Catat transfer pencairan ini?')" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                                                        Catat Transfer
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('bendahara.transaksi.mutasi.index', ['q' => $referensi]) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                                                    Buka Mutasi Kas
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
            @endif
            {{-- Section 3: Transaksi Masuk Menunggu Mutasi --}}
            @if(in_array($section, ['angsuran', 'simpanan'], true))
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gradient-to-r from-violet-50/80 to-white px-6 py-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100">
                                <svg class="h-5 w-5 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div>
                                <h3 id="pembayaran" class="scroll-mt-6 text-base font-bold text-gray-900">Transaksi Masuk Menunggu Mutasi</h3>
                                <p class="mt-0.5 text-sm text-gray-500">Angsuran dan setoran diverifikasi hanya melalui Mutasi Kas agar bukti, saldo, dan jurnal selalu konsisten.</p>
                            </div>
                        </div>
                        <a href="{{ route('bendahara.transaksi.mutasi.index', ['status' => 'menunggu_verifikasi']) }}" class="inline-flex items-center gap-1.5 w-fit rounded-lg bg-violet-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-violet-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Buka Antrian Mutasi
                        </a>
                    </div>
                </div>

                <div class="grid divide-y divide-gray-100">
                    @if($section === 'angsuran')
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-100">
                                <svg class="h-4 w-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <p id="angsuran" class="scroll-mt-6 text-sm font-bold text-gray-800">Pembayaran Angsuran</p>
                            <span class="ml-auto rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-bold text-violet-700">{{ $pembayaran->count() }}</span>
                        </div>
                        <div class="space-y-2">
                            @forelse ($pembayaran->take(5) as $item)
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-gray-100 bg-gray-50/50 px-4 py-3 transition hover:bg-violet-50/50 hover:border-violet-200">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-gray-800">{{ $item->pembiayaan->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">Angsuran ke-{{ $item->angsuran->bulan_ke ?? '-' }}</p>
                                    </div>
                                    <a href="{{ route('bendahara.transaksi.mutasi.index', ['q' => 'pembayaran_angsuran:' . $item->id]) }}" class="shrink-0 inline-flex items-center gap-1 rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-violet-700">
                                        Verifikasi
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            @empty
                                <div class="flex flex-col items-center rounded-xl border border-dashed border-gray-200 py-8 text-center">
                                    <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="mt-2 text-sm text-gray-500">Tidak ada angsuran menunggu</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    @endif
                    @if($section === 'simpanan')
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <p id="simpanan" class="scroll-mt-6 text-sm font-bold text-gray-800">Setoran Simpanan</p>
                            <span class="ml-auto rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">{{ $setoran->count() }}</span>
                        </div>
                        <div class="space-y-2">
                            @forelse ($setoran->take(5) as $item)
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-gray-100 bg-gray-50/50 px-4 py-3 transition hover:bg-emerald-50/50 hover:border-emerald-200">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-gray-800">{{ $item->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ ucfirst($item->jenis_simpanan) }}</p>
                                    </div>
                                    <a href="{{ route('bendahara.transaksi.mutasi.index', ['q' => 'setoran_simpanan:' . $item->id]) }}" class="shrink-0 inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-700">
                                        Verifikasi
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            @empty
                                <div class="flex flex-col items-center rounded-xl border border-dashed border-gray-200 py-8 text-center">
                                    <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="mt-2 text-sm text-gray-500">Tidak ada setoran menunggu</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    @endif
                </div>
            </section>
            @endif

        </div>
    </div>
</x-bendahara-layout>
