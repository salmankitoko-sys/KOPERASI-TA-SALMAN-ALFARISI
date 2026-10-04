{{-- ========== PANEL: OPINI SYARIAH ========== --}}
<div x-show="activeTab === 'opini'" x-cloak
     x-data="opiniDps()"
     x-init="loadOpini()"
     class="space-y-6">

    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3 mb-6">
            <div>
                <h4 class="font-bold text-gray-900">Nasihat & Opini Syariah</h4>
                <p class="text-sm text-gray-500 mt-0.5">Sampaikan nasihat dan rekomendasi tercatat untuk akad pembiayaan maupun produk.</p>
            </div>
            <button @click="loadOpini()"
                    class="inline-flex items-center gap-1 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition font-medium whitespace-nowrap shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh
            </button>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Disetujui</p>
                <p class="text-xl font-bold text-emerald-600" x-text="stats.disetujui">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Perlu Revisi</p>
                <p class="text-xl font-bold text-amber-600" x-text="stats.perlu_revisi">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Ditolak</p>
                <p class="text-xl font-bold text-red-600" x-text="stats.ditolak">0</p>
            </div>
        </div>

        {{-- Form Opini --}}
        <div class="mb-6 rounded-xl border border-gray-200 bg-gray-50/50 p-5">
            <p class="font-bold text-gray-900 mb-4">Terbitkan Opini Baru</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Jenis Objek *</label>
                    <select x-model="form.jenis_objek" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="pembiayaan">Pembiayaan / Akad</option>
                        <option value="produk">Produk</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">ID Objek *</label>
                    <input type="number" x-model="form.objek_id" placeholder="ID pembiayaan/produk"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Hasil *</label>
                    <select x-model="form.hasil" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="Disetujui">Disetujui</option>
                        <option value="Perlu Revisi">Perlu Revisi</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Catatan</label>
                    <textarea x-model="form.catatan" rows="1" placeholder="Catatan syariah..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button @click="submitOpini()" :disabled="form.saving"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg transition disabled:opacity-60">
                    <span x-show="!form.saving">Terbitkan Opini</span>
                    <span x-show="form.saving">Menyimpan...</span>
                </button>
            </div>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="text-center py-8">
            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-sm text-gray-500 mt-3">Memuat opini...</p>
        </div>

        {{-- Tabel --}}
        <div x-show="!loading" class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full min-w-max text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider">
                        <th class="px-4 py-3 text-left whitespace-nowrap">Nomor Opini</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Jenis Objek</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">ID Objek</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Hasil</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Catatan</th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Ditandatangani</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="o in list" :key="o.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap" x-text="o.nomor_opini"></td>
                            <td class="px-4 py-3 text-center whitespace-nowrap" x-text="o.jenis_objek === 'pembiayaan' ? 'Pembiayaan' : 'Produk'"></td>
                            <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap" x-text="o.objek_id"></td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold"
                                      :class="hasilClass(o.hasil)"
                                      x-text="o.hasil"></span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-[220px]" x-text="o.catatan || '-'"></td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap" x-text="o.dps?.name || '-'"></td>
                            <td class="px-4 py-3 text-center text-gray-600 whitespace-nowrap" x-text="o.tanggal_opini"></td>
                        </tr>
                    </template>
                    <tr x-show="!list || list.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <p class="font-medium">Belum ada opini syariah.</p>
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
                <button @click="loadOpini(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
                        :class="pagination.prev_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition disabled:opacity-60">
                    Sebelumnya
                </button>
                <button @click="loadOpini(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
                        :class="pagination.next_page_url ? 'bg-white text-gray-700 hover:bg-gray-50' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                        class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium transition disabled:opacity-60">
                    Berikutnya
                </button>
            </div>
        </div>
    </div>

    <script>
        function opiniDps() {
            return {
                loading: false,
                list: [],
                stats: { disetujui: 0, perlu_revisi: 0, ditolak: 0 },
                pagination: { current_page: 1, last_page: 1, from: 0, to: 0, total: 0, prev_page_url: null, next_page_url: null },
                form: { jenis_objek: 'pembiayaan', objek_id: '', hasil: 'Disetujui', catatan: '', saving: false },

                hasilClass(h) {
                    const map = { 'Disetujui': 'bg-emerald-100 text-emerald-700', 'Perlu Revisi': 'bg-amber-100 text-amber-700', 'Ditolak': 'bg-red-100 text-red-700' };
                    return map[h] || 'bg-gray-100 text-gray-600';
                },

                async loadOpini(page = null) {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        if (page) params.append('page', page);

                        const res = await fetch('{{ route("dps.opini.index") }}?' + params.toString(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();

                        this.list = data.data || [];
                        this.stats = data.stats || this.stats;
                        this.pagination = data.pagination || this.pagination;
                    } catch (e) {
                        console.error('Gagal memuat opini:', e);
                    } finally {
                        this.loading = false;
                    }
                },

                async submitOpini() {
                    if (!this.form.objek_id) {
                        alert('ID objek wajib diisi.');
                        return;
                    }
                    this.form.saving = true;
                    try {
                        const res = await fetch('{{ route("dps.opini.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                                            'X-CSRF-TOKEN': document.querySelector('meta[name\'csrf-token\']')?.getAttribute('content') ?? '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                jenis_objek: this.form.jenis_objek,
                                objek_id: this.form.objek_id,
                                hasil: this.form.hasil,
                                catatan: this.form.catatan,
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            alert(data.message);
                            this.form.objek_id = '';
                            this.form.catatan = '';
                            this.loadOpini();
                        } else {
                            alert(data.message || 'Gagal menerbitkan opini.');
                        }
                    } catch (e) {
                        alert('Gagal terhubung ke server.');
                    } finally {
                        this.form.saving = false;
                    }
                }
            }
        }
    </script>
</div>

