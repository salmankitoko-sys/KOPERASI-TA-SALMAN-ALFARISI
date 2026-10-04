<?php

namespace Tests\Feature;

use App\Models\Pembiayaan;
use App\Models\User;
use App\Models\ValidasiAkadDps;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DpsAutomaticReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Mail::fake();
    }

    public function test_dps_generates_pdf_snapshot_and_sends_it_to_chairperson(): void
    {
        $dps = User::factory()->dps()->create();
        $ketua = User::factory()->create(['role' => User::ROLE_KETUA]);
        $anggota = User::factory()->create(['role' => User::ROLE_ANGGOTA]);
        $pembiayaan = Pembiayaan::create([
            'user_id' => $anggota->id, 'kode' => 'AKAD-LAP-001', 'akad' => 'murabahah',
            'jumlah_pembiayaan' => 5000000, 'tenor' => 10, 'angsuran_bulanan' => 550000,
            'tanggal_pengajuan' => today(), 'status' => 'diajukan',
        ]);
        ValidasiAkadDps::create([
            'pembiayaan_id' => $pembiayaan->id, 'versi' => 1, 'hasil' => 'perlu_perbaikan',
            'checklist' => [], 'snapshot_data' => [], 'snapshot_hash' => str_repeat('a', 64),
            'kesimpulan' => 'Dokumen perlu dilengkapi.', 'catatan_perbaikan' => 'Lengkapi bukti harga barang.',
            'divalidasi_oleh' => $dps->id, 'divalidasi_pada' => now(),
        ]);

        $response = $this->actingAs($dps)->postJson(route('dps.laporan.generate'), [
            'periode' => 'Semester', 'semester' => now()->year.'-1',
            'periode_mulai' => today()->subMonth()->toDateString(),
            'periode_selesai' => today()->toDateString(),
        ]);

        $response->assertCreated()->assertJsonPath('success', true);
        $laporanId = $response->json('data.id');
        $this->assertDatabaseHas('laporan_pengawasan', ['id' => $laporanId, 'status' => 'Terbit']);
        $this->assertDatabaseHas('inbox_entries', ['user_id' => $ketua->id, 'title' => 'Laporan Pengawasan DPS']);
        Storage::disk('public')->assertExists('laporan-pengawasan/laporan-dps-'.$laporanId.'.pdf');

        $this->actingAs($ketua)->get(route('laporan-pengawasan.pdf', $laporanId))
            ->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_non_dps_cannot_generate_report(): void
    {
        $ketua = User::factory()->create(['role' => User::ROLE_KETUA]);
        $this->actingAs($ketua)->postJson(route('dps.laporan.generate'), [])->assertForbidden();
    }
}