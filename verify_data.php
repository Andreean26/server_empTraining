<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== EMPLOYEE TRAINING DATABASE DUMMY DATA VERIFICATION ===\n\n";

// Database Statistics
echo "📊 DATABASE STATISTICS:\n";
echo "Roles: " . App\Models\Role::count() . "\n";
echo "Users: " . App\Models\User::count() . "\n";
echo "Employees: " . App\Models\Employee::count() . "\n";
echo "Trainings: " . App\Models\Training::count() . "\n";
echo "Enrollments: " . App\Models\TrainingEnrollment::count() . "\n\n";

// Roles
echo "👥 ROLES:\n";
App\Models\Role::all()->each(function($role) {
    echo "- {$role->name}\n";
});

echo "\n👨‍💼 EMPLOYEES BY DEPARTMENT:\n";
$employees = App\Models\Employee::all()->groupBy('department');
foreach ($employees as $department => $empList) {
    echo "\n{$department}:\n";
    foreach ($empList as $emp) {
        echo "  - {$emp->name} ({$emp->position})\n";
    }
}

echo "\n📚 TRAINING SESSIONS:\n";
App\Models\Training::all()->each(function($training) {
    echo "- {$training->title} (by {$training->trainer_name})\n";
});

echo "\n📋 ENROLLMENT STATUS SUMMARY:\n";
$enrollments = App\Models\TrainingEnrollment::all()->groupBy('status');
foreach ($enrollments as $status => $enrollmentList) {
    echo "- {$status}: " . $enrollmentList->count() . " enrollments\n";
}

echo "\n✅ DUMMY DATA SUCCESSFULLY LOADED!\n";
echo "Total Records: " . (
    App\Models\Role::count() + 
    App\Models\User::count() + 
    App\Models\Employee::count() + 
    App\Models\Training::count() + 
    App\Models\TrainingEnrollment::count()
) . "\n\n";

echo "🚀 Ready for API testing at: http://localhost:8000/api-tester.html\n";
echo "Demo accounts available:\n";
echo "- admin@company.com / password123\n";
echo "- hr@company.com / password123\n";
echo "- supervisor@company.com / password123\n";
echo "- employee@company.com / password123\n";
