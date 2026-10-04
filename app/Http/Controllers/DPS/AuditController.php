<?php

namespace App\Http\Controllers\DPS;

use App\Http\Controllers\Controller;
use App\Models\Pembiayaan;
use App\Models\ValidasiAkadDps;
use App\Support\AkadSyariahChecklist;
use App\Support\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('dps.view-akad');

        $query = Pembiayaan::query()
            ->with(['user:id,name,email', 'validatorDps:id,name'])
            ->latest('tanggal_pengajuan');

        $query->when($request->filled('search'), function ($query) use ($request) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($query) use ($search) {
                $query->where('kode', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
            });
        });
        $query->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')));
        $query->when($request->filled('akad'), fn ($query) => $query->where('akad', $request->input('akad')));
        $query->when($request->filled('validasi_dps'), fn ($query) => $query->where('status_validasi_dps', $request->input('validasi_dps')));

        $pembiayaan = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $pembiayaan->items(),
            'pagination' => [
                'current_page' => $pembiayaan->currentPage(),
                'last_page' => $pembiayaan->lastPage(),
                'from' => $pembiayaan->firstItem() ?? 0,
                'to' => $pembiayaan->lastItem() ?? 0,
                'total' => $pembiayaan->total(),
                'prev_page_url' => $pembiayaan->previousPageUrl(),
                'next_page_url' => $pembiayaan->nextPageUrl(),
            ],
        ]);
    }

    public function show(int $id)
    {
        Gate::authorize('dps.view-akad');

        $pembiayaan = Pembiayaan::with([
            'user:id,name,email', 'detail', 'dokumen', 'angsuran',
            'validatorDps:id,name', 'riwayatValidasiDps.validator:id,name',
        ])->findOrFail($id);

        $validasiTerbaru = $pembiayaan->riwayatValidasiDps->first();
        $snapshotHash = AkadSyariahChecklist::hash($pembiayaan);

        return response()->json([
            'success' => true,
            'pembiayaan' => $pembiayaan,
            'riwayatAkad' => [
                'detail' => $pembiayaan->detail,
                'dokumen' => $pembiayaan->dokumen,
                'angsuran' => $pembiayaan->angsuran,
            ],
            'reviewAkad' => $this->reviewAkad($pembiayaan),
            'ringkasanReview' => $this->ringkasanReview($pembiayaan),
            'checklist' => AkadSyariahChecklist::definitions($pembiayaan->akad),
            'referensiFatwa' => AkadSyariahChecklist::references($pembiayaan->akad),
            'pemeriksaanSistem' => AkadSyariahChecklist::systemChecks($pembiayaan),
            'validasiTerbaru' => $validasiTerbaru,
            'validasiMasihBerlaku' => $validasiTerbaru
                ? hash_equals($validasiTerbaru->snapshot_hash, $snapshotHash)
                : false,
            'snapshotHash' => $snapshotHash,
            'riwayatValidasi' => $pembiayaan->riwayatValidasiDps->values(),
        ]);
    }

    public function store(Request $request, ?Pembiayaan $pembiayaan = null)
    {
        Gate::authorize('dps.view-akad');

        if ($pembiayaan?->exists) {
            $request->merge(['pembiayaan_id' => $pembiayaan->id]);
        }

        $checklist = collect($request->input('checklist', []))
            ->map(function ($item, $kode) {
                $item = is_array($item) ? $item : [];
                $status = $item['status'] ?? null;

                return [
                    'kode' => $item['kode'] ?? (is_string($kode) ? $kode : null),
                    'status' => match ($status) {
                        'sesuai' => 'ya',
                        'tidak_sesuai' => 'tidak',
                        default => $status,
                    },
                    'catatan' => $item['catatan'] ?? null,
                ];
            })
            ->values()
            ->all();
        $request->merge(['checklist' => $checklist]);

        $validator = Validator::make($request->all(), [
            'pembiayaan_id' => ['required', 'integer', 'exists:pembiayaan,id'],
            'hasil' => ['required', Rule::in(['sesuai', 'perlu_perbaikan', 'tidak_sesuai'])],
            'checklist' => ['required', 'array', 'min:1'],
            'checklist.*.kode' => ['required', 'string'],
            'checklist.*.status' => ['required', Rule::in(['ya', 'tidak', 'catatan'])],
            'checklist.*.catatan' => ['nullable', 'string', 'max:500'],
            'kesimpulan' => ['required', 'string', 'max:3000'],
            'catatan_perbaikan' => ['nullable', 'required_if:hasil,perlu_perbaikan,tidak_sesuai', 'string', 'max:2000'],
            'referensi_tambahan' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi input gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pembiayaan = Pembiayaan::with(['user', 'detail', 'dokumen', 'angsuran'])
            ->findOrFail($request->integer('pembiayaan_id'));

        $blockingFindings = AkadSyariahChecklist::blockingFindings($pembiayaan);
        if ($request->input('hasil') === 'sesuai' && $blockingFindings !== []) {
            return response()->json([
                'success' => false,
                'message' => 'Akad belum dapat dinyatakan sesuai syariah karena pemeriksaan sistem masih menemukan ketidaksesuaian.',
                'blocking_findings' => $blockingFindings,
            ], 422);
        }

        try {
            $validasi = DB::transaction(function () use ($request, $pembiayaan, $checklist) {
                $versi = ((int) ValidasiAkadDps::where('pembiayaan_id', $pembiayaan->id)->max('versi')) + 1;
                $referensi = AkadSyariahChecklist::references($pembiayaan->akad);
                if ($request->filled('referensi_tambahan')) {
                    $referensi[] = $request->input('referensi_tambahan');
                }

                $validasi = ValidasiAkadDps::create([
                    'pembiayaan_id' => $pembiayaan->id,
                    'versi' => $versi,
                    'hasil' => $request->input('hasil'),
                    'checklist' => $checklist,
                    'snapshot_data' => AkadSyariahChecklist::snapshot($pembiayaan),
                    'snapshot_hash' => AkadSyariahChecklist::hash($pembiayaan),
                    'referensi_fatwa' => $referensi,
                    'referensi_tambahan' => $request->input('referensi_tambahan'),
                    'kesimpulan' => $request->input('kesimpulan'),
                    'catatan_perbaikan' => $request->input('catatan_perbaikan'),
                    'jumlah_kriteria' => count($checklist),
                    'jumlah_sesuai' => collect($checklist)->where('status', 'ya')->count(),
                    'jumlah_tidak_sesuai' => collect($checklist)->where('status', 'tidak')->count(),
                    'divalidasi_oleh' => auth()->id(),
                    'divalidasi_pada' => now(),
                ]);

                $pembiayaan->update([
                    'status_validasi_dps' => $request->input('hasil'),
                    'catatan_validasi_dps' => $request->input('kesimpulan'),
                    'divalidasi_oleh_dps' => auth()->id(),
                    'tanggal_validasi_dps' => now(),
                ]);

                return $validasi;
            });

            NotificationService::akadDivalidasiDps($validasi, $pembiayaan);
            $pembiayaan->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Validasi DPS untuk '.$pembiayaan->kode.' berhasil disimpan.',
                'data' => $validasi,
                'validasi' => $validasi,
                'pembiayaan' => $pembiayaan,
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan validasi akad DPS.',
            ], 500);
        }
    }

    public function history(int $id)
    {
        Gate::authorize('dps.view-akad');

        $pembiayaan = Pembiayaan::findOrFail($id);
        $riwayat = ValidasiAkadDps::with('validator:id,name')
            ->where('pembiayaan_id', $pembiayaan->id)
            ->orderByDesc('versi')
            ->get();

        return response()->json(['success' => true, 'data' => $riwayat]);
    }

    public function compareSnapshots(int $id)
    {
        Gate::authorize('dps.view-akad');

        $pembiayaan = Pembiayaan::with(['detail', 'dokumen', 'angsuran'])->findOrFail($id);
        $validasi = ValidasiAkadDps::where('pembiayaan_id', $pembiayaan->id)
            ->latest('versi')
            ->first();
        $current = AkadSyariahChecklist::snapshot($pembiayaan);

        if (! $validasi) {
            return response()->json([
                'success' => true,
                'has_changes' => false,
                'diff' => [],
                'message' => 'Belum ada riwayat validasi untuk dibandingkan.',
            ]);
        }

        $diff = $this->snapshotDiff($validasi->snapshot_data ?? [], $current);

        return response()->json([
            'success' => true,
            'has_changes' => $diff !== [],
            'diff' => $diff,
            'validated_hash' => $validasi->snapshot_hash,
            'current_hash' => AkadSyariahChecklist::hash($pembiayaan),
        ]);
    }

    private function snapshotDiff(array $before, array $after, string $prefix = ''): array
    {
        $diff = [];
        foreach (array_unique(array_merge(array_keys($before), array_keys($after))) as $key) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;
            $old = $before[$key] ?? null;
            $new = $after[$key] ?? null;
            if (is_array($old) && is_array($new)) {
                $diff = array_merge($diff, $this->snapshotDiff($old, $new, $path));
            } elseif ($old !== $new) {
                $diff[] = ['field' => $path, 'before' => $old, 'after' => $new];
            }
        }
        return $diff;
    }

    private function reviewAkad(Pembiayaan $pembiayaan): array
    {
        return [
            ['label' => 'Kode', 'value' => $pembiayaan->kode, 'type' => 'text'],
            ['label' => 'Akad', 'value' => $pembiayaan->akad, 'type' => 'label'],
            ['label' => 'Objek', 'value' => $pembiayaan->objek_pembiayaan, 'type' => 'text'],
            ['label' => 'Jumlah Pembiayaan', 'value' => $pembiayaan->jumlah_pembiayaan, 'type' => 'money'],
            ['label' => 'Tenor', 'value' => $pembiayaan->tenor, 'type' => 'number'],
            ['label' => 'Status', 'value' => $pembiayaan->status, 'type' => 'status'],
        ];
    }

    private function ringkasanReview(Pembiayaan $pembiayaan): array
    {
        return [
            ['label' => 'Anggota', 'value' => $pembiayaan->user?->name ?? '-', 'type' => 'text'],
            ['label' => 'Dokumen', 'value' => $pembiayaan->dokumen->count(), 'type' => 'number'],
            ['label' => 'Jadwal Angsuran', 'value' => $pembiayaan->angsuran->count(), 'type' => 'number'],
            ['label' => 'Validasi DPS', 'value' => $pembiayaan->status_validasi_dps, 'type' => 'validasi'],
        ];
    }
}