{{--
    Sidebar Pengurus
    Mengikuti pola visual sidebar Anggota (indigo-950, brand badge, grouped menu, user mini-card).
    $safeRoute closure diasumsikan sudah didefinisikan di index.blade.php (sama seperti pola Anggota).
--}}
<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-indigo-950 via-indigo-950 to-slate-950 text-indigo-100 transform transition-transform duration-200 ease-in-out lg:translate-x-0 flex flex-col shadow-2xl shadow-indigo-900/20"
>
    {{-- Brand --}}
    <div class="flex items-center gap-3 px-5 h-16 border-b border-indigo-800/60 bg-white/5 backdrop-blur-sm">
        <div class="relative flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-400 to-cyan-400 font-bold text-sm text-white shadow-lg shadow-indigo-500/30">
            KD
        </div>
        <div class="leading-tight">
            <p class="text-sm font-semibold text-white">SIPDKS</p>
            <p class="text-xs text-indigo-300">Panel Pengurus</p>
        </div>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        @php
            $menuGroups = [
                'Utama' => [
                    ['label' => 'Dashboard', 'tab' => 'dashboard', 'icon' => 'home'],
                ],
                'Operasional' => [
                    ['label' => 'Manajemen Anggota', 'tab' => 'anggota', 'icon' => 'users'],
                    ['label' => 'Simpanan', 'tab' => 'simpanan', 'icon' => 'wallet'],
                    ['label' => 'Pembiayaan', 'tab' => 'pembiayaan', 'icon' => 'cash'],
                    ['label' => 'Keuangan', 'tab' => 'keuangan', 'icon' => 'bank'],
                    ['label' => 'Marketplace', 'tab' => 'marketplace', 'icon' => 'shop'],
                ],
                'Laporan & Sistem' => [
                    ['label' => 'Laporan', 'tab' => 'laporan', 'icon' => 'chart'],
                    ['label' => 'Notifikasi', 'tab' => 'notifikasi', 'icon' => 'bell'],
                    ['label' => 'Pengaturan', 'tab' => 'pengaturan', 'icon' => 'cog'],
                ],
            ];

            $icons = [
                'home' => 'M3 12l9-9 9 9M4 10v10h5v-6h6v6h5V10',
                'users' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-3a4 4 0 100-8 4 4 0 000 8zm6 3a4 4 0 10-8 0',
                'wallet' => 'M3 7h18M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 11h.01',
                'cash' => 'M12 8c-1.66 0-3 .9-3 2s1.34 2 3 2 3 .9 3 2-1.34 2-3 2m0-8V6m0 12v-2M3 6h18v12H3z',
                'bank' => 'M3 8h18M5 8V6h14v2M7 8v10m10-10v10M3 18h18',
                'shop' => 'M3 3h18l-1.5 9h-15L3 3zM6 12v7a1 1 0 001 1h10a1 1 0 001-1v-7M9 9V5a3 3 0 016 0v4',
                'chart' => 'M9 17V9m4 8V5m4 12v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z',
                'bell' => 'M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                'cog' => 'M10.3 4.3a2 2 0 013.4 0l.4.7 1-.3a2 2 0 012.5 2.5l-.3 1 .7.4a2 2 0 010 3.4l-.7.4.3 1a2 2 0 01-2.5 2.5l-1-.3-.4.7a2 2 0 01-3.4 0l-.4-.7-1 .3a2 2 0 01-2.5-2.5l.3-1-.7-.4a2 2 0 010-3.4l.7-.4-.3-1a2 2 0 012.5-2.5l1 .3z',
            ];
        @endphp

        @foreach ($menuGroups as $groupLabel => $items)
            <div>
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-indigo-400">
                    {{ $groupLabel }}
                </p>
                <div class="space-y-1">
                    @foreach ($items as $item)
                        <button
                            type="button"
                            @click="setActiveTab('{{ $item['tab'] }}')"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition text-left"
                            :class="activeTab === '{{ $item['tab'] }}' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-indigo-200 hover:bg-indigo-800/60 hover:text-white'"
                        >
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" />
                                </svg>
                            </span>
                            <span>{{ $item['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    {{-- User mini-card --}}
    <div class="p-3 border-t border-indigo-800/60">
        <div class="flex items-center gap-3 px-2 py-2 rounded-lg bg-indigo-900/60">
            <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-xs font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Pengurus' }}</p>
                <p class="text-[11px] text-indigo-300 truncate">Pengurus Koperasi</p>
            </div>
        </div>
    </div>
</aside>

{{-- Overlay mobile --}}
<div
    x-show="sidebarOpen"
    x-cloak
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-black/40 z-30 lg:hidden"
></div>
