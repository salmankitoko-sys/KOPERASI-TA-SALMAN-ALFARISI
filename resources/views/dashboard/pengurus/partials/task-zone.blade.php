@php
    $activityModules = [
        'semua' => ['label' => 'Semua', 'count' => ($activityFeed ?? collect())->count()],
        'anggota' => ['label' => 'Anggota', 'count' => $activityCounts['anggota'] ?? 0],
        'simpanan' => ['label' => 'Simpanan', 'count' => $activityCounts['simpanan'] ?? 0],
        'pembiayaan' => ['label' => 'Pembiayaan', 'count' => $activityCounts['pembiayaan'] ?? 0],
        'marketplace' => ['label' => 'Marketplace', 'count' => $activityCounts['marketplace'] ?? 0],
    ];
    $activityTones = [
        'indigo' => ['surface' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'line' => 'bg-indigo-200'],
        'emerald' => ['surface' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'line' => 'bg-emerald-200'],
        'amber' => ['surface' => 'bg-amber-50', 'text' => 'text-amber-600', 'line' => 'bg-amber-200'],
        'sky' => ['surface' => 'bg-sky-50', 'text' => 'text-sky-600', 'line' => 'bg-sky-200'],
    ];
    $activityIcons = [
        'anggota' => 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm13 10v-2a4 4 0 00-3-3.87m-1-11a4 4 0 010 7.75',
        'simpanan' => 'M3 7h18M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 12h2',
        'pembiayaan' => 'M12 8c-1.66 0-3 .9-3 2s1.34 2 3 2 3 .9 3 2-1.34 2-3 2m0-8V6m0 12v-2M3 6h18v12H3z',
        'marketplace' => 'M3 3h18l-1.5 9h-15L3 3zM6 12v7a1 1 0 001 1h10a1 1 0 001-1v-7',
    ];
@endphp

<section class="dashboard-section-card" x-data="{ activityFilter: 'semua' }" data-testid="activity-feed">
    <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
        <div class="flex items-start gap-3">
            <span class="dashboard-section-icon bg-sky-50 text-sky-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <div>
                <h2 class="text-base font-bold text-gray-900">Aktivitas terbaru</h2>
                <p class="mt-1 text-sm leading-6 text-gray-500">Satu timeline untuk memantau perubahan terbaru di seluruh operasional.</p>
            </div>
        </div>

        <div class="dashboard-activity-filters" role="group" aria-label="Filter aktivitas">
            @foreach ($activityModules as $key => $module)
                <button
                    type="button"
                    @click="activityFilter = '{{ $key }}'"
                    :class="activityFilter === '{{ $key }}' ? 'is-active' : ''"
                    class="dashboard-activity-filter"
                >
                    {{ $module['label'] }}
                    <span>{{ $module['count'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1.5fr)_minmax(300px,0.6fr)]">
        <div class="dashboard-timeline">
            @forelse (($activityFeed ?? collect()) as $index => $activity)
                @php $tone = $activityTones[$activity['tone']] ?? $activityTones['indigo']; @endphp
                <article
                    x-show="activityFilter === 'semua' || activityFilter === '{{ $activity['module'] }}'"
                    x-transition.opacity.duration.200ms
                    class="dashboard-timeline-row group"
                >
                    <div class="relative flex shrink-0 flex-col items-center self-stretch">
                        <span class="dashboard-timeline-icon {{ $tone['surface'] }} {{ $tone['text'] }}">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $activityIcons[$activity['module']] ?? $activityIcons['anggota'] }}" />
                            </svg>
                        </span>
                        @if (! $loop->last)
                            <span class="mt-2 h-full w-px {{ $tone['line'] }}" aria-hidden="true"></span>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1 pb-5">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="truncate text-sm font-bold text-gray-800">{{ $activity['title'] }}</h3>
                                    <span class="dashboard-activity-status {{ $tone['surface'] }} {{ $tone['text'] }}">{{ $activity['status'] }}</span>
                                </div>
                                <p class="mt-1 truncate text-sm text-gray-500">{{ $activity['description'] }}</p>
                            </div>
                            <span class="shrink-0 text-xs font-medium text-gray-400">{{ $activity['time'] }}</span>
                        </div>
                        <button
                            type="button"
                            @click="setActiveTab('{{ $activity['tab'] }}')"
                            class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-indigo-600 opacity-80 transition group-hover:opacity-100"
                        >
                            Lihat detail
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5l5 5-5 5" />
                            </svg>
                        </button>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center">
                    <p class="text-sm font-semibold text-gray-700">Belum ada aktivitas terbaru</p>
                    <p class="mt-1 text-xs text-gray-500">Perubahan operasional akan tampil otomatis di sini.</p>
                </div>
            @endforelse
        </div>

        <aside class="dashboard-daily-brief">
            <div class="relative z-10">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-indigo-100">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    Snapshot hari ini
                </span>
                <h3 class="mt-4 text-xl font-bold text-white">Kondisi operasional</h3>
                <p class="mt-2 text-sm leading-6 text-indigo-100/75">
                    {{ ($attentionTotal ?? 0) > 0 ? ($attentionTotal . ' item masih membutuhkan perhatian pengurus.') : 'Seluruh antrean utama sudah tertangani.' }}
                </p>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div class="dashboard-brief-stat">
                        <span>Anggota baru</span>
                        <strong>{{ $stats['anggota_baru'] ?? 0 }}</strong>
                    </div>
                    <div class="dashboard-brief-stat">
                        <span>Pengajuan</span>
                        <strong>{{ $stats['pengajuan'] ?? 0 }}</strong>
                    </div>
                    <div class="dashboard-brief-stat">
                        <span>Transaksi aktif</span>
                        <strong>{{ $stats['transaksi_aktif'] ?? 0 }}</strong>
                    </div>
                    <div class="dashboard-brief-stat">
                        <span>NPF</span>
                        <strong>{{ number_format($stats['npf'] ?? 0, 2, ',', '.') }}%</strong>
                    </div>
                </div>

                <button type="button" @click="setActiveTab('laporan')" class="dashboard-brief-action">
                    Buka laporan lengkap
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5l5 5-5 5" />
                    </svg>
                </button>
            </div>
        </aside>
    </div>
</section>
