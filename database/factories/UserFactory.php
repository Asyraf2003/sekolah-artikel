<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $randomSuffix = Str::lower(Str::random(10));

        return [
            'name'              => 'Other ' . $randomSuffix,
            'email'             => 'other_' . $randomSuffix . '@example.com', 
            'email_verified_at' => now(),
            'password'          => Hash::make('12345678'), 
            'remember_token'    => Str::random(10),
        ];
    }
}
