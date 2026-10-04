<div class="pk-tab-content" id="pk_murabahah">
  <section class="bg-white border border-gray-200 rounded-lg p-5">
    <div class="mb-4">
      <h2 class="text-lg font-black text-gray-900">Pembiayaan Murabahah</h2>
      <p class="text-sm text-gray-500 mt-0.5">Harga jual = harga beli + margin. Angsuran dibagi rata selama tenor.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Harga beli barang</label><input type="number" id="pk_m_beli" name="m_harga_beli" value="{{ old('m_harga_beli') }}" min="0" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Margin keuntungan (%)</label><input type="number" id="pk_m_margin" name="m_margin_persen" value="{{ old('m_margin_persen') }}" min="0" step="0.25" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Jangka waktu (bulan)</label><input type="number" id="pk_m_tenor" name="m_tenor" value="{{ old('m_tenor') }}" min="1" max="240" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Objek pembiayaan</label><input id="pk_m_objek" name="m_objek" value="{{ old('m_objek') }}" placeholder="Contoh: motor operasional usaha" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4" id="pk_m_results"></div>
    <div class="max-h-[390px] overflow-auto border border-gray-200 rounded-lg mt-4 bg-white">
      <table class="w-full border-collapse text-sm">
        <thead>
          <tr>
            <th class="sticky top-0 bg-teal-700 text-white text-left text-xs uppercase tracking-wide px-3 py-2.5">Bulan</th>
            <th class="sticky top-0 bg-teal-700 text-white text-right text-xs uppercase tracking-wide px-3 py-2.5">Pokok</th>
            <th class="sticky top-0 bg-teal-700 text-white text-right text-xs uppercase tracking-wide px-3 py-2.5">Margin</th>
            <th class="sticky top-0 bg-teal-700 text-white text-right text-xs uppercase tracking-wide px-3 py-2.5">Angsuran</th>
            <th class="sticky top-0 bg-teal-700 text-white text-right text-xs uppercase tracking-wide px-3 py-2.5">Sisa pokok</th>
          </tr>
        </thead>
        <tbody id="pk_m_sched"></tbody>
      </table>
    </div>
  </section>
</div>
