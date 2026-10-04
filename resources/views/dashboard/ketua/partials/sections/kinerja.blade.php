<section class="space-y-5">
    <div><h2 class="text-xl font-bold text-stone-900">Kinerja Koperasi</h2><p class="text-sm text-stone-500">Tren enam bulan sebagai bahan evaluasi kebijakan dan rapat Pengurus.</p></div>
    <div class="grid gap-4 lg:grid-cols-3">
        @foreach ($tren as $item)<article class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm"><div class="flex items-center justify-between"><strong>{{ $item['label'] }}</strong><span class="text-xs text-stone-400">Periode bulanan</span></div><dl class="mt-4 space-y-2 text-sm"><div class="flex justify-between"><dt>Anggota baru</dt><dd class="font-semibold">{{ $item['anggota'] }}</dd></div><div class="flex justify-between"><dt>Simpanan</dt><dd class="font-semibold">Rp {{ number_format($item['simpanan'], 0, ',', '.') }}</dd></div><div class="flex justify-between"><dt>Pembiayaan</dt><dd class="font-semibold">Rp {{ number_format($item['pembiayaan'], 0, ',', '.') }}</dd></div></dl></article>@endforeach
    </div>
</section>
