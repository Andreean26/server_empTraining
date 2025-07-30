<?php

namespace App\Http\Controllers;

use App\Models\TrainingEnrollment;
use App\Models\Training;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingEnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TrainingEnrollment::with(['training', 'employee']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by training
        if ($request->has('training_id') && $request->training_id !== '') {
            $query->where('training_id', $request->training_id);
        }

        // Filter by employee
        if ($request->has('employee_id') && $request->employee_id !== '') {
            $query->where('employee_id', $request->employee_id);
        }

        // Search by employee name or training title
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('training', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get filter options
        $trainings = Training::orderBy('title')->get();
        $employees = Employee::orderBy('name')->get();
        $statuses = ['enrolled', 'attended', 'completed', 'cancelled'];

        return view('enrollments.index', compact('enrollments', 'trainings', 'employees', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $trainings = Training::where('start_date', '>=', now())->orderBy('start_date')->get();
        $employees = Employee::orderBy('name')->get();
        
        return view('enrollments.create', compact('trainings', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'training_id' => 'required|exists:trainings,id',
            'employee_id' => 'required|exists:employees,id',
            'status' => 'required|in:enrolled,attended,completed,cancelled',
            'evaluation_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Check if enrollment already exists
        $existingEnrollment = TrainingEnrollment::where('training_id', $request->training_id)
                                              ->where('employee_id', $request->employee_id)
                                              ->first();

        if ($existingEnrollment) {
            return redirect()->back()
                           ->with('error', 'Employee is already enrolled in this training.')
                           ->withInput();
        }

        // Check training capacity
        $training = Training::findOrFail($request->training_id);
        $currentEnrollments = $training->trainingEnrollments()
                                     ->whereIn('status', ['enrolled', 'attended', 'completed'])
                                     ->count();

        if ($currentEnrollments >= $training->max_participants) {
            return redirect()->back()
                           ->with('error', 'Training has reached maximum capacity.')
                           ->withInput();
        }

        $enrollmentData = $request->only(['training_id', 'employee_id', 'status', 'evaluation_data']);
        $enrollmentData['enrolled_at'] = now();

        if ($request->status === 'attended') {
            $enrollmentData['attended_at'] = now();
        } elseif ($request->status === 'completed') {
            $enrollmentData['attended_at'] = now();
            $enrollmentData['completed_at'] = now();
        }

        TrainingEnrollment::create($enrollmentData);

        return redirect()->route('enrollments.index')
                        ->with('success', 'Enrollment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $enrollment = TrainingEnrollment::with(['training', 'employee'])->findOrFail($id);
        
        return view('enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $enrollment = TrainingEnrollment::findOrFail($id);
        $trainings = Training::orderBy('title')->get();
        $employees = Employee::orderBy('name')->get();
        
        return view('enrollments.edit', compact('enrollment', 'trainings', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $enrollment = TrainingEnrollment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:enrolled,attended,completed,cancelled',
            'evaluation_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $updateData = $request->only(['status', 'evaluation_data']);

        // Update timestamp based on status
        if ($request->status === 'attended' && !$enrollment->attended_at) {
            $updateData['attended_at'] = now();
        } elseif ($request->status === 'completed') {
            if (!$enrollment->attended_at) {
                $updateData['attended_at'] = now();
            }
            if (!$enrollment->completed_at) {
                $updateData['completed_at'] = now();
            }
        } elseif ($request->status === 'cancelled') {
            $updateData['attended_at'] = null;
            $updateData['completed_at'] = null;
        }

        $enrollment->update($updateData);

        return redirect()->route('enrollments.index')
                        ->with('success', 'Enrollment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $enrollment = TrainingEnrollment::findOrFail($id);
        
        $enrollment->delete();

        return redirect()->route('enrollments.index')
                        ->with('success', 'Enrollment deleted successfully.');
    }

    /**
     * Update enrollment status
     */
    public function updateStatus(Request $request, string $id)
    {
        $enrollment = TrainingEnrollment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:enrolled,attended,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator);
        }

        $updateData = ['status' => $request->status];

        // Update timestamps based on status
        if ($request->status === 'attended' && !$enrollment->attended_at) {
            $updateData['attended_at'] = now();
        } elseif ($request->status === 'completed') {
            if (!$enrollment->attended_at) {
                $updateData['attended_at'] = now();
            }
            if (!$enrollment->completed_at) {
                $updateData['completed_at'] = now();
            }
        } elseif ($request->status === 'cancelled') {
            $updateData['attended_at'] = null;
            $updateData['completed_at'] = null;
        }

        $enrollment->update($updateData);

        return redirect()->back()
                        ->with('success', 'Enrollment status updated successfully.');
    }

    /**
     * Bulk update enrollments
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:training_enrollments,id',
            'action' => 'required|in:mark_attended,mark_completed,mark_cancelled'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator);
        }

        $status = match($request->action) {
            'mark_attended' => 'attended',
            'mark_completed' => 'completed',
            'mark_cancelled' => 'cancelled',
            default => 'enrolled'
        };

        $updatedCount = 0;
        foreach ($request->enrollment_ids as $enrollmentId) {
            $enrollment = TrainingEnrollment::find($enrollmentId);
            if ($enrollment) {
                $updateData = ['status' => $status];

                if ($status === 'attended' && !$enrollment->attended_at) {
                    $updateData['attended_at'] = now();
                } elseif ($status === 'completed') {
                    if (!$enrollment->attended_at) {
                        $updateData['attended_at'] = now();
                    }
                    if (!$enrollment->completed_at) {
                        $updateData['completed_at'] = now();
                    }
                }

                $enrollment->update($updateData);
                $updatedCount++;
            }
        }

        return redirect()->back()
                        ->with('success', "{$updatedCount} enrollments updated successfully.");
    }

    /**
     * Export enrollments to CSV
     */
    public function export(Request $request)
    {
        $query = TrainingEnrollment::with(['training', 'employee']);

        // Apply same filters as index
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('training_id') && $request->training_id !== '') {
            $query->where('training_id', $request->training_id);
        }

        $enrollments = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="enrollments.csv"',
        ];

        $callback = function() use ($enrollments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Employee', 'Training', 'Status', 'Enrolled At', 'Attended At', 'Completed At']);

            foreach ($enrollments as $enrollment) {
                fputcsv($file, [
                    $enrollment->id,
                    $enrollment->employee->name,
                    $enrollment->training->title,
                    $enrollment->status,
                    $enrollment->enrolled_at,
                    $enrollment->attended_at,
                    $enrollment->completed_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
