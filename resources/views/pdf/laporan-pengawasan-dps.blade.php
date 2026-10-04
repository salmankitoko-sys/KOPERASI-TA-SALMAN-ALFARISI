<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
@page{margin:38px 42px 54px}body{font-family:DejaVu Sans,sans-serif;color:#1f2937;font-size:10px;line-height:1.45}.header{border-bottom:3px solid #4338ca;padding-bottom:13px;margin-bottom:18px}.brand{font-size:10px;font-weight:bold;letter-spacing:1px;color:#047857}.title{font-size:21px;font-weight:bold;margin:4px 0;color:#312e81}.muted{color:#6b7280}.meta,.summary,.detail{width:100%;border-collapse:collapse;margin:12px 0 18px}.meta td,.summary th,.summary td,.detail th,.detail td{border:1px solid #d1d5db;padding:6px 7px}.meta td:first-child{width:28%;font-weight:bold;background:#f3f4f6}.summary th,.detail th{background:#eef2ff;color:#312e81;text-align:left}.number{text-align:right}.section{margin:17px 0}.section h2{font-size:13px;color:#312e81;border-bottom:1px solid #c7d2fe;padding-bottom:4px;margin-bottom:7px}.content{white-space:pre-line}.badge{display:inline-block;padding:2px 6px;border-radius:3px;background:#eef2ff}.approval{margin-top:28px;width:100%;border-collapse:collapse}.approval td{width:50%;text-align:center;vertical-align:top}.signature{height:55px}.footer{position:fixed;bottom:-36px;left:0;right:0;text-align:center;color:#9ca3af;font-size:8px;border-top:1px solid #e5e7eb;padding-top:6px}.page-break{page-break-before:always}
</style>
</head>
<body>
@php($data = $laporan->data_laporan ?? [])
<div class="header"><div class="brand">KOPERASI SYARIAH - DEWAN PENGAWAS SYARIAH</div><div class="title">Laporan Pengawasan dan Validasi Akad</div><div class="muted">Dokumen disusun otomatis dari data validasi akad pada sistem.</div></div>
<table class="meta">
<tr><td>Nomor laporan</td><td>DPS/{{ str_pad($laporan->id, 4, '0', STR_PAD_LEFT) }}/{{ $laporan->created_at->format('Y') }}</td></tr>
<tr><td>Jenis periode</td><td>{{ $laporan->periode }} {{ $laporan->semester ? '- '.$laporan->semester : '' }}</td></tr>
<tr><td>Rentang data</td><td>{{ $laporan->periode_mulai?->format('d/m/Y') }} sampai {{ $laporan->periode_selesai?->format('d/m/Y') }}</td></tr>
<tr><td>Disusun oleh</td><td>{{ $laporan->dps?->name ?? 'DPS' }}</td></tr>
<tr><td>Tanggal penerbitan</td><td>{{ $laporan->tanggal_publikasi?->format('d/m/Y') }}</td></tr>
</table>
<div class="section"><h2>1. Ringkasan Eksekutif</h2><div class="content">{{ $laporan->ringkasan }}</div></div>
<div class="section"><h2>2. Statistik Validasi Akad</h2><table class="summary"><tr><th>Indikator</th><th class="number">Jumlah</th></tr><tr><td>Total validasi</td><td class="number">{{ data_get($data, 'statistik.total', 0) }}</td></tr><tr><td>Sesuai syariah</td><td class="number">{{ data_get($data, 'statistik.sesuai', 0) }}</td></tr><tr><td>Perlu perbaikan</td><td class="number">{{ data_get($data, 'statistik.perlu_perbaikan', 0) }}</td></tr><tr><td>Tidak sesuai</td><td class="number">{{ data_get($data, 'statistik.tidak_sesuai', 0) }}</td></tr></table></div>
<div class="section"><h2>3. Sebaran Jenis Akad</h2><table class="summary"><tr><th>Akad</th><th class="number">Jumlah</th></tr>@forelse(data_get($data, 'akad', []) as $akad => $jumlah)<tr><td>{{ strtoupper($akad) }}</td><td class="number">{{ $jumlah }}</td></tr>@empty<tr><td colspan="2">Tidak ada data.</td></tr>@endforelse</table></div>
<div class="section"><h2>4. Isu Kepatuhan Utama</h2>@forelse(data_get($data, 'isu', []) as $isu)<p>{{ $loop->iteration }}. {{ $isu }}</p>@empty<p>Tidak terdapat isu kepatuhan pada periode ini.</p>@endforelse</div>
<div class="section"><h2>5. Rekomendasi DPS</h2><div class="content">{{ $laporan->rekomendasi ?: 'Mempertahankan kepatuhan dokumen dan pelaksanaan akad sesuai hasil validasi DPS.' }}</div></div>
<div class="section"><h2>6. Kesimpulan</h2><p>Berdasarkan data periode laporan, tingkat kesesuaian akad adalah <strong>{{ data_get($data, 'statistik.persentase_sesuai', 0) }}%</strong>. Hasil ini menjadi bahan pengawasan dan tindak lanjut Ketua Koperasi.</p></div>
<div class="page-break"></div>
<div class="section"><h2>Lampiran - Rincian Validasi Akad</h2><table class="detail"><thead><tr><th>No.</th><th>Kode</th><th>Anggota</th><th>Akad</th><th>Hasil</th><th>Tanggal</th></tr></thead><tbody>@forelse(data_get($data, 'rincian', []) as $item)<tr><td>{{ $loop->iteration }}</td><td>{{ $item['kode'] }}</td><td>{{ $item['anggota'] }}</td><td>{{ strtoupper($item['akad']) }}</td><td><span class="badge">{{ str($item['hasil'])->replace('_', ' ')->title() }}</span></td><td>{{ $item['tanggal'] }}</td></tr>@empty<tr><td colspan="6">Tidak ada validasi pada periode ini.</td></tr>@endforelse</tbody></table></div>
<table class="approval"><tr><td>Disusun oleh DPS,<div class="signature"></div><strong>{{ $laporan->dps?->name ?? '(________________)' }}</strong></td><td>Diterima oleh Ketua Koperasi,<div class="signature"></div><strong>(________________)</strong></td></tr></table>
<div class="footer">Dicetak otomatis pada {{ now()->format('d/m/Y H:i') }} - Sistem Informasi Koperasi Syariah</div>
</body>
</html>