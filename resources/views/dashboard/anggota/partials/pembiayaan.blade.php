@php
    $formErrors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $tujuanOptions = $tujuanOptions ?? [
        'modal_usaha' => 'Modal Usaha',
        'pembelian_barang' => 'Pembelian Barang',
        'pendidikan' => 'Pendidikan',
        'renovasi' => 'Renovasi',
        'kebutuhan_lainnya' => 'Kebutuhan Lainnya',
    ];
    $selectedTujuan = old('tujuan_pembiayaan', request('tujuan', 'kebutuhan_lainnya'));
@endphp

<div x-show="activeTab === 'pembiayaan'" x-cloak>
{{-- ========== PANEL: PEMBIAYAAN (versi Tailwind) ========== --}}
<div x-data="{
    page: @js($formErrors->any() ? 'form' : (($openPengajuan ?? false) ? 'pilih' : 'list')),
    selectedAkad: @js(strtolower(old('jenis_akad', 'murabahah'))),
    tujuanPembiayaan: @js($selectedTujuan),
    init() {
        if (this.page === 'form') {
            setTimeout(() => window.PembiayaanKalkulator?.switchAkadById(this.selectedAkad || 'murabahah'), 0);
        }
    },
    pilihAkad(id) {
        this.selectedAkad = id;
        this.page = 'form';
        setTimeout(() => window.PembiayaanKalkulator?.switchAkadById(id), 0);
    }
}" class="space-y-6">

    {{-- ================= VIEW: LIST (default) ================= --}}
    <div x-show="page === 'list'" x-cloak class="space-y-6">

        {{-- ---------- CARD 1: STATUS PEMBIAYAAN & ANGSURAN ---------- --}}
        <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-3">
                    <h4 class="font-bold text-gray-900">Pembiayaan & Angsuran</h4>

                </div>
                <button
                    @click="page = 'pilih'"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-700 px-3.5 py-2.5 text-xs font-bold text-white shadow-md shadow-indigo-200 transition-all duration-200 hover:bg-indigo-800 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <span class="text-base leading-none">+</span>
                    <span>Ajukan Pembiayaan Baru</span>
                </button>
            </div>
            <p class="text-xs text-gray-500 mb-5">Daftar pembiayaan yang sedang berjalan.</p>

            @if($pembiayaanAktif->isEmpty())
                <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-sm text-gray-500">Anda belum memiliki pembiayaan aktif.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pembiayaanAktif as $p)
                        <div class="bg-white border border-gray-100 rounded-xl p-4 text-xs shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <span class="font-bold text-gray-900">{{ $p->kode }}</span>
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold
                                        @switch($p->status)
                                            @case('diajukan') bg-yellow-100 text-yellow-700 @break
                                            @case('disetujui') bg-blue-100 text-blue-700 @break
                                            @case('berjalan') bg-emerald-100 text-emerald-700 @break
                                            @case('ditolak') bg-red-100 text-red-700 @break
                                            @default bg-gray-100 text-gray-600
                                        @endswitch
                                    ">{{ ucfirst($p->status) }}</span>
                                </div>
                                <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($p->tanggal_pengajuan)->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-2">
                                <div>
                                    <span class="text-gray-500">Akad</span>
                                    <p class="font-semibold text-gray-900">{{ $p->akad_label }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Tujuan</span>
                                    <p class="font-semibold text-gray-900">{{ $p->tujuan_label ?? 'Kebutuhan Lainnya' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Pembiayaan</span>
                                    <p class="font-semibold text-gray-900">{{ $rupiah($p->jumlah_pembiayaan) }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Angsuran/bulan</span>
                                    <p class="font-semibold text-gray-900">{{ $rupiah($p->angsuran_bulanan) }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Tenor</span>
                                    <p class="font-semibold text-gray-900">{{ $p->tenor }} bulan</p>
                                </div>
                            </div>
                            @if($p->objek_pembiayaan)
                                <p class="text-gray-500 mb-1">Objek: <span class="text-gray-700">{{ $p->objek_pembiayaan }}</span></p>
                            @endif
                            <p class="text-gray-500 mb-1">Dokumen: <span class="text-gray-700">{{ $p->dokumen_count ?? $p->dokumen->count() }} file</span></p>
                            @if($p->status === 'berjalan' || $p->status === 'disetujui')
                                <div class="flex flex-col gap-2 pt-2 border-t border-gray-100 sm:flex-row sm:items-center sm:justify-between">
                                    <span class="text-gray-500">Jatuh tempo berikutnya: <strong class="text-gray-900">{{ $p->jatuhTempo ? \Carbon\Carbon::parse($p->jatuhTempo)->translatedFormat('d M Y') : '-' }}</strong></span>
                                    <span class="text-gray-500">Sisa tagihan: <strong class="text-indigo-600">{{ $rupiah($p->sisaTagihan ?? $p->sisaAngsuran ?? 0) }}</strong></span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if(is_object($notifikasiAngsuran) && method_exists($notifikasiAngsuran, 'isNotEmpty') && $notifikasiAngsuran->isNotEmpty())
                <div class="mt-4 bg-red-50 border border-red-100 rounded-xl p-4 text-xs text-red-800 space-y-1">
                    <p class="font-bold">⚠ Pengingat Angsuran</p>
                    @foreach($notifikasiAngsuran as $notif)
                        <p class="opacity-90">• {{ $notif->pesan ?? '' }}</p>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ================= VIEW: PILIH AKAD ================= --}}
    <div x-show="page === 'pilih'" x-cloak x-data="{
        hovered: null,
        clicked: null,
        visible: false
    }" x-init="setTimeout(() => visible = true, 100)" class="space-y-5">
        <button
            type="button"
            @click="page = 'list'"
            class="flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-800 transition-all hover:gap-3">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Pembiayaan
        </button>

        {{-- Hero Section --}}
        <section class="relative overflow-hidden rounded-2xl border border-gray-200/80 bg-gradient-to-br from-teal-50 via-white to-indigo-50 p-6 sm:p-8 shadow-sm">
            {{-- Floating decorations --}}
            <div class="absolute -right-20 -top-20 h-60 w-60 rounded-full bg-teal-100/30 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 h-48 w-48 rounded-full bg-indigo-100/30 blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 top-1/4 h-3 w-3 rounded-full bg-teal-300/40 animate-pulse pointer-events-none"></div>
            <div class="absolute left-1/4 bottom-1/3 h-2 w-2 rounded-full bg-indigo-300/40 animate-pulse pointer-events-none" style="animation-delay: 1s"></div>

            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                    <div>
                        {{-- Step indicator --}}
                        <div class="flex items-center gap-2 mb-3">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-teal-600 text-[11px] font-bold text-white shadow-sm shadow-teal-200">
                                1
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest text-teal-700">Langkah 1 dari 3</span>
                        </div>
                        <h4 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">Pilih Akad Pembiayaan</h4>
                        <p class="text-sm text-gray-500 mt-1.5 max-w-lg">Pilih jenis akad yang paling sesuai dengan kebutuhan dan tujuan pembiayaan Anda. Setiap akad memiliki mekanisme perhitungan yang berbeda.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="group/step">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-600 text-xs font-bold text-white shadow-lg shadow-teal-200 transition-all group-hover/step:scale-110">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="h-0.5 w-8 rounded-full bg-teal-200 sm:w-12"></div>
                        <div class="group/step">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl border-2 border-dashed border-gray-300 text-xs font-bold text-gray-400 transition-all">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                        <div class="h-0.5 w-8 rounded-full bg-gray-200 sm:w-12"></div>
                        <div class="group/step">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl border-2 border-dashed border-gray-300 text-xs font-bold text-gray-400 transition-all">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Akad Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

            {{-- MURABAHAH --}}
            <button
                type="button"
                @click="clicked = 'murabahah'; pilihAkad('murabahah')"
                @mouseenter="hovered = 'murabahah'"
                @mouseleave="hovered = null"
                :class="clicked === 'murabahah' ? 'scale-95 ring-2 ring-teal-500 ring-offset-2' : ''"
                class="group relative text-left rounded-2xl border border-gray-200 bg-white p-0 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-teal-300 overflow-hidden"
                x-transition:enter="transition ease-out duration-500 delay-100"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            >
                <div class="h-2 bg-gradient-to-r from-teal-400 to-teal-600 transition-all duration-300 group-hover:h-3"></div>
                <div class="p-5 sm:p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-500 to-teal-700 text-white shadow-lg shadow-teal-200 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-teal-700 transition-colors group-hover:bg-teal-100">
                            <span class="h-1.5 w-1.5 rounded-full bg-teal-500"></span>
                            Jual Beli
                        </span>
                    </div>
                    <h5 class="text-lg font-black text-gray-900 group-hover:text-teal-800 transition-colors">Murabahah</h5>
                    <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">Jual beli barang dengan <strong class="text-gray-700">margin keuntungan</strong> yang disepakati antara koperasi dan anggota.</p>
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Harga Jual</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">DP</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Margin</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Angsuran</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-teal-700 transition-all group-hover:gap-2.5">
                            Pilih akad ini
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </span>
                        <div class="flex -space-x-2">
                            <div class="h-7 w-7 rounded-full border-2 border-white bg-teal-100 flex items-center justify-center text-[9px] font-bold text-teal-700">M</div>
                            <div class="h-7 w-7 rounded-full border-2 border-white bg-teal-50 flex items-center justify-center text-[9px] font-bold text-teal-500">u</div>
                        </div>
                    </div>
                </div>
            </button>

            {{-- MUDHARABAH --}}
            <button
                type="button"
                @click="clicked = 'mudharabah'; pilihAkad('mudharabah')"
                @mouseenter="hovered = 'mudharabah'"
                @mouseleave="hovered = null"
                :class="clicked === 'mudharabah' ? 'scale-95 ring-2 ring-blue-500 ring-offset-2' : ''"
                class="group relative text-left rounded-2xl border border-gray-200 bg-white p-0 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-blue-300 overflow-hidden"
                x-transition:enter="transition ease-out duration-500 delay-200"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            >
                <div class="h-2 bg-gradient-to-r from-blue-400 to-blue-600 transition-all duration-300 group-hover:h-3"></div>
                <div class="p-5 sm:p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-white shadow-lg shadow-blue-200 transition-all duration-300 group-hover:scale-110 group-hover:-rotate-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 10v2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-700 transition-colors group-hover:bg-blue-100">
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                            Bagi Hasil
                        </span>
                    </div>
                    <h5 class="text-lg font-black text-gray-900 group-hover:text-blue-800 transition-colors">Mudharabah</h5>
                    <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">Koperasi menyediakan <strong class="text-gray-700">modal usaha</strong>, anggota mengelola dan membagi hasil sesuai nisbah.</p>
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Modal Koperasi</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Nisbah</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Laba Usaha</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 transition-all group-hover:gap-2.5">
                            Pilih akad ini
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </span>
                        <div class="flex -space-x-2">
                            <div class="h-7 w-7 rounded-full border-2 border-white bg-blue-100 flex items-center justify-center text-[9px] font-bold text-blue-700">M</div>
                            <div class="h-7 w-7 rounded-full border-2 border-white bg-blue-50 flex items-center justify-center text-[9px] font-bold text-blue-500">d</div>
                        </div>
                    </div>
                </div>
            </button>

            {{-- MUSYARAKAH --}}
            <button
                type="button"
                @click="clicked = 'musyarakah'; pilihAkad('musyarakah')"
                @mouseenter="hovered = 'musyarakah'"
                @mouseleave="hovered = null"
                :class="clicked === 'musyarakah' ? 'scale-95 ring-2 ring-violet-500 ring-offset-2' : ''"
                class="group relative text-left rounded-2xl border border-gray-200 bg-white p-0 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-violet-300 overflow-hidden"
                x-transition:enter="transition ease-out duration-500 delay-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            >
                <div class="h-2 bg-gradient-to-r from-violet-400 to-violet-600 transition-all duration-300 group-hover:h-3"></div>
                <div class="p-5 sm:p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500 to-violet-700 text-white shadow-lg shadow-violet-200 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full bg-violet-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-violet-700 transition-colors group-hover:bg-violet-100">
                            <span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>
                            Kemitraan
                        </span>
                    </div>
                    <h5 class="text-lg font-black text-gray-900 group-hover:text-violet-800 transition-colors">Musyarakah</h5>
                    <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">Koperasi dan anggota <strong class="text-gray-700">menyertakan modal bersama</strong> dan membagi hasil sesuai porsi.</p>
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Modal Bersama</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Porsi Modal</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Nisbah</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-violet-700 transition-all group-hover:gap-2.5">
                            Pilih akad ini
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </span>
                        <div class="flex -space-x-2">
                            <div class="h-7 w-7 rounded-full border-2 border-white bg-violet-100 flex items-center justify-center text-[9px] font-bold text-violet-700">M</div>
                            <div class="h-7 w-7 rounded-full border-2 border-white bg-violet-50 flex items-center justify-center text-[9px] font-bold text-violet-500">u</div>
                        </div>
                    </div>
                </div>
            </button>

            {{-- IJARAH --}}
            <button
                type="button"
                @click="clicked = 'ijarah'; pilihAkad('ijarah')"
                @mouseenter="hovered = 'ijarah'"
                @mouseleave="hovered = null"
                :class="clicked === 'ijarah' ? 'scale-95 ring-2 ring-amber-500 ring-offset-2' : ''"
                class="group relative text-left rounded-2xl border border-gray-200 bg-white p-0 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-amber-300 overflow-hidden"
                x-transition:enter="transition ease-out duration-500 delay-400"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            >
                <div class="h-2 bg-gradient-to-r from-amber-400 to-amber-600 transition-all duration-300 group-hover:h-3"></div>
                <div class="p-5 sm:p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 text-white shadow-lg shadow-amber-200 transition-all duration-300 group-hover:scale-110 group-hover:-rotate-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-700 transition-colors group-hover:bg-amber-100">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Sewa
                        </span>
                    </div>
                    <h5 class="text-lg font-black text-gray-900 group-hover:text-amber-800 transition-colors">Ijarah</h5>
                    <p class="text-sm text-gray-500 mt-1.5 leading-relaxed"><strong class="text-gray-700">Sewa manfaat aset</strong> dengan ujrah yang jelas dan opsional pembelian di akhir masa sewa.</p>
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Ujrah/Bulan</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Perawatan</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Opsi Beli</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 transition-all group-hover:gap-2.5">
                            Pilih akad ini
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </span>
                        <div class="flex -space-x-2">
                            <div class="h-7 w-7 rounded-full border-2 border-white bg-amber-100 flex items-center justify-center text-[9px] font-bold text-amber-700">I</div>
                            <div class="h-7 w-7 rounded-full border-2 border-white bg-amber-50 flex items-center justify-center text-[9px] font-bold text-amber-500">j</div>
                        </div>
                    </div>
                </div>
            </button>

            {{-- QARDH --}}
            <button
                type="button"
                @click="clicked = 'qardh'; pilihAkad('qardh')"
                @mouseenter="hovered = 'qardh'"
                @mouseleave="hovered = null"
                :class="clicked === 'qardh' ? 'scale-95 ring-2 ring-emerald-500 ring-offset-2' : ''"
                class="group relative text-left rounded-2xl border border-gray-200 bg-white p-0 shadow-sm transition-all duration-300 hover:shadow-xl hover:-translate-y-1 hover:border-emerald-300 overflow-hidden"
                x-transition:enter="transition ease-out duration-500 delay-500"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            >
                <div class="h-2 bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-300 group-hover:h-3"></div>
                <div class="p-5 sm:p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-white shadow-lg shadow-emerald-200 transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700 transition-colors group-hover:bg-emerald-100">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Pinjaman
                        </span>
                    </div>
                    <h5 class="text-lg font-black text-gray-900 group-hover:text-emerald-800 transition-colors">Qardh</h5>
                    <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">Pinjaman <strong class="text-gray-700">tanpa keuntungan</strong>. Kembalikan pokok sesuai tenor tanpa margin atau bunga.</p>
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Pokok Saja</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Tanpa Margin</span>
                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600">Angsuran Tetap</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 transition-all group-hover:gap-2.5">
                            Pilih akad ini
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </span>
                        <div class="flex -space-x-2">
                            <div class="h-7 w-7 rounded-full border-2 border-white bg-emerald-100 flex items-center justify-center text-[9px] font-bold text-emerald-700">Q</div>
                            <div class="h-7 w-7 rounded-full border-2 border-white bg-emerald-50 flex items-center justify-center text-[9px] font-bold text-emerald-500">a</div>
                        </div>
                    </div>
                </div>
            </button>
        </div>

        {{-- Info Section --}}
        <div x-transition:enter="transition ease-out duration-500 delay-600"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-indigo-50/60 to-violet-50/40 p-5 sm:p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-100">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-indigo-900">Belum yakin memilih akad?</h5>
                    <p class="text-xs text-indigo-600/80 mt-1 leading-relaxed">Hubungi pengurus koperasi untuk mendapatkan penjelasan lebih lanjut mengenai masing-masing akad pembiayaan. Pemilihan akad akan menentukan mekanisme perhitungan kewajiban Anda.</p>
                </div>
            </div>
        </div>
    </div>

 <form action="{{ route('anggota.pembiayaan.store') }}" method="POST" id="pembiayaanForm" enctype="multipart/form-data" x-data="{ submitting: false }" @submit="PembiayaanKalkulator.calculateActive(); submitting = true">
    @csrf

    {{-- ================= VIEW: AJUKAN PEMBIAYAAN BARU ================= --}}
    <div x-show="page === 'form'" x-cloak class="space-y-4">

        {{-- tombol kembali --}}
        <button
            type="button"
            @click="page = 'pilih'"
            class="flex items-center gap-2 text-xs font-bold text-gray-500 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Pilih Akad
        </button>

        @if ($formErrors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 text-red-800 text-sm font-semibold px-4 py-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($formErrors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @once('pembiayaan-kalkulator-print-style')
        <style>
            @media print {
                body * { visibility: hidden; }
                #pk_printArea, #pk_printArea * { visibility: visible; }
                #pk_printArea { display: block !important; position: absolute; left: 0; top: 0; }
            }
            #pk_printArea { display: none; }

            /* Hilangkan spinner bawaan pada seluruh kolom angka form pembiayaan. */
            #pembiayaanForm input[type="number"]::-webkit-inner-spin-button,
            #pembiayaanForm input[type="number"]::-webkit-outer-spin-button {
                margin: 0;
                -webkit-appearance: none;
            }
            #pembiayaanForm input[type="number"] {
                appearance: textfield;
                -moz-appearance: textfield;
            }
        </style>
        @endonce

        <main id="pembiayaanKalkulator" class="w-full max-w-[1180px] mx-auto font-sans text-gray-900 leading-relaxed bg-white border border-gray-200 rounded-2xl p-5 sm:p-6 shadow-sm">

          <section class="mb-5 border-b border-gray-100 pb-5">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
              <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-indigo-600 text-[11px] font-bold text-white shadow-sm shadow-indigo-200">
                        2
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest text-indigo-700">Langkah 2 dari 3</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black leading-tight">Lengkapi Ringkasan Pembiayaan</h1>
                <p class="max-w-2xl text-gray-500 text-sm mt-1">Isi angka utama sesuai akad yang dipilih. Formulir resmi tetap diunduh dan diunggah pada langkah berikutnya.</p>
              </div>
              <div class="flex flex-wrap gap-2">
                <button class="group inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 hover:shadow transition-all duration-200" type="button" onclick="PembiayaanKalkulator.downloadPDF()">
                    <svg class="h-3.5 w-3.5 text-gray-400 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Unduh Ringkasan
                </button>
                <button class="group inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 hover:shadow transition-all duration-200" type="button" onclick="window.print()">
                    <svg class="h-3.5 w-3.5 text-gray-400 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
                <button class="group inline-flex items-center gap-1.5 rounded-xl border border-teal-200 bg-teal-50 px-3.5 py-2 text-xs font-bold text-teal-700 shadow-sm hover:bg-teal-100 hover:border-teal-300 hover:shadow transition-all duration-200" type="button" onclick="PembiayaanKalkulator.isiContoh()">
                    <svg class="h-3.5 w-3.5 text-teal-500 group-hover:text-teal-700 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Isi Contoh
                </button>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5">
              <div class="grid gap-1.5">
                <label for="pk_anggota" class="text-[13px] text-gray-500 font-extrabold">Nama anggota</label>
                <input id="pk_anggota" name="nama_anggota" value="{{ auth()->user()->name }}" readonly aria-readonly="true" class="w-full cursor-not-allowed rounded-lg border border-gray-200 bg-gray-100 px-3 py-2.5 text-gray-700 min-h-[44px] focus:outline-none">
              </div>
              <div class="grid gap-1.5">
                <label class="text-[13px] text-gray-500 font-extrabold">Tanggal pengajuan</label>
                <input id="pk_tanggal" name="tanggal_simulasi" type="date" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 text-gray-900 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700">
              </div>
            </div>
          </section>

          <section class="mb-5 border-b border-gray-100 pb-5">
            <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-4">
              <div class="grid gap-3">
                <div class="grid gap-1.5">
                  <label for="tujuan_pembiayaan" class="text-[13px] text-gray-500 font-extrabold">Tujuan pembiayaan</label>
                  <select id="tujuan_pembiayaan" name="tujuan_pembiayaan" x-model="tujuanPembiayaan" onchange="PembiayaanKalkulator.calculateActive()"
                    class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 text-gray-900 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700">
                    @foreach($tujuanOptions as $value => $label)
                      <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                  </select>
                </div>

                <div x-show="tujuanPembiayaan === 'modal_usaha'" x-cloak class="grid gap-3 rounded-lg border border-emerald-100 bg-emerald-50/50 p-4">
                  @if($lapak)
                    <input type="hidden" name="toko_id" value="{{ $lapak->id }}">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                      <div>
                        <span class="block text-xs font-extrabold text-gray-500">Toko terkait</span>
                        <strong class="block text-gray-900 mt-0.5">{{ $lapak->nama_toko }}</strong>
                      </div>
                      <div>
                        <span class="block text-xs font-extrabold text-gray-500">Status toko</span>
                        <strong class="block mt-0.5 {{ $lapak->status === 'aktif' ? 'text-emerald-700' : 'text-amber-700' }}">{{ ucfirst($lapak->status) }}</strong>
                      </div>
                      <div>
                        <span class="block text-xs font-extrabold text-gray-500">Skor kredit</span>
                        <strong class="block text-emerald-700 mt-0.5">{{ $skorKredit->skor ?? 'Menghitung...' }}</strong>
                      </div>
                    </div>
                  @else
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-800">
                      Buat dan aktifkan toko terlebih dahulu sebelum mengajukan modal usaha.
                    </div>
                  @endif

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="grid gap-1.5">
                      <label for="rencana_penggunaan_dana" class="text-[13px] text-gray-500 font-extrabold">Rencana penggunaan dana</label>
                      <textarea id="rencana_penggunaan_dana" name="rencana_penggunaan_dana" rows="4"
                        class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 text-gray-900 focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"
                        placeholder="Contoh: Tambah stok sembako dan perlengkapan toko">{{ old('rencana_penggunaan_dana') }}</textarea>
                    </div>
                    <div class="grid gap-1.5">
                      <label for="estimasi_omzet_usaha" class="text-[13px] text-gray-500 font-extrabold">Estimasi omzet usaha / bulan</label>
                      <input id="estimasi_omzet_usaha" name="estimasi_omzet_usaha" type="number" min="0" value="{{ old('estimasi_omzet_usaha') }}" oninput="PembiayaanKalkulator.calculateActive()"
                        class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 text-gray-900 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"
                        placeholder="Contoh: 15000000">
                      <p class="text-xs text-gray-500">Dipakai pengurus sebagai konteks penilaian, bukan pengganti akad.</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm">
                <p class="font-black text-gray-900">Tujuan dan akad tetap terpisah</p>
                <p class="mt-1 text-gray-500">Tujuan menjelaskan penggunaan dana. Akad menentukan skema syariah dan perhitungan kewajiban.</p>
              </div>
            </div>
          </section>

          {{-- LAYOUT --}}
          <section class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_360px] gap-5 items-start">
            <div class="grid gap-4">

              @include('dashboard.anggota.partials.pembiayaan._murabahah')
              @include('dashboard.anggota.partials.pembiayaan._mudharabah')
              @include('dashboard.anggota.partials.pembiayaan._musyarakah')
              @include('dashboard.anggota.partials.pembiayaan._ijarah')
              @include('dashboard.anggota.partials.pembiayaan._qardh')
            </div>

            {{-- SIDEBAR --}}
            <aside class="grid gap-4 lg:sticky lg:top-4">
              <section class="overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-sm">
                <div class="bg-gradient-to-r from-indigo-50/80 to-white px-5 py-4 border-b border-gray-100">
                  <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100">
                      <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Review Pengajuan</h2>
                        <p class="text-[11px] text-gray-500">Cek estimasi sebelum diajukan.</p>
                    </div>
                  </div>
                </div>
                <div class="p-5 grid gap-3">
                  <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3.5 transition-colors hover:border-indigo-100 hover:bg-indigo-50/30">
                    <span class="block text-gray-500 text-[11px] font-bold uppercase tracking-wider">Jenis akad</span>
                    <strong class="block text-lg mt-0.5 text-gray-900" id="pk_kpiAkad">Murabahah</strong>
                  </div>
                  <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3.5 transition-colors hover:border-teal-100 hover:bg-teal-50/30">
                    <span class="block text-gray-500 text-[11px] font-bold uppercase tracking-wider" id="pk_summaryLabel">Estimasi angsuran bulanan</span>
                    <strong class="block text-lg mt-0.5 text-gray-900" id="pk_summaryValue">Rp&nbsp;0</strong>
                  </div>
                  <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3.5 transition-colors hover:border-emerald-100 hover:bg-emerald-50/30">
                    <span class="block text-gray-500 text-[11px] font-bold uppercase tracking-wider">Total kewajiban</span>
                    <strong class="block text-lg mt-0.5 text-emerald-700" id="pk_kpiTotal">Rp&nbsp;0</strong>
                  </div>
                  <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3.5 transition-colors hover:border-violet-100 hover:bg-violet-50/30">
                    <span class="block text-gray-500 text-[11px] font-bold uppercase tracking-wider">Tenor</span>
                    <strong class="block text-lg mt-0.5 text-gray-900" id="pk_kpiTenor">0 bulan</strong>
                  </div>
                </div>
              </section>
              <section class="overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-sm">
                <div class="bg-gradient-to-r from-gray-50 to-white px-5 py-4 border-b border-gray-100">
                  <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100">
                      <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Data Ringkas</h2>
                        <p class="text-[11px] text-gray-500">Ikhtisar yang masuk ke ringkasan.</p>
                    </div>
                  </div>
                </div>
                <div class="p-5 grid gap-2.5 text-sm border border-gray-100 rounded-xl bg-gray-50/30" id="pk_docPreview"></div>
              </section>
            </aside>
          </section>

          <section id="pk_printArea"><div class="p-8" id="pk_printPage"></div></section>
        </main>

        @include('dashboard.anggota.partials.pembiayaan._dokumen')
        <section class="mt-6 overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-sm">
          <div class="border-b border-emerald-100 bg-emerald-50 px-5 py-4">
            <h2 class="text-sm font-bold text-emerald-900">Rekening Tujuan Pencairan</h2>
            <p class="mt-1 text-xs text-emerald-700">Setelah disetujui Pengurus, permintaan pencairan otomatis masuk ke antrean Bendahara.</p>
          </div>
          <div class="grid gap-4 p-5 md:grid-cols-2">
            <label class="grid gap-1.5 text-sm font-semibold text-gray-700">
              Nama bank
              <input type="text" name="bank_tujuan" value="{{ old('bank_tujuan') }}" required maxlength="255" placeholder="Contoh: Bank Syariah Indonesia" class="rounded-xl border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              @error('bank_tujuan') <span class="text-xs font-normal text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="grid gap-1.5 text-sm font-semibold text-gray-700">
              Nomor rekening
              <input type="text" name="no_rekening_tujuan" value="{{ old('no_rekening_tujuan') }}" required maxlength="100" inputmode="numeric" placeholder="Nomor rekening anggota" class="rounded-xl border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              @error('no_rekening_tujuan') <span class="text-xs font-normal text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="grid gap-1.5 text-sm font-semibold text-gray-700">
              Nama pemilik rekening
              <input type="text" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening', auth()->user()->name) }}" required maxlength="255" placeholder="Sesuai buku rekening" class="rounded-xl border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              @error('nama_pemilik_rekening') <span class="text-xs font-normal text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="grid gap-1.5 text-sm font-semibold text-gray-700">
              Tanggal pencairan diharapkan
              <input type="date" name="tanggal_pencairan_diharapkan" value="{{ old('tanggal_pencairan_diharapkan', now()->toDateString()) }}" min="{{ now()->toDateString() }}" required class="rounded-xl border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              @error('tanggal_pencairan_diharapkan') <span class="text-xs font-normal text-red-600">{{ $message }}</span> @enderror
            </label>
          </div>
        </section>

        {{-- Hidden field: hasil kalkulasi JS yang disinkron & ikut dikirim ke server --}}
        <input type="hidden" id="pk_jenis_akad" name="jenis_akad" value="Murabahah">
        <input type="hidden" id="pk_ringkasan_label" name="ringkasan_label" value="">
        <input type="hidden" id="pk_nilai_utama" name="nilai_utama" value="0">
        <input type="hidden" id="pk_total_kewajiban" name="total_kewajiban" value="0">
        <input type="hidden" id="pk_tenor_final" name="tenor_final" value="0">
        <input type="hidden" id="pk_jadwal_json" name="jadwal_json" value="">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-8 border-t border-gray-200 bg-gradient-to-r from-gray-50/80 to-white pt-6 rounded-b-xl">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500">Pastikan ringkasan dan formulir sudah benar sebelum dikirim ke pengurus.</p>
            </div>
            <div class="flex items-center justify-end gap-3">
            <button
                type="reset"
                onclick="setTimeout(() => PembiayaanKalkulator.calculateActive(), 0)"
                class="group px-5 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-bold shadow-sm hover:bg-gray-50 hover:border-gray-300 hover:shadow transition-all duration-200">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Formulir
                </span>
            </button>

            <button
                type="submit"
                :disabled="submitting"
                class="inline-flex items-center gap-2.5 px-7 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-sm font-bold shadow-lg shadow-indigo-200 hover:from-indigo-700 hover:to-indigo-800 hover:shadow-xl hover:shadow-indigo-300 hover:-translate-y-0.5 active:translate-y-0 active:shadow-md transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed">
                <template x-if="!submitting">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajukan Pembiayaan
                    </span>
                </template>
                <template x-if="submitting">
                    <span class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mengirim...
                    </span>
                </template>
            </button>
            </div>
        </div>
    </div>

    @once('pembiayaan-kalkulator-script')
    <script>
    window.PembiayaanKalkulator = (function () {
      const fmt = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Number.isFinite(n) ? n : 0);
      const num = id => Number((document.getElementById(id)?.value) || 0);
      const val = id => (document.getElementById(id)?.value || '').trim();

      const TAB_ACTIVE = ['bg-teal-700', 'text-white', 'border-teal-700'];
      const TAB_INACTIVE = ['bg-white', 'text-gray-500', 'border-gray-200'];
      const STATUS_OK = ['bg-emerald-50', 'text-emerald-800', 'border-emerald-200'];
      const STATUS_BAD = ['bg-red-50', 'text-red-800', 'border-red-200'];

      let activeAkad = 'murabahah';
      let activeData = { akad: 'Murabahah', summaryLabel: 'Estimasi angsuran bulanan', summaryValue: 0, total: 0, tenor: 0, rows: [], schedule: [] };

      function switchAkad(id, el = null) {
        activeAkad = id;
        document.querySelectorAll('#pembiayaanKalkulator .pk-tab').forEach(t => {
          t.classList.remove(...TAB_ACTIVE);
          t.classList.add(...TAB_INACTIVE);
          const small = t.querySelector('.pk-tab-small');
          if (small) { small.classList.remove('text-teal-100'); small.classList.add('text-gray-400'); }
        });
        document.querySelectorAll('#pembiayaanKalkulator .pk-tab-content').forEach(c => c.classList.add('hidden'));
        if (el) {
          el.classList.remove(...TAB_INACTIVE);
          el.classList.add(...TAB_ACTIVE);
          const smallActive = el.querySelector('.pk-tab-small');
          if (smallActive) { smallActive.classList.remove('text-gray-400'); smallActive.classList.add('text-teal-100'); }
        }
        document.getElementById('pk_' + id)?.classList.remove('hidden');
        calculateActive();
      }

      function switchAkadById(id) {
        const tab = document.querySelector(`#pembiayaanKalkulator .pk-tab[data-akad="${id}"]`);
        switchAkad(id, tab);
      }

      function card(label, value) {
        return `<div class="border border-gray-200 rounded-lg bg-white p-3.5">
                   <span class="block text-gray-500 text-xs font-extrabold">${label}</span>
                   <strong class="block mt-1 text-base text-teal-800 break-words">${value}</strong>
                 </div>`;
      }

      function calcMurabahah() {
        const beli = num('pk_m_beli'), marginPct = num('pk_m_margin'), tenor = num('pk_m_tenor');
        const pokok = beli;
        const margin = Math.round(beli * marginPct / 100);
        const hargaJual = beli + margin;
        const totalAngsuran = pokok + margin;
        const angsuran = tenor > 0 ? Math.ceil(totalAngsuran / tenor) : 0;
        const pokokBulanan = tenor > 0 ? Math.floor(pokok / tenor) : 0;
        const marginBulanan = tenor > 0 ? Math.floor(margin / tenor) : 0;
        const schedule = [];
        let sisaPokok = pokok, sisaMargin = margin;
        for (let bulan = 1; bulan <= Math.min(tenor, 240); bulan++) {
          const isLast = bulan === tenor;
          const p = isLast ? sisaPokok : Math.min(pokokBulanan, sisaPokok);
          const m = isLast ? sisaMargin : Math.min(marginBulanan, sisaMargin);
          sisaPokok = Math.max(0, sisaPokok - p);
          sisaMargin = Math.max(0, sisaMargin - m);
          schedule.push([bulan, p, m, p + m, sisaPokok]);
        }
        document.getElementById('pk_m_results').innerHTML = [
          card('Harga jual', fmt(hargaJual)),
          card('Harga beli', fmt(pokok)),
          card('Total margin', fmt(margin)),
          card('Angsuran / bulan', fmt(angsuran)),
          card('Jangka waktu', tenor + ' bulan'),
          card('Total kewajiban', fmt(totalAngsuran))
        ].join('');
        document.getElementById('pk_m_sched').innerHTML = schedule.map(r =>
          `<tr>
             <td class="border-b border-gray-200 px-3 py-2.5">${r[0]}</td>
             <td class="border-b border-gray-200 px-3 py-2.5 text-right">${fmt(r[1])}</td>
             <td class="border-b border-gray-200 px-3 py-2.5 text-right">${fmt(r[2])}</td>
             <td class="border-b border-gray-200 px-3 py-2.5 text-right">${fmt(r[3])}</td>
             <td class="border-b border-gray-200 px-3 py-2.5 text-right">${fmt(r[4])}</td>
           </tr>`).join('');
        activeData = {
          akad: 'Murabahah', summaryLabel: 'Estimasi angsuran bulanan', summaryValue: angsuran, total: totalAngsuran, tenor,
          rows: [['Objek pembiayaan', val('pk_m_objek') || '-'], ['Harga beli barang', fmt(beli)], ['Margin keuntungan', marginPct + '%'], ['Total margin', fmt(margin)], ['Total kewajiban', fmt(totalAngsuran)]],
          schedule
        };
        renderSide();
      }

      function calcMudharabah() {
        const modal = num('pk_d_modal'), nk = num('pk_d_nk'), na = num('pk_d_na'), omzet = num('pk_d_omzet'), biaya = num('pk_d_biaya'), tenor = num('pk_d_tenor');
        const laba = omzet - biaya;
        const koperasi = Math.round(laba * nk / 100);
        const anggota = Math.round(laba * na / 100);
        const totalNisbah = nk + na;
        const totalKoperasi = koperasi * tenor;
        const roi = modal > 0 ? (totalKoperasi / modal * 100) : 0;
        const status = document.getElementById('pk_d_status');
        const ok = totalNisbah === 100;
        status.classList.remove(...(ok ? STATUS_BAD : STATUS_OK));
        status.classList.add(...(ok ? STATUS_OK : STATUS_BAD));
        status.textContent = ok ? 'Nisbah sudah 100%. Simulasi siap dibuat.' : 'Total nisbah saat ini ' + totalNisbah.toFixed(1) + '%. Sesuaikan hingga 100%.';
        document.getElementById('pk_d_results').innerHTML = [
          card('Laba usaha / bulan', fmt(laba)),
          card('Bagi hasil koperasi / bulan', fmt(koperasi)),
          card('Bagi hasil anggota / bulan', fmt(anggota)),
          card('Estimasi ROI koperasi', roi.toFixed(2) + '%'),
          card('Total BH koperasi', fmt(totalKoperasi)),
          card('Total BH anggota', fmt(anggota * tenor))
        ].join('');
        activeData = {
          akad: 'Mudharabah', summaryLabel: 'Estimasi bagi hasil koperasi / bulan', summaryValue: koperasi, total: totalKoperasi, tenor,
          rows: [['Modal koperasi', fmt(modal)], ['Omzet / bulan', fmt(omzet)], ['Biaya operasional / bulan', fmt(biaya)], ['Laba usaha / bulan', fmt(laba)], ['Nisbah koperasi', nk + '%'], ['Nisbah anggota', na + '%'], ['Total nisbah', totalNisbah + '%'], ['ROI koperasi', roi.toFixed(2) + '%']],
          schedule: []
        };
        renderSide();
      }

      function calcMusyarakah() {
        const modalKoperasi = num('pk_s_modal_koperasi'), modalAnggota = num('pk_s_modal_anggota'), tenor = num('pk_s_tenor');
        const nk = num('pk_s_nk'), na = num('pk_s_na'), omzet = num('pk_s_omzet'), biaya = num('pk_s_biaya');
        const totalModal = modalKoperasi + modalAnggota;
        const porsiKoperasi = totalModal > 0 ? (modalKoperasi / totalModal * 100) : 0;
        const porsiAnggota = totalModal > 0 ? (modalAnggota / totalModal * 100) : 0;
        const laba = omzet - biaya;
        const koperasi = Math.round(Math.max(0, laba) * nk / 100);
        const anggota = Math.round(Math.max(0, laba) * na / 100);
        const totalNisbah = nk + na;
        const totalKoperasi = koperasi * tenor;
        const status = document.getElementById('pk_s_status');
        const ok = totalNisbah === 100;
        status.classList.remove(...(ok ? STATUS_BAD : STATUS_OK));
        status.classList.add(...(ok ? STATUS_OK : STATUS_BAD));
        status.textContent = ok ? 'Nisbah sudah 100%. Simulasi siap dibuat.' : 'Total nisbah saat ini ' + totalNisbah.toFixed(1) + '%. Sesuaikan hingga 100%.';
        document.getElementById('pk_s_results').innerHTML = [
          card('Total modal usaha', fmt(totalModal)),
          card('Porsi modal koperasi', porsiKoperasi.toFixed(2) + '%'),
          card('Porsi modal anggota', porsiAnggota.toFixed(2) + '%'),
          card('Laba usaha / bulan', fmt(Math.max(0, laba))),
          card('Bagi hasil koperasi / bulan', fmt(koperasi)),
          card('Bagi hasil anggota / bulan', fmt(anggota))
        ].join('');
        activeData = {
          akad: 'Musyarakah', summaryLabel: 'Estimasi bagi hasil koperasi / bulan', summaryValue: koperasi, total: totalKoperasi, tenor,
          rows: [['Usaha yang dibiayai', val('pk_s_usaha') || '-'], ['Modal koperasi', fmt(modalKoperasi)], ['Modal anggota', fmt(modalAnggota)], ['Porsi modal koperasi', porsiKoperasi.toFixed(2) + '%'], ['Porsi modal anggota', porsiAnggota.toFixed(2) + '%'], ['Omzet / bulan', fmt(omzet)], ['Biaya operasional / bulan', fmt(biaya)], ['Laba usaha / bulan', fmt(Math.max(0, laba))], ['Nisbah koperasi', nk + '%'], ['Nisbah anggota', na + '%'], ['Total nisbah', totalNisbah + '%']],
          schedule: []
        };
        renderSide();
      }

      function calcIjarah() {
        const aset = num('pk_i_aset'), tenor = num('pk_i_tenor'), ujrah = num('pk_i_ujrah'), rawat = num('pk_i_rawat'), opsiPct = num('pk_i_opsi');
        const bulanan = ujrah + rawat;
        const totalUjrah = ujrah * tenor;
        const totalRawat = rawat * tenor;
        const opsi = Math.round(aset * opsiPct / 100);
        const total = totalUjrah + totalRawat + opsi;
        const efektif = aset > 0 && tenor > 0 ? (totalUjrah / aset / tenor * 100) : 0;
        document.getElementById('pk_i_results').innerHTML = [
          card('Tagihan / bulan', fmt(bulanan)),
          card('Total ujrah', fmt(totalUjrah)),
          card('Total perawatan', fmt(totalRawat)),
          card('Opsi beli akhir', fmt(opsi)),
          card('Total estimasi bayar', fmt(total)),
          card('Rate ujrah efektif', efektif.toFixed(2) + '% / bulan')
        ].join('');
        activeData = {
          akad: 'Ijarah', summaryLabel: 'Estimasi tagihan bulanan', summaryValue: bulanan, total, tenor,
          rows: [['Objek sewa', val('pk_i_objek') || '-'], ['Nilai aset', fmt(aset)], ['Ujrah / bulan', fmt(ujrah)], ['Biaya perawatan / bulan', fmt(rawat)], ['Opsi beli akhir', opsiPct + '%'], ['Nominal opsi beli', fmt(opsi)], ['Total estimasi bayar', fmt(total)]],
          schedule: []
        };
        renderSide();
      }

      function calcQardh() {
        const pokok = num('pk_q_pokok'), tenor = num('pk_q_tenor');
        const angsuranPerBulan = tenor > 0 ? Math.floor(pokok / tenor) : 0;
        const sisaAkhir = pokok - (angsuranPerBulan * tenor);
        const schedule = [];
        let sisaPokok = pokok;
        for (let bulan = 1; bulan <= Math.min(tenor, 240); bulan++) {
          const isLast = bulan === tenor;
          const bayar = isLast ? sisaPokok : Math.min(angsuranPerBulan, sisaPokok);
          sisaPokok = Math.max(0, sisaPokok - bayar);
          schedule.push([bulan, bayar, 0, bayar, sisaPokok]);
        }
        document.getElementById('pk_q_results').innerHTML = [
          card('Pokok pinjaman', fmt(pokok)),
          card('Angsuran / bulan', fmt(angsuranPerBulan)),
          card('Total kewajiban', fmt(pokok))
        ].join('');
        document.getElementById('pk_q_sched').innerHTML = schedule.map(r =>
          `<tr>
             <td class="border-b border-gray-200 px-3 py-2.5">${r[0]}</td>
             <td class="border-b border-gray-200 px-3 py-2.5 text-right">${fmt(r[1])}</td>
             <td class="border-b border-gray-200 px-3 py-2.5 text-right">${fmt(r[2])}</td>
             <td class="border-b border-gray-200 px-3 py-2.5 text-right">${fmt(r[3])}</td>
             <td class="border-b border-gray-200 px-3 py-2.5 text-right">${fmt(r[4])}</td>
           </tr>`).join('');
        activeData = {
          akad: 'Qardh', summaryLabel: 'Estimasi angsuran bulanan', summaryValue: angsuranPerBulan, total: pokok, tenor,
          rows: [['Objek pinjaman', val('pk_q_objek') || '-'], ['Pokok pinjaman', fmt(pokok)], ['Margin / keuntungan', fmt(0)], ['Total kewajiban', fmt(pokok)], ['Angsuran / bulan', fmt(angsuranPerBulan)]],
          schedule
        };
        renderSide();
      }

      function calculateActive() {
        if (activeAkad === 'murabahah') calcMurabahah();
        if (activeAkad === 'mudharabah') calcMudharabah();
        if (activeAkad === 'musyarakah') calcMusyarakah();
        if (activeAkad === 'ijarah') calcIjarah();
        if (activeAkad === 'qardh') calcQardh();
      }

      function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[ch]));
      }

      function formatTanggal() {
        const raw = val('pk_tanggal');
        const date = raw ? new Date(raw + 'T00:00:00') : new Date();
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
      }

      function renderSide() {
        const tujuanSelect = document.getElementById('tujuan_pembiayaan');
        const tujuanLabel = tujuanSelect?.selectedOptions?.[0]?.textContent || 'Kebutuhan Lainnya';
        setText('pk_summaryLabel', activeData.summaryLabel);
        setText('pk_summaryValue', fmt(activeData.summaryValue));
        setText('pk_kpiAkad', activeData.akad);
        setText('pk_kpiTotal', fmt(activeData.total));
        setText('pk_kpiTenor', (activeData.tenor || 0) + ' bulan');

        // sinkron hidden field agar ikut ter-submit ke server
        setValue('pk_jenis_akad', activeData.akad);
        setValue('pk_ringkasan_label', activeData.summaryLabel);
        setValue('pk_nilai_utama', activeData.summaryValue);
        setValue('pk_total_kewajiban', activeData.total);
        setValue('pk_tenor_final', activeData.tenor || 0);
        setValue('pk_jadwal_json', activeData.schedule.length ? JSON.stringify(activeData.schedule) : '');

        const lines = [
          ['Anggota', escapeHtml(val('pk_anggota') || '-')],
          ['Tujuan', escapeHtml(tujuanLabel)],
          ['Akad', activeData.akad],
          ['Tanggal', formatTanggal()],
          ['Nilai utama', fmt(activeData.summaryValue)]
        ];
        const preview = document.getElementById('pk_docPreview');
        if (preview) preview.innerHTML = lines.map((r, i) =>
          `<div class="flex justify-between gap-3 ${i < lines.length - 1 ? 'border-b border-dashed border-gray-200 pb-2' : ''}">
             <span class="text-gray-500">${r[0]}</span><b class="text-right text-gray-900">${r[1]}</b>
           </div>`).join('');
        const printPage = document.getElementById('pk_printPage');
        if (printPage) printPage.innerHTML = buildPrintHtml();
      }

      function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
      }

      function setValue(id, value) {
        const el = document.getElementById(id);
        if (el) el.value = value;
      }

      function buildPrintHtml() {
        const tujuanSelect = document.getElementById('tujuan_pembiayaan');
        const tujuanLabel = tujuanSelect?.selectedOptions?.[0]?.textContent || 'Kebutuhan Lainnya';
        const rowsWithPurpose = [['Tujuan pembiayaan', tujuanLabel], ...activeData.rows];
        const rows = rowsWithPurpose.map(r => `<tr><td style="border:1px solid #999;padding:7px;">${escapeHtml(r[0])}</td><td style="border:1px solid #999;padding:7px;text-align:right;">${escapeHtml(String(r[1]))}</td></tr>`).join('');
        const schedule = activeData.schedule.length
          ? `<table style="width:100%;border-collapse:collapse;margin-top:14px;font-size:9pt;"><thead><tr><th style="border:1px solid #999;padding:7px;background:#eee;">Bulan</th><th style="border:1px solid #999;padding:7px;background:#eee;">Pokok</th><th style="border:1px solid #999;padding:7px;background:#eee;">Margin</th><th style="border:1px solid #999;padding:7px;background:#eee;">Angsuran</th><th style="border:1px solid #999;padding:7px;background:#eee;">Sisa Pokok</th></tr></thead><tbody>${activeData.schedule.map(r => `<tr><td style="border:1px solid #999;padding:7px;">${r[0]}</td><td style="border:1px solid #999;padding:7px;text-align:right;">${fmt(r[1])}</td><td style="border:1px solid #999;padding:7px;text-align:right;">${fmt(r[2])}</td><td style="border:1px solid #999;padding:7px;text-align:right;">${fmt(r[3])}</td><td style="border:1px solid #999;padding:7px;text-align:right;">${fmt(r[4])}</td></tr>`).join('')}</tbody></table>`
          : '';
        return `<div style="font-family:Arial,sans-serif;color:#111;">
          <h1 style="font-size:18px;">Simulasi Pembiayaan Syariah - ${activeData.akad}</h1>
          <p><b>Anggota: ${escapeHtml(val('pk_anggota') || '-')}</b><br>Tanggal simulasi: ${formatTanggal()}</p>
          <table style="width:100%;border-collapse:collapse;margin-top:14px;font-size:10pt;"><tbody>
            <tr><th style="border:1px solid #999;padding:7px;text-align:left;background:#eee;">${activeData.summaryLabel}</th><td style="border:1px solid #999;padding:7px;text-align:right;">${fmt(activeData.summaryValue)}</td></tr>
            <tr><th style="border:1px solid #999;padding:7px;text-align:left;background:#eee;">Total</th><td style="border:1px solid #999;padding:7px;text-align:right;">${fmt(activeData.total)}</td></tr>
            <tr><th style="border:1px solid #999;padding:7px;text-align:left;background:#eee;">Tenor</th><td style="border:1px solid #999;padding:7px;text-align:right;">${activeData.tenor || 0} bulan</td></tr>
            ${rows}
          </tbody></table>${schedule}</div>`;
      }

      function downloadPDF() {
        calculateActive();
        if (!window.jspdf || !window.jspdf.jsPDF) {
          alert('Library PDF belum dimuat. Pastikan CDN jsPDF sudah ditambahkan, atau gunakan tombol Cetak lalu pilih Save as PDF.');
          window.print();
          return;
        }
        const doc = new window.jspdf.jsPDF({ unit: 'mm', format: 'a4' });
        doc.setTextColor(17, 94, 89); doc.setFont('helvetica', 'bold'); doc.setFontSize(18);
        doc.text('Simulasi Pembiayaan Syariah - ' + activeData.akad, 14, 18);
        doc.setFont('helvetica', 'normal'); doc.setTextColor(74, 85, 82); doc.setFontSize(10);
        doc.text('Anggota: ' + (val('pk_anggota') || '-'), 14, 26);
        doc.text('Tanggal simulasi: ' + formatTanggal(), 14, 32);
        doc.autoTable({
          startY: 40, theme: 'grid', head: [['Ringkasan', 'Nilai']],
          body: [[activeData.summaryLabel, fmt(activeData.summaryValue)], ['Total', fmt(activeData.total)], ['Tenor', (activeData.tenor || 0) + ' bulan'], ...activeData.rows],
          headStyles: { fillColor: [15, 118, 110] }, styles: { font: 'helvetica', fontSize: 9, cellPadding: 3 }, columnStyles: { 1: { halign: 'right' } }
        });
        if (activeData.schedule.length) {
          doc.autoTable({
            startY: doc.lastAutoTable.finalY + 8, theme: 'striped',
            head: [['Bulan', 'Pokok', 'Margin', 'Angsuran', 'Sisa Pokok']],
            body: activeData.schedule.map(r => [r[0], fmt(r[1]), fmt(r[2]), fmt(r[3]), fmt(r[4])]),
            headStyles: { fillColor: [17, 94, 89] }, styles: { font: 'helvetica', fontSize: 8, cellPadding: 2.5 }, columnStyles: { 1: { halign: 'right' }, 2: { halign: 'right' }, 3: { halign: 'right' }, 4: { halign: 'right' } }
          });
        }
        doc.setFontSize(8); doc.setTextColor(120, 120, 120);
        doc.text('Dibuat melalui Kalkulator Pembiayaan Syariah', 14, 286);
        const clean = (val('pk_anggota') || 'anggota').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        doc.save('simulasi-pembiayaan-' + activeData.akad.toLowerCase() + '-' + clean + '.pdf');
      }

      function isiContoh() {
        const m = { pk_m_beli: 25000000, pk_m_margin: 12, pk_m_tenor: 24, pk_m_objek: 'Motor operasional usaha' };
        const d = { pk_d_modal: 50000000, pk_d_nk: 40, pk_d_na: 60, pk_d_omzet: 18000000, pk_d_biaya: 11000000, pk_d_tenor: 12 };
        const s = { pk_s_modal_koperasi: 30000000, pk_s_modal_anggota: 20000000, pk_s_nk: 45, pk_s_na: 55, pk_s_omzet: 22000000, pk_s_biaya: 14000000, pk_s_tenor: 18, pk_s_usaha: 'Perluasan usaha sembako' };
        const i = { pk_i_aset: 80000000, pk_i_tenor: 36, pk_i_ujrah: 2500000, pk_i_rawat: 250000, pk_i_opsi: 20, pk_i_objek: 'Mesin produksi' };
        const q = { pk_q_pokok: 5000000, pk_q_tenor: 10, pk_q_objek: 'Biaya pendidikan anak' };
        Object.entries({ ...m, ...d, ...s, ...i, ...q }).forEach(([id, value]) => { const el = document.getElementById(id); if (el) el.value = value; });
        calculateActive();
      }

      document.addEventListener('DOMContentLoaded', function () {
        const tgl = document.getElementById('pk_tanggal');
        if (tgl) tgl.valueAsDate = new Date();

        document.querySelectorAll('#pembiayaanForm input[type="number"]').forEach((input) => {
          input.addEventListener('wheel', (event) => event.currentTarget.blur(), { passive: true });
        });
        calculateActive();
      });

      return { switchAkad, switchAkadById, calculateActive, downloadPDF, isiContoh };
    })();
    </script>
    @endonce

    </form>
</div>
</div>
