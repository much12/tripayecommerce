<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun admin default
        User::updateOrCreate(
            ['email' => 'admin@tripay.test'],
            [
                'name' => 'Admin Tripay',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        // Contoh akun pelanggan biasa
        User::updateOrCreate(
            ['email' => 'user@tripay.test'],
            [
                'name' => 'Pelanggan Contoh',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ],
        );

        $this->call(ProductSeeder::class);
    }
}
