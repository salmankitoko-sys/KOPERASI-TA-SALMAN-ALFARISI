<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'no_hp' => '08123456789',
            'tanggal_lahir' => '1990-01-01',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('anggota.dashboard', absolute: false));
    }

    public function test_new_customers_can_register_for_marketplace_only(): void
    {
        $response = $this->post('/register', [
            'account_type' => User::ROLE_PELANGGAN,
            'name' => 'Marketplace Customer',
            'no_hp' => '08123456780',
            'email' => 'customer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pelanggan.dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'role' => User::ROLE_PELANGGAN,
        ]);
    }

    public function test_marketplace_customer_cannot_access_anggota_dashboard(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_PELANGGAN]);

        $this->actingAs($customer)
            ->get(route('pelanggan.dashboard'))
            ->assertOk();

        $this->actingAs($customer)
            ->get(route('anggota.dashboard'))
            ->assertForbidden();
    }
}
