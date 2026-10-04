<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\LaporanPengurus;
use App\Models\Pembiayaan;
use App\Models\Simpanan;
use App\Models\User;
use App\Support\NotificationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanOtomatisController extends Controller
{
    public function index(Request $request): View
    {
        return view('pengurus.laporan-otomatis.index', ['report' => $this->build($request)]);
    }

    public function export(Request $request): StreamedResponse
    {
        $report = $this->build($request);

        return response()->streamDownload(function () use ($report): void {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, ['Laporan Operasional Koperasi']);
            fputcsv($stream, ['Periode', $report['from'].' s.d. '.$report['to']]);
            foreach ($report['sections'] as $section) {
                fputcsv($stream, []);
                fputcsv($stream, [$section['title']]);
                foreach ($section['summary'] as $label => $value) {
                    fputcsv($stream, [$label, $value]);
                }
                fputcsv($stream, $section['headers']);
                foreach ($section['rows'] as $row) {
                    fputcsv($stream, $row);
                }
            }
            fclose($stream);
        }, 'laporan-operasional-'.$report['from'].'-'.$report['to'].'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function submit(Request $request): RedirectResponse
    {
        $report = $this->build($request);
        $input = $request->validate([
            'kendala' => 'nullable|string|max:5000',
            'tindak_lanjut' => 'nullable|string|max:5000',
        ]);
        $capaian = collect($report['sections'])
            ->map(fn ($section) => $section['title'].PHP_EOL.collect($section['summary'])->map(fn ($value, $label) => $label.': '.(is_numeric($value) ? number_format((float) $value, 0, ',', '.') : $value))->implode(PHP_EOL))
            ->implode("\n");
        $laporan = LaporanPengurus::create([
            'judul' => $report['title'].' · '.$report['from'].' s.d. '.$report['to'],
            'periode_mulai' => $report['from'],
            'periode_selesai' => $report['to'],
            'ringkasan' => 'Laporan ini dibuat otomatis dari data operasional sistem untuk kategori '.$report['title'].'.',
            'capaian' => $capaian,
            'kendala' => $input['kendala'] ?? null,
            'tindak_lanjut' => $input['tindak_lanjut'] ?? null,
            'status' => 'dikirim',
            'dibuat_oleh' => auth()->id(),
            'dikirim_pada' => now(),
        ]);
        NotificationHelper::sendToRole('ketua', 'Laporan Otomatis dari Pengurus', "{$laporan->judul} telah dikirim untuk ditinjau.", ['laporan_pengurus_id' => $laporan->id, 'tab' => 'laporan', 'pdf_url' => route('laporan-pengurus.pdf', $laporan)]);

        return redirect()->route('pengurus.laporan.index')->with('success', 'Laporan otomatis berhasil dikirim kepada Ketua Koperasi.');
    }

    private function build(Request $request): array
    {
        $from = $request->date('from')?->toDateString() ?? now()->startOfMonth()->toDateString();
        $to = $request->date('to')?->toDateString() ?? now()->toDateString();
        abort_if($from > $to, 422, 'Tanggal awal harus sebelum tanggal akhir.');

        return [
            'title' => 'Laporan Operasional Koperasi',
            'from' => $from,
            'to' => $to,
            'sections' => [
                array_merge(['title' => 'Laporan Anggota'], $this->anggota($from, $to)),
                array_merge(['title' => 'Laporan Simpanan'], $this->simpanan($from, $to)),
                array_merge(['title' => 'Laporan Pembiayaan'], $this->pembiayaan($from, $to)),
            ],
        ];
    }

    private function anggota(string $from, string $to): array
    {
        $rows = User::where('role', User::ROLE_ANGGOTA)->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])->orderBy('name')->get();

        return ['summary' => ['Anggota baru' => $rows->count(), 'Aktif' => $rows->where('is_active', true)->count(), 'Calon' => $rows->where('status', 'Calon')->count()], 'headers' => ['Nama', 'Email', 'No. HP', 'Status', 'Tanggal Bergabung'], 'rows' => $rows->map(fn ($r) => [$r->name, $r->email, $r->no_hp, $r->status, $r->tgl_gabung?->format('Y-m-d') ?? $r->created_at->format('Y-m-d')])->all()];
    }

    private function simpanan(string $from, string $to): array
    {
        $rows = Simpanan::with('user')->whereBetween('tanggal', [$from, $to])->orderByDesc('tanggal')->get();

        return ['summary' => ['Total transaksi' => $rows->count(), 'Total simpanan masuk' => (float) $rows->where('status', 'masuk')->sum('jumlah'), 'Menunggu' => $rows->where('status', 'pending')->count()], 'headers' => ['Tanggal', 'Anggota', 'Jenis', 'Jumlah', 'Status'], 'rows' => $rows->map(fn ($r) => [$r->tanggal->format('Y-m-d'), $r->user?->name, $r->jenis, $r->jumlah, $r->status])->all()];
    }

    private function pembiayaan(string $from, string $to): array
    {
        $rows = Pembiayaan::with('user')->whereBetween('tanggal_pengajuan', [$from, $to])->latest('tanggal_pengajuan')->get();

        return ['summary' => ['Total pengajuan' => $rows->count(), 'Nilai pengajuan' => (float) $rows->sum('jumlah_pembiayaan'), 'Disetujui/berjalan' => $rows->whereIn('status', ['disetujui', 'berjalan'])->count()], 'headers' => ['Kode', 'Anggota', 'Akad', 'Nilai', 'Tenor', 'Status', 'Tanggal'], 'rows' => $rows->map(fn ($r) => [$r->kode, $r->user?->name, $r->akad, $r->jumlah_pembiayaan, $r->tenor, $r->status, $r->tanggal_pengajuan->format('Y-m-d')])->all()];
    }

}
