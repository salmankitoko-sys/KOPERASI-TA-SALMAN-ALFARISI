<section class="rounded-3xl border border-gray-200 bg-white p-5 lg:p-6 shadow-sm">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.24em] text-gray-500">Yang perlu diperhatikan</h2>
            <p class="mt-1 text-sm text-gray-600">Prioritas kerja yang muncul dari data terkini di tiap menu.</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Berbasis data</span>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-3 lg:grid-cols-2">
        @foreach ($priorityItems as $index => $item)
            <div class="dashboard-card animate-fade-in-up" data-accent="{{ ['indigo','amber','emerald','sky'][$index % 4] }}" style="animation-delay: {{ $index * 0.08 }}s;">
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-indigo-500 shadow-sm shadow-indigo-200"></span>
                    <p class="text-sm font-semibold text-gray-900">{{ $item['title'] }}</p>
                </div>
                <p class="mt-3 text-sm text-gray-600">{{ $item['description'] }}</p>
                <div class="mt-3 dashboard-card-surface">
                    <p class="text-xs font-medium uppercase tracking-[0.2em] text-gray-500">{{ $item['meta'] }}</p>
                </div>
                <div class="mt-4 flex justify-end">
                    <button
                        type="button"
                        @click="setActiveTab('{{ $item['tab'] }}')"
                        class="dashboard-card-button"
                    >
                        Detail
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</section>
