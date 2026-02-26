<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'conan@gmail.com'],
            [
                'name' => 'Conan',
                'password' => Hash::make('12345678'),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'emilly@gmail.com'],
            [
                'name' => 'Emilly',
                'password' => Hash::make('12345678'),
            ]
        );
    }
}
