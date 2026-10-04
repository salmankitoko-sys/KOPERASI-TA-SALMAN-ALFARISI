<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-indigo-700 leading-tight">
                    {{ $title ?? 'Pembiayaan & Angsuran' }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $description ?? '' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                // Ensure all variables used by the partial have defaults
                $pembiayaanAktif    = $pembiayaanAktif ?? collect();
                $notifikasiAngsuran = $notifikasiAngsuran ?? collect();

                if (!isset($rupiah) || !is_callable($rupiah)) {
                    $rupiah = fn ($v) => 'Rp ' . number_format($v ?? 0, 0, ',', '.');
                }
            @endphp
            @php
                $formErrors = $errors ?? new \Illuminate\Support\ViewErrorBag();
            @endphp

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm font-semibold px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            @if ($formErrors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 text-sm font-semibold px-4 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($formErrors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div x-data="{ activeTab: 'pembiayaan' }">
                @include('dashboard.anggota.partials.pembiayaan')
            </div>
        </div>
    </div>
</x-app-layout>

