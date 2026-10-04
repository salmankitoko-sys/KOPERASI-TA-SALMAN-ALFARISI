<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use App\Models\Pembiayaan;
use App\Models\PencairanDana;
use App\Models\RekeningKoperasi;
use App\Models\SetoranSimpanan;
use App\Models\Simpanan;
use App\Models\TransaksiPembayaran;
use App\Services\DisbursementService;
use App\Support\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function index()
    {
        $pencairan = PencairanDana::with(['pembiayaan.user'])->where('status', 'menunggu')->orderBy('created_at', 'desc')->get();
        $pencairanSiapTransfer = PencairanDana::with(['pembiayaan.user', 'rekeningKoperasi'])
            ->where('status', 'diproses')
            ->orderBy('updated_at', 'desc')
            ->get();
        $referensiPencairan = $pencairanSiapTransfer
            ->map(fn (PencairanDana $item) => 'pencairan:'.$item->id)
            ->all();
        $mutasiPencairan = TransaksiPembayaran::whereIn('referensi', $referensiPencairan)
            ->orderBy('id')
            ->get()
            ->keyBy('referensi');
        $rekeningAktif = RekeningKoperasi::where('is_aktif', true)->orderBy('nama_bank')->get();
        $pembayaran = PembayaranAngsuran::with(['pembiayaan.user', 'angsuran'])->where('status', 'menunggu_verifikasi')->orderBy('created_at', 'desc')->get();
        $setoran = SetoranSimpanan::with('user')->where('status', 'menunggu_verifikasi')->orderBy('created_at', 'desc')->get();

        return view('pengurus.transaksi.index', compact(
            'pencairan',
            'pencairanSiapTransfer',
            'mutasiPencairan',
            'rekeningAktif',
            'pembayaran',
            'setoran',
        ));
    }

    /**
     * Daftar transaksi pagar (mutasi/jurnal kas) terpusat
     */
    public function mutasiIndex(Request $request)
    {
        $query = TransaksiPembayaran::with('user', 'rekening')->orderBy('created_at', 'desc');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")->orWhere('referensi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_transaksi', $request->input('jenis'));
        }

        if ($request->filled('rekening_id')) {
            $query->where('rekening_koperasi_id', $request->input('rekening_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $mutasi = $query->paginate(50)->appends($request->query());
        $rekeningList = RekeningKoperasi::orderBy('nama_bank')->get();

        return view('pengurus.transaksi.mutasi', compact('mutasi', 'rekeningList'));
    }

    public function rekeningIndex()
    {
        $rekening = RekeningKoperasi::orderBy('nama_bank')->get();

        return view('pengurus.transaksi.rekening', compact('rekening'));
    }

    public function rekeningStore(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:255',
            'atas_nama' => 'required|string|max:255',
            'kode_rekening' => 'nullable|string|max:50',
        ]);

        RekeningKoperasi::create($request->only(['nama_bank', 'nomor_rekening', 'atas_nama', 'kode_rekening']));

        return back()->with('success', 'Rekening koperasi berhasil ditambahkan.');
    }

    public function rekeningUpdateStatus($id)
    {
        $rekening = RekeningKoperasi::findOrFail($id);
        $rekening->update(['is_aktif' => ! $rekening->is_aktif]);

        return back()->with('success', 'Status rekening '.($rekening->is_aktif ? 'diaktifkan' : 'dinonaktifkan').'.');
    }

    public function pencairanApprove(Request $request, $id)
    {
        $request->validate([
            'rekening_koperasi_id' => 'required|exists:rekening_koperasi,id',
        ]);

        $rekening = RekeningKoperasi::findOrFail($request->integer('rekening_koperasi_id'));
        if (! $rekening->is_aktif) {
            return back()->with('error', 'Rekening sumber tidak aktif. Pilih rekening koperasi lain.');
        }

        DB::transaction(function () use ($id, $rekening) {
            $item = PencairanDana::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($item->status !== 'menunggu') {
                throw ValidationException::withMessages([
                    'pencairan' => 'Permintaan pencairan ini sudah diproses.',
                ]);
            }

            $pembiayaan = Pembiayaan::whereKey($item->pembiayaan_id)->lockForUpdate()->firstOrFail();
            if ($pembiayaan->status !== 'disetujui') {
                throw ValidationException::withMessages([
                    'pencairan' => 'Pembiayaan belum siap untuk dicairkan.',
                ]);
            }

            $sudahDialokasikan = PencairanDana::where('pembiayaan_id', $pembiayaan->id)
                ->where('id', '!=', $item->id)
                ->whereIn('status', ['menunggu', 'diproses', 'selesai'])
                ->sum('nominal_pencairan');

            if (($sudahDialokasikan + $item->nominal_pencairan) > $pembiayaan->jumlah_pembiayaan) {
                throw ValidationException::withMessages([
                    'nominal_pencairan' => 'Total pencairan melebihi plafon pembiayaan.',
                ]);
            }

            $item->update([
                'status' => 'diproses',
                'rekening_koperasi_id' => $rekening->id,
                'disetujui_oleh' => auth()->id(),
                'tanggal_persetujuan' => today()->toDateString(),
            ]);
        });

        NotificationService::pencairanDisetujui(PencairanDana::findOrFail($id));

        return back()->with('success', 'Pencairan disetujui. Catat transfer setelah dana benar-benar dikirim.');
    }

    public function pencairanTolak(Request $request, $id)
    {
        $request->validate(['catatan' => 'nullable|string|max:1000']);
        $item = PencairanDana::findOrFail($id);
        if ($item->status !== 'menunggu') {
            return back()->with('info', 'Permintaan ini sudah diproses dan tidak dapat ditolak dari tahap ini.');
        }

        $item->update(['status' => 'ditolak', 'catatan' => $request->catatan]);

        NotificationService::pencairanDitolak($item);

        return back()->with('success', 'Permintaan pencairan ditolak.');
    }

    public function pencairanTransfer(Request $request, $id)
    {
        $request->validate([
            'nomor_referensi_transfer' => 'required|string|max:100',
            'tanggal_transfer' => 'required|date',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $request->file('bukti_transfer')->store('transaksi', 'public');

        DB::transaction(function () use ($id, $request, $path) {
            $item = PencairanDana::with('pembiayaan')
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->status !== 'diproses' || ! $item->rekening_koperasi_id) {
                throw ValidationException::withMessages([
                    'pencairan' => 'Pencairan harus disetujui dan memiliki rekening sumber sebelum transfer dicatat.',
                ]);
            }

            $item->update([
                'metode_pencairan' => 'manual',
                'bukti_transfer' => $path,
                'nomor_referensi_transfer' => $request->string('nomor_referensi_transfer')->trim(),
                'tanggal_transfer' => $request->date('tanggal_transfer')->toDateString(),
                'dicairkan_oleh' => auth()->id(),
            ]);

            $referensi = 'pencairan:'.$item->id;
            $dataMutasi = [
                'user_id' => $item->pembiayaan->user_id,
                'jenis_transaksi' => 'pencairan',
                'judul' => 'Pencairan '.($item->pembiayaan->kode ?? 'Pembiayaan'),
                'jumlah' => $item->nominal_pencairan,
                'metode_pembayaran' => 'transfer',
                'status' => 'menunggu_verifikasi',
                'keterangan' => 'Transfer pencairan oleh '.auth()->user()->name
                    .'; referensi bank: '.$item->nomor_referensi_transfer,
                'rekening_koperasi_id' => $item->rekening_koperasi_id,
                'bukti_transfer' => $path,
            ];

            $mutasi = TransaksiPembayaran::where('referensi', $referensi)->lockForUpdate()->first();
            if ($mutasi) {
                $mutasi->update($dataMutasi);
            } else {
                TransaksiPembayaran::create($dataMutasi + ['referensi' => $referensi]);
            }
        });

        NotificationService::pencairanDitransfer(PencairanDana::findOrFail($id));

        return back()->with('success', 'Bukti transfer dicatat dan dikirim ke Mutasi Kas untuk verifikasi.');
    }

    /**
     * Trigger disbursement via payment gateway API.
     * Only available when disbursement is enabled in config.
     */
    public function pencairanDisbursement(Request $request, $id)
    {
        $disbursementService = app(DisbursementService::class);

        if (!$disbursementService->isEnabled()) {
            return back()->with('error', 'Layanan disbursement API belum aktif. Gunakan transfer manual.');
        }

        DB::transaction(function () use ($id, $disbursementService) {
            $item = PencairanDana::with('pembiayaan')
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            // Must be in 'diproses' status (already approved)
            if ($item->status !== 'diproses') {
                throw ValidationException::withMessages([
                    'pencairan' => 'Pencairan harus disetujui terlebih dahulu sebelum pencairan via API.',
                ]);
            }

            // Must not have been disbursed already
            if ($item->disbursement_status === 'success') {
                throw ValidationException::withMessages([
                    'pencairan' => 'Pencairan ini sudah berhasil dicairkan.',
                ]);
            }

            // Prevent duplicate disbursement
            if (in_array($item->disbursement_status, ['processing', 'pending'])) {
                throw ValidationException::withMessages([
                    'pencairan' => 'Pencairan sedang diproses oleh gateway.',
                ]);
            }

            // Call disbursement API
            $result = $disbursementService->createDisbursement($item);

            // Update pencairan with disbursement data
            $item->update([
                'metode_pencairan' => 'disbursement_api',
                'disbursement_reference' => $result['reference'],
                'disbursement_status' => 'processing',
                'disbursement_at' => now(),
                'disbursement_response' => $result['response'],
                'dicairkan_oleh' => auth()->id(),
            ]);

            // Create journal entry
            $referensi = 'pencairan:' . $item->id;
            $existing = TransaksiPembayaran::where('referensi', $referensi)->first();
            $journalData = [
                'user_id' => $item->pembiayaan->user_id,
                'jenis_transaksi' => 'pencairan',
                'judul' => 'Pencairan ' . ($item->pembiayaan->kode ?? 'Pembiayaan') . ' (Disbursement API)',
                'jumlah' => $item->nominal_pencairan,
                'metode_pembayaran' => 'disbursement_api',
                'status' => 'diverifikasi',
                'keterangan' => 'Disbursement via API. Reference: ' . $result['reference'],
            ];

            if ($existing) {
                $existing->update($journalData);
            } else {
                TransaksiPembayaran::create($journalData + ['referensi' => $referensi]);
            }
        });

        NotificationService::pencairanDitransfer(PencairanDana::findOrFail($id));

        return back()->with('success', 'Disbursement dikirim ke gateway. Menunggu konfirmasi callback.');
    }

    /**
     * Aksi gabungan: Setujui + Cairkan Dana (1 langkah).
     * Jika disbursement API aktif â†’ langsung kirim ke gateway.
     * Jika tidak â†’ proses manual transfer dengan bukti.
     */
    public function pencairanCairkan(Request $request, $id)
    {
        $request->validate([
            'rekening_koperasi_id' => 'required|exists:rekening_koperasi,id',
            'metode' => 'required|in:api,manual',
            // Manual fields (required if metode=manual)
            'nomor_referensi_transfer' => 'required_if:metode,manual|nullable|string|max:100',
            'tanggal_transfer' => 'required_if:metode,manual|nullable|date',
            'bukti_transfer' => 'required_if:metode,manual|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $rekening = RekeningKoperasi::findOrFail($request->integer('rekening_koperasi_id'));
        if (! $rekening->is_aktif) {
            return back()->with('error', 'Rekening sumber tidak aktif. Pilih rekening koperasi lain.');
        }

        $metode = $request->input('metode');

        if ($metode === 'api' && ! app(DisbursementService::class)->isEnabled()) {
            return back()->withInput()->with('error', 'Layanan disbursement API belum aktif. Pilih metode Transfer Manual.');
        }

        // === STEP 1: Approve pencairan ===
        DB::transaction(function () use ($id, $rekening) {
            $item = PencairanDana::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($item->status !== 'menunggu') {
                throw ValidationException::withMessages([
                    'pencairan' => 'Permintaan pencairan ini sudah diproses.',
                ]);
            }

            $pembiayaan = Pembiayaan::whereKey($item->pembiayaan_id)->lockForUpdate()->firstOrFail();
            if ($pembiayaan->status !== 'disetujui') {
                throw ValidationException::withMessages([
                    'pencairan' => 'Pembiayaan belum siap untuk dicairkan.',
                ]);
            }

            $sudahDialokasikan = PencairanDana::where('pembiayaan_id', $pembiayaan->id)
                ->where('id', '!=', $item->id)
                ->whereIn('status', ['menunggu', 'diproses', 'selesai'])
                ->sum('nominal_pencairan');

            if (($sudahDialokasikan + $item->nominal_pencairan) > $pembiayaan->jumlah_pembiayaan) {
                throw ValidationException::withMessages([
                    'nominal_pencairan' => 'Total pencairan melebihi plafon pembiayaan.',
                ]);
            }

            $item->update([
                'status' => 'diproses',
                'rekening_koperasi_id' => $rekening->id,
                'disetujui_oleh' => auth()->id(),
                'tanggal_persetujuan' => today()->toDateString(),
            ]);
        });

        NotificationService::pencairanDisetujui(PencairanDana::findOrFail($id));

        // === STEP 2: Cairkan dana ===
        if ($metode === 'api') {
            return $this->executeDisbursementApi($id);
        } else {
            return $this->executeManualTransfer($id, $request);
        }
    }

    /**
     * Eksekusi disbursement via API gateway.
     */
    private function executeDisbursementApi(string $id)
    {
        $disbursementService = app(DisbursementService::class);

        if (!$disbursementService->isEnabled()) {
            return back()->with('error', 'Layanan disbursement API belum aktif. Gunakan transfer manual.');
        }

        DB::transaction(function () use ($id, $disbursementService) {
            $item = PencairanDana::with('pembiayaan')
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->status !== 'diproses') {
                throw ValidationException::withMessages([
                    'pencairan' => 'Pencairan tidak dalam status diproses.',
                ]);
            }

            if ($item->disbursement_status === 'success') {
                throw ValidationException::withMessages([
                    'pencairan' => 'Pencairan ini sudah berhasil dicairkan.',
                ]);
            }

            if (in_array($item->disbursement_status, ['processing', 'pending'])) {
                throw ValidationException::withMessages([
                    'pencairan' => 'Pencairan sedang diproses oleh gateway.',
                ]);
            }

            $result = $disbursementService->createDisbursement($item);

            $item->update([
                'metode_pencairan' => 'disbursement_api',
                'disbursement_reference' => $result['reference'],
                'disbursement_status' => 'processing',
                'disbursement_at' => now(),
                'disbursement_response' => $result['response'],
                'dicairkan_oleh' => auth()->id(),
            ]);

            $referensi = 'pencairan:' . $item->id;
            $existing = TransaksiPembayaran::where('referensi', $referensi)->first();
            $journalData = [
                'user_id' => $item->pembiayaan->user_id,
                'jenis_transaksi' => 'pencairan',
                'judul' => 'Pencairan ' . ($item->pembiayaan->kode ?? 'Pembiayaan') . ' (Disbursement API)',
                'jumlah' => $item->nominal_pencairan,
                'metode_pembayaran' => 'disbursement_api',
                'status' => 'diverifikasi',
                'keterangan' => 'Disbursement via API. Reference: ' . $result['reference'],
            ];

            if ($existing) {
                $existing->update($journalData);
            } else {
                TransaksiPembayaran::create($journalData + ['referensi' => $referensi]);
            }
        });

        NotificationService::pencairanDitransfer(PencairanDana::findOrFail($id));

        return back()->with('success', 'Dana dicairkan via Disbursement API. Menunggu konfirmasi callback.');
    }

    /**
     * Eksekusi manual transfer dengan bukti.
     */
    private function executeManualTransfer(string $id, Request $request)
    {
        $request->validate([
            'nomor_referensi_transfer' => 'required|string|max:100',
            'tanggal_transfer' => 'required|date',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $request->file('bukti_transfer')->store('transaksi', 'public');

        DB::transaction(function () use ($id, $request, $path) {
            $item = PencairanDana::with('pembiayaan')
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->status !== 'diproses' || ! $item->rekening_koperasi_id) {
                throw ValidationException::withMessages([
                    'pencairan' => 'Pencairan harus disetujui dan memiliki rekening sumber sebelum transfer dicatat.',
                ]);
            }

            $item->update([
                'metode_pencairan' => 'manual',
                'bukti_transfer' => $path,
                'nomor_referensi_transfer' => $request->string('nomor_referensi_transfer')->trim(),
                'tanggal_transfer' => $request->date('tanggal_transfer')->toDateString(),
                'dicairkan_oleh' => auth()->id(),
            ]);

            $referensi = 'pencairan:'.$item->id;
            $dataMutasi = [
                'user_id' => $item->pembiayaan->user_id,
                'jenis_transaksi' => 'pencairan',
                'judul' => 'Pencairan '.($item->pembiayaan->kode ?? 'Pembiayaan'),
                'jumlah' => $item->nominal_pencairan,
                'metode_pembayaran' => 'transfer',
                'status' => 'menunggu_verifikasi',
                'keterangan' => 'Transfer manual oleh '.auth()->user()->name
                    .'; referensi bank: '.$item->nomor_referensi_transfer,
                'rekening_koperasi_id' => $item->rekening_koperasi_id,
                'bukti_transfer' => $path,
            ];

            $mutasi = TransaksiPembayaran::where('referensi', $referensi)->lockForUpdate()->first();
            if ($mutasi) {
                $mutasi->update($dataMutasi);
            } else {
                TransaksiPembayaran::create($dataMutasi + ['referensi' => $referensi]);
            }
        });

        NotificationService::pencairanDitransfer(PencairanDana::findOrFail($id));

        return back()->with('success', 'Bukti transfer dicatat dan dikirim ke Mutasi Kas untuk verifikasi.');
    }

    public function angsuranApprove($id)
    {
        return redirect()
            ->route('bendahara.transaksi.mutasi.index', ['q' => 'pembayaran_angsuran:'.$id])
            ->with('info', 'Verifikasi angsuran dilakukan melalui Mutasi Kas agar jurnal tidak tercatat ganda.');
    }

    public function angsuranTolak(Request $request, $id)
    {
        return redirect()
            ->route('bendahara.transaksi.mutasi.index', ['q' => 'pembayaran_angsuran:'.$id])
            ->with('info', 'Penolakan angsuran dilakukan melalui Mutasi Kas agar status bukti dan jurnal tetap selaras.');
    }

    public function simpananApprove($id)
    {
        return redirect()
            ->route('bendahara.transaksi.mutasi.index', ['q' => 'setoran_simpanan:'.$id])
            ->with('info', 'Verifikasi setoran dilakukan melalui Mutasi Kas agar jurnal tidak tercatat ganda.');
    }

    public function simpananTolak(Request $request, $id)
    {
        return redirect()
            ->route('bendahara.transaksi.mutasi.index', ['q' => 'setoran_simpanan:'.$id])
            ->with('info', 'Penolakan setoran dilakukan melalui Mutasi Kas agar status bukti dan jurnal tetap selaras.');
    }

    /**
     * Approve atau tolak entri mutasi kas terpusat
     */
    public function mutasiApprove(Request $request, $id)
    {
        $t = TransaksiPembayaran::findOrFail($id);
        if (in_array($t->status, ['diverifikasi', 'selesai', 'ditolak'], true)) {
            return back()->with('info', 'Transaksi sudah diproses.');
        }

        if ($t->referensi && str_starts_with($t->referensi, 'pencairan:')) {
            [, $pencairanId] = explode(':', $t->referensi, 2) + [null, null];
            $pencairanId = (int) $pencairanId;
            $pencairan = PencairanDana::find($pencairanId);
            if (! $pencairan || $pencairan->status !== 'diproses' || ! $pencairan->bukti_transfer || ! $pencairan->tanggal_transfer) {
                return back()->with('error', 'Pencairan belum memiliki bukti transfer yang lengkap dan belum dapat diverifikasi.');
            }
        }

        DB::transaction(function () use ($t, $request) {
            $t = TransaksiPembayaran::whereKey($t->id)->lockForUpdate()->firstOrFail();
            $type = null;
            $refId = null;
            if ($t->referensi) {
                [$type, $refId] = explode(':', $t->referensi) + [null, null];
            }

            $updateData = ['status' => $type === 'pencairan' ? 'selesai' : 'diverifikasi'];
            if ($type === 'pencairan') {
                $p = PencairanDana::whereKey($refId)->lockForUpdate()->first();
                if (! $p || $p->status !== 'diproses' || ! $p->bukti_transfer || ! $p->tanggal_transfer) {
                    throw ValidationException::withMessages([
                        'mutasi' => 'Pencairan belum memiliki bukti transfer yang lengkap.',
                    ]);
                }
                $updateData['rekening_koperasi_id'] = $p->rekening_koperasi_id;
                $p->update([
                    'status' => 'selesai',
                    'diverifikasi_oleh' => auth()->id(),
                    'tanggal_verifikasi' => today()->toDateString(),
                ]);
                $totalDicairkan = PencairanDana::where('pembiayaan_id', $p->pembiayaan_id)
                    ->where('status', 'selesai')
                    ->sum('nominal_pencairan');
                $pembiayaan = Pembiayaan::whereKey($p->pembiayaan_id)->lockForUpdate()->first();
                if ($pembiayaan && $totalDicairkan >= $pembiayaan->jumlah_pembiayaan) {
                    $pembiayaan->update(['status' => 'berjalan']);
                }
            } elseif ($request->filled('rekening_koperasi_id')) {
                $updateData['rekening_koperasi_id'] = $request->input('rekening_koperasi_id');
            }

            $t->update($updateData);

            if ($type === 'pembayaran_angsuran') {
                $p = PembayaranAngsuran::find($refId);
                if ($p) {
                    $p->update(['status' => 'diverifikasi', 'diverifikasi_oleh' => auth()->id(), 'tanggal_verifikasi' => today()->toDateString()]);
                    // Tandai angsuran sebagai dibayar dan pastikan pokok mengurangi sisa pembiayaan.
                    $angsuranUpdate = ['status' => 'dibayar', 'tanggal_bayar' => today()->toDateString()];
                    if ((float) ($p->angsuran->pokok ?? 0) <= 0) {
                        $pokokAngsuran = $this->hitungPokokAngsuranFallback($p->pembiayaan, $p->angsuran_id);
                        $angsuranUpdate['pokok'] = $pokokAngsuran;
                        $angsuranUpdate['sisa_pokok'] = max((float) $p->pembiayaan->jumlah_pembiayaan - $this->totalPokokTerbayarSetelah($p->pembiayaan, $p->angsuran_id, $pokokAngsuran), 0);
                    }
                    $p->angsuran->update($angsuranUpdate);
                    // Notify anggota
                    NotificationService::angsuranDiverifikasi($p);
                }
            } elseif ($type === 'setoran_simpanan') {
                $s = SetoranSimpanan::find($refId);
                if ($s) {
                    $saldoSebelumnya = Simpanan::where('user_id', $s->user_id)
                        ->where('status', 'masuk')
                        ->sum('jumlah');
                    $s->update([
                        'status' => 'diverifikasi',
                        'diverifikasi_oleh' => auth()->id(),
                        'saldo_setelah' => $saldoSebelumnya + $s->nominal,
                    ]);
                    Simpanan::create([
                        'user_id' => $s->user_id,
                        'jenis' => $s->jenis_simpanan,
                        'jumlah' => $s->nominal,
                        'keterangan' => 'Setoran verifikasi via mutasi',
                        'bukti_transfer' => $s->bukti_transfer,
                        'status' => 'masuk',
                        'tanggal' => $s->tanggal_setor,
                    ]);
                    // Notify anggota
                    NotificationService::simpananDiverifikasi($s);
                }
            }
        });

        return back()->with('success', 'Transaksi mutasi diverifikasi.');
    }

    public function mutasiTolak(Request $request, $id)
    {
        $t = TransaksiPembayaran::findOrFail($id);
        if (in_array($t->status, ['ditolak', 'diverifikasi', 'selesai'], true)) {
            return back()->with('info', 'Transaksi sudah diproses.');
        }

        $referensi = $t->referensi;

        DB::transaction(function () use ($t, $request) {
            $t->update(['status' => 'ditolak', 'keterangan' => ($t->keterangan ?? '').'\nDitolak: '.($request->catatan ?? '-')]);

            if ($t->referensi) {
                [$type, $refId] = explode(':', $t->referensi) + [null, null];
                if ($type === 'pencairan') {
                    $p = PencairanDana::find($refId);
                    if ($p) {
                        $catatan = trim(($p->catatan ? $p->catatan."\n" : '').'Bukti transfer ditolak: '.($request->catatan ?? '-'));
                        $p->update([
                            'status' => $p->tanggal_transfer ? 'diproses' : 'ditolak',
                            'catatan' => $catatan,
                        ]);
                    }
                } elseif ($type === 'pembayaran_angsuran') {
                    $p = PembayaranAngsuran::find($refId);
                    if ($p) {
                        $p->update(['status' => 'ditolak', 'catatan_penolakan' => $request->catatan, 'diverifikasi_oleh' => auth()->id(), 'tanggal_verifikasi' => today()->toDateString()]);
                        // Notify anggota
                        NotificationService::angsuranDitolak($p);
                    }
                } elseif ($type === 'setoran_simpanan') {
                    $s = SetoranSimpanan::find($refId);
                    if ($s) {
                        $s->update(['status' => 'ditolak', 'catatan_penolakan' => $request->catatan, 'diverifikasi_oleh' => auth()->id()]);
                        // Notify anggota
                        NotificationService::simpananDitolak($s);
                    }
                }
            }
        });

        return back()->with('success', 'Transaksi mutasi ditolak.');
    }
    private function hitungPokokAngsuranFallback(Pembiayaan $pembiayaan, int $angsuranId): float
    {
        $tenor = max((int) $pembiayaan->tenor, 1);
        $pokokPerAngsuran = (float) $pembiayaan->jumlah_pembiayaan / $tenor;
        $pokokTerbayarSebelum = (float) $pembiayaan->angsuran()
            ->where('id', '!=', $angsuranId)
            ->where('status', 'dibayar')
            ->sum('pokok');
        $sisaPokok = max((float) $pembiayaan->jumlah_pembiayaan - $pokokTerbayarSebelum, 0);

        return min($pokokPerAngsuran, $sisaPokok);
    }

    private function totalPokokTerbayarSetelah(Pembiayaan $pembiayaan, int $angsuranId, float $pokokAngsuran): float
    {
        return (float) $pembiayaan->angsuran()
            ->where('id', '!=', $angsuranId)
            ->where('status', 'dibayar')
            ->sum('pokok') + $pokokAngsuran;
    }
}
