<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'display_name' => 'Administrator',
                'description' => 'Full access to all system features',
                'is_active' => true,
                'permissions' => [
                    'users.create',
                    'users.read',
                    'users.update',
                    'users.delete',
                    'roles.create',
                    'roles.read',
                    'roles.update',
                    'roles.delete',
                    'employees.create',
                    'employees.read',
                    'employees.update',
                    'employees.delete',
                    'trainings.create',
                    'trainings.read',
                    'trainings.update',
                    'trainings.delete',
                    'enrollments.create',
                    'enrollments.read',
                    'enrollments.update',
                    'enrollments.delete',
                    'reports.read',
                    'export.data',
                    'import.data',
                ]
            ],
            [
                'name' => 'HR Manager',
                'display_name' => 'HR Manager',
                'description' => 'Manage employees and trainings',
                'is_active' => true,
                'permissions' => [
                    'employees.create',
                    'employees.read',
                    'employees.update',
                    'employees.delete',
                    'trainings.create',
                    'trainings.read',
                    'trainings.update',
                    'trainings.delete',
                    'enrollments.create',
                    'enrollments.read',
                    'enrollments.update',
                    'enrollments.delete',
                    'reports.read',
                    'export.data',
                    'import.data',
                ]
            ],
            [
                'name' => 'Employee',
                'display_name' => 'Employee',
                'description' => 'View trainings and enrollments',
                'is_active' => true,
                'permissions' => [
                    'trainings.read',
                    'enrollments.read',
                ]
            ],
            [
                'name' => 'Trainer',
                'display_name' => 'Trainer',
                'description' => 'Manage assigned trainings',
                'is_active' => true,
                'permissions' => [
                    'trainings.read',
                    'trainings.update',
                    'enrollments.read',
                    'enrollments.update',
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}
