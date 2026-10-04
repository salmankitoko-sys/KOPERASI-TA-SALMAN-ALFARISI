<section class="space-y-8">
    <section class="space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-xl font-bold text-stone-900">Laporan Dewan Pengawas Syariah</h2>
                        <p class="text-sm text-stone-500">Dokumen pengawasan dan validasi kepatuhan syariah yang telah diterbitkan DPS.</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-right">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Laporan diterima</p>
                <p class="mt-1 text-2xl font-bold text-emerald-950">{{ number_format($laporanDps->count()) }}</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            @forelse($laporanDps as $item)
                <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition hover:border-emerald-200 hover:shadow-md">
                    <div class="border-b border-stone-100 bg-gradient-to-r from-emerald-50 to-white px-5 py-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Pengawasan Syariah</p>
                                <h3 class="mt-1 text-base font-bold text-stone-900">
                                    {{ $item->periode }}{{ $item->semester ? ' · '.$item->semester : '' }}
                                </h3>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Telah diterbitkan
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col p-5">
                        <dl class="grid gap-3 text-sm sm:grid-cols-2">
                            <div class="rounded-lg bg-stone-50 p-3">
                                <dt class="text-xs text-stone-500">Disusun oleh</dt>
                                <dd class="mt-1 font-semibold text-stone-800">{{ $item->dps?->name ?? 'Dewan Pengawas Syariah' }}</dd>
                            </div>
                            <div class="rounded-lg bg-stone-50 p-3">
                                <dt class="text-xs text-stone-500">Periode pengawasan</dt>
                                <dd class="mt-1 font-semibold text-stone-800">
                                    {{ $item->periode_mulai?->translatedFormat('d M Y') ?? '-' }}–{{ $item->periode_selesai?->translatedFormat('d M Y') ?? '-' }}
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-4">
                            <h4 class="text-xs font-bold uppercase tracking-wide text-stone-500">Ringkasan</h4>
                            <p class="mt-2 text-sm leading-6 text-stone-600">{{ $item->ringkasan ?: 'Tidak ada ringkasan laporan.' }}</p>
                        </div>

                        @if($item->rekomendasi)
                            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <h4 class="text-xs font-bold uppercase tracking-wide text-amber-800">Rekomendasi DPS</h4>
                                <p class="mt-1 text-sm leading-6 text-amber-900">{{ $item->rekomendasi }}</p>
                            </div>
                        @endif

                        <div class="mt-auto flex flex-wrap items-center justify-between gap-3 border-t border-stone-100 pt-5">
                            <p class="text-xs text-stone-500">
                                Dikirim {{ $item->dikirim_pada?->translatedFormat('d M Y, H:i') ?? $item->tanggal_publikasi?->translatedFormat('d M Y') ?? '-' }}
                            </p>
                            <a href="{{ route('laporan-pengawasan.pdf', $item) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 12-4-4m4 4 4-4M4 20h16" />
                                </svg>
                                Unduh PDF
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center lg:col-span-2">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-stone-100 text-stone-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H8.25m0 12.75h7.5m-7.5 3h4.5M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </span>
                    <h3 class="mt-4 font-semibold text-stone-900">Belum ada laporan DPS</h3>
                    <p class="mt-1 text-sm text-stone-500">Laporan akan tampil setelah diterbitkan dan dikirim oleh Dewan Pengawas Syariah.</p>
                </div>
            @endforelse
        </div>
    </section>

    @include('dashboard.ketua.partials.sections.laporan-bendahara')
    <div class="flex flex-wrap items-end justify-between gap-3"><div><h2 class="text-xl font-bold">Laporan Pengurus</h2><p class="text-sm text-stone-500">Laporan operasional yang diserahkan Pengurus untuk ditinjau Ketua.</p></div><span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">{{ $laporanPengurus->where('status', 'dikirim')->count() }} menunggu tinjauan</span></div>
    <div class="space-y-4">@forelse($laporanPengurus as $item)
        <article class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3"><div><h3 class="font-bold text-stone-900">{{ $item->judul }}</h3><p class="text-xs text-stone-500">{{ $item->pembuat?->name }} Â· {{ $item->periode_mulai->format('d M Y') }}â€“{{ $item->periode_selesai->format('d M Y') }}</p></div><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $item->status === 'diterima' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'perlu_tindak_lanjut' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800') }}">{{ str($item->status)->replace('_',' ')->title() }}</span></div>
            <div class="mt-4 grid gap-4 text-sm md:grid-cols-2"><div><strong>Ringkasan</strong><p class="mt-1 text-stone-600">{{ $item->ringkasan }}</p></div><div><strong>Capaian</strong><p class="mt-1 text-stone-600">{{ $item->capaian ?: '-' }}</p></div><div><strong>Kendala</strong><p class="mt-1 text-stone-600">{{ $item->kendala ?: '-' }}</p></div><div><strong>Rencana tindak lanjut</strong><p class="mt-1 text-stone-600">{{ $item->tindak_lanjut ?: '-' }}</p></div></div>
            <div class="mt-4"><a href="{{ route('laporan-pengurus.pdf',$item) }}" class="inline-flex rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-800">Unduh File PDF</a></div>
            @if($item->status === 'dikirim')<form method="POST" action="{{ route('ketua.laporan.review', $item) }}" class="mt-5 rounded-xl bg-stone-50 p-4">@csrf<label class="text-xs font-semibold text-stone-600">Catatan Ketua</label><textarea name="catatan_ketua" rows="2" class="mt-1 w-full rounded-lg border-stone-300 text-sm" placeholder="Wajib diisi jika meminta tindak lanjut"></textarea><div class="mt-3 flex flex-wrap gap-2"><button name="keputusan" value="diterima" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Terima Laporan</button><button name="keputusan" value="perlu_tindak_lanjut" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white">Minta Tindak Lanjut</button></div></form>@elseif($item->catatan_ketua)<div class="mt-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-900"><strong>Catatan Ketua:</strong> {{ $item->catatan_ketua }}</div>@endif
        </article>
    @empty<div class="rounded-xl border bg-white py-12 text-center text-stone-500">Belum ada laporan yang dikirim Pengurus.</div>@endforelse</div>
</section>
