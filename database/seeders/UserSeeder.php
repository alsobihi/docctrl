<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // This will find the user with this email, or create them if they don't exist.
        User::updateOrCreate(
            ['email' => 'alsobihi.ai@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('1'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
