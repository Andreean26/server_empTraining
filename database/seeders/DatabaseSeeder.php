<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in order due to foreign key dependencies
        $this->call([
            RoleSeeder::class,      // Must be first (no dependencies)
            UserSeeder::class,      // Depends on roles
            EmployeeSeeder::class,  // No dependencies on users/roles
            TrainingSeeder::class,  // Depends on users (created_by)
            TrainingEnrollmentSeeder::class, // Depends on trainings and employees
        ]);

        $this->command->info('🎉 All seeders completed successfully!');
        $this->command->info('📊 Database populated with:');
        $this->command->info('   - 4 Roles (Administrator, HR Manager, Supervisor, Employee)');
        $this->command->info('   - 4 Users with different roles');
        $this->command->info('   - 10 Employees across various departments');
        $this->command->info('   - 8 Training sessions');
        $this->command->info('   - Multiple training enrollments with different statuses');
    }
}
