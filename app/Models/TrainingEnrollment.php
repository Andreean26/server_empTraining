<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'training_id',
        'employee_id',
        'status',
        'enrolled_at',
        'attended_at',
        'completed_at',
        'notes',
        'is_certified',
        'evaluation_data'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'attended_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_certified' => 'boolean',
        'evaluation_data' => 'array',
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
            if (empty($model->enrolled_at)) {
                $model->enrolled_at = now();
            }
        });
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCertified($query)
    {
        return $query->where('is_certified', true);
    }
}
