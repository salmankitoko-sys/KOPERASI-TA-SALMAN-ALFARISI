<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-indigo-700">Setoran Simpanan QRIS</h2>
                <p class="mt-0.5 text-sm text-gray-500">Pilih jenis simpanan dan masukkan nominal, lalu bayar via QRIS.</p>
            </div>
            <a href="{{ route('anggota.dashboard', ['tab' => 'simpanan']) }}" class="inline-flex w-fit items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('anggota.payments.qris.simpanan') }}" class="space-y-5" id="simpananForm">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Simpanan</label>
                        <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-3">
                            @foreach (['pokok' => 'Simpanan Pokok', 'wajib' => 'Simpanan Wajib', 'sukarela' => 'Simpanan Sukarela'] as $value => $label)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="jenis_simpanan" value="{{ $value }}" class="peer sr-only" required
                                        {{ old('jenis_simpanan') === $value ? 'checked' : '' }}>
                                    <div class="rounded-xl border-2 border-gray-200 bg-white p-4 text-center transition-all hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50">
                                        <span class="text-sm font-semibold text-gray-700 peer-checked:text-indigo-700">{{ $label }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('jenis_simpanan', 'simpanan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nominal" class="block text-sm font-medium text-gray-700">Nominal Setoran</label>
                        <div class="relative mt-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-500">Rp</span>
                            <input id="nominal" type="number" name="nominal" min="1000" step="100"
                                value="{{ old('nominal') }}"
                                class="w-full rounded-xl border-gray-300 pl-12 pr-4 text-right text-lg font-semibold focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="0" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Minimal Rp 1.000</p>
                        @error('nominal', 'simpanan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nominal Cepat --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nominal Cepat</label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ([10000, 25000, 50000, 100000, 250000, 500000] as $quick)
                                <button type="button" data-quick-nominal="{{ $quick }}"
                                    class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600">
                                    Rp {{ number_format($quick, 0, ',', '.') }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div id="preview" class="hidden rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                        <p class="text-sm font-semibold text-indigo-700">Ringkasan Setoran</p>
                        <div class="mt-2 space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-indigo-600">Jenis</span>
                                <span id="previewJenis" class="font-medium text-indigo-900">-</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-indigo-600">Nominal</span>
                                <span id="previewNominal" class="font-bold text-indigo-900">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <button type="submit" id="submitBtn" disabled
                            class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                Bayar via QRIS
                            </span>
                        </button>
                        <p class="mt-2 text-center text-xs text-gray-400">Anda akan diarahkan ke halaman QR Code setelah mengklik tombol di atas.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('simpananForm');
        const jenisInputs = form.querySelectorAll('input[name="jenis_simpanan"]');
        const nominalInput = document.getElementById('nominal');
        const preview = document.getElementById('preview');
        const previewJenis = document.getElementById('previewJenis');
        const previewNominal = document.getElementById('previewNominal');
        const submitBtn = document.getElementById('submitBtn');

        const jenisLabels = {
            pokok: 'Simpanan Pokok',
            wajib: 'Simpanan Wajib',
            sukarela: 'Simpanan Sukarela',
        };

        const rupiah = (v) => 'Rp ' + Number(v).toLocaleString('id-ID');

        function updatePreview() {
            const jenis = form.querySelector('input[name="jenis_simpanan"]:checked')?.value;
            const nominal = parseInt(nominalInput.value) || 0;

            if (jenis && nominal >= 1000) {
                previewJenis.textContent = jenisLabels[jenis] || jenis;
                previewNominal.textContent = rupiah(nominal);
                preview.classList.remove('hidden');
                submitBtn.disabled = false;
            } else {
                preview.classList.add('hidden');
                submitBtn.disabled = true;
            }
        }

        jenisInputs.forEach(el => el.addEventListener('change', updatePreview));
        nominalInput.addEventListener('input', updatePreview);
        form.querySelectorAll('[data-quick-nominal]').forEach(button => {
            button.addEventListener('click', () => {
                nominalInput.value = button.dataset.quickNominal;
                updatePreview();
            });
        });
        updatePreview();
    </script>
</x-app-layout>
