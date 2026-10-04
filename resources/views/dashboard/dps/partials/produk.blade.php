{{-- ========== PANEL: MODERASI PRODUK MARKETPLACE ========== --}}
<div x-show="activeTab === 'produk'" x-cloak
     x-data="produkModerasiDps()"
     x-init="loadProduk()"
     class="space-y-6">

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3 mb-6">
            <div>
                <h4 class="font-bold text-gray-900">Kajian Produk Marketplace</h4>
                <p class="text-sm text-gray-500 mt-0.5">Review kepatuhan barang/jasa marketplace. DPS tidak mengedit produk atau mengambil alih keputusan operasional pengurus.</p>
            </div>
            <button @click="loadProduk()"
                    class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition font-medium whitespace-nowrap shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh
            </button>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Menunggu Review</p>
                <p class="text-xl font-bold text-amber-600" x-text="stats.menunggu">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Aktif</p>
                <p class="text-xl font-bold text-emerald-600" x-text="stats.aktif">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Nonaktif / Habis</p>
                <p class="text-xl font-bold text-red-600" x-text="stats.nonaktif">0</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="flex flex-wrap gap-3 mb-4">
            <input type="text" x-model="filter.search" @keyup.enter="loadProduk()" placeholder="Cari produk / toko..."
                   class="flex-1 min-w-[180px] px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            <select x-model="filter.status" @change="loadProduk()"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                <option value="">Semua Status</option>
                <option value="pending">Menunggu Review</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
                <option value="habis">Habis</option>
            </select>
            <button @click="loadProduk()" class="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition font-medium">
                Cari
            </button>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="text-center py-8">
            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-sm text-gray-500 mt-3">Memuat produk...</p>
        </div>

        {{-- Tabel --}}
        <div x-show="!loading" class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full min-w-max text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider">
                        <th class="px-4 py-3 text-left whitespace-nowrap">Produk</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Toko</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Harga</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Stok</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Akad</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="p in list" :key="p.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap" x-text="p.nama"></td>
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap" x-text="p.toko?.nama_toko || '-'"></td>
                            <td class="px-4 py-3 text-right text-gray-700 whitespace-nowrap" x-text="rupiah(p.harga)"></td>
                            <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap" x-text="p.stok"></td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold bg-indigo-100 text-indigo-700"
                                      x-text="akadLabel(p.akad)"></span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold"
                                      :class="statusClass(p.status)"
                                      x-text="statusLabel(p.status)"></span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <button @click="openDetail(p.id)"
                                        class="px-2.5 py-1 text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                                    Review
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="!list || list.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <p class="font-medium">Belum ada produk.</p>
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
                <button @click="loadProduk(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
                        :class="pagination.prev_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition disabled:opacity-60">
                    Sebelumnya
                </button>
                <button @click="loadProduk(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
                        :class="pagination.next_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition disabled:opacity-60">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Detail & Review --}}
    <div x-show="detail.id" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" @click="detail.id = null"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[85vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-gray-900" x-text="detail.produk?.nama || 'Detail Produk'"></h4>
                    <p class="text-xs text-gray-500" x-text="detail.produk?.toko?.nama_toko || ''"></p>
                </div>
                <button @click="detail.id = null" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-6">
                <template x-if="detail.loading">
                    <div class="text-center py-8">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </template>

                <template x-if="!detail.loading && detail.produk">
                    <div class="space-y-4">
                        <div class="rounded-lg bg-gray-50 p-3 flex items-center justify-between">
                            <p class="text-xs text-gray-500">Harga</p>
                            <p class="text-sm font-semibold text-gray-900" x-text="rupiah(detail.produk.harga)"></p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3 flex items-center justify-between">
                            <p class="text-xs text-gray-500">Stok</p>
                            <p class="text-sm font-semibold text-gray-900" x-text="detail.produk.stok"></p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3 flex items-center justify-between">
                            <p class="text-xs text-gray-500">Akad</p>
                            <p class="text-sm font-semibold text-gray-900" x-text="akadLabel(detail.produk.akad)"></p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500 mb-1">Deskripsi</p>
                            <p class="text-sm text-gray-800" x-text="detail.produk.deskripsi || '-'"></p>
                        </div>

                        {{-- Opini sebelumnya --}}
                        <template x-if="detail.opini">
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3">
                                <p class="text-xs font-bold text-emerald-700 mb-1">Opini Terakhir</p>
                                <p class="text-xs text-emerald-800">
                                    <span x-text="detail.opini.nomor_opini"></span> — <span x-text="detail.opini.hasil"></span>
                                    <span x-text="detail.opini.tanggal_opini"></span>
                                </p>
                                <p class="text-xs text-emerald-700 mt-1" x-text="detail.opini.catatan || ''"></p>
                            </div>
                        </template>

                        {{-- Form review (hanya untuk status pending) --}}
                        <template x-if="detail.produk.status === 'pending'">
                            <div class="border-t border-gray-100 pt-4 space-y-3">
                                <p class="text-sm font-bold text-gray-900">Review Kepatuhan Syariah</p>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 block mb-1">Hasil Review</label>
                                    <select x-model="review.hasil" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                        <option value="Disetujui">Disetujui</option>
                                        <option value="Perlu Revisi">Perlu Revisi</option>
                                        <option value="Ditolak">Ditolak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 block mb-1">Catatan</label>
                                    <textarea x-model="review.catatan" rows="3" placeholder="Catatan syariah..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                                </div>
                                <button @click="submitReview(detail.produk.id)"
                                        :disabled="review.saving"
                                        class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg transition disabled:opacity-60">
                                    <span x-show="!review.saving">Kirim Review</span>
                                    <span x-show="review.saving">Menyimpan...</span>
                                </button>
                            </div>
                        </template>

                        <template x-if="detail.produk.status !== 'pending'">
                            <div class="rounded-lg bg-gray-50 p-3 text-center text-xs text-gray-500">
                                Produk sudah direview. Status: <span class="font-bold" x-text="statusLabel(detail.produk.status)"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function produkModerasiDps() {
            return {
                loading: false,
                list: [],
                stats: { menunggu: 0, aktif: 0, nonaktif: 0 },
                pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0, prev_page_url: null, next_page_url: null },
                filter: { search: '', status: '' },
                detail: { id: null, loading: false, produk: null, opini: null },
                review: { hasil: 'Disetujui', catatan: '', saving: false },

                rupiah(v) {
                    if (!v && v !== 0) return 'Rp 0';
                    if (isNaN(v)) return 'Rp 0';
                    return 'Rp ' + Number(v).toLocaleString('id-ID');
                },
                statusLabel(s) {
                    const map = { pending: 'Menunggu Review', aktif: 'Aktif', nonaktif: 'Nonaktif', habis: 'Habis' };
                    return map[s] || s;
                },
                statusClass(s) {
                    const map = {
                        pending: 'bg-amber-100 text-amber-700',
                        aktif: 'bg-emerald-100 text-emerald-700',
                        nonaktif: 'bg-red-100 text-red-700',
                        habis: 'bg-gray-100 text-gray-600',
                    };
                    return map[s] || 'bg-gray-100 text-gray-600';
                },
                akadLabel(a) {
                    const map = { murabahah: 'Murabahah', salam: 'Salam', istishna: 'Istishna' };
                    return map[a] || a;
                },

                async loadProduk(page = null) {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        if (page) params.append('page', page);
                        if (this.filter.search) params.append('search', this.filter.search);
                        if (this.filter.status) params.append('status', this.filter.status);

                        const res = await fetch('{{ route("dps.produk.index") }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();

                        this.list = data.data || [];
                        this.stats = data.stats || this.stats;
                        this.pagination = data.pagination || this.pagination;
                    } catch (e) {
                        console.error('Gagal memuat produk:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                async openDetail(id) {
                    this.detail = { id, loading: true, produk: null, opini: null };
                    this.review = { hasil: 'Disetujui', catatan: '', saving: false };
                    try {
                        const res = await fetch('{{ route("dps.produk.show", ":id") }}'.replace(':id', id), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.detail.produk = data.produk || null;
                        this.detail.opini = data.opini || null;
                    } catch (e) {
                        console.error('Gagal memuat detail produk:', e);
                    } finally {
                        this.detail.loading = false;
                    }
                },

                async submitReview(id) {
                    this.review.saving = true;
                    try {
                        const res = await fetch('{{ route("dps.produk.review", ":id") }}'.replace(':id', id), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name\'csrf-token\']')?.getAttribute('content') ?? '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ hasil: this.review.hasil, catatan: this.review.catatan })
                        });
                        const data = await res.json();
                        alert(data.message || 'Review selesai.');
                        this.detail.id = null;
                        this.loadProduk();
                    } catch (e) {
                        alert('Gagal mengirim review.');
                    } finally {
                        this.review.saving = false;
                    }
                }
            }
        }
    </script>
</div>

