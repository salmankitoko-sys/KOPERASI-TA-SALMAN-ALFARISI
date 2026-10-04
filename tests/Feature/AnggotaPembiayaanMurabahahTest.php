<?php

namespace Tests\Feature;

use App\Models\Angsuran;
use App\Models\DetailAkad;
use App\Models\Pembiayaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnggotaPembiayaanMurabahahTest extends TestCase
{
    use RefreshDatabase;

    public function test_murabahah_submission_calculates_without_down_payment_or_administration_fee(): void
    {
        Storage::fake('public');
        $anggota = User::factory()->create(['role' => 'anggota']);

        $this->actingAs($anggota)
            ->post(route('anggota.pembiayaan.store'), [
                'jenis_akad' => 'Murabahah',
                'tujuan_pembiayaan' => 'kebutuhan_lainnya',
                'tenor_final' => 2,
                'total_kewajiban' => 1,
                'nilai_utama' => 1,
                'm_harga_beli' => 1000000,
                'm_margin_persen' => 10,
                'm_tenor' => 2,
                'm_objek' => 'Laptop untuk usaha',
                'm_dp' => 900000,
                'm_admin' => 50000,
                'bank_tujuan' => 'Bank Syariah Indonesia',
                'no_rekening_tujuan' => '1234567890',
                'nama_pemilik_rekening' => $anggota->name,
                'tanggal_pencairan_diharapkan' => now()->toDateString(),
                'jadwal_json' => json_encode([[1, 1, 0, 1, 0]]),
                'formulir_pengajuan' => UploadedFile::fake()->create('formulir.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('anggota.dashboard'))
            ->assertSessionHas('success');

        $pembiayaan = Pembiayaan::firstOrFail();
        $detail = DetailAkad::where('pembiayaan_id', $pembiayaan->id)->firstOrFail();

        $this->assertSame(1000000.0, (float) $pembiayaan->jumlah_pembiayaan);
        $this->assertSame(1100000.0, (float) $detail->harga_jual);
        $this->assertSame(0.0, (float) $detail->dp);
        $this->assertSame(0.0, (float) $detail->biaya_admin);
        $this->assertSame(550000.0, (float) $pembiayaan->angsuran_bulanan);
        $this->assertSame(
            [550000.0, 550000.0],
            Angsuran::where('pembiayaan_id', $pembiayaan->id)
                ->orderBy('bulan_ke')
                ->pluck('jumlah_bayar')
                ->map(fn ($amount) => (float) $amount)
                ->all()
        );
    }

    public function test_anggota_angsuran_response_keeps_each_financing_schedule_separate(): void
    {
        $anggota = User::factory()->create(['role' => 'anggota']);
        $murabahah = $this->createFinancing($anggota, 'PBJ-MUR-001', 'murabahah');
        $ijarah = $this->createFinancing($anggota, 'PBJ-IJ-001', 'ijarah');
        $menungguPencairan = $this->createFinancing($anggota, 'PBJ-MUR-002', 'murabahah', 'disetujui');
        $this->createInstallment($murabahah, 1);
        $this->createInstallment($ijarah, 1);
        $this->createInstallment($menungguPencairan, 1);

        $response = $this->actingAs($anggota)
            ->getJson(route('anggota.angsuran.index', ['ajax' => 1]))
            ->assertOk()
            ->assertJsonCount(3, 'pembiayaanList')
            ->assertJsonPath('pagination.total', 3)
            ->assertJsonPath('stats.total', 3);

        $schedules = collect($response->json('pembiayaanList'))->keyBy('kode');
        $this->assertSame('Murabahah', $schedules['PBJ-MUR-001']['akad']);
        $this->assertSame($murabahah->id, $schedules['PBJ-MUR-001']['angsuran'][0]['pembiayaan_id']);
        $this->assertSame('Ijarah', $schedules['PBJ-IJ-001']['akad']);
        $this->assertSame($ijarah->id, $schedules['PBJ-IJ-001']['angsuran'][0]['pembiayaan_id']);
        $this->assertSame('disetujui', $schedules['PBJ-MUR-002']['status']);
        $this->assertFalse($schedules['PBJ-MUR-002']['dapat_dibayar']);
        $this->assertTrue($schedules['PBJ-MUR-001']['dapat_dibayar']);
    }

    public function test_anggota_dashboard_lists_active_financing_instead_of_empty_placeholder(): void
    {
        $anggota = User::factory()->create(['role' => 'anggota']);
        $pembiayaan = $this->createFinancing($anggota, 'PBJ-DASH-001', 'murabahah');
        $this->createInstallment($pembiayaan, 1);

        $this->actingAs($anggota)
            ->get(route('anggota.dashboard'))
            ->assertOk()
            ->assertSee('PBJ-DASH-001')
            ->assertSee('Murabahah')
            ->assertSee('1 akad aktif');
    }

    private function createFinancing(User $anggota, string $kode, string $akad, string $status = 'berjalan'): Pembiayaan
    {
        return Pembiayaan::create([
            'user_id' => $anggota->id,
            'kode' => $kode,
            'akad' => $akad,
            'objek_pembiayaan' => 'Objek '.$akad,
            'jumlah_pembiayaan' => 1000000,
            'tenor' => 2,
            'angsuran_bulanan' => 500000,
            'tanggal_pengajuan' => now()->toDateString(),
            'status' => $status,
        ]);
    }

    private function createInstallment(Pembiayaan $pembiayaan, int $bulan): void
    {
        Angsuran::create([
            'pembiayaan_id' => $pembiayaan->id,
            'bulan_ke' => $bulan,
            'jatuh_tempo' => now()->addMonth()->toDateString(),
            'jumlah_bayar' => 500000,
            'pokok' => 450000,
            'margin' => 50000,
            'sisa_pokok' => 500000,
            'status' => 'belum_bayar',
        ]);
    }
}
