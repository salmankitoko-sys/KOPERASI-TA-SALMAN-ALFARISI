{{-- PANEL: LAPORAN DPS OTOMATIS --}}
<div x-show="activeTab === 'laporan'" x-cloak x-data="laporanDps()" x-init="loadLaporan()" class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div><h4 class="font-bold text-gray-900">Cetak Laporan Pengawasan Otomatis</h4><p class="mt-1 text-sm text-gray-500">Sistem merangkum data validasi akad, membuat PDF, dan langsung mengirimkannya kepada Ketua.</p></div>
            <button @click="loadLaporan()" class="rounded-lg border px-3 py-2 text-sm font-semibold text-gray-700">Refresh</button>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border p-4 text-center"><p class="text-xs text-gray-500">Total Laporan</p><p class="text-xl font-bold text-indigo-600" x-text="stats.total">0</p></div>
            <div class="rounded-xl border p-4 text-center"><p class="text-xs text-gray-500">Draf</p><p class="text-xl font-bold text-amber-600" x-text="stats.draf">0</p></div>
            <div class="rounded-xl border p-4 text-center"><p class="text-xs text-gray-500">Terkirim ke Ketua</p><p class="text-xl font-bold text-emerald-600" x-text="stats.terbit">0</p></div>
        </div>

        <form @submit.prevent="generateLaporan()" class="mt-6 space-y-4 rounded-xl border border-indigo-100 bg-indigo-50/50 p-5">
            <div><h5 class="font-bold text-indigo-950">Parameter Laporan</h5><p class="text-xs text-indigo-700">Isi periode data yang akan dirangkum. Rekomendasi dapat dikosongkan agar dibuat otomatis.</p></div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <label class="text-xs font-semibold text-gray-600">Jenis Periode<select x-model="form.periode" class="mt-1 w-full rounded-lg border-gray-300 text-sm"><option>Semester</option><option>Tahunan</option></select></label>
                <label class="text-xs font-semibold text-gray-600">Nama Periode<input x-model="form.semester" placeholder="Contoh: 2026-1" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                <label class="text-xs font-semibold text-gray-600">Tanggal Mulai<input type="date" required x-model="form.periode_mulai" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
                <label class="text-xs font-semibold text-gray-600">Tanggal Selesai<input type="date" required x-model="form.periode_selesai" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
            </div>
            <label class="block text-xs font-semibold text-gray-600">Rekomendasi DPS (opsional)<textarea x-model="form.rekomendasi" rows="3" placeholder="Kosongkan untuk rekomendasi otomatis berdasarkan hasil validasi" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></textarea></label>
            <div class="rounded-lg bg-white p-4 text-xs text-gray-600"><strong>Isi PDF:</strong> identitas dan periode, ringkasan eksekutif, statistik hasil validasi, sebaran jenis akad, isu kepatuhan, rekomendasi, kesimpulan, pengesahan, serta lampiran rincian validasi.</div>
            <button type="submit" :disabled="form.saving" class="rounded-lg bg-indigo-700 px-4 py-2.5 text-sm font-bold text-white disabled:opacity-60"><span x-show="!form.saving">Cetak & Kirim ke Ketua</span><span x-show="form.saving">Menyusun laporan...</span></button>
        </form>

        <div class="mt-6 overflow-x-auto rounded-xl border">
            <table class="w-full min-w-[760px] text-xs"><thead class="bg-gray-50 text-gray-500"><tr><th class="px-4 py-3 text-left">Periode</th><th class="px-4 py-3 text-left">Rentang Data</th><th class="px-4 py-3 text-left">Ringkasan</th><th class="px-4 py-3 text-center">Status</th><th class="px-4 py-3 text-center">Dokumen</th></tr></thead>
                <tbody class="divide-y"><template x-for="l in list" :key="l.id"><tr><td class="px-4 py-3 font-semibold" x-text="l.periode + (l.semester ? ' - '+l.semester : '')"></td><td class="px-4 py-3" x-text="(l.periode_mulai || '-') + ' s.d. ' + (l.periode_selesai || '-')"></td><td class="max-w-xs px-4 py-3"><span class="line-clamp-2" x-text="l.ringkasan"></span></td><td class="px-4 py-3 text-center"><span class="rounded-full bg-emerald-100 px-2 py-1 font-bold text-emerald-700" x-text="l.status"></span></td><td class="px-4 py-3 text-center"><a :href="'/laporan-pengawasan/'+l.id+'/pdf'" class="rounded-lg bg-indigo-50 px-3 py-2 font-bold text-indigo-700">Unduh PDF</a></td></tr></template><tr x-show="!loading && list.length === 0"><td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada laporan.</td></tr></tbody>
            </table>
        </div>
    </div>

    <script>
        function laporanDps() {
            return {
                loading: false, list: [], stats: {total: 0, draf: 0, terbit: 0},
                form: {periode: 'Semester', semester: '', periode_mulai: '', periode_selesai: '', rekomendasi: '', saving: false},
                async loadLaporan() {
                    this.loading = true;
                    try { const res = await fetch('{{ route("dps.laporan.index") }}', {headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}); const data = await res.json(); this.list = data.data || []; this.stats = data.stats || this.stats; }
                    finally { this.loading = false; }
                },
                async generateLaporan() {
                    if (!this.form.periode_mulai || !this.form.periode_selesai) return alert('Tanggal mulai dan selesai wajib diisi.');
                    if (!confirm('Cetak laporan dan kirimkan kepada Ketua Koperasi?')) return;
                    this.form.saving = true;
                    try {
                        const res = await fetch('{{ route("dps.laporan.generate") }}', {method:'POST', headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'}, body:JSON.stringify(this.form)});
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || Object.values(data.errors || {}).flat().join('\n'));
                        alert(data.message); await this.loadLaporan(); window.open(data.download_url, '_blank');
                    } catch (e) { alert(e.message || 'Gagal membuat laporan.'); }
                    finally { this.form.saving = false; }
                }
            }
        }
    </script>
</div>