<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'trainer_name' => 'required|string|max:255',
            'trainer_email' => 'nullable|email|max:255',
            'training_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'required|integer|min:1|max:1000',
            'pdf_material' => 'nullable|file|mimes:pdf|max:512', // 512KB = 500KB approx
            'additional_info' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Training title is required',
            'description.required' => 'Training description is required',
            'trainer_name.required' => 'Trainer name is required',
            'training_date.required' => 'Training date is required',
            'training_date.after_or_equal' => 'Training date must be today or later',
            'start_time.required' => 'Start time is required',
            'end_time.required' => 'End time is required',
            'end_time.after' => 'End time must be after start time',
            'max_participants.required' => 'Maximum participants is required',
            'max_participants.min' => 'Maximum participants must be at least 1',
            'pdf_material.mimes' => 'Only PDF files are allowed',
            'pdf_material.max' => 'PDF file size must not exceed 500KB',
        ];
    }
}
