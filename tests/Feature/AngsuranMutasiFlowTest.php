<?php

namespace Tests\Feature;

use App\Models\Angsuran;
use App\Models\PembayaranAngsuran;
use App\Models\Pembiayaan;
use App\Models\TransaksiPembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AngsuranMutasiFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_anggota_can_submit_bukti_angsuran_and_bendahara_can_verify_via_mutasi()
    {
        Storage::fake('public');

        // create anggota
        $anggota = User::factory()->create(['role' => 'anggota']);

        // create pembiayaan
        $p = Pembiayaan::create([
            'user_id' => $anggota->id,
            'kode' => 'PJ-TEST-1',
            'akad' => 'murabahah',
            'objek_pembiayaan' => 'Test modal',
            'jumlah_pembiayaan' => 1000000,
            'tenor' => 12,
            'angsuran_bulanan' => 100000,
            'tanggal_pengajuan' => now()->toDateString(),
            'status' => 'berjalan',
        ]);

        // create angsuran
        $angsuran = Angsuran::create([
            'pembiayaan_id' => $p->id,
            'bulan_ke' => 1,
            'jatuh_tempo' => now()->toDateString(),
            'jumlah_bayar' => 100000,
            'pokok' => 80000,
            'margin' => 20000,
            'sisa_pokok' => 920000,
            'status' => 'belum_bayar',
        ]);

        $this->actingAs($anggota)
            ->get(route('anggota.transaksi.angsuran.create', [
                'pembiayaan_id' => $p->id,
                'angsuran_id' => $angsuran->id,
                'manual' => 1,
            ]))
            ->assertOk()
            ->assertSee('Pembayaran Angsuran')
            ->assertSee('PJ-TEST-1')
            ->assertSee('Jatuh tempo tagihan');

        // Anggota mengirim bukti untuk angsuran yang nominalnya sudah terkunci pada jadwal.
        $this->actingAs($anggota)
            ->post(route('anggota.transaksi.angsuran.store'), [
                'pembiayaan_id' => $p->id,
                'angsuran_id' => $angsuran->id,
                'manual' => 1,
                'jumlah_dibayar' => 1,
                'tanggal_bayar' => now()->toDateString(),
                'bukti_transfer' => UploadedFile::fake()->create('bukti-angsuran.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('anggota.transaksi.dashboard'));

        // Assert PembayaranAngsuran exists with menunggu_verifikasi
        $this->assertDatabaseHas('pembayaran_angsuran', [
            'pembiayaan_id' => $p->id,
            'angsuran_id' => $angsuran->id,
            'jumlah_dibayar' => 100000,
            'status' => 'menunggu_verifikasi',
        ]);

        $pembayaran = PembayaranAngsuran::first();
        Storage::disk('public')->assertExists($pembayaran->bukti_transfer);

        // Assert TransaksiPembayaran entry created and status menunggu_verifikasi
        $this->assertDatabaseHas('transaksi_pembayaran', [
            'jenis_transaksi' => 'angsuran',
            'referensi' => 'pembayaran_angsuran:'.$pembayaran->id,
            'status' => 'menunggu_verifikasi',
        ]);

        $mutasi = TransaksiPembayaran::where('referensi', 'pembayaran_angsuran:'.$pembayaran->id)->first();
        $this->assertNotNull($mutasi);

        // Pengurus tidak boleh menjalankan uang; Bendahara yang memverifikasi mutasi
        $bendahara = User::factory()->create(['role' => User::ROLE_BENDAHARA]);

        $this->actingAs($bendahara)
            ->post(route('bendahara.transaksi.mutasi.approve', $mutasi->id), [])
            ->assertRedirect();

        // reload models
        $pembayaran->refresh();
        $angsuran->refresh();
        $mutasi->refresh();

        $this->assertEquals('diverifikasi', $pembayaran->status);
        $this->assertEquals('dibayar', $angsuran->status);
        $this->assertEquals('diverifikasi', $mutasi->status);
    }
}
