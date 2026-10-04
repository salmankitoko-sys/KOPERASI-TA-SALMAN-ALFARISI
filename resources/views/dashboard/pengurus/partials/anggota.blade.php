@php
    $rupiah = fn ($v) => 'Rp ' . number_format((float) ($v ?? 0), 0, ',', '.');
    $statusBadge = function ($status) {
        return match ($status) {
            'Aktif' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            'Calon' => 'bg-amber-50 text-amber-700 ring-amber-200',
            default => 'bg-red-50 text-red-700 ring-red-200',
        };
    };

    $anggotaCollection = collect($anggotaList ?? []);
    $anggotaRows = $anggotaCollection->map(function ($anggota) {
        $tglGabung = $anggota->tgl_gabung ?? null;

        return [
            'id' => $anggota->id,
            'nomor_anggota' => 'AGR-' . str_pad((string) $anggota->id, 4, '0', STR_PAD_LEFT),
            'name' => $anggota->name,
            'email' => $anggota->email,
            'no_hp' => $anggota->no_hp,
            'pekerjaan' => $anggota->pekerjaan,
            'penghasilan' => (float) ($anggota->penghasilan ?? 0),
            'status' => $anggota->status ?? 'Calon',
            'tgl_gabung' => $tglGabung ? $tglGabung->format('Y-m-d') : '',
            'tgl_gabung_label' => $tglGabung ? $tglGabung->format('d/m/Y') : '-',
            'created_at_label' => $anggota->created_at?->format('d/m/Y') ?? '-',
            'simpanan_pokok' => (float) ($anggota->simpanan_pokok ?? 0),
        ];
    })->values();

    $anggotaStats = [
        'total_anggota' => $stats['total_users'] ?? $anggotaCollection->count(),
        'anggota_aktif' => $anggotaCollection->where('status', 'Aktif')->count(),
        'anggota_calon' => $anggotaCollection->where('status', 'Calon')->count(),
        'anggota_nonaktif' => $anggotaCollection->where('status', 'Non-Aktif')->count(),
        'total_pokok' => $stats['simpanan_pokok'] ?? $anggotaCollection->sum('simpanan_pokok'),
    ];
@endphp

<section
    x-show="activeTab === 'anggota'"
    x-cloak
    x-data="manajemenAnggota({
        rows: {{ Js::from($anggotaRows) }},
        stats: {{ Js::from($anggotaStats) }},
        endpoints: {
            index: {{ Js::from(route('pengurus.anggota.index')) }},
            show: {{ Js::from(url('/pengurus/anggota/__ID__')) }},
            edit: {{ Js::from(url('/pengurus/anggota/__ID__/edit')) }}
        }
    })"
    class="space-y-6"
>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manajemen Anggota</h3>
            <p class="text-sm text-gray-500">Tambah, verifikasi, dan perbarui data anggota koperasi.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('pengurus.anggota.create') }}"
                class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-lg bg-indigo-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
                </svg>
                Tambah Anggota
            </a>
            <button type="button" @click="exportCsv()"
                class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-indigo-800 transition hover:border-indigo-700 hover:bg-indigo-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15V3m0 12 4-4m-4 4-4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                </svg>
                Ekspor CSV
            </button>
            <button type="button" @click="window.print()"
                class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z" />
                </svg>
                Cetak
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div x-show="feedback.message" x-cloak
        :class="feedback.type === 'error' ? 'border-red-200 bg-red-50 text-red-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'"
        class="rounded-lg border px-4 py-3 text-sm"
        x-text="feedback.message">
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Anggota</p>
            <p class="mt-2 text-2xl font-bold text-gray-900" x-text="stats.total_anggota"></p>
            <p class="mt-1 text-xs text-gray-500">Seluruh anggota terdaftar.</p>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Aktif</p>
            <p class="mt-2 text-2xl font-bold text-emerald-700" x-text="stats.anggota_aktif"></p>
            <p class="mt-1 text-xs text-gray-500">Anggota yang sudah diverifikasi.</p>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Calon</p>
            <p class="mt-2 text-2xl font-bold text-amber-700" x-text="stats.anggota_calon"></p>
            <p class="mt-1 text-xs text-gray-500">Masih perlu dilengkapi.</p>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Simpanan Pokok</p>
            <p class="mt-2 text-2xl font-bold text-indigo-900" x-text="formatRupiah(stats.total_pokok)"></p>
            <p class="mt-1 text-xs text-gray-500">Total yang sudah masuk.</p>
        </div>
    </div>

    <div class="grid gap-6">
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_180px_160px]">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                        </svg>
                        <input type="text" x-model.debounce.250ms="filters.search" @input="loadData()"
                            placeholder="Cari nama, email, HP, pekerjaan..."
                            class="min-h-[44px] w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                    <select x-model="filters.status" @change="loadData()"
                        class="min-h-[44px] w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="">Semua status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Calon">Calon</option>
                        <option value="Non-Aktif">Non-Aktif</option>
                    </select>
                    <select x-model="filters.sort" @change="loadData()"
                        class="min-h-[44px] w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="terbaru">Terbaru</option>
                        <option value="terlama">Terlama</option>
                        <option value="nama">Nama A-Z</option>
                    </select>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Daftar Anggota</h4>
                        <p class="text-xs text-gray-500">
                            <span x-text="pagination.total"></span> data ditemukan.
                        </p>
                    </div>
                    <button type="button" @click="loadData()" :disabled="loading"
                        class="inline-flex min-h-[38px] items-center justify-center rounded-lg border border-gray-200 bg-white px-3 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 disabled:opacity-60">
                        Muat Ulang
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Anggota</th>
                                <th class="px-4 py-3">Kontak</th>
                                <th class="px-4 py-3">Pekerjaan</th>
                                <th class="px-4 py-3 text-right">Pokok</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-if="loading">
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">Memuat data anggota...</td>
                                </tr>
                            </template>

                            <template x-for="anggota in rows" :key="anggota.id">
                                <tr class="transition hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-gray-900" x-text="anggota.nomor_anggota"></td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-gray-900" x-text="anggota.name"></p>
                                        <p class="text-xs text-gray-500" x-text="'Gabung: ' + (anggota.tgl_gabung_label || anggota.created_at_label || '-')"></p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-gray-700" x-text="anggota.email"></p>
                                        <p class="text-xs text-gray-500" x-text="anggota.no_hp || '-'"></p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-800" x-text="anggota.pekerjaan || '-'"></p>
                                        <p class="text-xs text-gray-500" x-text="formatRupiah(anggota.penghasilan) + ' / bulan'"></p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700" x-text="formatRupiah(anggota.simpanan_pokok)"></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset"
                                            :class="badgeClass(anggota.status)"
                                            x-text="anggota.status">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a :href="editEndpointFor(anggota.id)"
                                                class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-800 transition hover:bg-indigo-50">
                                                Edit
                                            </a>
                                            <button type="button" @click="hapusAnggota(anggota)"
                                                class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="!loading && rows.length === 0">
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                        <svg class="mx-auto mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m5-3a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm6 3a4 4 0 1 0-8 0" />
                                        </svg>
                                        <p class="font-medium">Belum ada data anggota</p>
                                        <p class="mt-1 text-xs">Data baru akan tampil setelah disimpan.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-gray-500">
                        Halaman <span x-text="pagination.current_page"></span> dari <span x-text="pagination.last_page"></span>
                    </p>
                    <div class="flex gap-2">
                        <button type="button" @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page <= 1 || loading"
                            class="min-h-[38px] rounded-lg border border-gray-200 bg-white px-3 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 disabled:opacity-50">
                            Sebelumnya
                        </button>
                        <button type="button" @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page || loading"
                            class="min-h-[38px] rounded-lg border border-gray-200 bg-white px-3 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 disabled:opacity-50">
                            Berikutnya
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @once
        <script>
            function manajemenAnggota(config) {
                return {
                    rows: config.rows || [],
                    stats: config.stats || {},
                    endpoints: config.endpoints,
                    loading: false,
                    filters: {
                        search: '',
                        status: '',
                        sort: 'terbaru',
                        page: 1
                    },
                    pagination: {
                        current_page: 1,
                        last_page: 1,
                        total: (config.rows || []).length
                    },
                    feedback: {
                        message: '',
                        type: 'success'
                    },
                    csrfToken() {
                        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    },

                    endpointFor(id) {
                        return this.endpoints.show.replace('__ID__', id);
                    },

                    editEndpointFor(id) {
                        return this.endpoints.edit.replace('__ID__', id);
                    },

                    badgeClass(status) {
                        if (status === 'Aktif') return 'bg-emerald-50 text-emerald-700 ring-emerald-200';
                        if (status === 'Calon') return 'bg-amber-50 text-amber-700 ring-amber-200';
                        return 'bg-red-50 text-red-700 ring-red-200';
                    },

                    formatRupiah(value) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(Number(value || 0));
                    },

                    async loadData(page = 1) {
                        this.loading = true;
                        this.filters.page = page;

                        const params = new URLSearchParams({
                            page: this.filters.page,
                            per_page: 50,
                            sort: this.filters.sort
                        });

                        if (this.filters.search) params.set('search', this.filters.search);
                        if (this.filters.status) params.set('status', this.filters.status);

                        try {
                            const res = await fetch(`${this.endpoints.index}?${params.toString()}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            const data = await res.json();

                            if (!res.ok) {
                                this.feedback = { message: data.message || 'Gagal memuat data anggota.', type: 'error' };
                                return;
                            }

                            this.rows = data.data || [];
                            this.pagination = data.pagination || this.pagination;
                            this.stats = data.stats || this.stats;
                        } catch (error) {
                            this.feedback = { message: 'Gagal terhubung ke server.', type: 'error' };
                        } finally {
                            this.loading = false;
                        }
                    },

                    changePage(page) {
                        if (page < 1 || page > this.pagination.last_page) return;
                        this.loadData(page);
                    },

                    async hapusAnggota(anggota) {
                        if (!confirm(`Hapus anggota "${anggota.name}"?`)) return;

                        try {
                            const res = await fetch(this.endpointFor(anggota.id), {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': this.csrfToken(),
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            const data = await res.json().catch(() => ({}));

                            if (!res.ok) {
                                this.feedback = { message: data.message || 'Gagal menghapus anggota.', type: 'error' };
                                return;
                            }

                            this.feedback = { message: data.message || 'Anggota berhasil dihapus.', type: 'success' };
                            await this.loadData(this.pagination.current_page || 1);
                        } catch (error) {
                            this.feedback = { message: 'Gagal terhubung ke server.', type: 'error' };
                        }
                    },

                    exportCsv() {
                        const headers = ['ID', 'Nama', 'Email', 'No HP', 'Pekerjaan', 'Status', 'Tanggal Gabung', 'Simpanan Pokok'];
                        const lines = this.rows.map((row) => [
                            row.nomor_anggota,
                            row.name,
                            row.email,
                            row.no_hp || '',
                            row.pekerjaan || '',
                            row.status,
                            row.tgl_gabung || '',
                            row.simpanan_pokok || 0
                        ]);

                        const csv = [headers, ...lines]
                            .map((line) => line.map((cell) => `"${String(cell).replaceAll('"', '""')}"`).join(','))
                            .join('\n');

                        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = `anggota-koperasi-${new Date().toISOString().slice(0, 10)}.csv`;
                        link.click();
                        URL.revokeObjectURL(link.href);
                    }
                };
            }
        </script>
    @endonce
</section>
