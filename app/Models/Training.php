<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'trainer_name',
        'trainer_email',
        'training_date',
        'start_time',
        'end_time',
        'location',
        'max_participants',
        'pdf_material',
        'is_active',
        'additional_info',
        'created_by'
    ];

    protected $casts = [
        'training_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
        'additional_info' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'training_enrollments')
                    ->withPivot(['status', 'enrolled_at', 'attended_at', 'completed_at', 'notes', 'is_certified', 'evaluation_data'])
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('training_date', '>=', now()->format('Y-m-d'));
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('training_date', [$startDate, $endDate]);
    }

    public function getAvailableSlotsAttribute()
    {
        $enrolled = $this->enrollments()->where('status', '!=', 'cancelled')->count();
        return $this->max_participants - $enrolled;
    }
}
