<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'nasrul',
            'email' => 'dwi@example.com',
            'password' => Hash::make('123'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('123'),
            'role' => 'user',
        ]);
    }
}
