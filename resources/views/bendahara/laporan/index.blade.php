<x-bendahara-layout>
<x-slot name='header'><div><p>Pelaporan Otomatis</p><h1>Laporan Bendahara</h1><p>Disusun langsung dari transaksi koperasi yang telah diverifikasi.</p></div></x-slot>
<main class='mx-auto max-w-7xl space-y-6 p-6'>@if(session('success'))<div>{{ session('success') }}</div>@endif @include('bendahara.laporan.partials.form') @include('bendahara.laporan.partials.list')</main>
</x-bendahara-layout>
