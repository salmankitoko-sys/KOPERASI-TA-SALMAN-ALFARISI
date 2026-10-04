<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\NotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserController extends Controller
{
    public function create(): View
    {
        return view('admin.users.create', ['roles' => $this->roleOptions()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys($this->roleOptions()))],
            'is_active' => ['required', 'boolean'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            ...$data,
            'email_verified_at' => now(),
            'status' => $data['role'] === User::ROLE_ANGGOTA ? 'Calon' : 'Aktif',
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    /**
     * Display all user accounts for administrator monitoring.
     */
    public function index(Request $request): View
    {
        $users = $this->userQuery($request)
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $this->roleOptions(),
            'statusOptions' => $this->statusOptions(),
            'filters' => $request->only(['search', 'role', 'status']),
            'summary' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
            ],
        ]);
    }

    /**
     * Show a complete user-account snapshot.
     */
    public function show(User $user): View
    {
        $activity = [
            'simpanan_count' => DB::table('simpanan')->where('user_id', $user->id)->count(),
            'simpanan_total' => (float) DB::table('simpanan')->where('user_id', $user->id)->sum('jumlah'),
            'pembiayaan_count' => DB::table('pembiayaan')->where('user_id', $user->id)->count(),
            'pembiayaan_total' => (float) DB::table('pembiayaan')->where('user_id', $user->id)->sum('jumlah_pembiayaan'),
            'pesanan_count' => DB::table('pesanan')->where('pembeli_id', $user->id)->count(),
            'pesanan_total' => (float) DB::table('pesanan')->where('pembeli_id', $user->id)->sum('total'),
            'toko_count' => DB::table('tokos')->where('user_id', $user->id)->count(),
            'produk_count' => DB::table('produk')
                ->join('tokos', 'produk.toko_id', '=', 'tokos.id')
                ->where('tokos.user_id', $user->id)
                ->count(),
        ];

        $recentOrders = DB::table('pesanan')
            ->where('pembeli_id', $user->id)
            ->latest('id')
            ->limit(5)
            ->get(['nomor_pesanan', 'status', 'status_pembayaran', 'total', 'created_at']);

        $recentPembiayaan = DB::table('pembiayaan')
            ->where('user_id', $user->id)
            ->latest('id')
            ->limit(5)
            ->get(['kode', 'akad', 'tujuan_pembiayaan', 'jumlah_pembiayaan', 'status', 'created_at']);

        return view('admin.users.show', [
            'user' => $user,
            'roles' => $this->roleOptions(),
            'activity' => $activity,
            'recentOrders' => $recentOrders,
            'recentPembiayaan' => $recentPembiayaan,
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => $this->roleOptions(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(array_keys($this->roleOptions()))],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        if ($user->is(auth()->user())) {
            $data['role'] = User::ROLE_ADMIN;
            $data['is_active'] = true;
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }


    /**
     * Export the filtered user-account data as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        return response()->streamDownload(function () use ($request): void {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, [
                'Nama',
                'Email',
                'Peran',
                'Status Akses',
                'Status',
                'No HP',
                'Email Terverifikasi',
                'Login Terakhir',
                'Dibuat Pada',
            ]);

            $roles = $this->roleOptions();

            $this->userQuery($request)
                ->orderBy('name')
                ->cursor()
                ->each(function (User $user) use ($stream, $roles): void {
                    fputcsv($stream, [
                        $user->name,
                        $user->email,
                        $roles[$user->role] ?? ucfirst($user->role),
                        $user->is_active ? 'Aktif' : 'Nonaktif',
                        $user->status ?? '-',
                        $user->no_hp ?? '-',
                        $user->email_verified_at?->format('Y-m-d H:i') ?? 'Belum',
                        $user->last_login_at?->format('Y-m-d H:i') ?? 'Belum pernah',
                        $user->created_at?->format('Y-m-d H:i'),
                    ]);
                });

            fclose($stream);
        }, 'akses-user-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Remove a user account except the active administrator account.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ((int) $user->id === (int) auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Akun admin yang sedang digunakan tidak dapat dihapus.');
        }

        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->delete();

        NotificationService::userDihapus($user);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil dihapus.');
    }

    /**
     * @return array<string, string>
     */
    private function roleOptions(): array
    {
        return [
            User::ROLE_ADMIN => 'Admin',
            User::ROLE_KETUA => 'Ketua Koperasi',
            User::ROLE_PENGURUS => 'Pengurus',
            User::ROLE_BENDAHARA => 'Bendahara',
            User::ROLE_DPS => 'Dewan Pengawas Syariah',
            User::ROLE_ANGGOTA => 'Anggota',
            User::ROLE_PELANGGAN => 'Pelanggan Marketplace',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function statusOptions(): array
    {
        return [
            'active' => 'Aktif',
            'inactive' => 'Nonaktif',
        ];
    }

    private function userQuery(Request $request): Builder
    {
        $search = trim((string) $request->input('search'));
        $role = (string) $request->input('role');
        $status = (string) $request->input('status');

        return User::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                });
            })
            ->when(array_key_exists($role, $this->roleOptions()), function (Builder $query) use ($role): void {
                $query->where('role', $role);
            })
            ->when(array_key_exists($status, $this->statusOptions()), function (Builder $query) use ($status): void {
                $query->where('is_active', $status === 'active');
            });
    }
}
