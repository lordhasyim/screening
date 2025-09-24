<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create default admin users
        $adminUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@team.um.ac.id',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'is_active' => true,
            ],
            [
                'name' => 'Admin team',
                'email' => 'admin@team.um.ac.id',
                'password' => Hash::make('team2024'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Tim Skrining 1',
                'email' => 'skrining1@team.um.ac.id',
                'password' => Hash::make('skrining123'),
                'role' => 'viewer',
                'is_active' => true,
            ],
            [
                'name' => 'Tim Skrining 2',
                'email' => 'skrining2@team.um.ac.id',
                'password' => Hash::make('skrining123'),
                'role' => 'viewer',
                'is_active' => true,
            ],
        ];

        foreach ($adminUsers as $userData) {
            AdminUser::firstOrCreate(
                ['email' => $userData['email']], // Check by email
                $userData // Create with all data if not exists
            );
        }
    }
}