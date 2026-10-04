<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Anggota
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Anggota</h1>
                    <p class="mt-1 text-sm text-gray-500">Perbarui data {{ $anggota->name }}.</p>
                </div>
                <a href="{{ route('pengurus.dashboard', ['tab' => 'anggota']) }}"
                    class="inline-flex min-h-[44px] items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Kembali ke Manajemen
                </a>
            </div>

            @include('pengurus.anggota._form', ['anggota' => $anggota])
        </div>
    </div>
</x-app-layout>
