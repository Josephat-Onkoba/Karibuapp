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
            'name' => 'Josephat Onkoba',
            'email' => 'josephat.onkoba@zetech.ac.ke',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

    }
} 