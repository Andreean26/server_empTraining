<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Employee;
use App\Models\TrainingEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Training::with('trainingEnrollments.employee');

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('trainer_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by date
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->where('end_date', '<=', $request->date_to);
        }

        $trainings = $query->orderBy('start_date', 'desc')->paginate(15);

        return view('trainings.index', compact('trainings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('trainings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'trainer_name' => 'required|string|max:255',
            'trainer_email' => 'required|email|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'max_participants' => 'required|integer|min:1',
            'pdf_material' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
            'training_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $data = $request->except('pdf_material');

        // Handle file upload
        if ($request->hasFile('pdf_material')) {
            $file = $request->file('pdf_material');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['pdf_path'] = $file->storeAs('training_materials', $filename, 'public');
        }

        Training::create($data);

        return redirect()->route('trainings.index')
                        ->with('success', 'Training created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $training = Training::with(['trainingEnrollments.employee'])->findOrFail($id);
        
        // Get enrollment statistics
        $enrollmentStats = [
            'total' => $training->trainingEnrollments->count(),
            'completed' => $training->trainingEnrollments->where('status', 'completed')->count(),
            'enrolled' => $training->trainingEnrollments->where('status', 'enrolled')->count(),
            'attended' => $training->trainingEnrollments->where('status', 'attended')->count(),
            'cancelled' => $training->trainingEnrollments->where('status', 'cancelled')->count(),
            'available_slots' => $training->max_participants - $training->trainingEnrollments->whereIn('status', ['enrolled', 'attended', 'completed'])->count()
        ];

        return view('trainings.show', compact('training', 'enrollmentStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $training = Training::findOrFail($id);
        
        return view('trainings.edit', compact('training'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $training = Training::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'trainer_name' => 'required|string|max:255',
            'trainer_email' => 'required|email|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'max_participants' => 'required|integer|min:1',
            'pdf_material' => 'nullable|file|mimes:pdf|max:10240',
            'training_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $data = $request->except('pdf_material');

        // Handle file upload
        if ($request->hasFile('pdf_material')) {
            // Delete old file if exists
            if ($training->pdf_path && Storage::disk('public')->exists($training->pdf_path)) {
                Storage::disk('public')->delete($training->pdf_path);
            }

            $file = $request->file('pdf_material');
            $filename = time() . '_' . $file->getClientOriginalName();
            $data['pdf_path'] = $file->storeAs('training_materials', $filename, 'public');
        }

        $training->update($data);

        return redirect()->route('trainings.index')
                        ->with('success', 'Training updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $training = Training::findOrFail($id);
        
        // Check if training has enrollments
        if ($training->trainingEnrollments->count() > 0) {
            return redirect()->back()
                           ->with('error', 'Cannot delete training with existing enrollments.');
        }

        // Delete PDF file if exists
        if ($training->pdf_path && Storage::disk('public')->exists($training->pdf_path)) {
            Storage::disk('public')->delete($training->pdf_path);
        }

        $training->delete();

        return redirect()->route('trainings.index')
                        ->with('success', 'Training deleted successfully.');
    }

    /**
     * Download training material PDF
     */
    public function downloadPdf(string $id)
    {
        $training = Training::findOrFail($id);
        
        if (!$training->pdf_path || !Storage::disk('public')->exists($training->pdf_path)) {
            return redirect()->back()
                           ->with('error', 'Training material not found.');
        }

        return response()->download(storage_path('app/public/' . $training->pdf_path));
    }

    /**
     * Show training enrollments
     */
    public function enrollments(string $id)
    {
        $training = Training::with(['trainingEnrollments.employee'])->findOrFail($id);
        
        return view('trainings.enrollments', compact('training'));
    }

    /**
     * Show enrollment form
     */
    public function enroll(string $id)
    {
        $training = Training::findOrFail($id);
        $employees = Employee::whereNotIn('id', function($query) use ($id) {
            $query->select('employee_id')
                  ->from('training_enrollments')
                  ->where('training_id', $id)
                  ->whereIn('status', ['enrolled', 'attended', 'completed']);
        })->get();
        
        return view('trainings.enroll', compact('training', 'employees'));
    }

    /**
     * Process enrollment
     */
    public function processEnrollment(Request $request, string $id)
    {
        $training = Training::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $enrolledCount = 0;
        foreach ($request->employee_ids as $employeeId) {
            // Check if already enrolled
            $existingEnrollment = TrainingEnrollment::where('training_id', $id)
                                                  ->where('employee_id', $employeeId)
                                                  ->first();

            if (!$existingEnrollment) {
                TrainingEnrollment::create([
                    'training_id' => $id,
                    'employee_id' => $employeeId,
                    'status' => 'enrolled',
                    'enrolled_at' => now()
                ]);
                $enrolledCount++;
            }
        }

        return redirect()->route('trainings.show', $id)
                        ->with('success', "{$enrolledCount} employees enrolled successfully.");
    }
}
