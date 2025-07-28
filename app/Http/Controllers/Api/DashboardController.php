<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function index()
    {
        // Basic statistics
        $stats = [
            'total_employees' => Employee::count(),
            'total_trainings' => Training::count(),
            'total_enrollments' => TrainingEnrollment::count(),
            'total_users' => User::count(),
            'active_trainings' => Training::where('training_date', '>=', now()->format('Y-m-d'))->count(),
            'completed_trainings' => TrainingEnrollment::where('status', 'completed')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Dashboard statistics retrieved successfully',
            'data' => $stats
        ]);
    }

    /**
     * Get recent trainings
     */
    public function recentTrainings()
    {
        $recentTrainings = Training::with(['creator', 'enrollments'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Recent trainings retrieved successfully',
            'data' => $recentTrainings
        ]);
    }

    /**
     * Get upcoming trainings
     */
    public function upcomingTrainings()
    {
        $upcomingTrainings = Training::with(['enrollments.employee'])
            ->where('training_date', '>=', now()->format('Y-m-d'))
            ->orderBy('training_date', 'asc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Upcoming trainings retrieved successfully',
            'data' => $upcomingTrainings
        ]);
    }

    /**
     * Get training statistics by department
     */
    public function trainingsByDepartment()
    {
        $trainingsByDepartment = Employee::select('department', DB::raw('count(*) as total'))
            ->join('training_enrollments', 'employees.id', '=', 'training_enrollments.employee_id')
            ->whereNull('employees.deleted_at')
            ->whereNull('training_enrollments.deleted_at')
            ->groupBy('department')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Trainings by department retrieved successfully',
            'data' => $trainingsByDepartment
        ]);
    }

    /**
     * Get monthly completion trends
     */
    public function monthlyCompletions()
    {
        $monthlyCompletions = TrainingEnrollment::select(
                DB::raw('strftime("%Y", completed_at) as year'),
                DB::raw('strftime("%m", completed_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Monthly completion trends retrieved successfully',
            'data' => $monthlyCompletions
        ]);
    }

    /**
     * Get enrollment status distribution
     */
    public function enrollmentStatus()
    {
        $statusDistribution = TrainingEnrollment::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Enrollment status distribution retrieved successfully',
            'data' => $statusDistribution
        ]);
    }

    /**
     * Get top performing employees
     */
    public function topPerformers()
    {
        $topPerformers = Employee::select('employees.*', DB::raw('AVG(training_enrollments.completion_score) as avg_score'))
            ->join('training_enrollments', 'employees.id', '=', 'training_enrollments.employee_id')
            ->where('training_enrollments.status', 'completed')
            ->whereNotNull('training_enrollments.completion_score')
            ->groupBy('employees.id')
            ->orderBy('avg_score', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Top performers retrieved successfully',
            'data' => $topPerformers
        ]);
    }

    /**
     * Get training completion rates by month
     */
    public function completionRates()
    {
        $completionRates = DB::table('training_enrollments')
            ->select(
                DB::raw('strftime("%Y-%m", enrolled_at) as month'),
                DB::raw('COUNT(*) as total_enrolled'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as total_completed'),
                DB::raw('ROUND((SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 2) as completion_rate')
            )
            ->where('enrolled_at', '>=', now()->subMonths(12))
            ->whereNull('deleted_at')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Completion rates retrieved successfully',
            'data' => $completionRates
        ]);
    }
}
