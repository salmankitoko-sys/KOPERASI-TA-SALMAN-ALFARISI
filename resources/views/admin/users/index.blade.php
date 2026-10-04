<x-admin-layout title="Manajemen User">
    @php
        $roleClasses = [
            \App\Models\User::ROLE_ADMIN => 'bg-slate-900 text-white',
            \App\Models\User::ROLE_PENGURUS => 'bg-indigo-100 text-indigo-800',
            \App\Models\User::ROLE_DPS => 'bg-emerald-100 text-emerald-800',
            \App\Models\User::ROLE_ANGGOTA => 'bg-sky-100 text-sky-800',
            \App\Models\User::ROLE_PELANGGAN => 'bg-amber-100 text-amber-800',
        ];
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Manajemen User</h2>
                    <p class="mt-1 text-sm text-gray-500">Tambah, lihat, edit, dan hapus akun pengguna sistem.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">Tambah User</a>
                    <a href="{{ route('admin.users.export', request()->only(['search', 'role', 'status'])) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Ekspor CSV</a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm transition hover:border-indigo-300">
                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500">Total user</span>
                    <span class="mt-1 block text-2xl font-semibold text-gray-900">{{ $summary['total'] }}</span>
                </a>
                <a href="{{ route('admin.users.index', ['status' => 'active']) }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 transition hover:border-emerald-300">
                    <span class="text-xs font-medium uppercase tracking-wide text-emerald-700">Akses aktif</span>
                    <span class="mt-1 block text-2xl font-semibold text-emerald-950">{{ $summary['active'] }}</span>
                </a>
                <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}" class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 transition hover:border-rose-300">
                    <span class="text-xs font-medium uppercase tracking-wide text-rose-700">Akses nonaktif</span>
                    <span class="mt-1 block text-2xl font-semibold text-rose-950">{{ $summary['inactive'] }}</span>
                </a>
            </div>

            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-[minmax(0,1fr)_220px_160px_auto]">
                <div>
                    <label for="search" class="sr-only">Cari user</label>
                    <input id="search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama, email, atau nomor HP" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="role" class="sr-only">Filter role</label>
                    <select id="role" name="role" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua role</option>
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['role'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="sr-only">Filter status akses</label>
                    <select id="status" name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua akses</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">Terapkan</button>
                    @if (array_filter($filters))
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Reset</a>
                    @endif
                </div>
            </form>

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[980px] text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-5 py-3">User</th>
                                <th class="px-5 py-3">Role</th>
                                <th class="px-5 py-3">Status akses</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Login terakhir</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="mt-1 text-gray-500">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $roleClasses[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $roles[$user->role] ?? ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">{{ $user->status ?? '-' }}</td>
                                    <td class="px-5 py-4 text-gray-600">
                                        {{ $user->last_login_at?->locale('id')->diffForHumans() ?? 'Belum pernah' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-indigo-600 transition hover:text-indigo-800">Detail</a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="font-medium text-amber-600 transition hover:text-amber-800">Edit</a>
                                            @if ($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $user->name }}? Data terkait user ini juga dapat ikut terhapus.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-medium text-rose-600 transition hover:text-rose-800">Hapus</button>
                                                </form>
                                            @else
                                                <span class="text-xs font-medium text-gray-400">Akun aktif</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-gray-500">Tidak ada user yang sesuai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="border-t border-gray-200 px-5 py-4">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
