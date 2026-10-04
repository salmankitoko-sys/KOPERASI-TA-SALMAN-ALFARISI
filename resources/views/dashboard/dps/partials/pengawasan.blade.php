{{-- ========== PANEL: PENGAWASAN OPERASIONAL ========== --}}
<div x-show="activeTab === 'pengawasan'" x-cloak
     x-data="pengawasanDps()"
     x-init="loadPengawasan()"
     class="space-y-6">

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <div>
                <h4 class="font-bold text-gray-900">Pengawasan Kepatuhan Operasional</h4>
                <p class="text-sm text-gray-500 mt-0.5">
                    Pantau simpanan, pembiayaan, dan layanan transaksi secara baca-saja.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button @click="activeTab = 'temuan'"
                        class="px-3 py-2 border border-red-200 bg-red-50 text-red-700 text-sm rounded-lg hover:bg-red-100 transition font-semibold">
                    Catat Temuan
                </button>
                <button @click="activeTab = 'opini'"
                        class="px-3 py-2 border border-indigo-200 bg-indigo-50 text-indigo-700 text-sm rounded-lg hover:bg-indigo-100 transition font-semibold">
                    Beri Nasihat / Opini
                </button>
                <button @click="loadPengawasan()"
                        class="px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition font-medium">
                    Refresh
                </button>
            </div>
        </div>

        <div class="mb-6 rounded-xl border border-emerald-100 bg-emerald-50 p-4">
            <p class="text-sm font-bold text-emerald-900">Batas kewenangan</p>
            <p class="text-xs text-emerald-800 mt-1">
                DPS menilai kepatuhan, meminta data, mencatat temuan, dan memberi rekomendasi.
                Persetujuan pencairan, verifikasi kas, serta perubahan status transaksi tetap dilakukan pengurus.
            </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <p class="text-[11px] text-gray-500">Saldo Simpanan</p>
                <p class="text-lg font-bold text-emerald-700 mt-1" x-text="rupiah(stats.saldo_simpanan)"></p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <p class="text-[11px] text-gray-500">Setoran Menunggu</p>
                <p class="text-lg font-bold text-amber-600 mt-1" x-text="stats.setoran_menunggu"></p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <p class="text-[11px] text-gray-500">Pembiayaan Aktif</p>
                <p class="text-lg font-bold text-indigo-600 mt-1" x-text="stats.pembiayaan_aktif"></p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <p class="text-[11px] text-gray-500">Menunggu DPS</p>
                <p class="text-lg font-bold text-blue-600 mt-1" x-text="stats.akad_menunggu_dps"></p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <p class="text-[11px] text-gray-500">Perlu Tindak Lanjut</p>
                <p class="text-lg font-bold text-red-600 mt-1" x-text="stats.perlu_tindak_lanjut"></p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <p class="text-[11px] text-gray-500">Transaksi Menunggu</p>
                <p class="text-lg font-bold text-orange-600 mt-1" x-text="stats.transaksi_menunggu"></p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
            <div class="rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <p class="text-sm font-bold text-gray-900">Produk Simpanan</p>
                    <p class="text-[11px] text-gray-500">Realisasi yang sudah masuk</p>
                </div>
                <div class="divide-y divide-gray-100">
                    <template x-for="item in matriks.simpanan" :key="item.jenis">
                        <div class="px-4 py-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-800" x-text="label(item.jenis)"></p>
                                <p class="text-[10px] text-gray-500" x-text="item.total_transaksi + ' transaksi'"></p>
                            </div>
                            <p class="text-xs font-bold text-emerald-700" x-text="rupiah(item.nominal)"></p>
                        </div>
                    </template>
                    <p x-show="matriks.simpanan.length === 0" class="px-4 py-6 text-xs text-center text-gray-400">Belum ada data.</p>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <p class="text-sm font-bold text-gray-900">Akad Pembiayaan</p>
                    <p class="text-[11px] text-gray-500">Hasil evaluasi per jenis akad</p>
                </div>
                <div class="divide-y divide-gray-100">
                    <template x-for="item in matriks.pembiayaan" :key="item.akad">
                        <div class="px-4 py-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold text-gray-800" x-text="label(item.akad)"></p>
                                    <p class="text-[10px] text-gray-500" x-text="item.total_akad + ' akad · ' + item.sesuai + ' sesuai'"></p>
                                </div>
                                <p class="text-xs font-bold text-indigo-700" x-text="rupiah(item.nominal)"></p>
                            </div>
                            <p x-show="Number(item.perlu_tindak_lanjut) > 0"
                               class="text-[10px] font-semibold text-red-600 mt-1"
                               x-text="item.perlu_tindak_lanjut + ' perlu tindak lanjut'"></p>
                        </div>
                    </template>
                    <p x-show="matriks.pembiayaan.length === 0" class="px-4 py-6 text-xs text-center text-gray-400">Belum ada data.</p>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <p class="text-sm font-bold text-gray-900">Layanan Transaksi</p>
                    <p class="text-[11px] text-gray-500">Mutasi menurut jenis layanan</p>
                </div>
                <div class="divide-y divide-gray-100">
                    <template x-for="item in matriks.layanan" :key="item.jenis_transaksi">
                        <div class="px-4 py-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-800" x-text="label(item.jenis_transaksi)"></p>
                                <p class="text-[10px] text-gray-500" x-text="item.total_transaksi + ' transaksi'"></p>
                            </div>
                            <p class="text-xs font-bold text-blue-700" x-text="rupiah(item.nominal)"></p>
                        </div>
                    </template>
                    <p x-show="matriks.layanan.length === 0" class="px-4 py-6 text-xs text-center text-gray-400">Belum ada data.</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <p class="text-sm font-bold text-gray-900">Inspeksi Mutasi & Layanan</p>
                <p class="text-[11px] text-gray-500">Telusuri transaksi untuk kebutuhan observasi, pemeriksaan dokumen, dan pengambilan sampel.</p>
            </div>

            <div class="p-4 flex flex-wrap gap-3">
                <input type="text" x-model="filter.q" @keyup.enter="loadPengawasan()"
                       placeholder="Cari anggota, judul, atau referensi..."
                       class="flex-1 min-w-[220px] px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <select x-model="filter.jenis" @change="loadPengawasan()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Semua Layanan</option>
                    <template x-for="jenis in jenisLayanan" :key="jenis">
                        <option :value="jenis" x-text="label(jenis)"></option>
                    </template>
                </select>
                <select x-model="filter.status" @change="loadPengawasan()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Semua Status</option>
                    <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                    <option value="diverifikasi">Diverifikasi</option>
                    <option value="selesai">Selesai</option>
                    <option value="ditolak">Ditolak</option>
                </select>
                <button @click="loadPengawasan()"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">
                    Terapkan
                </button>
            </div>

            <div x-show="loading" class="py-8 text-center text-sm text-gray-500">Memuat data pengawasan...</div>

            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full min-w-max text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider">
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Anggota</th>
                            <th class="px-4 py-3 text-left">Layanan</th>
                            <th class="px-4 py-3 text-left">Referensi</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="item in list" :key="item.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600" x-text="tanggal(item.created_at)"></td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900" x-text="item.user?.name || '-'"></p>
                                    <p class="text-[10px] text-gray-500" x-text="item.judul || '-'"></p>
                                </td>
                                <td class="px-4 py-3 text-gray-700" x-text="label(item.jenis_transaksi)"></td>
                                <td class="px-4 py-3 font-mono text-[10px] text-gray-500" x-text="item.referensi || '-'"></td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900" x-text="rupiah(item.jumlah)"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold"
                                          :class="statusClass(item.status)"
                                          x-text="label(item.status)"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="list.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada transaksi untuk ditampilkan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div x-show="pagination.last_page > 1"
                 class="px-4 py-3 border-t border-gray-100 flex items-center justify-between gap-3">
                <p class="text-xs text-gray-500"
                   x-text="'Menampilkan ' + pagination.from + '–' + pagination.to + ' dari ' + pagination.total"></p>
                <div class="flex gap-2">
                    <button @click="loadPengawasan(pagination.current_page - 1)"
                            :disabled="pagination.current_page <= 1"
                            class="px-3 py-1.5 border border-gray-300 rounded-lg text-xs disabled:opacity-40">Sebelumnya</button>
                    <button @click="loadPengawasan(pagination.current_page + 1)"
                            :disabled="pagination.current_page >= pagination.last_page"
                            class="px-3 py-1.5 border border-gray-300 rounded-lg text-xs disabled:opacity-40">Berikutnya</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function pengawasanDps() {
            return {
                loading: false,
                stats: {
                    saldo_simpanan: 0,
                    setoran_menunggu: 0,
                    pembiayaan_aktif: 0,
                    akad_menunggu_dps: 0,
                    perlu_tindak_lanjut: 0,
                    transaksi_menunggu: 0,
                },
                matriks: { simpanan: [], pembiayaan: [], layanan: [] },
                jenisLayanan: [],
                list: [],
                pagination: { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 },
                filter: { q: '', jenis: '', status: '' },

                label(value) {
                    if (!value) return '-';
                    return String(value)
                        .replaceAll('_', ' ')
                        .replace(/\b\w/g, character => character.toUpperCase());
                },

                rupiah(value) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value || 0));
                },

                tanggal(value) {
                    if (!value) return '-';
                    return new Intl.DateTimeFormat('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric'
                    }).format(new Date(value));
                },

                statusClass(status) {
                    return {
                        menunggu_verifikasi: 'bg-amber-100 text-amber-700',
                        diverifikasi: 'bg-blue-100 text-blue-700',
                        selesai: 'bg-emerald-100 text-emerald-700',
                        ditolak: 'bg-red-100 text-red-700',
                    }[status] || 'bg-gray-100 text-gray-600';
                },

                async loadPengawasan(page = 1) {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({ page });
                        if (this.filter.q) params.append('q', this.filter.q);
                        if (this.filter.jenis) params.append('jenis', this.filter.jenis);
                        if (this.filter.status) params.append('status', this.filter.status);

                        const response = await fetch('{{ route("dps.pengawasan.index") }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Data pengawasan tidak dapat dimuat.');
                        }

                        this.stats = data.stats || this.stats;
                        this.matriks = data.matriks || this.matriks;
                        this.jenisLayanan = data.jenis_layanan || [];
                        this.list = data.data || [];
                        this.pagination = data.pagination || this.pagination;
                    } catch (error) {
                        console.error('Gagal memuat pengawasan:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
</div>
