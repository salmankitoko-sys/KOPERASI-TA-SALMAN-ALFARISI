<div x-show='activeTab === pembayaran' x-cloak class='space-y-6'>
    <div class='rounded-2xl bg-gradient-to-r from-indigo-700 to-violet-700 px-6 py-7 text-white shadow-sm'>
        <p class='text-sm font-semibold text-indigo-100'>Pusat Transaksi</p>
        <h2 class='mt-1 text-2xl font-bold'>Pembayaran</h2>
        <p class='mt-2 max-w-2xl text-sm text-indigo-100'>Pilih jenis transaksi untuk melanjutkan pembayaran. Seluruh status dan riwayat dapat dipantau dari satu tempat.</p>
    </div>

    <div class='grid gap-4 md:grid-cols-2'>
        <a href='{{ route('anggota.angsuran.index') }}' class='group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md'>
            <span class='flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-xl'>🗓️</span>
            <h3 class='mt-4 font-bold text-gray-900 group-hover:text-indigo-700'>Bayar Angsuran</h3>
            <p class='mt-1 text-sm leading-6 text-gray-500'>Pilih tagihan pembiayaan, lalu gunakan QRIS atau unggah bukti transfer.</p>
            <span class='mt-4 inline-flex text-sm font-semibold text-indigo-600'>Pilih angsuran →</span>
        </a>

        <a href='{{ route('anggota.payments.simpanan.qris') }}' class='group rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md'>
            <span class='flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-xl'>💳</span>
            <h3 class='mt-4 font-bold text-gray-900 group-hover:text-emerald-700'>Setor Simpanan</h3>
            <p class='mt-1 text-sm leading-6 text-gray-500'>Buat pembayaran QRIS untuk simpanan pokok, wajib, atau sukarela.</p>
            <span class='mt-4 inline-flex text-sm font-semibold text-emerald-600'>Pilih simpanan →</span>
        </a>


    </div>

    <div class='flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-5 sm:flex-row sm:items-center sm:justify-between'>
        <div><h3 class='font-semibold text-gray-900'>Riwayat pembayaran QRIS</h3><p class='mt-1 text-sm text-gray-500'>Lihat status invoice, pembayaran berhasil, dibatalkan, atau kedaluwarsa.</p></div>
        <a href='{{ route('anggota.payments.history') }}' class='inline-flex shrink-0 items-center justify-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700'>Lihat riwayat</a>
    </div>
</div>
