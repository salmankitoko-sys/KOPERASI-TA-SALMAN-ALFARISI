<div class="pk-tab-content hidden" id="pk_ijarah">
  <section class="bg-white border border-gray-200 rounded-lg p-5">
    <div class="mb-4">
      <h2 class="text-lg font-black text-gray-900">Pembiayaan Ijarah</h2>
      <p class="text-sm text-gray-500 mt-0.5">Simulasi sewa aset dengan ujrah bulanan, perawatan, dan opsi beli akhir.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Nilai aset</label><input type="number" id="pk_i_aset" name="i_nilai_aset" value="{{ old('i_nilai_aset') }}" min="0" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Jangka waktu sewa (bulan)</label><input type="number" id="pk_i_tenor" name="i_tenor" value="{{ old('i_tenor') }}" min="1" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Ujrah / bulan</label><input type="number" id="pk_i_ujrah" name="i_ujrah" value="{{ old('i_ujrah') }}" min="0" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Biaya perawatan / bulan</label><input type="number" id="pk_i_rawat" name="i_rawat" value="{{ old('i_rawat') }}" min="0" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Opsi beli di akhir (%)</label><input type="number" id="pk_i_opsi" name="i_opsi_persen" value="{{ old('i_opsi_persen') }}" min="0" step="1" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
      <div class="grid gap-1.5"><label class="text-[13px] text-gray-500 font-extrabold">Objek sewa</label><input id="pk_i_objek" name="i_objek" value="{{ old('i_objek') }}" placeholder="Contoh: mesin produksi" oninput="PembiayaanKalkulator.calculateActive()" class="w-full border border-gray-200 bg-white rounded-lg px-3 py-2.5 min-h-[44px] focus:outline focus:outline-[3px] focus:outline-teal-700/20 focus:border-teal-700"></div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4" id="pk_i_results"></div>
  </section>
</div>
