<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DpsRoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dps_user_can_access_the_dps_dashboard(): void
    {
        $user = User::factory()->dps()->create();

        $response = $this->actingAs($user)->get('/dps/dashboard');

        $response
            ->assertOk()
            ->assertDontSee('class="border-b border-gray-200 bg-white"', false);
    }

    public function test_non_dps_user_cannot_access_the_dps_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ANGGOTA,
        ]);

        $response = $this->actingAs($user)->get('/dps/dashboard');

        $response->assertForbidden();
    }

    public function test_dps_user_can_view_notifications_and_regular_user_is_forbidden(): void
    {
        $dpsUser = User::factory()->dps()->create();

        $dpsResponse = $this->actingAs($dpsUser)->get('/dps/notifikasi');

        $dpsResponse->assertOk();
        $dpsResponse->assertJsonPath('success', true);

        $member = User::factory()->create([
            'role' => User::ROLE_ANGGOTA,
        ]);

        $memberResponse = $this->actingAs($member)->get('/dps/notifikasi');

        $memberResponse->assertForbidden();
    }

    public function test_removed_dps_features_are_no_longer_routable(): void
    {
        $dpsUser = User::factory()->dps()->create();

        foreach (['/dps/pengawasan', '/dps/produk', '/dps/temuan', '/dps/opini'] as $uri) {
            $this->actingAs($dpsUser)->get($uri)->assertNotFound();
        }
    }}
