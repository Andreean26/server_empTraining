<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TrainingEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with('trainingEnrollments.training');

        // Filter by department
        if ($request->has('department') && $request->department !== '') {
            $query->where('department', $request->department);
        }

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate(15);
        $departments = Employee::distinct()->pluck('department');

        return view('employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Employee::distinct()->pluck('department');
        return view('employees.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'hire_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        Employee::create($request->all());

        return redirect()->route('employees.index')
                        ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['trainingEnrollments.training'])->findOrFail($id);
        
        // Get enrollment statistics
        $enrollmentStats = [
            'total' => $employee->trainingEnrollments->count(),
            'completed' => $employee->trainingEnrollments->where('status', 'completed')->count(),
            'enrolled' => $employee->trainingEnrollments->where('status', 'enrolled')->count(),
            'attended' => $employee->trainingEnrollments->where('status', 'attended')->count(),
            'cancelled' => $employee->trainingEnrollments->where('status', 'cancelled')->count(),
        ];

        return view('employees.show', compact('employee', 'enrollmentStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);
        $departments = Employee::distinct()->pluck('department');
        
        return view('employees.edit', compact('employee', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'department' => 'required|string|max:100',
            'position' => 'required|string|max:100',
            'hire_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $employee->update($request->all());

        return redirect()->route('employees.index')
                        ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        
        // Check if employee has training enrollments
        if ($employee->trainingEnrollments->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete employee with training enrollments.');
        }

        $employee->delete();

        return redirect()->route('employees.index')
                        ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Show employee training history
     */
    public function trainingHistory(string $id)
    {
        $employee = Employee::with(['trainingEnrollments.training'])->findOrFail($id);
        
        return view('employees.training-history', compact('employee'));
    }

    /**
     * Export employees to CSV
     */
    public function export(Request $request)
    {
        $query = Employee::query();

        // Apply same filters as index
        if ($request->has('department') && $request->department !== '') {
            $query->where('department', $request->department);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $employees = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employees.csv"',
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'Department', 'Position', 'Hire Date', 'Phone']);

            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->id,
                    $employee->name,
                    $employee->email,
                    $employee->department,
                    $employee->position,
                    $employee->hire_date,
                    $employee->phone
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
