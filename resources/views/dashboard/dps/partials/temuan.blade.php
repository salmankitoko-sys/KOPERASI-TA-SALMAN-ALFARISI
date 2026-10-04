{{-- ========== PANEL: TEMUAN AUDIT ========== --}}
<div x-show="activeTab === 'temuan'" x-cloak
     x-data="temuanDps()"
     x-init="loadTemuan()"
     class="space-y-6">

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3 mb-6">
            <div>
                <h4 class="font-bold text-gray-900">Temuan & Tindak Lanjut</h4>
                <p class="text-sm text-gray-500 mt-0.5">Catat penyimpangan, berikan rekomendasi, dan pantau penyelesaiannya.</p>
            </div>
            <div class="flex gap-2">
                <button @click="loadTemuan()"
                        class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition font-medium whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Refresh
                </button>
                <button @click="openForm()"
                        class="inline-flex items-center gap-1 px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition font-medium whitespace-nowrap">
                    + Temuan Baru
                </button>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Dibuka</p>
                <p class="text-xl font-bold text-red-600" x-text="stats.dibuka">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Ditindaklanjuti</p>
                <p class="text-xl font-bold text-amber-600" x-text="stats.ditindaklanjuti">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Diverifikasi</p>
                <p class="text-xl font-bold text-blue-600" x-text="stats.diverifikasi">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Ditutup</p>
                <p class="text-xl font-bold text-emerald-600" x-text="stats.ditutup">0</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap gap-3 mb-4">
            <input type="text" x-model="filter.search" @keyup.enter="loadTemuan()" placeholder="Cari nomor / jenis temuan..."
                   class="flex-1 min-w-[200px] px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            <select x-model="filter.status" @change="loadTemuan()"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                <option value="">Semua Status</option>
                <option value="Dibuka">Dibuka</option>
                <option value="Ditindaklanjuti">Ditindaklanjuti</option>
                <option value="Diverifikasi">Diverifikasi</option>
                <option value="Ditutup">Ditutup</option>
            </select>
            <select x-model="filter.tingkat_resiko" @change="loadTemuan()"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                <option value="">Semua Risiko</option>
                <option value="Rendah">Rendah</option>
                <option value="Sedang">Sedang</option>
                <option value="Tinggi">Tinggi</option>
                <option value="Kritis">Kritis</option>
            </select>
            <button @click="loadTemuan()" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition font-medium">
                Cari
            </button>
        </div>

        {{-- Form Temuan Baru --}}
        <div x-show="form.open" x-cloak class="mb-6 rounded-xl border border-indigo-200 bg-indigo-50/50 p-5">
            <p class="font-bold text-gray-900 mb-4">Buat Temuan Baru</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Jenis Temuan *</label>
                    <input type="text" x-model="form.jenis_temuan" placeholder="cth: Margin tidak sesuai akad"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Kategori</label>
                    <select x-model="form.kategori" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Pilih kategori...</option>
                        <option value="Kepatuhan Akad">Kepatuhan Akad</option>
                        <option value="Dokumen">Dokumen</option>
                        <option value="Perhitungan">Perhitungan</option>
                        <option value="Prosedur">Prosedur</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Tingkat Risiko *</label>
                    <select x-model="form.tingkat_resiko" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="Rendah">Rendah</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Kritis">Kritis</option>
                    </select>
                </div>

                {{-- Pembiayaan autocomplete --}}
                <div class="relative">
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Referensi Pembiayaan (opsional)</label>
                    <input type="text" x-model="form.pembiayaan_search"
                           @input.debounce.300ms="searchPembiayaan()"
                           @focus="form.pembiayaan_results.length > 0 && (form.pembiayaan_dropdown = true)"
                           placeholder="Cari kode / nama anggota..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <div x-show="form.pembiayaan_dropdown && form.pembiayaan_results.length > 0"
                         x-cloak class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="r in form.pembiayaan_results" :key="r.id">
                            <button @click="form.pembiayaan_id = r.id; form.pembiayaan_search = r.label; form.pembiayaan_dropdown = false; form.pembiayaan_results = []"
                                    class="w-full text-left px-3 py-2 text-xs hover:bg-indigo-50 transition border-b border-gray-50 last:border-0">
                                <span class="font-semibold text-gray-900" x-text="r.label"></span>
                            </button>
                        </template>
                    </div>
                    <div x-show="form.pembiayaan_id" class="mt-1">
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                            ID: <span x-text="form.pembiayaan_id"></span>
                            <button @click="form.pembiayaan_id = ''; form.pembiayaan_search = ''" class="text-indigo-400 hover:text-indigo-600">×</button>
                        </span>
                    </div>
                </div>

                {{-- Produk autocomplete --}}
                <div class="relative">
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Referensi Produk (opsional)</label>
                    <input type="text" x-model="form.produk_search"
                           @input.debounce.300ms="searchProduk()"
                           @focus="form.produk_results.length > 0 && (form.produk_dropdown = true)"
                           placeholder="Cari nama produk..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <div x-show="form.produk_dropdown && form.produk_results.length > 0"
                         x-cloak class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="r in form.produk_results" :key="r.id">
                            <button @click="form.produk_id = r.id; form.produk_search = r.label; form.produk_dropdown = false; form.produk_results = []"
                                    class="w-full text-left px-3 py-2 text-xs hover:bg-indigo-50 transition border-b border-gray-50 last:border-0">
                                <span class="font-semibold text-gray-900" x-text="r.label"></span>
                            </button>
                        </template>
                    </div>
                    <div x-show="form.produk_id" class="mt-1">
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                            ID: <span x-text="form.produk_id"></span>
                            <button @click="form.produk_id = ''; form.produk_search = ''" class="text-indigo-400 hover:text-indigo-600">×</button>
                        </span>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Deskripsi Temuan *</label>
                    <textarea x-model="form.deskripsi" rows="3" placeholder="Jelaskan temuan audit..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Rekomendasi</label>
                    <textarea x-model="form.rekomendasi" rows="2" placeholder="Rekomendasi perbaikan..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button @click="submitTemuan()" :disabled="form.saving"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg transition disabled:opacity-60">
                    <span x-show="!form.saving">Simpan Temuan</span>
                    <span x-show="form.saving">Menyimpan...</span>
                </button>
                <button @click="form.open = false" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition font-medium">
                    Batal
                </button>
            </div>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="text-center py-8">
            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-sm text-gray-500 mt-3">Memuat temuan...</p>
        </div>

        {{-- Tabel --}}
        <div x-show="!loading" class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full min-w-max text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider">
                        <th class="px-4 py-3 text-left whitespace-nowrap">Nomor</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Jenis Temuan</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Risiko</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Dibuat</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Tanggal</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="t in list" :key="t.id">
                        <tr class="hover:bg-gray-50 transition" :class="{'bg-red-50/40': t.tingkat_resiko === 'Kritis'}">
                            <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap" x-text="t.nomor_temuan"></td>
                            <td class="px-4 py-3 text-gray-700">
                                <span x-text="t.jenis_temuan"></span>
                                <span class="block text-[10px] text-gray-400" x-text="t.kategori || ''"></span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold"
                                      :class="risikoClass(t.tingkat_resiko)"
                                      x-text="t.tingkat_resiko"></span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold"
                                      :class="statusClass(t.status)"
                                      x-text="t.status"></span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap" x-text="t.dibuat_oleh?.name || '-'"></td>
                            <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap" x-text="t.tanggal_temuan"></td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <button @click="openDetail(t.id)"
                                        class="px-2.5 py-1 text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!list || list.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <p class="font-medium">Belum ada temuan audit.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div x-show="!loading && pagination.last_page > 1"
             class="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-gray-100 pt-4 mt-4">
            <p class="text-sm text-gray-600" x-text="'Menampilkan ' + pagination.from + ' - ' + pagination.to + ' dari ' + pagination.total"></p>
            <div class="flex gap-2">
                <button @click="loadTemuan(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
                        :class="pagination.prev_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition disabled:opacity-60">
                    Sebelumnya
                </button>
                <button @click="loadTemuan(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
                        :class="pagination.next_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition disabled:opacity-60">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Detail & Update Status --}}
    <div x-show="detail.id" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="detail.id = null"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[85vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-gray-900" x-text="detail.data?.nomor_temuan || 'Detail Temuan'"></h4>
                    <p class="text-xs text-gray-500" x-text="detail.data?.jenis_temuan || ''"></p>
                </div>
                <button @click="detail.id = null" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                <template x-if="detail.loading">
                    <div class="text-center py-8">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </template>

                <template x-if="!detail.loading && detail.data">
                    <div class="space-y-4">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-[10px] text-gray-500 mb-1">Deskripsi</p>
                            <p class="text-sm text-gray-800" x-text="detail.data.deskripsi"></p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-[10px] text-gray-500 mb-1">Rekomendasi</p>
                            <p class="text-sm text-gray-800" x-text="detail.data.rekomendasi || '-'"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-[10px] text-gray-500">Tingkat Risiko</p>
                                <p class="text-sm font-semibold text-gray-900" x-text="detail.data.tingkat_resiko"></p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-[10px] text-gray-500">Status</p>
                                <p class="text-sm font-semibold text-gray-900" x-text="detail.data.status"></p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-[10px] text-gray-500">Pembiayaan</p>
                                <p class="text-sm font-semibold text-gray-900" x-text="detail.data.pembiayaan?.kode || '-'"></p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-[10px] text-gray-500">Produk</p>
                                <p class="text-sm font-semibold text-gray-900" x-text="detail.data.produk?.nama || '-'"></p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-[10px] text-gray-500">Dibuat oleh</p>
                                <p class="text-sm font-semibold text-gray-900" x-text="detail.data.dibuat_oleh?.name || '-'"></p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3">
                                <p class="text-[10px] text-gray-500">Tanggal Temuan</p>
                                <p class="text-sm font-semibold text-gray-900" x-text="detail.data.tanggal_temuan"></p>
                            </div>
                        </div>

                        {{-- Update status --}}
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-sm font-bold text-gray-900 mb-2">Perbarui Status Verifikasi</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="s in ['Dibuka', 'Ditindaklanjuti', 'Diverifikasi', 'Ditutup']" :key="s">
                                    <button @click="updateStatus(s)"
                                            :disabled="s === detail.data.status || detail.updating"
                                            class="px-3 py-1.5 text-xs font-bold rounded-lg border transition disabled:opacity-40"
                                            :class="s === detail.data.status ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'">
                                        <span x-text="s"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function temuanDps() {
            return {
                loading: false,
                list: [],
                stats: { dibuka: 0, ditindaklanjuti: 0, diverifikasi: 0, ditutup: 0 },
                pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0, prev_page_url: null, next_page_url: null },
                filter: { search: '', status: '', tingkat_resiko: '' },
                form: {
                    open: false, saving: false,
                    jenis_temuan: '', kategori: '', tingkat_resiko: 'Sedang', deskripsi: '', rekomendasi: '',
                    pembiayaan_id: '', pembiayaan_search: '', pembiayaan_results: [], pembiayaan_dropdown: false,
                    produk_id: '', produk_search: '', produk_results: [], produk_dropdown: false,
                },
                detail: { id: null, loading: false, updating: false, data: null },

                risikoClass(r) {
                    const map = { Rendah: 'bg-emerald-100 text-emerald-700', Sedang: 'bg-amber-100 text-amber-700', Tinggi: 'bg-orange-100 text-orange-700', Kritis: 'bg-red-100 text-red-700' };
                    return map[r] || 'bg-gray-100 text-gray-600';
                },
                statusClass(s) {
                    const map = {
                        'Dibuka': 'bg-red-100 text-red-700',
                        'Ditindaklanjuti': 'bg-amber-100 text-amber-700',
                        'Diverifikasi': 'bg-blue-100 text-blue-700',
                        'Ditutup': 'bg-emerald-100 text-emerald-700',
                    };
                    return map[s] || 'bg-gray-100 text-gray-600';
                },

                openForm() {
                    this.form = {
                        open: true, saving: false,
                        jenis_temuan: '', kategori: '', tingkat_resiko: 'Sedang', deskripsi: '', rekomendasi: '',
                        pembiayaan_id: '', pembiayaan_search: '', pembiayaan_results: [], pembiayaan_dropdown: false,
                        produk_id: '', produk_search: '', produk_results: [], produk_dropdown: false,
                    };
                },

                async searchPembiayaan() {
                    if (this.form.pembiayaan_search.length < 2) { this.form.pembiayaan_results = []; return; }
                    try {
                        const res = await fetch('{{ route("dps.temuan.search.pembiayaan") }}?q=' + encodeURIComponent(this.form.pembiayaan_search), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.form.pembiayaan_results = data.data || [];
                        this.form.pembiayaan_dropdown = true;
                    } catch (e) { console.error(e); }
                },

                async searchProduk() {
                    if (this.form.produk_search.length < 2) { this.form.produk_results = []; return; }
                    try {
                        const res = await fetch('{{ route("dps.temuan.search.produk") }}?q=' + encodeURIComponent(this.form.produk_search), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.form.produk_results = data.data || [];
                        this.form.produk_dropdown = true;
                    } catch (e) { console.error(e); }
                },

                async loadTemuan(page = null) {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        if (page) params.append('page', page);
                        if (this.filter.search) params.append('search', this.filter.search);
                        if (this.filter.status) params.append('status', this.filter.status);
                        if (this.filter.tingkat_resiko) params.append('tingkat_resiko', this.filter.tingkat_resiko);

                        const res = await fetch('{{ route("dps.temuan.index") }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();

                        this.list = data.data || [];
                        this.stats = data.stats || this.stats;
                        this.pagination = data.pagination || this.pagination;
                    } catch (e) {
                        console.error('Gagal memuat temuan:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                async submitTemuan() {
                    if (!this.form.jenis_temuan || !this.form.deskripsi) {
                        alert('Jenis temuan dan deskripsi wajib diisi.');
                        return;
                    }
                    this.form.saving = true;
                    try {
                        const res = await fetch('{{ route("dps.temuan.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') ?? '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                jenis_temuan: this.form.jenis_temuan,
                                kategori: this.form.kategori,
                                tingkat_resiko: this.form.tingkat_resiko,
                                deskripsi: this.form.deskripsi,
                                rekomendasi: this.form.rekomendasi,
                                pembiayaan_id: this.form.pembiayaan_id || null,
                                produk_id: this.form.produk_id || null,
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            alert(data.message);
                            this.form.open = false;
                            this.loadTemuan();
                        } else {
                            alert(data.message || 'Gagal menyimpan temuan.');
                        }
                    } catch (e) {
                        alert('Gagal terhubung ke server.');
                    } finally {
                        this.form.saving = false;
                    }
                },

                async openDetail(id) {
                    this.detail = { id, loading: true, updating: false, data: null };
                    try {
                        const res = await fetch('{{ route("dps.temuan.show", ":id") }}'.replace(':id', id), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.detail.data = data.data || null;
                    } catch (e) {
                        console.error('Gagal memuat detail temuan:', e);
                    } finally {
                        this.detail.loading = false;
                    }
                },

                async updateStatus(status) {
                    if (!confirm('Ubah status temuan menjadi ' + status + '?')) return;
                    this.detail.updating = true;
                    try {
                        const res = await fetch('{{ route("dps.temuan.update", ":id") }}'.replace(':id', this.detail.data.id), {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content') ?? '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ status })
                        });
                        const data = await res.json();
                        alert(data.message || 'Status diperbarui.');
                        this.openDetail(this.detail.data.id);
                        this.loadTemuan();
                    } catch (e) {
                        alert('Gagal memperbarui status.');
                    } finally {
                        this.detail.updating = false;
                    }
                }
            }
        }
    </script>
</div>
