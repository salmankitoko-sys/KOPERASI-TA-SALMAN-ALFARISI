<x-bendahara-layout>
<x-slot name="header">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div><p class="text-xs font-bold uppercase tracking-[.18em] text-emerald-600">Dashboard</p><h1 class="mt-1 text-2xl font-bold text-slate-900">Ringkasan Keuangan</h1><p class="mt-1 text-sm text-slate-500">Pantau kas, antrean transaksi, dan pencairan prioritas koperasi.</p></div>
        <div class="flex gap-2"><a href="{{ route('bendahara.transaksi.rekening.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Kelola rekening</a><a href="{{ route('bendahara.pencairan.index') }}" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-200 hover:bg-emerald-700">Proses pencairan</a></div>
    </div>
</x-slot>
@php
$rupiah=fn($v)=>'Rp '.number_format((float)$v,0,',','.');
$statusTone=fn($s)=>match($s){'selesai','diverifikasi'=>'bg-emerald-50 text-emerald-700','ditolak'=>'bg-rose-50 text-rose-700','diproses'=>'bg-sky-50 text-sky-700',default=>'bg-amber-50 text-amber-700'};
@endphp
<div class="mx-auto max-w-[1500px] space-y-6 p-4 sm:p-6 lg:p-8">
    <section class="relative overflow-hidden rounded-3xl bg-slate-950 p-6 text-white shadow-xl lg:p-8">
        <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-emerald-500/20 blur-3xl"></div>
        <div class="relative grid gap-7 xl:grid-cols-[1.4fr_.6fr]">
            <div><span class="inline-flex rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-xs font-bold text-emerald-300">Posisi kas realtime</span><h2 class="mt-5 text-3xl font-bold tracking-tight sm:text-4xl">{{ $rupiah($stats['saldo_bersih']) }}</h2><p class="mt-2 text-sm text-slate-400">Saldo bersih berdasarkan mutasi terverifikasi.</p>
                <div class="mt-6 flex flex-wrap gap-3"><a href="{{ route('bendahara.transaksi.mutasi.index') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-slate-900">Lihat mutasi kas</a><a href="{{ route('bendahara.laporan.index') }}" class="rounded-xl border border-white/20 px-4 py-2.5 text-sm font-bold text-white hover:bg-white/10">Laporan keuangan</a></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs text-slate-400">Masuk hari ini</p><p class="mt-2 text-lg font-bold text-emerald-300">{{ $rupiah($stats['masuk_hari_ini']) }}</p></div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs text-slate-400">Keluar hari ini</p><p class="mt-2 text-lg font-bold text-rose-300">{{ $rupiah($stats['keluar_hari_ini']) }}</p></div>
                <div class="col-span-2 rounded-2xl border border-white/10 bg-white/5 p-4"><div class="flex justify-between text-xs text-slate-400"><span>Total kas masuk</span><span>Total kas keluar</span></div><div class="mt-2 flex justify-between gap-3 font-bold"><span class="text-emerald-300">{{ $rupiah($stats['kas_masuk']) }}</span><span class="text-rose-300">{{ $rupiah($stats['kas_keluar']) }}</span></div></div>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach([
            ['Pencairan siap',$stats['siap_dicairkan'],'bendahara.pencairan.index','bg-amber-50 text-amber-700'],
            ['Mutasi menunggu',$stats['mutasi_pending'],'bendahara.transaksi.mutasi.index','bg-violet-50 text-violet-700'],
            ['Angsuran masuk',$stats['pembayaran_pending'],'bendahara.angsuran.index','bg-sky-50 text-sky-700'],
            ['Rekening aktif',$stats['rekening_aktif'],'bendahara.transaksi.rekening.index','bg-slate-100 text-slate-700'],
        ] as [$label,$value,$route,$tone])
        <a href="{{ route($route) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"><span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-bold {{ $tone }}">{{ $label }}</span><div class="mt-4 flex items-end justify-between"><p class="text-3xl font-bold text-slate-900">{{ $value }}</p><span class="text-slate-300 transition group-hover:translate-x-1 group-hover:text-emerald-500">&rarr;</span></div></a>
        @endforeach
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b px-5 py-4"><div><h3 class="font-bold text-slate-900">Pencairan prioritas</h3><p class="text-xs text-slate-500">Urutan permintaan paling lama.</p></div><a href="{{ route('bendahara.pencairan.index') }}" class="text-sm font-bold text-emerald-600">Lihat semua</a></div>
            <div class="divide-y divide-slate-100">
                @forelse($priorityDisbursements as $item)
                <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-center"><div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 font-bold text-amber-700">{{ strtoupper(substr($item->pembiayaan?->user?->name ?? 'A',0,1)) }}</div><div class="min-w-0 flex-1"><p class="truncate text-sm font-bold text-slate-900">{{ $item->pembiayaan?->user?->name ?? 'Anggota' }}</p><p class="text-xs text-slate-500">{{ $item->pembiayaan?->kode ?? '-' }} &middot; {{ $item->bank_tujuan }} {{ $item->no_rekening_tujuan }}</p></div><div class="sm:text-right"><p class="text-sm font-bold">{{ $rupiah($item->nominal_pencairan) }}</p><span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusTone($item->status) }}">{{ ucfirst($item->status) }}</span></div></div>
                @empty <div class="p-10 text-center text-sm text-slate-500">Tidak ada pencairan dalam antrean.</div> @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between"><div><h3 class="font-bold text-slate-900">Rekening sumber</h3><p class="text-xs text-slate-500">Rekening koperasi untuk transaksi.</p></div><a href="{{ route('bendahara.transaksi.rekening.index') }}" class="text-sm font-bold text-emerald-600">Kelola</a></div>
            <div class="mt-4 space-y-3">@forelse($accounts as $account)<div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-xs font-black text-slate-600">{{ strtoupper(substr($account->nama_bank,0,3)) }}</span><div class="min-w-0 flex-1"><p class="truncate text-sm font-bold">{{ $account->nama_bank }}</p><p class="text-xs text-slate-500">{{ $account->nomor_rekening }} &middot; {{ $account->atas_nama }}</p></div><span class="h-2.5 w-2.5 rounded-full {{ $account->is_aktif ? 'bg-emerald-500' : 'bg-slate-300' }}"></span></div>@empty<p class="py-8 text-center text-sm text-slate-500">Belum ada rekening koperasi.</p>@endforelse</div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b px-5 py-4"><div><h3 class="font-bold text-slate-900">Aktivitas transaksi terbaru</h3><p class="text-xs text-slate-500">Jejak pemasukan dan pengeluaran koperasi.</p></div><a href="{{ route('bendahara.transaksi.mutasi.index') }}" class="text-sm font-bold text-emerald-600">Buka mutasi</a></div>
        <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">Transaksi</th><th class="px-5 py-3">Anggota</th><th class="px-5 py-3">Metode</th><th class="px-5 py-3 text-right">Jumlah</th><th class="px-5 py-3">Status</th></tr></thead><tbody class="divide-y">@forelse($recentMutations as $item)<tr class="hover:bg-slate-50"><td class="px-5 py-4"><p class="font-bold text-slate-800">{{ $item->judul }}</p><p class="text-xs text-slate-400">{{ $item->created_at?->format('d M Y, H:i') }}</p></td><td class="px-5 py-4">{{ $item->user?->name ?? '-' }}</td><td class="px-5 py-4">{{ ucfirst(str_replace('_',' ',$item->metode_pembayaran ?? '-')) }}</td><td class="px-5 py-4 text-right font-bold">{{ $rupiah($item->jumlah) }}</td><td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusTone($item->status) }}">{{ ucfirst(str_replace('_',' ',$item->status)) }}</span></td></tr>@empty<tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada transaksi.</td></tr>@endforelse</tbody></table></div>
    </section>
</div>
</x-bendahara-layout>
