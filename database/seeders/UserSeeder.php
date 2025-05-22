<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@karibu.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create usher user
        User::create([
            'name' => 'Usher User',
            'email' => 'usher@karibu.com',
            'password' => Hash::make('password123'),
            'role' => 'usher',
        ]);
    }
} 