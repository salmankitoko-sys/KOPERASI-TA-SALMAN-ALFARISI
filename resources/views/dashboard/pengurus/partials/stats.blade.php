<section class="dashboard-section-card">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex items-start gap-3">
            <span class="dashboard-section-icon bg-indigo-50 text-indigo-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7" />
                </svg>
            </span>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-base font-bold text-gray-900">Pusat operasional</h2>
                    <span class="dashboard-live-chip"><span></span> Data live</span>
                </div>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">
                    Buka modul kerja sekaligus lihat indikator utamanya tanpa meninggalkan dashboard.
                </p>
            </div>
        </div>
        <p class="text-xs font-semibold text-gray-400">{{ count($menuSummary) }} modul terhubung</p>
    </div>

    @php
        $icons = [
            'users' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-3a4 4 0 100-8 4 4 0 000 8zm6 3a4 4 0 10-8 0',
            'wallet' => 'M3 7h18M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 11h.01',
            'cash' => 'M12 8c-1.66 0-3 .9-3 2s1.34 2 3 2 3 .9 3 2-1.34 2-3 2m0-8V6m0 12v-2M3 6h18v12H3z',
            'shop' => 'M3 3h18l-1.5 9h-15L3 3zM6 12v7a1 1 0 001 1h10a1 1 0 001-1v-7M9 9V5a3 3 0 016 0v4',
            'chart' => 'M9 17V9m4 8V5m4 12v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z',
            'bank' => 'M3 8h18M5 8V6h14v2M7 8v10m10-10v10M3 18h18',
            'bell' => 'M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
        ];
    @endphp

    <div class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-12" data-testid="dashboard-menu-grid">
        @foreach ($menuSummary as $index => $item)
            <button
                type="button"
                @click="setActiveTab('{{ $item['tab'] }}')"
                class="dashboard-menu-tile group {{ $index < 4 ? 'xl:col-span-3' : 'xl:col-span-4' }}"
                data-accent="{{ $item['accent'] ?? 'indigo' }}"
                aria-label="Buka {{ $item['title'] }}"
                style="animation-delay: {{ $index * 0.06 }}s"
            >
                <span class="dashboard-menu-glow" aria-hidden="true"></span>
                <span class="relative z-10 flex items-start justify-between gap-3">
                    <span>
                        <span class="block text-left text-xs font-bold uppercase tracking-[0.16em] text-gray-500">{{ $item['title'] }}</span>
                        <span class="mt-3 block break-words text-left text-xl font-bold tracking-tight text-gray-900">{{ $item['value'] }}</span>
                    </span>
                    <span class="dashboard-menu-icon">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] ?? $icons['chart'] }}" />
                        </svg>
                    </span>
                </span>

                <span class="relative z-10 mt-5 block text-left">
                    <span class="block text-sm font-semibold text-gray-700">{{ $item['detail'] }}</span>
                    <span class="mt-1 block text-xs leading-5 text-gray-500">{{ $item['caption'] }}</span>
                </span>

                <span class="relative z-10 mt-5 flex items-center justify-between border-t border-gray-200/70 pt-3 text-xs font-bold">
                    <span class="dashboard-menu-link">Buka modul</span>
                    <svg class="h-4 w-4 text-gray-300 transition duration-200 group-hover:translate-x-1 group-hover:text-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5l5 5-5 5" />
                    </svg>
                </span>
            </button>
        @endforeach
    </div>
</section>
