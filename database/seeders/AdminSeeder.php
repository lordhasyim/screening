<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create default admin users
        $adminUsers = [
            [
                'name' => 'Super Admin',
                'nip' => '12345678',
                'email' => 'superadmin@team.um.ac.id',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'is_active' => true,
            ],
            [
                'name' => 'Admin team',
                'nip' => '12345679',
                'email' => 'admin@team.um.ac.id',
                'password' => Hash::make('team2024'),
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Tim Skrining 1',
                'nip' => '12345671',
                'email' => 'skrining1@team.um.ac.id',
                'password' => Hash::make('skrining123'),
                'role' => 'viewer',
                'is_active' => true,
            ],
            [
                'name' => 'Tim Skrining 2',
                'nip' => '123456711',
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
