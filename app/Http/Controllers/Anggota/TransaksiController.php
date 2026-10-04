<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use App\Models\Pembiayaan;
use App\Models\PencairanDana;
use App\Models\RekeningKoperasi;
use App\Models\SetoranSimpanan;
use App\Models\TransaksiPembayaran;
use Illuminate\Http\Request;
use App\Support\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function dashboard()
    {
        $pembiayaanAktif = Pembiayaan::where('user_id', auth()->id())
            ->whereIn('status', ['disetujui', 'berjalan'])
            ->with('angsuran')
            ->orderBy('created_at', 'desc')
            ->get();

        $menungguVerifikasi = collect([]);
        $menungguVerifikasi = $menungguVerifikasi->merge(
            PembayaranAngsuran::whereHas('pembiayaan', fn ($q) => $q->where('user_id', auth()->id()))
                ->where('status', 'menunggu_verifikasi')
                ->with('angsuran')
                ->get()
        )->merge(
            SetoranSimpanan::where('user_id', auth()->id())->where('status', 'menunggu_verifikasi')->get()
        )->merge(
            PencairanDana::whereHas('pembiayaan', fn ($query) => $query->where('user_id', auth()->id()))
                ->whereIn('status', ['menunggu', 'diproses'])
                ->with('pembiayaan')
                ->get()
        );

        return view('anggota.transaksi.dashboard', compact('pembiayaanAktif', 'menungguVerifikasi'));
    }

    public function pencairanCreate()
    {
        $pembiayaan = Pembiayaan::where('user_id', auth()->id())
            ->where('status', 'disetujui')
            ->get();

        return view('anggota.transaksi.pencairan', compact('pembiayaan'));
    }

    public function pencairanStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pembiayaan_id' => 'required|exists:pembiayaan,id',
            'nominal_pencairan' => 'required|numeric|min:1',
            'bank_tujuan' => 'required|string|max:255',
            'no_rekening_tujuan' => 'required|string|max:255',
            'nama_pemilik_rekening' => 'required|string|max:255',
            'tanggal_pencairan' => 'required|date',
            'catatan' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request) {
            $pembiayaan = Pembiayaan::whereKey($request->integer('pembiayaan_id'))
                ->lockForUpdate()
                ->firstOrFail();

            if ($pembiayaan->user_id !== auth()->id()) {
                abort(403);
            }

            if ($pembiayaan->status !== 'disetujui') {
                throw ValidationException::withMessages([
                    'pembiayaan_id' => 'Pencairan hanya dapat diajukan untuk pembiayaan yang sudah disetujui.',
                ]);
            }

            $sisaPlafon = $pembiayaan->jumlah_pembiayaan - PencairanDana::where('pembiayaan_id', $pembiayaan->id)
                ->whereIn('status', ['menunggu', 'diproses', 'selesai'])
                ->sum('nominal_pencairan');

            if ($request->float('nominal_pencairan') > $sisaPlafon) {
                throw ValidationException::withMessages([
                    'nominal_pencairan' => 'Nominal melebihi sisa plafon pembiayaan yang dapat dicairkan.',
                ]);
            }

            PencairanDana::create([
                'pembiayaan_id' => $pembiayaan->id,
                'nominal_pencairan' => $request->nominal_pencairan,
                'bank_tujuan' => $request->bank_tujuan,
                'no_rekening_tujuan' => $request->no_rekening_tujuan,
                'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
                'tanggal_pencairan' => $request->tanggal_pencairan,
                'status' => 'menunggu',
                'catatan' => $request->catatan,
            ]);
        });

        NotificationService::pencairanDiajukan(PencairanDana::latest()->where('pembiayaan_id', $request->pembiayaan_id)->first());

        return redirect()->route('anggota.transaksi.dashboard')->with('success', 'Permintaan pencairan dikirim. Bendahara akan memeriksa rekening tujuan dan memproses pencairannya.');
    }

    public function angsuranCreate(Request $request)
    {
        if (! $request->boolean('manual') && $request->filled('angsuran_id')) {
            return redirect()->route('anggota.angsuran.bayar', [
                'angsuran' => $request->integer('angsuran_id'),
            ]);
        }

        $pembiayaanList = Pembiayaan::where('user_id', auth()->id())
            ->where('status', 'berjalan')
            ->with(['angsuran' => fn ($q) => $q->where('status', 'belum_bayar')->orderBy('bulan_ke')])
            ->get();

        $rekeningKoperasi = RekeningKoperasi::where('is_aktif', true)->get();
        $angsuranByPembiayaan = $pembiayaanList->mapWithKeys(function (Pembiayaan $pembiayaan) {
            return [
                $pembiayaan->id => $pembiayaan->angsuran->map(function (Angsuran $angsuran) {
                    return [
                        'id' => $angsuran->id,
                        'label' => 'Angsuran ke-'.$angsuran->bulan_ke,
                        'nominal' => (float) $angsuran->jumlah_bayar,
                        'jatuh_tempo' => $angsuran->jatuh_tempo,
                    ];
                })->values(),
            ];
        });
        $selectedPembiayaanId = $request->integer('pembiayaan_id');
        $selectedAngsuranId = $request->integer('angsuran_id');
        $selectedPembiayaan = $pembiayaanList->firstWhere('id', $selectedPembiayaanId);

        if (! $selectedPembiayaan) {
            $selectedPembiayaanId = null;
            $selectedAngsuranId = null;
        } elseif (! $selectedPembiayaan->angsuran->contains('id', $selectedAngsuranId)) {
            $selectedAngsuranId = null;
        }

        return view('anggota.transaksi.angsuran', compact(
            'pembiayaanList',
            'rekeningKoperasi',
            'angsuranByPembiayaan',
            'selectedPembiayaanId',
            'selectedAngsuranId',
        ));
    }

    public function angsuranStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pembiayaan_id' => 'required|exists:pembiayaan,id',
            'angsuran_id' => 'required|exists:angsuran,id',
            'tanggal_bayar' => 'required|date',
            'rekening_koperasi' => 'nullable|exists:rekening_koperasi,id',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $pembiayaan = Pembiayaan::findOrFail($request->pembiayaan_id);
        if ($pembiayaan->user_id !== auth()->id()) {
            abort(403);
        }

        if ($pembiayaan->status !== 'berjalan') {
            return back()->withErrors([
                'pembiayaan_id' => 'Pembayaran angsuran tersedia setelah pencairan pembiayaan selesai diverifikasi.',
            ])->withInput();
        }

        $angsuran = Angsuran::findOrFail($request->angsuran_id);
        if ($angsuran->pembiayaan_id !== $pembiayaan->id) {
            abort(403);
        }

        if ($angsuran->status !== 'belum_bayar') {
            return back()->withErrors(['angsuran_id' => 'Angsuran ini sudah diproses.'])->withInput();
        }

        if (PembayaranAngsuran::where('angsuran_id', $angsuran->id)->where('status', 'menunggu_verifikasi')->exists()) {
            return back()->withErrors(['angsuran_id' => 'Bukti pembayaran untuk angsuran ini sedang diverifikasi.'])->withInput();
        }

        if ($request->filled('rekening_koperasi') && ! RekeningKoperasi::whereKey($request->integer('rekening_koperasi'))->where('is_aktif', true)->exists()) {
            return back()->withErrors(['rekening_koperasi' => 'Pilih rekening koperasi yang masih aktif.'])->withInput();
        }

        $path = null;
        if ($request->hasFile('bukti_transfer')) {
            $path = $request->file('bukti_transfer')->store('transaksi', 'public');
        }

        $pembayaran = PembayaranAngsuran::create([
            'pembiayaan_id' => $pembiayaan->id,
            'angsuran_id' => $angsuran->id,
            'jumlah_dibayar' => $angsuran->jumlah_bayar,
            'tanggal_bayar' => $request->tanggal_bayar,
            'bukti_transfer' => $path,
            'status' => 'menunggu_verifikasi',
            'catatan' => $request->catatan,
        ]);

        // Catat entri transaksi untuk antrian verifikasi kas
        TransaksiPembayaran::create([
            'user_id' => auth()->id(),
            'jenis_transaksi' => 'angsuran',
            'judul' => 'Pembayaran angsuran '.($pembiayaan->kode ?? '').' - Angsuran ke-'.$angsuran->bulan_ke,
            'jumlah' => $pembayaran->jumlah_dibayar,
            'metode_pembayaran' => 'transfer',
            'status' => 'menunggu_verifikasi',
            'referensi' => 'pembayaran_angsuran:'.$pembayaran->id,
            'keterangan' => 'Bukti transfer anggota',
            'rekening_koperasi_id' => $request->input('rekening_koperasi'),
        ]);

        NotificationService::angsuranDibayar($pembayaran);

        return redirect()->route('anggota.transaksi.dashboard')->with('success', 'Pembayaran angsuran berhasil dikirim untuk verifikasi.');
    }
    public function simpananCreate()
    {
        return view('anggota.transaksi.simpanan');
    }

    public function simpananStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_simpanan' => 'required|in:pokok,wajib,sukarela',
            'nominal' => 'required|numeric|min:1000',
            'tanggal_setor' => 'required|date|before_or_equal:today',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'jenis_simpanan.required' => 'Pilih jenis simpanan.',
            'nominal.required' => 'Masukkan jumlah setoran.',
            'nominal.min' => 'Jumlah setoran minimal Rp 1.000.',
            'tanggal_setor.required' => 'Pilih tanggal transfer.',
            'tanggal_setor.before_or_equal' => 'Tanggal transfer tidak boleh melewati hari ini.',
            'bukti_transfer.required' => 'Bukti transfer wajib diunggah.',
            'bukti_transfer.mimes' => 'Bukti transfer harus berupa JPG, JPEG, PNG, atau PDF.',
            'bukti_transfer.max' => 'Ukuran bukti transfer maksimal 2 MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('anggota.dashboard', ['tab' => 'simpanan'])
                ->withErrors($validator, 'simpanan')
                ->withInput();
        }

        $path = null;
        if ($request->hasFile('bukti_transfer')) {
            $path = $request->file('bukti_transfer')->store('transaksi', 'public');
        }

        $setoran = SetoranSimpanan::create([
            'user_id' => auth()->id(),
            'jenis_simpanan' => $request->jenis_simpanan,
            'nominal' => $request->nominal,
            'bukti_transfer' => $path,
            'tanggal_setor' => $request->tanggal_setor,
            'status' => 'menunggu_verifikasi',
        ]);

        // Catat transaksi untuk antrian kas
        TransaksiPembayaran::create([
            'user_id' => auth()->id(),
            'jenis_transaksi' => 'setoran_simpanan',
            'judul' => 'Setoran '.ucfirst($setoran->jenis_simpanan),
            'jumlah' => $setoran->nominal,
            'metode_pembayaran' => 'transfer',
            'status' => 'menunggu_verifikasi',
            'referensi' => 'setoran_simpanan:'.$setoran->id,
            'keterangan' => 'Setoran simpanan oleh anggota',
            'bukti_transfer' => $path,
            'rekening_koperasi_id' => $request->input('rekening_koperasi'),
        ]);

        NotificationService::simpananDisetor($setoran);

        return redirect()->route('anggota.dashboard', ['tab' => 'simpanan'])
            ->with('success', 'Setoran simpanan dan bukti transfer berhasil dikirim untuk verifikasi.');
    }
}
