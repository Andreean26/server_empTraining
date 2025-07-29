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
                'name' => 'John Doe',
                'email' => 'john.doe@company.com',
                'phone' => '+1234567890',
                'department' => 'IT',
                'position' => 'Software Engineer',
                'hire_date' => '2023-01-15',
                'metadata' => json_encode([
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'emergency_contact' => 'Jane Doe',
                    'emergency_phone' => '+0987654321',
                    'blood_type' => 'O+',
                    'address' => '123 Main St, Jakarta'
                ])
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@company.com',
                'phone' => '+1234567891',
                'department' => 'HR',
                'position' => 'HR Specialist',
                'hire_date' => '2023-02-20',
                'metadata' => json_encode([
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'emergency_contact' => 'Mike Johnson',
                    'emergency_phone' => '+0987654322',
                    'blood_type' => 'A+',
                    'address' => '456 Oak Ave, Jakarta'
                ])
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@company.com',
                'phone' => '+1234567892',
                'department' => 'Finance',
                'position' => 'Financial Analyst',
                'hire_date' => '2023-03-10',
                'metadata' => json_encode([
                    'first_name' => 'Michael',
                    'last_name' => 'Brown',
                    'emergency_contact' => 'Lisa Brown',
                    'emergency_phone' => '+0987654323',
                    'blood_type' => 'B+',
                    'address' => '789 Pine St, Jakarta'
                ])
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@company.com',
                'phone' => '+1234567893',
                'department' => 'Marketing',
                'position' => 'Marketing Manager',
                'hire_date' => '2023-04-05',
                'metadata' => json_encode([
                    'first_name' => 'Emily',
                    'last_name' => 'Davis',
                    'emergency_contact' => 'Robert Davis',
                    'emergency_phone' => '+0987654324',
                    'blood_type' => 'AB+',
                    'address' => '321 Elm Rd, Jakarta'
                ])
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@company.com',
                'phone' => '+1234567894',
                'department' => 'IT',
                'position' => 'DevOps Engineer',
                'hire_date' => '2023-05-12',
                'metadata' => json_encode([
                    'first_name' => 'David',
                    'last_name' => 'Wilson',
                    'emergency_contact' => 'Anna Wilson',
                    'emergency_phone' => '+0987654325',
                    'blood_type' => 'O-',
                    'address' => '654 Cedar Ln, Jakarta'
                ])
            ],
            [
                'name' => 'Jessica Garcia',
                'email' => 'jessica.garcia@company.com',
                'phone' => '+1234567895',
                'department' => 'Sales',
                'position' => 'Sales Representative',
                'hire_date' => '2023-06-18',
                'metadata' => json_encode([
                    'first_name' => 'Jessica',
                    'last_name' => 'Garcia',
                    'emergency_contact' => 'Carlos Garcia',
                    'emergency_phone' => '+0987654326',
                    'blood_type' => 'A-',
                    'address' => '987 Maple Ave, Jakarta'
                ])
            ],
            [
                'name' => 'Daniel Martinez',
                'email' => 'daniel.martinez@company.com',
                'phone' => '+1234567896',
                'department' => 'Operations',
                'position' => 'Operations Manager',
                'hire_date' => '2023-07-22',
                'metadata' => json_encode([
                    'first_name' => 'Daniel',
                    'last_name' => 'Martinez',
                    'emergency_contact' => 'Maria Martinez',
                    'emergency_phone' => '+0987654327',
                    'blood_type' => 'B-',
                    'address' => '147 Birch St, Jakarta'
                ])
            ],
            [
                'name' => 'Amanda Anderson',
                'email' => 'amanda.anderson@company.com',
                'phone' => '+1234567897',
                'department' => 'HR',
                'position' => 'HR Manager',
                'hire_date' => '2023-08-14',
                'metadata' => json_encode([
                    'first_name' => 'Amanda',
                    'last_name' => 'Anderson',
                    'emergency_contact' => 'James Anderson',
                    'emergency_phone' => '+0987654328',
                    'blood_type' => 'AB-',
                    'address' => '258 Willow Dr, Jakarta'
                ])
            ],
            [
                'name' => 'Christopher Taylor',
                'email' => 'christopher.taylor@company.com',
                'phone' => '+1234567898',
                'department' => 'IT',
                'position' => 'Senior Developer',
                'hire_date' => '2023-09-01',
                'metadata' => json_encode([
                    'first_name' => 'Christopher',
                    'last_name' => 'Taylor',
                    'emergency_contact' => 'Laura Taylor',
                    'emergency_phone' => '+0987654329',
                    'blood_type' => 'O+',
                    'address' => '369 Spruce Ave, Jakarta'
                ])
            ],
            [
                'name' => 'Nicole Thomas',
                'email' => 'nicole.thomas@company.com',
                'phone' => '+1234567899',
                'department' => 'Finance',
                'position' => 'Senior Accountant',
                'hire_date' => '2023-10-11',
                'metadata' => json_encode([
                    'first_name' => 'Nicole',
                    'last_name' => 'Thomas',
                    'emergency_contact' => 'Kevin Thomas',
                    'emergency_phone' => '+0987654330',
                    'blood_type' => 'A+',
                    'address' => '741 Poplar Rd, Jakarta'
                ])
            ]
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
