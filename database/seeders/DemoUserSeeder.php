<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'timezone' => 'Europe/Amsterdam',
                'challenge_start_date' => now()->subDays(10)->toDateString(),
                'is_admin' => true,
                'is_public' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'username' => 'demo',
                'password' => Hash::make('password'),
                'timezone' => 'Europe/Amsterdam',
                'challenge_start_date' => now()->subDays(5)->toDateString(),
            ]
        );
    }
}
