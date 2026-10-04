@php
    $attentionTones = [
        'amber' => ['surface' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'ring' => 'ring-amber-100'],
        'sky' => ['surface' => 'bg-sky-50', 'text' => 'text-sky-700', 'dot' => 'bg-sky-500', 'ring' => 'ring-sky-100'],
        'rose' => ['surface' => 'bg-rose-50', 'text' => 'text-rose-700', 'dot' => 'bg-rose-500', 'ring' => 'ring-rose-100'],
        'violet' => ['surface' => 'bg-violet-50', 'text' => 'text-violet-700', 'dot' => 'bg-violet-500', 'ring' => 'ring-violet-100'],
        'emerald' => ['surface' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'ring' => 'ring-emerald-100'],
    ];
@endphp

<section class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(340px,0.8fr)]">
    <div
        class="dashboard-insight-card animate-fade-in-up"
        x-data="{
            metric: 'simpanan',
            highlightedIndex: null,
            labels: {{ Js::from($trendLabels ?? []) }},
            series: {{ Js::from($trendData ?? []) }},
            get current() { return this.series[this.metric] ?? { label: '', color: '#6366f1', values: [] }; },
            get total() { return this.current.values.reduce((sum, value) => sum + Number(value || 0), 0); },
            get maxValue() { return Math.max(...this.current.values.map(Number), 0); },
            barHeight(value) {
                if (this.maxValue === 0) return 7;
                return Math.max(7, Math.round((Number(value) / this.maxValue) * 100));
            },
            formatRupiah(value) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value || 0));
            }
        }"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="dashboard-section-icon bg-indigo-50 text-indigo-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9m5 10V5m5 14v-7m5 7V3" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Tren kinerja 6 bulan</h2>
                        <p class="mt-0.5 text-sm text-gray-500">Bandingkan arus utama koperasi tanpa membuka laporan.</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-metric-switcher" role="group" aria-label="Pilih metrik grafik">
                @foreach (($trendData ?? []) as $key => $trend)
                    <button
                        type="button"
                        @click="metric = '{{ $key }}'; highlightedIndex = null"
                        :class="metric === '{{ $key }}' ? 'is-active' : ''"
                        class="dashboard-metric-button"
                    >
                        {{ $trend['shortLabel'] }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="mt-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400" x-text="current.label"></p>
                <p class="mt-1 text-2xl font-bold tracking-tight text-gray-900" x-text="formatRupiah(total)"></p>
            </div>
            <p class="hidden text-right text-xs leading-5 text-gray-500 sm:block">
                Total pada periode<br>6 bulan terakhir
            </p>
        </div>

        <div class="dashboard-chart mt-5" aria-label="Grafik tren enam bulan">
            <div class="dashboard-chart-grid" aria-hidden="true"></div>
            <div class="dashboard-chart-bars">
                <template x-for="(value, index) in current.values" :key="`${metric}-${index}`">
                    <button
                        type="button"
                        class="dashboard-chart-column"
                        @mouseenter="highlightedIndex = index"
                        @mouseleave="highlightedIndex = null"
                        @focus="highlightedIndex = index"
                        @blur="highlightedIndex = null"
                        :aria-label="`${labels[index]}: ${formatRupiah(value)}`"
                    >
                        <span
                            class="dashboard-chart-tooltip"
                            :class="highlightedIndex === index ? 'is-visible' : ''"
                            x-text="formatRupiah(value)"
                        ></span>
                        <span
                            class="dashboard-chart-bar"
                            :style="`height: ${barHeight(value)}%; background-color: ${current.color}`"
                        ></span>
                        <span class="dashboard-chart-label" x-text="labels[index]"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    <div class="dashboard-insight-card animate-fade-in-up anim-delay-100">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="dashboard-section-icon bg-amber-50 text-amber-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 4.2L2.8 17.3A2 2 0 004.5 20h15a2 2 0 001.7-2.7L13.7 4.2a2 2 0 00-3.4 0z" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Perlu tindakan</h2>
                    <p class="mt-0.5 text-sm text-gray-500">Antrean keputusan operasional.</p>
                </div>
            </div>
            <span class="dashboard-attention-total">{{ $attentionTotal ?? 0 }}</span>
        </div>

        <div class="mt-5 space-y-2.5">
            @forelse (($attentionItems ?? []) as $item)
                @php $tone = $attentionTones[$item['tone']] ?? $attentionTones['emerald']; @endphp
                <button
                    type="button"
                    @click="setActiveTab('{{ $item['tab'] }}')"
                    class="dashboard-attention-row group"
                >
                    <span class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tone['surface'] }} {{ $tone['text'] }} ring-4 {{ $tone['ring'] }}">
                        <span class="absolute right-1 top-1 h-1.5 w-1.5 rounded-full {{ $tone['dot'] }}"></span>
                        <span class="text-sm font-bold">{{ $item['value'] }}</span>
                    </span>
                    <span class="min-w-0 flex-1 text-left">
                        <span class="block text-sm font-semibold text-gray-800">{{ $item['label'] }}</span>
                        <span class="mt-0.5 block truncate text-xs text-gray-500">{{ $item['description'] }}</span>
                    </span>
                    <svg class="h-4 w-4 shrink-0 text-gray-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @empty
                <div class="rounded-2xl bg-emerald-50 p-4 text-sm font-medium text-emerald-700">
                    Semua antrean operasional sudah tertangani.
                </div>
            @endforelse
        </div>

        <button type="button" @click="setActiveTab('notifikasi')" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-indigo-600 transition hover:text-indigo-800">
            Buka pusat notifikasi
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-5-5l5 5-5 5" />
            </svg>
        </button>
    </div>
</section>
