{{-- ========== PANEL: KEUANGAN ========== --}}
@php
    // Hitung statistik dari data yang tersedia
    $totalSimpanan = $simpanan->total ?? 0;
    $totalPembiayaan = $pembiayaanAktif->sum('jumlah_pembiayaan');
    $totalAngsuranLunas = $angsuranList->where('status', 'dibayar')->sum('jumlah_bayar');
    $totalAngsuranBelum = $angsuranList->where('status', 'belum_bayar')->sum('jumlah_bayar');
    $totalAngsuranTerlewat = $angsuranList->where('status', 'belum_bayar')->filter(function($a) {
        return $a->jatuh_tempo < now()->toDateString();
    })->sum('jumlah_bayar');
    $jumlahPembiayaanAktif = $pembiayaanAktif->count();
    $pemasukanToko = $tokoSummary->omzet_selesai ?? 0;
@endphp

<div x-show="activeTab === 'keuangan'" x-cloak class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div>
                <h4 class="font-bold text-xl text-gray-900">Ringkasan Keuangan</h4>
                <p class="text-xs text-gray-500 mt-0.5">Pantau seluruh posisi keuangan Anda di koperasi secara terintegrasi.</p>
            </div>

        </div>
        <span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-full font-medium">
            {{ now()->translatedFormat('F Y') }}
        </span>
    </div>

    {{-- Kartu Metrik Utama --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-5 shadow-lg text-white">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-100">Total Simpanan</span>
                <div class="p-1.5 bg-white/20 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 10v2"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black tracking-tight">{{ $rupiah($totalSimpanan) }}</p>
            <p class="text-[11px] text-indigo-200 mt-1">Pokok + Wajib + Sukarela</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-5 shadow-lg text-white">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-100">Pembiayaan Berjalan</span>
                <div class="p-1.5 bg-white/20 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black tracking-tight">{{ $rupiah($totalPembiayaan) }}</p>
            <p class="text-[11px] text-emerald-200 mt-1">{{ $jumlahPembiayaanAktif }} pembiayaan aktif</p>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl p-5 shadow-lg text-white">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-100">Angsuran Lunas</span>
                <div class="p-1.5 bg-white/20 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black tracking-tight">{{ $rupiah($totalAngsuranLunas) }}</p>
            <p class="text-[11px] text-amber-200 mt-1">Total pembayaran angsuran berhasil</p>
        </div>

        <div class="bg-gradient-to-br from-violet-500 to-violet-700 rounded-2xl p-5 shadow-lg text-white">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-violet-100">Pemasukan Toko</span>
                <div class="p-1.5 bg-white/20 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black tracking-tight">{{ $rupiah($pemasukanToko) }}</p>
            <p class="text-[11px] text-violet-200 mt-1">Total dari pesanan selesai</p>
        </div>
    </div>

    <section class="border-y border-gray-200 bg-white">
        <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h5 class="text-base font-semibold text-gray-900">Transaksi Saya</h5>
                <p class="mt-1 text-sm text-gray-500">Ajukan transaksi dan pantau prosesnya tanpa akses ke data kas koperasi.</p>
            </div>
            <span class="w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Akses Anggota</span>
        </div>
        <div class="grid divide-y divide-gray-100 sm:grid-cols-2 xl:grid-cols-3 xl:divide-x xl:divide-y-0">

            <a href="{{ route('anggota.transaksi.pencairan.create') }}" class="group flex items-center gap-3 px-5 py-4 transition hover:bg-gray-50">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </span>
                <span>
                    <span class="block text-sm font-semibold text-gray-900 group-hover:text-indigo-700">Pencairan Dana</span>
                    <span class="mt-0.5 block text-xs text-gray-500">Kirim rekening tujuan pembiayaan.</span>
                </span>
            </a>
            <a href="{{ route('anggota.transaksi.angsuran.create') }}" class="group flex items-center gap-3 px-5 py-4 transition hover:bg-gray-50">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span>
                    <span class="block text-sm font-semibold text-gray-900 group-hover:text-indigo-700">Bayar Angsuran</span>
                    <span class="mt-0.5 block text-xs text-gray-500">Kirim bukti pembayaran angsuran.</span>
                </span>
            </a>
            <a href="{{ route('anggota.payments.simpanan.qris') }}" class="group flex items-center gap-3 px-5 py-4 transition hover:bg-gray-50">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M5 7v10a2 2 0 002 2h10a2 2 0 002-2V7M16 11h.01" />
                    </svg>
                </span>
                <span>
                    <span class="block text-sm font-semibold text-gray-900 group-hover:text-indigo-700">Setor Simpanan</span>
                    <span class="mt-0.5 block text-xs text-gray-500">Bayar setoran langsung melalui QRIS.</span>
                </span>
            </a>
        </div>
    </section>

    {{-- Detail Keuangan 2 Kolom --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Kolom Kiri: Rincian Simpanan --}}
        <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h5 class="font-bold text-gray-900">Rincian Simpanan</h5>
                <button @click="activeTab = 'simpanan'" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1">
                    Detail
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                        <span class="text-sm text-gray-600 font-medium">Simpanan Pokok</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $rupiah($simpanan->pokok) }}</span>
                </div>
                <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-sm text-gray-600 font-medium">Simpanan Wajib</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $rupiah($simpanan->wajib) }}</span>
                </div>
                <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <span class="text-sm text-gray-600 font-medium">Simpanan Sukarela</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900">{{ $rupiah($simpanan->sukarela) }}</span>
                </div>
                <div class="flex items-center justify-between bg-indigo-600 rounded-xl px-4 py-3">
                    <span class="text-sm font-bold text-white">Total Simpanan</span>
                    <span class="text-sm font-black text-white">{{ $rupiah($totalSimpanan) }}</span>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Rincian Pembiayaan & Angsuran --}}
        <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h5 class="font-bold text-gray-900">Ringkasan Pembiayaan & Angsuran</h5>
                <button @click="activeTab = 'angsuran'" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1">
                    Detail
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>

            {{-- Progress Summary --}}
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-emerald-50 rounded-xl p-3 text-center border border-emerald-100">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase">Lunas</p>
                    <p class="text-lg font-black text-emerald-700">{{ $rupiah($totalAngsuranLunas) }}</p>
                </div>
                <div class="bg-amber-50 rounded-xl p-3 text-center border border-amber-100">
                    <p class="text-[10px] font-bold text-amber-600 uppercase">Belum</p>
                    <p class="text-lg font-black text-amber-700">{{ $rupiah($totalAngsuranBelum) }}</p>
                </div>
                <div class="bg-red-50 rounded-xl p-3 text-center border border-red-100">
                    <p class="text-[10px] font-bold text-red-600 uppercase">Terlewat</p>
                    <p class="text-lg font-black text-red-700">{{ $rupiah($totalAngsuranTerlewat) }}</p>
                </div>
            </div>

            {{-- Daftar Pembiayaan Aktif (Ringkas) --}}
            @if($pembiayaanAktif->isNotEmpty())
                <div class="space-y-2">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pembiayaan Aktif</p>
                    @foreach($pembiayaanAktif->take(3) as $p)
                        <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-2.5 text-xs">
                            <div>
                                <span class="font-semibold text-gray-900">{{ $p->kode }}</span>
                                <span class="ml-2 px-1.5 py-0.5 rounded-full text-[10px] font-bold
                                    @switch($p->status)
                                        @case('diajukan') bg-yellow-100 text-yellow-700 @break
                                        @case('disetujui') bg-blue-100 text-blue-700 @break
                                        @case('berjalan') bg-emerald-100 text-emerald-700 @break
                                        @default bg-gray-100 text-gray-600
                                    @endswitch
                                ">{{ $p->status_label ?? $p->status }}</span>
                            </div>
                            <span class="font-bold text-gray-900">{{ $rupiah($p->jumlah_pembiayaan) }}</span>
                        </div>
                    @endforeach
                    @if($pembiayaanAktif->count() > 3)
                        <p class="text-[11px] text-gray-400 text-center mt-1">+{{ $pembiayaanAktif->count() - 3 }} pembiayaan lainnya</p>
                    @endif
                </div>
            @else
                <div class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-sm text-gray-500">Belum ada pembiayaan aktif.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Bagan Visual Perbandingan Keuangan --}}
    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
        <h5 class="font-bold text-gray-900 mb-4">Komposisi Keuangan</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Simpanan Composition Bar --}}
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Komposisi Simpanan</p>
                @php
                    $maxSimpanan = max($simpanan->pokok, $simpanan->wajib, $simpanan->sukarela, 1);
                @endphp
                <div class="space-y-2.5">
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600">Pokok</span>
                            <span class="font-semibold text-gray-900">{{ $rupiah($simpanan->pokok) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-indigo-500 h-2.5 rounded-full" style="width: {{ ($simpanan->pokok / $maxSimpanan) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600">Wajib</span>
                            <span class="font-semibold text-gray-900">{{ $rupiah($simpanan->wajib) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ ($simpanan->wajib / $maxSimpanan) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600">Sukarela</span>
                            <span class="font-semibold text-gray-900">{{ $rupiah($simpanan->sukarela) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ ($simpanan->sukarela / $maxSimpanan) * 100 }}%"></div>
                        </div>
                    </div>
</div>
                </div>
            </div>

            {{-- Angsuran Status Pie-like --}}
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Status Angsuran</p>
                @php
                    $totalAngsuranAll = $angsuranList->count();
                    $totalLunasCount = $angsuranList->where('status', 'dibayar')->count();
                    $totalBelumCount = $angsuranList->where('status', 'belum_bayar')->count();
                    $totalTerlewatCount = $angsuranList->where('status', 'belum_bayar')->filter(function($a) {
                        return $a->jatuh_tempo < now()->toDateString();
                    })->count();
                @endphp
                <div class="grid grid-cols-1 gap-3">
                    {{-- Progress bar total --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex justify-between text-xs mb-2">
                            <span class="text-gray-600 font-medium">Progress Angsuran</span>
                            <span class="font-bold text-gray-900">{{ $totalLunasCount }}/{{ $totalAngsuranAll }}</span>
                        </div>
                        @if($totalAngsuranAll > 0)
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-emerald-500 h-3 rounded-full" style="width: {{ ($totalLunasCount / $totalAngsuranAll) * 100 }}%"></div>
                            </div>
                        @else
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gray-300 h-3 rounded-full" style="width: 100%"></div>
                            </div>
                        @endif
                    </div>

                    {{-- Statistik tambahan --}}
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-emerald-50 rounded-lg p-3 text-center border border-emerald-100">
                            <p class="text-lg font-black text-emerald-700">{{ $totalLunasCount }}</p>
                            <p class="text-[10px] font-bold text-emerald-600 uppercase">Lunas</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3 text-center border border-amber-100">
                            <p class="text-lg font-black text-amber-700">{{ $totalBelumCount - $totalTerlewatCount }}</p>
                            <p class="text-[10px] font-bold text-amber-600 uppercase">Belum</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-3 text-center border border-red-100">
                            <p class="text-lg font-black text-red-700">{{ $totalTerlewatCount }}</p>
                            <p class="text-[10px] font-bold text-red-600 uppercase">Terlewat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifikasi Angsuran --}}
    @if($notifikasiAngsuran->isNotEmpty())
        <div class="bg-red-50 border border-red-200/80 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-lg">âš ï¸</span>
                <h5 class="font-bold text-red-800">Pengingat Angsuran Jatuh Tempo</h5>
            </div>
            <div class="space-y-2">
                @foreach($notifikasiAngsuran as $notif)
                    <div class="flex items-center justify-between bg-white/80 rounded-xl px-4 py-2.5 text-xs border border-red-100">
                        <span class="text-red-800 font-medium">{{ $notif->pesan ?? $notif['pesan'] ?? '' }}</span>
                        <span class="font-bold text-red-700">{{ $rupiah($notif->jumlah ?? $notif['jumlah'] ?? 0) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Info Finansial --}}
    <div class="bg-indigo-50/50 border border-indigo-100/70 rounded-2xl p-5 flex gap-3 items-start">
        <span class="text-lg">ðŸ’¡</span>
        <div class="text-xs text-indigo-900 leading-relaxed">
            <strong>Ringkasan Finansial Terintegrasi:</strong> Halaman ini menampilkan seluruh posisi keuangan Anda sebagai anggota koperasi.
            Data terdiri dari total <strong>Simpanan</strong> (Pokok, Wajib, Sukarela),
            <strong>Pembiayaan Berjalan</strong>, <strong>Riwayat Angsuran</strong>, dan <strong>Pemasukan Toko</strong>.
            Pantau secara berkala untuk mengetahui perkembangan keuangan syariah Anda.
        </div>
    </div>
</div>


