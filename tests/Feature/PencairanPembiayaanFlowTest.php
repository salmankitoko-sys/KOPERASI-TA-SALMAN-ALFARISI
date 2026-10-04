<?php

namespace Tests\Feature;

use App\Models\Pembiayaan;
use App\Models\PencairanDana;
use App\Models\RekeningKoperasi;
use App\Models\TransaksiPembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PencairanPembiayaanFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_pencairan_is_journaled_once_only_after_bendahara_records_the_transfer(): void
    {
        Storage::fake('public');

        $anggota = User::factory()->create(['role' => 'anggota']);
        $bendahara = User::factory()->create(['role' => 'bendahara']);
        $pembiayaan = $this->createPembiayaan($anggota, 'PJ-CAIR-001');
        $rekeningKoperasi = RekeningKoperasi::create([
            'nama_bank' => 'Bank Syariah Indonesia',
            'nomor_rekening' => '1234567890',
            'atas_nama' => 'Koperasi Syariah',
            'kode_rekening' => 'KAS-BSI',
            'is_aktif' => true,
        ]);

        $this->actingAs($anggota)
            ->post(route('anggota.transaksi.pencairan.store'), [
                'pembiayaan_id' => $pembiayaan->id,
                'nominal_pencairan' => 1000000,
                'bank_tujuan' => 'Bank Syariah Indonesia',
                'no_rekening_tujuan' => '99887766',
                'nama_pemilik_rekening' => $anggota->name,
                'tanggal_pencairan' => now()->toDateString(),
            ])
            ->assertRedirect(route('anggota.transaksi.dashboard'));

        $pencairan = PencairanDana::firstOrFail();
        $this->assertSame('menunggu', $pencairan->status);
        $this->assertDatabaseMissing('transaksi_pembayaran', [
            'referensi' => 'pencairan:'.$pencairan->id,
        ]);

        $this->actingAs($bendahara)
            ->post(route('bendahara.transaksi.pencairan.approve', $pencairan->id), [
                'rekening_koperasi_id' => $rekeningKoperasi->id,
            ])
            ->assertRedirect();

        $pencairan->refresh();
        $this->assertSame('diproses', $pencairan->status);
        $this->assertSame($rekeningKoperasi->id, $pencairan->rekening_koperasi_id);
        $this->assertSame($bendahara->id, $pencairan->disetujui_oleh);
        $this->assertDatabaseMissing('transaksi_pembayaran', [
            'referensi' => 'pencairan:'.$pencairan->id,
        ]);

        $this->actingAs($bendahara)
            ->post(route('bendahara.transaksi.pencairan.transfer', $pencairan->id), [
                'nomor_referensi_transfer' => 'TRF-CAIR-001',
                'tanggal_transfer' => now()->toDateString(),
                'bukti_transfer' => UploadedFile::fake()->create('bukti-transfer.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect();

        $pencairan->refresh();
        $mutasi = TransaksiPembayaran::where('referensi', 'pencairan:'.$pencairan->id)->firstOrFail();
        $this->assertSame('diproses', $pencairan->status);
        $this->assertSame('TRF-CAIR-001', $pencairan->nomor_referensi_transfer);
        $this->assertNotNull($pencairan->bukti_transfer);
        Storage::disk('public')->assertExists($pencairan->bukti_transfer);
        $this->assertSame('menunggu_verifikasi', $mutasi->status);
        $this->assertSame($rekeningKoperasi->id, $mutasi->rekening_koperasi_id);
        $this->assertSame(1, TransaksiPembayaran::where('referensi', 'pencairan:'.$pencairan->id)->count());

        $this->actingAs($bendahara)
            ->post(route('bendahara.transaksi.mutasi.approve', $mutasi->id))
            ->assertRedirect();

        $pencairan->refresh();
        $pembiayaan->refresh();
        $mutasi->refresh();
        $this->assertSame('selesai', $pencairan->status);
        $this->assertSame($bendahara->id, $pencairan->diverifikasi_oleh);
        $this->assertSame('berjalan', $pembiayaan->status);
        $this->assertSame('selesai', $mutasi->status);
    }

    public function test_anggota_cannot_request_more_than_the_remaining_financing_limit(): void
    {
        $anggota = User::factory()->create(['role' => 'anggota']);
        $pembiayaan = $this->createPembiayaan($anggota, 'PJ-CAIR-002');

        PencairanDana::create([
            'pembiayaan_id' => $pembiayaan->id,
            'nominal_pencairan' => 750000,
            'bank_tujuan' => 'BSI',
            'no_rekening_tujuan' => '111222333',
            'nama_pemilik_rekening' => $anggota->name,
            'tanggal_pencairan' => now()->toDateString(),
            'status' => 'selesai',
        ]);

        $this->actingAs($anggota)
            ->from(route('anggota.transaksi.pencairan.create'))
            ->post(route('anggota.transaksi.pencairan.store'), [
                'pembiayaan_id' => $pembiayaan->id,
                'nominal_pencairan' => 500000,
                'bank_tujuan' => 'BSI',
                'no_rekening_tujuan' => '99887766',
                'nama_pemilik_rekening' => $anggota->name,
                'tanggal_pencairan' => now()->toDateString(),
            ])
            ->assertRedirect(route('anggota.transaksi.pencairan.create'))
            ->assertSessionHasErrors('nominal_pencairan');

        $this->assertSame(1, PencairanDana::count());
    }

    public function test_mutasi_cannot_verify_pencairan_without_transfer_proof(): void
    {
        $anggota = User::factory()->create(['role' => 'anggota']);
        $bendahara = User::factory()->create(['role' => 'bendahara']);
        $pembiayaan = $this->createPembiayaan($anggota, 'PJ-CAIR-003');
        $rekeningKoperasi = RekeningKoperasi::create([
            'nama_bank' => 'Bank Muamalat',
            'nomor_rekening' => '88776655',
            'atas_nama' => 'Koperasi Syariah',
            'is_aktif' => true,
        ]);
        $pencairan = PencairanDana::create([
            'pembiayaan_id' => $pembiayaan->id,
            'rekening_koperasi_id' => $rekeningKoperasi->id,
            'nominal_pencairan' => 1000000,
            'bank_tujuan' => 'BSI',
            'no_rekening_tujuan' => '444555666',
            'nama_pemilik_rekening' => $anggota->name,
            'tanggal_pencairan' => now()->toDateString(),
            'status' => 'diproses',
        ]);
        $mutasi = TransaksiPembayaran::create([
            'user_id' => $anggota->id,
            'jenis_transaksi' => 'pencairan',
            'judul' => 'Pencairan '.$pembiayaan->kode,
            'jumlah' => 1000000,
            'metode_pembayaran' => 'transfer',
            'status' => 'menunggu_verifikasi',
            'referensi' => 'pencairan:'.$pencairan->id,
            'rekening_koperasi_id' => $rekeningKoperasi->id,
        ]);

        $this->actingAs($bendahara)
            ->from(route('bendahara.transaksi.mutasi.index'))
            ->post(route('bendahara.transaksi.mutasi.approve', $mutasi->id))
            ->assertRedirect(route('bendahara.transaksi.mutasi.index'))
            ->assertSessionHas('error');

        $pencairan->refresh();
        $mutasi->refresh();
        $this->assertSame('diproses', $pencairan->status);
        $this->assertSame('menunggu_verifikasi', $mutasi->status);
    }

    public function test_bendahara_is_guided_to_activate_a_source_account_before_approving_disbursement(): void
    {
        $anggota = User::factory()->create(['role' => 'anggota']);
        $bendahara = User::factory()->create(['role' => 'bendahara']);
        $pembiayaan = $this->createPembiayaan($anggota, 'PJ-CAIR-004');
        PencairanDana::create([
            'pembiayaan_id' => $pembiayaan->id,
            'nominal_pencairan' => 1000000,
            'bank_tujuan' => 'BSI',
            'no_rekening_tujuan' => '777888999',
            'nama_pemilik_rekening' => $anggota->name,
            'tanggal_pencairan' => now()->toDateString(),
            'status' => 'menunggu',
        ]);
        $rekening = RekeningKoperasi::create([
            'nama_bank' => 'Bank Syariah Indonesia',
            'nomor_rekening' => '1122334455',
            'atas_nama' => 'Koperasi Syariah',
            'is_aktif' => false,
        ]);

        $this->actingAs($bendahara)
            ->get(route('bendahara.transaksi.index'))
            ->assertOk()
            ->assertSee('Tambahkan atau aktifkan rekening koperasi')
            ->assertSee('Kelola Rekening');

        $this->actingAs($bendahara)
            ->patch(route('bendahara.transaksi.rekening.status', $rekening->id))
            ->assertRedirect();

        $this->assertDatabaseHas('rekening_koperasi', [
            'id' => $rekening->id,
            'is_aktif' => true,
        ]);
    }

    private function createPembiayaan(User $anggota, string $kode): Pembiayaan
    {
        return Pembiayaan::create([
            'user_id' => $anggota->id,
            'kode' => $kode,
            'akad' => 'murabahah',
            'objek_pembiayaan' => 'Modal usaha',
            'jumlah_pembiayaan' => 1000000,
            'tenor' => 12,
            'angsuran_bulanan' => 100000,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_persetujuan' => now()->toDateString(),
            'status' => 'disetujui',
        ]);
    }
}
