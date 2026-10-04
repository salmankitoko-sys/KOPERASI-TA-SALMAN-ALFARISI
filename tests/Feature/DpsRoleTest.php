<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DpsRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_dps_can_open_dashboard_and_akad_validation(): void
    {
        $dps = User::factory()->create([
            'role' => User::ROLE_DPS,
        ]);

        $this->actingAs($dps);

        $this->get('/dps/dashboard')
            ->assertOk()
            ->assertSee('Panel pengawasan independen Koperasi Syariah.');

        $this->getJson('/dps/audit')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_non_dps_users_are_blocked_from_dps_area(): void
    {
        $anggota = User::factory()->create([
            'role' => User::ROLE_ANGGOTA,
        ]);

        $this->actingAs($anggota);

        $this->get('/dps/dashboard')
            ->assertForbidden();

        $this->getJson('/dps/audit')
            ->assertForbidden();
    }
}
