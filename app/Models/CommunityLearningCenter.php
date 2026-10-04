<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityLearningCenter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'community_learning_centers';

    protected $fillable = [
        'clc_name',
        'clc_code',
        'clc_type',
        'barangay',
        'physical_location_notes',
        'assigned_teacher_id',
        'status',
    ];

    public function assignedTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'clc_id');
    }

    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'clc_id')->where('enrollment_status', 'enrolled');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInBarangay($query, string $barangay)
    {
        return $query->where('barangay', $barangay);
    }
}
