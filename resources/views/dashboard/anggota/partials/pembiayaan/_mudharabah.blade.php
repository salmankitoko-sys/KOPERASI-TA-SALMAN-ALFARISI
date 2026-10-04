<div class="pk-tab-content hidden" id="pk_mudharabah">
  <section class="bg-white border border-gray-200 rounded-lg p-5">
    <div class="mb-4">
      <h2 class="text-lg font-black text-gray-900">Pembiayaan Mudharabah</h2>
      <p class="text-sm text-gray-500 mt-0.5">Bagi hasil dihitung dari estimasi laba usaha per bulan.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Modal koperasi</label><input type="number" id="pk_d_modal" name="d_modal" value="{{ old('d_modal') }}" min="0" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Nisbah koperasi (%)</label><input type="number" id="pk_d_nk" name="d_nisbah_koperasi" value="{{ old('d_nisbah_koperasi') }}" min="0" max="100" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Nisbah anggota (%)</label><input type="number" id="pk_d_na" name="d_nisbah_anggota" value="{{ old('d_nisbah_anggota') }}" min="0" max="100" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Estimasi omzet / bulan</label><input type="number" id="pk_d_omzet" name="d_omzet" value="{{ old('d_omzet') }}" min="0" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Biaya operasional / bulan</label><input type="number" id="pk_d_biaya" name="d_biaya" value="{{ old('d_biaya') }}" min="0" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Jangka waktu (bulan)</label><input type="number" id="pk_d_tenor" name="d_tenor" value="{{ old('d_tenor') }}" min="1" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
    </div>
    <div class="rounded-lg px-3.5 py-3 mt-3.5 font-extrabold text-sm border bg-emerald-50 text-emerald-800 border-emerald-200" id="pk_d_status">Nisbah sudah 100%. Simulasi siap dibuat.</div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4" id="pk_d_results"></div>
  </section>
</div>
