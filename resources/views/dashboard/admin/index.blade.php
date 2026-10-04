<x-admin-layout title="Dashboard Admin">
    @php
        $roleLabels = [
            \App\Models\User::ROLE_ADMIN => 'Admin',
            \App\Models\User::ROLE_KETUA => 'Ketua Koperasi',
            \App\Models\User::ROLE_PENGURUS => 'Pengurus',
            \App\Models\User::ROLE_DPS => 'DPS',
            \App\Models\User::ROLE_ANGGOTA => 'Anggota',
            \App\Models\User::ROLE_PELANGGAN => 'Pelanggan',
        ];
        $roleClasses = [
            \App\Models\User::ROLE_ADMIN => 'bg-slate-900 text-white',
            \App\Models\User::ROLE_KETUA => 'bg-violet-100 text-violet-800',
            \App\Models\User::ROLE_PENGURUS => 'bg-indigo-100 text-indigo-800',
            \App\Models\User::ROLE_DPS => 'bg-emerald-100 text-emerald-800',
            \App\Models\User::ROLE_ANGGOTA => 'bg-sky-100 text-sky-800',
            \App\Models\User::ROLE_PELANGGAN => 'bg-amber-100 text-amber-800',
        ];
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">
        <section class="overflow-hidden rounded-lg border border-indigo-900 bg-indigo-950 px-5 py-6 text-white shadow-lg shadow-indigo-950/15 lg:px-6">
            <div class="grid gap-7 xl:grid-cols-[minmax(0,1fr)_420px] xl:items-center">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-wide text-sky-300">Administrasi akses</p>
                    <h2 class="mt-3 text-2xl font-semibold sm:text-3xl">Kelola seluruh user dari satu tempat</h2>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-indigo-100 sm:text-base">
                        Lihat semua akun admin, ketua, pengurus, DPS, anggota, dan pelanggan marketplace. Admin dapat membuka detail akun dan menghapus data user yang tidak diperlukan.
                    </p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-indigo-950 transition hover:bg-indigo-100">Manajemen User</a>
                        <a href="{{ route('admin.users.export') }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-300/50 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">Ekspor Data</a>
                    </div>
                </div>

                <div class="grid grid-cols-2 divide-x divide-y divide-indigo-800 border border-indigo-800">
                    <div class="min-h-28 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-indigo-300">Total user</p>
                        <p class="mt-3 text-3xl font-semibold">{{ $stats['total_users'] }}</p>
                        <p class="mt-1 text-xs text-indigo-200">{{ $stats['new_this_month'] }} baru bulan ini</p>
                    </div>
                    <div class="min-h-28 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-indigo-300">Akses aktif</p>
                        <p class="mt-3 text-3xl font-semibold">{{ $stats['active_users'] }}</p>
                        <p class="mt-1 text-xs text-indigo-200">Dapat masuk ke sistem</p>
                    </div>
                    <div class="min-h-28 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-indigo-300">Anggota</p>
                        <p class="mt-3 text-3xl font-semibold">{{ $stats['total_anggota'] }}</p>
                        <p class="mt-1 text-xs text-indigo-200">Akun koperasi</p>
                    </div>
                    <div class="min-h-28 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-indigo-300">Pelanggan</p>
                        <p class="mt-3 text-3xl font-semibold">{{ $stats['total_pelanggan'] }}</p>
                        <p class="mt-1 text-xs text-indigo-200">Akun belanja</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <a href="{{ route('admin.users.index', ['status' => 'active']) }}" class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 transition hover:border-emerald-300 hover:bg-emerald-100/60">
                <p class="text-sm font-medium text-emerald-800">Akun aktif</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-950">{{ $stats['active_users'] }}</p>
                <p class="mt-1 text-xs text-emerald-700">Dapat menggunakan sistem</p>
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}" class="rounded-lg border border-rose-200 bg-rose-50 p-5 transition hover:border-rose-300 hover:bg-rose-100/60">
                <p class="text-sm font-medium text-rose-800">Akun nonaktif</p>
                <p class="mt-2 text-3xl font-semibold text-rose-950">{{ $stats['inactive_users'] }}</p>
                <p class="mt-1 text-xs text-rose-700">Tidak dapat masuk</p>
            </a>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-amber-200 bg-amber-50 p-5 transition hover:border-amber-300 hover:bg-amber-100/60">
                <p class="text-sm font-medium text-amber-800">Perlu perhatian</p>
                <p class="mt-2 text-3xl font-semibold text-amber-950">{{ $stats['attention_users'] }}</p>
                <p class="mt-1 text-xs text-amber-700">Nonaktif atau email belum diverifikasi</p>
            </a>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_300px]">
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">User terbaru</h3>
                        <p class="mt-1 text-sm text-gray-500">Akun yang terakhir terdaftar di sistem.</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">Lihat semua</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-5 py-3">Akun</th>
                                <th class="px-5 py-3">Role</th>
                                <th class="px-5 py-3">Akses</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentUsers as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="mt-1 text-gray-500">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $roleClasses[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center text-gray-500">Belum ada user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900">Ringkasan Role</h3>
                <p class="mt-1 text-sm text-gray-500">Komposisi akun yang bisa mengakses platform.</p>

                <div class="mt-5 space-y-3 text-sm">
                    <a href="{{ route('admin.users.index', ['role' => \App\Models\User::ROLE_ADMIN]) }}" class="flex items-center justify-between border-b border-gray-100 pb-3 text-gray-700 transition hover:text-indigo-700">
                        <span>Admin</span>
                        <strong>{{ $stats['total_admin'] }}</strong>
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => \App\Models\User::ROLE_PENGURUS]) }}" class="flex items-center justify-between border-b border-gray-100 pb-3 text-gray-700 transition hover:text-indigo-700">
                        <span>Pengurus</span>
                        <strong>{{ $stats['total_pengurus'] }}</strong>
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => \App\Models\User::ROLE_KETUA]) }}" class="flex items-center justify-between border-b border-gray-100 pb-3 text-gray-700 transition hover:text-indigo-700">
                        <span>Ketua Koperasi</span>
                        <strong>{{ $stats['total_ketua'] }}</strong>
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => \App\Models\User::ROLE_DPS]) }}" class="flex items-center justify-between border-b border-gray-100 pb-3 text-gray-700 transition hover:text-indigo-700">
                        <span>DPS</span>
                        <strong>{{ $stats['total_dps'] }}</strong>
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => \App\Models\User::ROLE_ANGGOTA]) }}" class="flex items-center justify-between border-b border-gray-100 pb-3 text-gray-700 transition hover:text-indigo-700">
                        <span>Anggota</span>
                        <strong>{{ $stats['total_anggota'] }}</strong>
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => \App\Models\User::ROLE_PELANGGAN]) }}" class="flex items-center justify-between text-gray-700 transition hover:text-indigo-700">
                        <span>Pelanggan</span>
                        <strong>{{ $stats['total_pelanggan'] }}</strong>
                    </a>
                </div>
            </aside>
        </section>
    </div>
</x-admin-layout>
