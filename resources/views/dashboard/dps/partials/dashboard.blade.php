<div x-show="activeTab === 'dashboard'" x-cloak class="space-y-6">

    {{-- Header DPS --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-gray-900">Assalamu'alaikum, {{ auth()->user()->name }} 👋</h2>
                <p class="text-sm text-gray-500 mt-0.5">Panel pengawasan independen Koperasi Syariah.</p>
            </div>
            <span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-full font-medium whitespace-nowrap">
                Dewan Pengawas Syariah
            </span>
        </div>
    </div>

    {{-- Ringkasan fungsi DPS yang tersedia --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <button @click="activeTab = 'audit'" class="text-left bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:border-indigo-200 hover:shadow transition">
            <p class="text-xs font-bold text-indigo-700">Validasi Akad</p>
            <p class="mt-2 text-2xl font-bold text-indigo-600">{{ $stats['akad_menunggu_validasi'] ?? 0 }}</p>
            <p class="mt-1 text-xs text-gray-500">Akad menunggu validasi</p>
        </button>
        <button @click="activeTab = 'laporan'" class="text-left bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:border-indigo-200 hover:shadow transition">
            <p class="text-xs font-bold text-indigo-700">Laporan</p>
            <p class="mt-2 text-2xl font-bold text-blue-600">{{ $stats['laporan_semester'] ?? 0 }}</p>
            <p class="mt-1 text-xs text-gray-500">Laporan pengawasan terbit</p>
        </button>
        <button @click="activeTab = 'notifikasi'" class="text-left bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:border-indigo-200 hover:shadow transition">
            <p class="text-xs font-bold text-indigo-700">Notifikasi</p>
            <p class="mt-2 text-sm font-semibold text-gray-800">Lihat pemberitahuan terbaru</p>
            <p class="mt-1 text-xs text-gray-500">Informasi validasi akad dan laporan</p>
        </button>
    </div>
    {{-- Disclaimer akses --}}
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 text-xs text-indigo-800">
        <p class="font-bold mb-1">🔒 Hak Akses DPS</p>
        <p class="opacity-90">
            DPS hanya memiliki akses ke Dashboard, Validasi Akad, Laporan, dan Notifikasi.
            DPS tidak menyetujui pencairan atau mengubah status operasional pembiayaan.
            Setiap aksi tercatat dalam audit trail.
        </p>
    </div>
</div>

