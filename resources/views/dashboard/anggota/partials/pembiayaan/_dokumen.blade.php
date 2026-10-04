<section class="w-full max-w-[1180px] mx-auto bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
  <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-5">
    <div>
      <p class="text-xs font-bold uppercase tracking-wider text-teal-700">Langkah 3 dari 3</p>
      <h2 class="text-base font-black text-gray-900 mt-1">Formulir Resmi Pengajuan</h2>
      <p class="text-sm text-gray-500 mt-0.5">Unduh formulir kosong, isi manual sesuai data pengajuan, lalu unggah kembali formulir yang sudah diisi.</p>
    </div>
    <a href="{{ route('anggota.pembiayaan.formulir') }}" class="inline-flex items-center justify-center rounded-lg bg-teal-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-teal-800 transition whitespace-nowrap">
      Download Formulir
    </a>
  </div>

  <div class="grid gap-1.5 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4">
    <label class="text-[13px] text-gray-500 font-extrabold">Upload formulir yang sudah diisi <span class="text-red-500">*</span></label>
    <input type="file" name="formulir_pengajuan" accept=".pdf,.doc,.docx" class="block w-full text-sm text-gray-700 border border-gray-200 bg-white rounded-lg file:mr-3 file:border-0 file:bg-gray-100 file:px-3 file:py-2.5 file:text-sm file:font-bold file:text-gray-700 hover:file:bg-gray-200">
    <p class="text-xs text-gray-400">Format yang diterima: PDF, DOC, atau DOCX. Maksimal 8 MB.</p>
  </div>
</section>
