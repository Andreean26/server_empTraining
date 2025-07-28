<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingEnrollment;
use App\Models\Training;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingEnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments.
     */
    public function index(Request $request)
    {
        $query = TrainingEnrollment::with(['training', 'employee']);

        // Filter by training
        if ($request->has('training_id')) {
            $query->where('training_id', $request->training_id);
        }

        // Filter by employee
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $enrollments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Training enrollments retrieved successfully',
            'data' => $enrollments
        ]);
    }

    /**
     * Enroll employee in training.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'training_id' => 'required|exists:trainings,id',
            'employee_id' => 'required|exists:employees,id',
            'status' => 'in:enrolled,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if employee is already enrolled
        $existingEnrollment = TrainingEnrollment::where('training_id', $request->training_id)
            ->where('employee_id', $request->employee_id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Employee is already enrolled in this training'
            ], 409);
        }

        // Check training capacity
        $training = Training::find($request->training_id);
        $currentEnrollments = TrainingEnrollment::where('training_id', $request->training_id)
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($currentEnrollments >= $training->max_participants) {
            return response()->json([
                'success' => false,
                'message' => 'Training is at full capacity'
            ], 400);
        }

        $enrollment = TrainingEnrollment::create([
            'training_id' => $request->training_id,
            'employee_id' => $request->employee_id,
            'status' => $request->status ?? 'enrolled',
            'enrolled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee enrolled successfully',
            'data' => $enrollment->load(['training', 'employee'])
        ], 201);
    }

    /**
     * Display the specified enrollment.
     */
    public function show($id)
    {
        $enrollment = TrainingEnrollment::with(['training', 'employee'])->find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Enrollment retrieved successfully',
            'data' => $enrollment
        ]);
    }

    /**
     * Update enrollment status.
     */
    public function update(Request $request, $id)
    {
        $enrollment = TrainingEnrollment::find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:enrolled,completed,cancelled',
            'completion_score' => 'nullable|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['status', 'completion_score', 'feedback']);

        // Set completion date if status is completed
        if ($request->status === 'completed' && $enrollment->status !== 'completed') {
            $data['completed_at'] = now();
        }

        $enrollment->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Enrollment updated successfully',
            'data' => $enrollment->load(['training', 'employee'])
        ]);
    }

    /**
     * Cancel enrollment.
     */
    public function destroy($id)
    {
        $enrollment = TrainingEnrollment::find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found'
            ], 404);
        }

        $enrollment->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Enrollment cancelled successfully'
        ]);
    }

    /**
     * Get enrollments by training
     */
    public function byTraining($trainingId)
    {
        $training = Training::find($trainingId);

        if (!$training) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found'
            ], 404);
        }

        $enrollments = TrainingEnrollment::with('employee')
            ->where('training_id', $trainingId)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Training enrollments retrieved successfully',
            'data' => [
                'training' => $training,
                'enrollments' => $enrollments,
                'statistics' => [
                    'total_enrolled' => $enrollments->where('status', 'enrolled')->count(),
                    'total_completed' => $enrollments->where('status', 'completed')->count(),
                    'total_cancelled' => $enrollments->where('status', 'cancelled')->count(),
                    'available_slots' => $training->max_participants - $enrollments->where('status', '!=', 'cancelled')->count(),
                ]
            ]
        ]);
    }

    /**
     * Get enrollments by employee
     */
    public function byEmployee($employeeId)
    {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $enrollments = TrainingEnrollment::with('training')
            ->where('employee_id', $employeeId)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Employee enrollments retrieved successfully',
            'data' => [
                'employee' => $employee,
                'enrollments' => $enrollments,
                'statistics' => [
                    'total_enrolled' => $enrollments->where('status', 'enrolled')->count(),
                    'total_completed' => $enrollments->where('status', 'completed')->count(),
                    'total_cancelled' => $enrollments->where('status', 'cancelled')->count(),
                    'completion_rate' => $enrollments->count() > 0 ? 
                        round(($enrollments->where('status', 'completed')->count() / $enrollments->count()) * 100, 2) : 0,
                ]
            ]
        ]);
    }
}
