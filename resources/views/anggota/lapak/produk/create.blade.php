<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-indigo-700 leading-tight">Tambah Produk</h2>
            <p class="text-sm text-gray-500 mt-0.5">Produk baru akan masuk moderasi sebelum tampil di marketplace.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 text-sm font-semibold px-4 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('anggota.lapak.produk.store') }}" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                @include('anggota.lapak.produk.form', ['produk' => null, 'submitLabel' => 'Simpan Produk'])
            </form>
        </div>
    </div>
</x-app-layout>
