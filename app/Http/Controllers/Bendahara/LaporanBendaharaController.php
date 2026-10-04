<?php
namespace App\Http\Controllers\Bendahara;
use App\Http\Controllers\Controller;
use App\Models\LaporanBendahara;
use App\Models\{PembayaranAngsuran, PencairanDana, SetoranSimpanan, TransaksiPembayaran};
use App\Support\NotificationHelper;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;
class LaporanBendaharaController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->validate([
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
        ]);
        $start = $period['periode_mulai'] ?? now()->startOfMonth()->format('Y-m-d');
        $end = $period['periode_selesai'] ?? now()->format('Y-m-d');
        $report = LaporanBendahara::firstOrNew([
            'dibuat_oleh' => auth()->id(),
            'periode_mulai' => $start,
            'periode_selesai' => $end,
        ]);

        if (! $report->exists || in_array($report->status, ['draf', 'perlu_tindak_lanjut'], true)) {
            $snapshot = $this->snapshot($start, $end);
            $report->fill(array_merge($snapshot, [
                'judul' => 'Laporan Bendahara '.$this->periodLabel($start, $end),
                'ringkasan' => $this->summary($snapshot),
                'rekomendasi' => $snapshot['transaksi_menunggu'] > 0
                    ? 'Selesaikan verifikasi transaksi tertunda dan lakukan rekonsiliasi dengan rekening koperasi.'
                    : 'Pertahankan rekonsiliasi kas dan pengarsipan bukti transaksi secara berkala.',
                'status' => $report->status ?: 'draf',
            ]))->save();
        }

        return view('bendahara.laporan.index', [
            'report' => $report->fresh(['peninjau']),
            'history' => LaporanBendahara::with('peninjau')->where('dibuat_oleh', auth()->id())->where('id', '!=', $report->id)->latest()->limit(12)->get(),
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        LaporanBendahara::create(array_merge($data, $this->snapshot($data['periode_mulai'], $data['periode_selesai']), ['dibuat_oleh' => auth()->id()]));
        return back()->with('success', 'Draf laporan dan rekap otomatis berhasil dibuat.');
    }

    public function update(Request $request, LaporanBendahara $laporan): RedirectResponse
    {
        $this->authorizeOwner($laporan);
        $data = $this->validated($request);
        $laporan->update(array_merge($data, $this->snapshot($data['periode_mulai'], $data['periode_selesai'])));
        return back()->with('success', 'Laporan dan rekap keuangan berhasil diperbarui.');
    }

    public function send(LaporanBendahara $laporan): RedirectResponse
    {
        $this->authorizeOwner($laporan);
        $snapshot = $this->snapshot($laporan->periode_mulai->format('Y-m-d'), $laporan->periode_selesai->format('Y-m-d'));
        $laporan->update(array_merge($snapshot, ['ringkasan' => $this->summary($snapshot), 'status' => 'dikirim', 'dikirim_pada' => now()]));
        NotificationHelper::sendToRole('ketua', 'Laporan Keuangan Bendahara Dikirim', $laporan->judul.' telah dikirim untuk ditinjau.', ['tab' => 'laporan']);
        return back()->with('success', 'Laporan berhasil dikirim kepada Ketua Koperasi.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['judul' => 'required|string|max:150', 'periode_mulai' => 'required|date', 'periode_selesai' => 'required|date|after_or_equal:periode_mulai', 'ringkasan' => 'required|string', 'kendala' => 'nullable|string', 'rekomendasi' => 'nullable|string']);
    }
    private function authorizeOwner(LaporanBendahara $laporan): void
    {
        abort_unless($laporan->dibuat_oleh === auth()->id() && in_array($laporan->status, ['draf', 'perlu_tindak_lanjut'], true), 403);
    }
    private function snapshot(string $start, string $end): array
    {
        $range = [$start.' 00:00:00', $end.' 23:59:59'];
        $incoming = TransaksiPembayaran::whereIn('status', ['diverifikasi', 'selesai'])->where('jenis_transaksi', '!=', 'pencairan')->whereBetween('updated_at', $range)->sum('jumlah');
        $outgoing = TransaksiPembayaran::where('status', 'selesai')->where('jenis_transaksi', 'pencairan')->whereBetween('updated_at', $range)->sum('jumlah');
        return [
            'kas_masuk' => $incoming, 'kas_keluar' => $outgoing, 'saldo_bersih' => $incoming - $outgoing,
            'setoran_simpanan' => SetoranSimpanan::where('status', 'diverifikasi')->whereBetween('updated_at', $range)->sum('nominal'),
            'penerimaan_angsuran' => PembayaranAngsuran::where('status', 'diverifikasi')->whereBetween('updated_at', $range)->sum('jumlah_dibayar'),
            'pencairan_pembiayaan' => PencairanDana::where('status', 'selesai')->whereBetween('updated_at', $range)->sum('nominal_pencairan'),
            'transaksi_menunggu' => TransaksiPembayaran::where('status', 'menunggu_verifikasi')->count() + SetoranSimpanan::where('status', 'menunggu_verifikasi')->count() + PembayaranAngsuran::where('status', 'menunggu_verifikasi')->count(),
        ];
    }

    private function summary(array $data): string
    {
        $position = $data['saldo_bersih'] >= 0 ? 'surplus' : 'defisit';
        return 'Pada periode laporan, arus kas koperasi mencatat '.$position.' bersih sebesar Rp '.number_format(abs($data['saldo_bersih']), 0, ',', '.').'. Kas masuk Rp '.number_format($data['kas_masuk'], 0, ',', '.').' dan kas keluar Rp '.number_format($data['kas_keluar'], 0, ',', '.').'.';
    }

    private function periodLabel(string $start, string $end): string
    {
        return \Carbon\Carbon::parse($start)->locale('id')->translatedFormat('d M Y').' - '.\Carbon\Carbon::parse($end)->locale('id')->translatedFormat('d M Y');
    }
}
