<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengurus\StoreAnggotaRequest;
use App\Models\User;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnggotaController extends Controller
{
    /**
     * Display a listing of anggota with simpanan pokok.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['Aktif', 'Calon', 'Non-Aktif'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort' => ['nullable', Rule::in(['terbaru', 'terlama', 'nama'])],
        ]);

        $query = User::where('role', 'anggota')
            ->withSum(['simpanan as simpanan_pokok' => function ($q) {
                $q->where('jenis', 'pokok')->where('status', 'masuk');
            }], 'jumlah');

        if ($request->filled('search')) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('pekerjaan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $validated['status']);
        }

        match ($validated['sort'] ?? 'terbaru') {
            'terlama' => $query->orderBy('created_at'),
            'nama' => $query->orderBy('name'),
            default => $query->orderByDesc('created_at'),
        };

        $perPage = (int) ($validated['per_page'] ?? 50);
        $anggotaList = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'success'      => true,
            'data'         => $anggotaList->getCollection()
                ->map(fn (User $user) => $this->anggotaPayload($user))
                ->values(),
            'pagination'   => [
                'current_page' => $anggotaList->currentPage(),
                'last_page'    => $anggotaList->lastPage(),
                'per_page'     => $anggotaList->perPage(),
                'total'        => $anggotaList->total(),
            ],
            'stats' => [
                'total_anggota'   => User::where('role', 'anggota')->count(),
                'anggota_aktif'   => User::where('role', 'anggota')->where('status', 'Aktif')->count(),
                'anggota_calon'   => User::where('role', 'anggota')->where('status', 'Calon')->count(),
                'anggota_nonaktif'=> User::where('role', 'anggota')->where('status', 'Non-Aktif')->count(),
                'total_pokok'     => Simpanan::where('jenis', 'pokok')->where('status', 'masuk')->sum('jumlah'),
            ],
        ]);
    }

    public function create(): View
    {
        return view('pengurus.anggota.create');
    }

    /**
     * Store a newly created anggota in storage.
     */
    public function store(StoreAnggotaRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Create user
            $user = User::create([
                'name'               => $data['name'],
                'email'              => $data['email'],
                'no_hp'              => $data['no_hp'] ?? null,
                'password'           => Hash::make($data['password'] ?? 'password'),
                'role'               => 'anggota',
                'pekerjaan'          => $data['pekerjaan'] ?? null,
                'penghasilan'        => $data['penghasilan'] ?? null,
                'status'             => $data['status'] ?? 'Aktif',
                'tgl_gabung'         => $data['tgl_gabung'] ?? now()->toDateString(),
                'email_verified_at'  => $data['status'] === 'Aktif' ? now() : null,
            ]);

            // Create simpanan pokok if provided
            if (!empty($data['pokok']) && $data['pokok'] > 0) {
                Simpanan::create([
                    'user_id'    => $user->id,
                    'jenis'      => 'pokok',
                    'jumlah'     => $data['pokok'],
                    'keterangan' => 'Simpanan pokok saat pendaftaran',
                    'status'     => 'masuk',
                    'tanggal'    => $data['tgl_gabung'] ?? now()->toDateString(),
                ]);
            }

            DB::commit();

            if (! $request->expectsJson()) {
                return redirect()
                    ->route('pengurus.dashboard', ['tab' => 'anggota'])
                    ->with('success', 'Anggota ' . $user->name . ' berhasil ditambahkan.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Anggota ' . $user->name . ' berhasil ditambahkan.',
                'data'    => $this->anggotaPayload($user->fresh(['simpanan'])),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            if (! $request->expectsJson()) {
                return back()
                    ->withInput()
                    ->withErrors(['anggota' => 'Gagal menambahkan anggota: ' . $e->getMessage()]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan anggota: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified anggota.
     */
    public function show($id)
    {
        $user = User::where('role', 'anggota')
            ->withSum(['simpanan as simpanan_pokok' => function ($q) {
                $q->where('jenis', 'pokok')->where('status', 'masuk');
            }], 'jumlah')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->anggotaPayload($user),
        ]);
    }

    public function edit($id): View
    {
        $anggota = User::where('role', 'anggota')
            ->withSum(['simpanan as simpanan_pokok' => function ($q) {
                $q->where('jenis', 'pokok')->where('status', 'masuk');
            }], 'jumlah')
            ->findOrFail($id);

        return view('pengurus.anggota.edit', compact('anggota'));
    }

    /**
     * Update the specified anggota in storage.
     */
    public function update(StoreAnggotaRequest $request, $id)
    {
        $user = User::where('role', 'anggota')->findOrFail($id);

        DB::beginTransaction();
        try {
            $data = $request->validated();

            $updateData = [
                'name'               => $data['name'],
                'email'              => $data['email'],
                'no_hp'              => $data['no_hp'] ?? $user->no_hp,
                'pekerjaan'          => $data['pekerjaan'] ?? $user->pekerjaan,
                'penghasilan'        => $data['penghasilan'] ?? $user->penghasilan,
                'status'             => $data['status'] ?? $user->status,
                'tgl_gabung'         => $data['tgl_gabung'] ?? $user->tgl_gabung,
                'email_verified_at'  => $data['status'] === 'Aktif'
                    ? ($user->email_verified_at ?? now())
                    : null,
            ];

            // Update password only if provided
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            if (array_key_exists('pokok', $data) && $data['pokok'] !== null) {
                $existingPokok = Simpanan::where('user_id', $user->id)
                    ->where('jenis', 'pokok')
                    ->where('status', 'masuk')
                    ->first();

                if ($existingPokok) {
                    $existingPokok->update(['jumlah' => $data['pokok']]);
                } else {
                    Simpanan::create([
                        'user_id'    => $user->id,
                        'jenis'      => 'pokok',
                        'jumlah'     => $data['pokok'],
                        'keterangan' => 'Simpanan pokok',
                        'status'     => 'masuk',
                        'tanggal'    => $data['tgl_gabung'] ?? now()->toDateString(),
                    ]);
                }
            }

            DB::commit();

            if (! $request->expectsJson()) {
                return redirect()
                    ->route('pengurus.dashboard', ['tab' => 'anggota'])
                    ->with('success', 'Anggota ' . $user->name . ' berhasil diperbarui.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Anggota ' . $user->name . ' berhasil diperbarui.',
                'data'    => $this->anggotaPayload($user->fresh()),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            if (! $request->expectsJson()) {
                return back()
                    ->withInput()
                    ->withErrors(['anggota' => 'Gagal memperbarui anggota: ' . $e->getMessage()]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui anggota: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified anggota from storage.
     */
    public function destroy($id)
    {
        $user = User::where('role', 'anggota')->findOrFail($id);

        try {
            $name = $user->name;
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Anggota ' . $name . ' berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus anggota: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function anggotaPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'nomor_anggota' => 'AGR-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
            'name' => $user->name,
            'email' => $user->email,
            'no_hp' => $user->no_hp,
            'pekerjaan' => $user->pekerjaan,
            'penghasilan' => (float) ($user->penghasilan ?? 0),
            'status' => $user->status ?? 'Calon',
            'tgl_gabung' => $user->tgl_gabung?->format('Y-m-d'),
            'tgl_gabung_label' => $user->tgl_gabung?->format('d/m/Y') ?? '-',
            'created_at' => $user->created_at?->format('Y-m-d'),
            'created_at_label' => $user->created_at?->format('d/m/Y') ?? '-',
            'simpanan_pokok' => (float) ($user->simpanan_pokok ?? $user->simpanan()->where('jenis', 'pokok')->where('status', 'masuk')->sum('jumlah')),
        ];
    }
}
