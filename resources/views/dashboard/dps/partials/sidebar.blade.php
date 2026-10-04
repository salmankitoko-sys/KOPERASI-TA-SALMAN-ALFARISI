{{--
    Sidebar DPS (Dewan Pengawas Syariah)
    Pola visual mengikuti sidebar Pengurus (indigo-950, brand badge, grouped menu, user mini-card).
--}}
<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-40 w-64 bg-indigo-950 text-indigo-100 transform transition-transform duration-200 ease-in-out lg:translate-x-0 flex flex-col"
>
    {{-- Brand --}}
    <div class="flex items-center gap-3 px-5 h-16 border-b border-indigo-800/60">
        <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center font-bold text-white text-sm">
            🕌
        </div>
        <div class="leading-tight">
            <p class="text-sm font-semibold text-white">SIPDKS</p>
            <p class="text-xs text-indigo-300">Panel DPS Syariah</p>
        </div>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        @php
            $menuGroups = [
                'Menu DPS' => [
                    ['label' => 'Dashboard', 'tab' => 'dashboard', 'icon' => 'home'],
                    ['label' => 'Validasi Akad', 'tab' => 'audit', 'icon' => 'doc'],
                    ['label' => 'Laporan', 'tab' => 'laporan', 'icon' => 'chart'],
                    ['label' => 'Notifikasi', 'tab' => 'notifikasi', 'icon' => 'bell'],
                ],
            ];
            $icons = [
                'home'  => 'M3 12l9-9 9 9M4 10v10h5v-6h6v6h5V10',
                'eye'   => 'M2 12s3.5-7 10-7 10 7-3.5 7-10 7S2 12 2 12zm10 3a3 3 0 100-6 3 3 0 000 6z',
                'doc'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'shop'  => 'M3 3h18l-1.5 9h-15L3 3zM6 12v7a1 1 0 001 1h10a1 1 0 001-1v-7M9 9V5a3 3 0 016 0v4',
                'flag'  => 'M3 21V4m0 0c4.97 0 5.03-3 10-3s5.03 3 10 3v11c-4.97 0-5.03 3-10 3s-5.03-3-10-3',
                'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'chart' => 'M9 17V9m4 8V5m4 12v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z',
                'bell'  => 'M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                'user'  => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-3a4 4 0 100-8 4 4 0 000 8zm6 3a4 4 0 10-8 0',
            ];
        @endphp

        @foreach ($menuGroups as $groupLabel => $items)
            <div>
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-indigo-400">
                    {{ $groupLabel }}
                </p>
                <div class="space-y-1">
                    @foreach ($items as $item)
                        @if (!empty($item['url']))
                            <a
                                href="{{ $item['url'] }}"
                                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition text-left text-indigo-200 hover:bg-indigo-800/60 hover:text-white"
                            >
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" />
                                </svg>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @else
                            <button
                                @click="activeTab = '{{ $item['tab'] }}'; sidebarOpen = false"
                                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition text-left"
                                :class="activeTab === '{{ $item['tab'] }}' ? 'bg-indigo-600 text-white' : 'text-indigo-200 hover:bg-indigo-800/60 hover:text-white'"
                            >
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" />
                                </svg>
                                <span>{{ $item['label'] }}</span>
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    {{-- User mini-card --}}
    <div class="p-3 border-t border-indigo-800/60">
        <div class="flex items-center gap-3 px-2 py-2 rounded-lg bg-indigo-900/60">
            <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-xs font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'D', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'DPS' }}</p>
                <p class="text-[11px] text-indigo-300 truncate">Dewan Pengawas Syariah</p>
            </div>
        </div>
    </div>
</aside>

