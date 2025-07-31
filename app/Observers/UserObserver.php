<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Auto-create employee record when user registers with Employee role
     */
    public function created(User $user): void
    {
        // Check if user has Employee role
        $employeeRole = Role::where('name', 'Employee')->first();
        
        if ($user->role_id && $user->role_id === $employeeRole?->id) {
            $this->createEmployeeRecord($user);
        }
    }

    /**
     * Handle the User "updated" event.
     * Create employee record if role changed to Employee
     */
    public function updated(User $user): void
    {
        // Check if role was changed to Employee
        if ($user->wasChanged('role_id')) {
            $employeeRole = Role::where('name', 'Employee')->first();
            
            if ($user->role_id === $employeeRole?->id) {
                // Check if employee record doesn't exist yet
                $existingEmployee = Employee::where('user_id', $user->id)->first();
                
                if (!$existingEmployee) {
                    $this->createEmployeeRecord($user);
                }
            }
        }
    }

    /**
     * Create employee record from user data
     */
    private function createEmployeeRecord(User $user): void
    {
        // Generate employee_id format: EMP + timestamp
        $employeeId = 'EMP' . now()->format('YmdHis');
        
        // Ensure employee_id is unique
        while (Employee::where('employee_id', $employeeId)->exists()) {
            $employeeId = 'EMP' . now()->format('YmdHis') . rand(10, 99);
        }

        Employee::create([
            'user_id' => $user->id,
            'employee_id' => $employeeId,
            'name' => $user->name,
            'email' => $user->email,
            'department' => 'General', // Default department
            'position' => 'Employee', // Default position
            'hire_date' => now(),
            'is_active' => true,
        ]);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Optionally soft delete related employee record
        Employee::where('user_id', $user->id)->delete();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // Restore employee record if it was soft deleted
        Employee::withTrashed()->where('user_id', $user->id)->restore();
    }
}
