<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{
    /**
     * Display a listing of trainings.
     */
    public function index(Request $request)
    {
        $query = Training::with(['creator', 'enrollments.employee']);

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('training_date', [$request->start_date, $request->end_date]);
        }

        // Filter by status (upcoming, ongoing, completed)
        if ($request->has('status')) {
            $now = now()->format('Y-m-d');
            switch ($request->status) {
                case 'upcoming':
                    $query->where('training_date', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('training_date', '=', $now);
                    break;
                case 'completed':
                    $query->where('training_date', '<', $now);
                    break;
            }
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $trainings = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Trainings retrieved successfully',
            'data' => $trainings
        ]);
    }

    /**
     * Store a newly created training.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'training_date' => 'required|date|after_or_equal:today',
            'duration_hours' => 'required|integer|min:1|max:24',
            'max_participants' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('pdf_file');
        $data['created_by'] = auth()->id();

        // Handle PDF upload
        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('training_materials', $filename, 'public');
            $data['pdf_path'] = $path;
        }

        $training = Training::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Training created successfully',
            'data' => $training->load(['creator', 'enrollments'])
        ], 201);
    }

    /**
     * Display the specified training.
     */
    public function show($id)
    {
        $training = Training::with(['creator', 'enrollments.employee'])->find($id);

        if (!$training) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Training retrieved successfully',
            'data' => $training
        ]);
    }

    /**
     * Update the specified training.
     */
    public function update(Request $request, $id)
    {
        $training = Training::find($id);

        if (!$training) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'training_date' => 'required|date',
            'duration_hours' => 'required|integer|min:1|max:24',
            'max_participants' => 'required|integer|min:1',
            'location' => 'required|string|max:255',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('pdf_file');

        // Handle PDF upload
        if ($request->hasFile('pdf_file')) {
            // Delete old file if exists
            if ($training->pdf_path) {
                Storage::disk('public')->delete($training->pdf_path);
            }

            $file = $request->file('pdf_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('training_materials', $filename, 'public');
            $data['pdf_path'] = $path;
        }

        $training->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Training updated successfully',
            'data' => $training->load(['creator', 'enrollments'])
        ]);
    }

    /**
     * Remove the specified training.
     */
    public function destroy($id)
    {
        $training = Training::find($id);

        if (!$training) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found'
            ], 404);
        }

        // Delete PDF file if exists
        if ($training->pdf_path) {
            Storage::disk('public')->delete($training->pdf_path);
        }

        $training->delete();

        return response()->json([
            'success' => true,
            'message' => 'Training deleted successfully'
        ]);
    }

    /**
     * Download training PDF
     */
    public function downloadPdf($id)
    {
        $training = Training::find($id);

        if (!$training || !$training->pdf_path) {
            return response()->json([
                'success' => false,
                'message' => 'PDF file not found'
            ], 404);
        }

        $filePath = storage_path('app/public/' . $training->pdf_path);

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'PDF file not found on server'
            ], 404);
        }

        return response()->download($filePath);
    }
}
