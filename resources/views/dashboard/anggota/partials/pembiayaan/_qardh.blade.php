<div id="pk_qardh" class="pk-tab-content hidden">
    <section class="rounded-xl border border-gray-200 bg-white p-5">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-100 text-sm font-bold text-teal-700">Q</div>
            <div>
                <h3 class="text-base font-bold text-gray-900">Pembiayaan Qardh</h3>
                <p class="text-xs text-gray-500">Pinjaman tanpa keuntungan (interest-free loan). Total kewajiban = pokok pinjaman.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="grid gap-1.5">
                <label class="text-[13px] text-gray-500 font-extrabold">Pokok pembiayaan (Rp)</label>
                <input id="pk_q_pokok" name="q_pokok" type="number" min="0"
                    placeholder="Contoh: 5000000"
                    oninput="PembiayaanKalkulator.calculateActive()"
                    class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 text-gray-900 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700">
            </div>
            <div class="grid gap-1.5">
                <label class="text-[13px] text-gray-500 font-extrabold">Tenor (bulan)</label>
                <input id="pk_q_tenor" name="q_tenor" type="number" min="1" max="240"
                    placeholder="Contoh: 10"
                    oninput="PembiayaanKalkulator.calculateActive()"
                    class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 text-gray-900 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700">
            </div>
            <div class="grid gap-1.5 sm:col-span-2">
                <label class="text-[13px] text-gray-500 font-extrabold">Objek / tujuan pinjaman</label>
                <input id="pk_q_objek" name="q_objek" type="text"
                    placeholder="Contoh: Biaya pendidikan anak"
                    oninput="PembiayaanKalkulator.calculateActive()"
                    class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 text-gray-900 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700">
            </div>
        </div>

        <div class="mt-4 rounded-lg border border-teal-100 bg-teal-50/60 px-4 py-3 text-xs text-teal-800">
            <strong>Qardh</strong> = pinjaman tanpa margin, nisbah, atau bagi hasil. Anggota hanya mengembalikan pokok pinjaman sesuai tenor.
        </div>

        <div id="pk_q_results" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4"></div>

        <div class="mt-4">
            <p class="text-xs font-bold text-gray-500 mb-2">Jadwal Angsuran (Simulasi)</p>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-3 py-2 text-left">Bulan</th>
                            <th class="px-3 py-2 text-right">Pokok</th>
                            <th class="px-3 py-2 text-right">Margin</th>
                            <th class="px-3 py-2 text-right">Angsuran</th>
                            <th class="px-3 py-2 text-right">Sisa Pokok</th>
                        </tr>
                    </thead>
                    <tbody id="pk_q_sched"></tbody>
                </table>
            </div>
        </div>
    </section>
</div>
