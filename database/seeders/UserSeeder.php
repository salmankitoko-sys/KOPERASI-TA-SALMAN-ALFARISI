<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the default accounts for each role.
     * Uses firstOrCreate so it's safe to run multiple times.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Ketua Koperasi',
                'email' => 'ketua@koperasi.com',
                'role' => User::ROLE_KETUA,
                'is_active' => true,
            ],
            [
                'name' => 'Admin Utama',
                'email' => 'admin@koperasi.test',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ],
            [
                'name' => 'Pengurus Satu',
                'email' => 'pengurus@koperasi.com',
                'role' => User::ROLE_PENGURUS,
                'is_active' => true,
            ],
            [
                'name' => 'Bendahara Satu',
                'email' => 'bendahara@koperasi.com',
                'role' => User::ROLE_BENDAHARA,
                'is_active' => true,
            ],

            [
                'name' => 'Anggota Satu',
                'email' => 'anggota@koperasi.com',
                'role' => User::ROLE_ANGGOTA,
                'is_active' => true,
                'no_hp' => '081234567890',
                'tgl_gabung' => now()->toDateString(),
            ],
            [
                'name' => 'Pelanggan Satu',
                'email' => 'pelanggan@koperasi.com',
                'role' => User::ROLE_PELANGGAN,
                'is_active' => true,
            ],
            [
                'name' => 'DPS Satu',
                'email' => 'dps@koperasi.com',
                'role' => User::ROLE_DPS,
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            User::firstOrCreate(
                ['email' => $account['email']],
                array_merge($account, [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]),
            );
        }
    }
}
