<?php

namespace Database\Seeders;

use App\Models\RekeningKoperasi;
use App\Models\TransaksiPembayaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class SaldoAwalKoperasiSeeder extends Seeder
{
    public function run(): void
    {
        $pencatat = User::where('role', 'bendahara')->first()
            ?? User::where('role', 'admin')->first();

        if (! $pencatat) {
            throw new RuntimeException('Bendahara atau admin diperlukan untuk mencatat saldo awal koperasi.');
        }

        $rekening = RekeningKoperasi::where('is_aktif', true)->first();

        TransaksiPembayaran::updateOrCreate(
            ['referensi' => 'MODAL-AWAL-KOPERASI-500JT'],
            [
                'user_id' => $pencatat->id,
                'jenis_transaksi' => 'modal_awal',
                'judul' => 'Modal Awal Koperasi',
                'jumlah' => 500_000_000,
                'metode_pembayaran' => 'saldo_awal',
                'status' => 'selesai',
                'keterangan' => 'Penambahan modal awal koperasi untuk membentuk saldo kas operasional.',
                'rekening_koperasi_id' => $rekening?->id,
                'bukti_transfer' => null,
            ]
        );
    }
}
