<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = Employee::with('trainingEnrollments.training');

        // Filter by department
        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        // Filter by position
        if ($request->has('position')) {
            $query->where('position', $request->position);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $employees = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Employees retrieved successfully',
            'data' => $employees
        ]);
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee = Employee::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully',
            'data' => $employee
        ], 201);
    }

    /**
     * Display the specified employee.
     */
    public function show($id)
    {
        $employee = Employee::with(['trainingEnrollments.training'])->find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee retrieved successfully',
            'data' => $employee
        ]);
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $employee->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'data' => $employee
        ]);
    }

    /**
     * Remove the specified employee.
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }

    /**
     * Get departments list
     */
    public function departments()
    {
        $departments = Employee::select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        return response()->json([
            'success' => true,
            'message' => 'Departments retrieved successfully',
            'data' => $departments
        ]);
    }

    /**
     * Get positions list
     */
    public function positions()
    {
        $positions = Employee::select('position')
            ->distinct()
            ->orderBy('position')
            ->pluck('position');

        return response()->json([
            'success' => true,
            'message' => 'Positions retrieved successfully',
            'data' => $positions
        ]);
    }
}
