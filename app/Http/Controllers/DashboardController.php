<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Basic statistics
        $stats = [
            'total_employees' => Employee::count(),
            'total_trainings' => Training::count(),
            'total_enrollments' => TrainingEnrollment::count(),
            'total_users' => User::count(),
            'active_trainings' => Training::where('training_date', '>=', now()->format('Y-m-d'))->count(),
            'completed_trainings' => TrainingEnrollment::where('status', 'completed')->count(),
        ];

        // Recent trainings
        $recentTrainings = Training::with(['creator', 'enrollments'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Upcoming trainings
        $upcomingTrainings = Training::with(['enrollments.employee'])
            ->where('training_date', '>=', now()->format('Y-m-d'))
            ->orderBy('training_date', 'asc')
            ->take(5)
            ->get();

        // Training by department (for charts)
        $trainingsByDepartment = Employee::select('department', DB::raw('count(*) as total'))
            ->join('training_enrollments', 'employees.id', '=', 'training_enrollments.employee_id')
            ->whereNull('employees.deleted_at')
            ->whereNull('training_enrollments.deleted_at')
            ->groupBy('department')
            ->get();

        // Monthly training completion trend (MySQL compatible)
        $monthlyCompletions = TrainingEnrollment::select(
                DB::raw('YEAR(completed_at) as year'),
                DB::raw('MONTH(completed_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('dashboard', compact(
            'user',
            'stats',
            'recentTrainings',
            'upcomingTrainings',
            'trainingsByDepartment',
            'monthlyCompletions'
        ));
    }
}
