<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'Administrator')->first();
        $hrRole = Role::where('name', 'HR Manager')->first();
        $employeeRole = Role::where('name', 'Employee')->first();

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@training.com',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@training.com',
                'password' => Hash::make('password123'),
                'role_id' => $hrRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@training.com',
                'password' => Hash::make('password123'),
                'role_id' => $employeeRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@training.com',
                'password' => Hash::make('password123'),
                'role_id' => $employeeRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
