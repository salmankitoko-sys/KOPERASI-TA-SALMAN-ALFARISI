<?php

namespace Tests\Feature;

use App\Models\DetailAkad;
use App\Models\Pembiayaan;
use App\Models\User;
use App\Models\ValidasiAkadDps;
use App\Support\AkadSyariahChecklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AutomaticDisbursementHandoffTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengurus_approval_automatically_hands_disbursement_to_bendahara(): void
    {
        Mail::fake();

        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        $pengurus = User::factory()->create(['role' => User::ROLE_PENGURUS]);
        $bendahara = User::factory()->create(['role' => User::ROLE_BENDAHARA]);

        $pembiayaan = Pembiayaan::create([
            'user_id' => $anggota->id,
            'kode' => 'PBJ-AUTO-001',
            'akad' => 'murabahah',
            'objek_pembiayaan' => 'Peralatan usaha',
            'jumlah_pembiayaan' => 1200000,
            'tenor' => 12,
            'angsuran_bulanan' => 110000,
            'tanggal_pengajuan' => today()->toDateString(),
            'bank_tujuan' => 'Bank Syariah Indonesia',
            'no_rekening_tujuan' => '7123456789',
            'nama_pemilik_rekening' => $anggota->name,
            'tanggal_pencairan_diharapkan' => today()->addDay()->toDateString(),
            'status' => 'diajukan',
            'status_validasi_dps' => 'sesuai',
        ]);

        DetailAkad::create([
            'pembiayaan_id' => $pembiayaan->id,
            'objek' => 'Peralatan usaha',
            'tenor' => 12,
            'harga_beli' => 1200000,
            'harga_jual' => 1320000,
            'margin_persen' => 10,
            'margin' => 120000,
            'dp' => 0,
            'angsuran_bulanan' => 110000,
        ]);

        $pembiayaan = $pembiayaan->fresh(['detail', 'dokumen', 'angsuran']);
        $snapshot = AkadSyariahChecklist::snapshot($pembiayaan);
        ValidasiAkadDps::create([
            'pembiayaan_id' => $pembiayaan->id,
            'versi' => 1,
            'hasil' => 'sesuai',
            'checklist' => [],
            'snapshot_data' => $snapshot,
            'snapshot_hash' => AkadSyariahChecklist::hash($pembiayaan),
            'kesimpulan' => 'Akad sesuai syariah.',
            'divalidasi_pada' => now(),
        ]);

        $this->actingAs($pengurus)
            ->postJson(route('pengurus.pembiayaan.approve', $pembiayaan))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pencairan_dana', [
            'pembiayaan_id' => $pembiayaan->id,
            'nominal_pencairan' => 1200000,
            'bank_tujuan' => 'Bank Syariah Indonesia',
            'no_rekening_tujuan' => '7123456789',
            'status' => 'menunggu',
        ]);

        $this->assertDatabaseHas('inbox_entries', [
            'user_id' => $bendahara->id,
            'title' => 'Permintaan Pencairan Dana Baru',
        ]);

        $this->assertDatabaseCount('pencairan_dana', 1);
    }
}