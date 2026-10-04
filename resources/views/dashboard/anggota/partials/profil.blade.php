{{-- ========== PANEL: PROFIL ========== --}}
<div x-show="activeTab === 'profil'" x-cloak class="space-y-6">
    <div class="bg-white border border-gray-200/80 rounded-2xl p-6 shadow-sm">
        <h4 class="font-bold text-gray-900 mb-1">Pengaturan Profil</h4>
        <p class="text-xs text-gray-500 mb-5">Kelola data akun Anda.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Nama</p>
                <p class="text-sm font-bold text-gray-900 mt-1">{{ $anggota->name ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Email</p>
                <p class="text-sm font-bold text-gray-900 mt-1">{{ $anggota->email ?? '-' }}</p>
            </div>
        </div>

        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 mt-5 px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition">
            Edit Profil Lengkap
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
    </div>
</div>