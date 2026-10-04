<section class="rounded-3xl border border-gray-200 bg-white p-5 lg:p-6 shadow-sm">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.24em] text-gray-500">Alur kerja</h2>
            <p class="mt-1 text-sm text-gray-600">Ringkasan status data dari setiap menu operasional.</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Status terkini</span>
    </div>

    @php
        $columns = $kanban ?? [];
        $colColor = [
            'Masuk' => 'bg-sky-50 text-sky-700 border-sky-200',
            'Diproses' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Menunggu Approval' => 'bg-violet-50 text-violet-700 border-violet-200',
            'Selesai Hari Ini' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ];
    @endphp

    <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($columns as $colName => $tasks)
            <div class="rounded-2xl border border-gray-200 bg-gray-50/70 p-3">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <span class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $colColor[$colName] ?? 'border-gray-200 bg-white text-gray-700' }}">
                        {{ $colName }}
                    </span>
                    <span class="text-xs font-medium text-gray-500">{{ count($tasks) }} item</span>
                </div>

                <div class="space-y-2">
                    @foreach ($tasks as $task)
                        <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-gray-900">{{ $task['title'] }}</p>
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-600">
                                    {{ $task['tag'] }}
                                </span>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">{{ $task['subtitle'] }}</p>
                            <div class="mt-3 flex justify-end">
                                <button
                                    type="button"
                                    @click="setActiveTab('{{ $task['tab'] ?? 'dashboard' }}')"
                                    class="inline-flex items-center rounded-full border border-gray-200 px-2.5 py-1 text-[11px] font-semibold text-gray-600 transition hover:border-indigo-300 hover:text-indigo-700"
                                >
                                    Detail
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
