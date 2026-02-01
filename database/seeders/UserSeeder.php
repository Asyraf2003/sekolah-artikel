<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('12345678');

        // 1. Admin (Data Spesifik)
        User::create([
            'name'              => 'Super Admin',
            'email'             => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password'          => $password,
            'role'              => 'admin',
        ]);

        // 2. Other (14 Akun via Factory Native)
        User::factory()->count(14)->create([
            'password' => $password,
            'role'     => 'other',
        ]);

        User::factory()->count(1)->create([
            'password' => $password,
            'role'     => 'user',
        ]);
    }
}