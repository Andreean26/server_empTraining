<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'name' => 'Ahmad Pratama',
                'email' => 'ahmad.pratama@company.com',
                'phone' => '081234567890',
                'department' => 'IT',
                'position' => 'Software Developer',
                'hire_date' => '2023-01-15',
                'is_active' => true,
                'metadata' => [
                    'skills' => ['PHP', 'Laravel', 'Vue.js'],
                    'education' => 'S1 Teknik Informatika',
                    'emergency_contact' => '081234567891'
                ]
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@company.com',
                'phone' => '081234567892',
                'department' => 'HR',
                'position' => 'HR Specialist',
                'hire_date' => '2022-05-20',
                'is_active' => true,
                'metadata' => [
                    'skills' => ['Recruitment', 'Training', 'Performance Management'],
                    'education' => 'S1 Psikologi',
                    'emergency_contact' => '081234567893'
                ]
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@company.com',
                'phone' => '081234567894',
                'department' => 'Finance',
                'position' => 'Accountant',
                'hire_date' => '2021-09-10',
                'is_active' => true,
                'metadata' => [
                    'skills' => ['Accounting', 'Tax', 'Financial Analysis'],
                    'education' => 'S1 Akuntansi',
                    'emergency_contact' => '081234567895'
                ]
            ],
            [
                'name' => 'Maya Dewi',
                'email' => 'maya.dewi@company.com',
                'phone' => '081234567896',
                'department' => 'Marketing',
                'position' => 'Marketing Executive',
                'hire_date' => '2023-03-01',
                'is_active' => true,
                'metadata' => [
                    'skills' => ['Digital Marketing', 'Content Creation', 'Social Media'],
                    'education' => 'S1 Komunikasi',
                    'emergency_contact' => '081234567897'
                ]
            ],
            [
                'name' => 'Rendi Kurniawan',
                'email' => 'rendi.kurniawan@company.com',
                'phone' => '081234567898',
                'department' => 'IT',
                'position' => 'System Administrator',
                'hire_date' => '2022-11-15',
                'is_active' => true,
                'metadata' => [
                    'skills' => ['Linux', 'Network', 'Docker'],
                    'education' => 'S1 Sistem Informasi',
                    'emergency_contact' => '081234567899'
                ]
            ]
        ];

        foreach ($employees as $employeeData) {
            Employee::updateOrCreate(
                ['email' => $employeeData['email']],
                $employeeData
            );
        }
    }
}
