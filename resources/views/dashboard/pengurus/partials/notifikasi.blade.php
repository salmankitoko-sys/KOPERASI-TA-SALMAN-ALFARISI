<section x-show="activeTab === 'notifikasi'" x-cloak>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Notifikasi dan Broadcast</h3>
            <p class="text-sm text-gray-500">Kirim pengumuman dan tinjau pesan yang diterima akun Anda.</p>
        </div>
        <a href="{{ route('inbox.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
            Buka inbox
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <h4 class="font-semibold text-gray-900">Broadcast Pesan</h4>
            <p class="mt-1 text-xs text-gray-500">Pesan akan dikirim ke seluruh penerima pada kelompok yang dipilih.</p>

            <form method="POST" action="{{ route('pengurus.marketplace.notifikasi.broadcast') }}" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label for="recipient" class="mb-1 block text-xs font-medium text-gray-600">Penerima</label>
                    <select id="recipient" name="recipient" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="semua_anggota" @selected(old('recipient') === 'semua_anggota')>Semua anggota</option>
                        <option value="semua_pengurus" @selected(old('recipient') === 'semua_pengurus')>Semua pengurus</option>
                        <option value="semua_admin" @selected(old('recipient') === 'semua_admin')>Semua administrator</option>
                        <option value="semua_user" @selected(old('recipient') === 'semua_user')>Semua pengguna</option>
                    </select>
                    @error('recipient')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="broadcast-title" class="mb-1 block text-xs font-medium text-gray-600">Judul pesan</label>
                    <input id="broadcast-title" name="title" type="text" value="{{ old('title') }}" maxlength="200" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Pengumuman RAT">
                    @error('title')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="broadcast-message" class="mb-1 block text-xs font-medium text-gray-600">Isi pesan</label>
                    <textarea id="broadcast-message" name="message" rows="5" maxlength="2000" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tulis pesan untuk penerima...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                    Kirim broadcast
                </button>
            </form>
        </div>

        <div class="space-y-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <h4 class="font-semibold text-gray-900">Inbox terbaru</h4>
                <span class="text-xs text-gray-500">{{ $chatCount }} belum dibaca</span>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                @forelse ($inboxEntries as $entry)
                    <article class="flex gap-3 border-b border-gray-100 p-4 last:border-b-0 {{ $entry->is_read ? 'bg-white' : 'bg-indigo-50/40' }}">
                        <div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $entry->is_read ? 'bg-gray-300' : 'bg-indigo-500' }}"></div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                <p class="text-sm font-semibold text-gray-900">{{ $entry->title }}</p>
                                <time class="shrink-0 text-xs text-gray-400">{{ $entry->created_at->locale('id')->diffForHumans() }}</time>
                            </div>
                            @if ($entry->message)
                                <p class="mt-1 text-sm leading-5 text-gray-600">{{ $entry->message }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">Belum ada pesan di inbox Anda.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>
