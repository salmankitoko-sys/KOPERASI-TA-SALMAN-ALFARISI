{{-- ========== PANEL: BAGI HASIL ========== --}}
<div x-show="activeTab === 'bagihasil'" x-cloak class="space-y-6">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
        <h4 class="font-bold text-gray-900 mb-1">Bagi Hasil</h4>
        <p class="text-xs text-gray-500 mb-5">Estimasi bagi hasil berdasarkan simpanan mudharabah Anda.</p>

        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl p-5 text-white">
            <p class="text-xs text-emerald-50 font-bold uppercase tracking-wider">Estimasi Periode {{ $bagiHasil->periode ?? '-' }}</p>
            <p class="text-3xl font-black mt-1">{{ $rupiah($bagiHasil->estimasi) }}</p>
        </div>

        <p class="text-[11px] text-gray-400 mt-4 leading-relaxed">
            Nilai bagi hasil bersifat estimasi dan dapat berubah mengikuti kinerja usaha koperasi pada periode berjalan, sesuai prinsip syariah.
        </p>
    </div>
</div>