<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    public const TRACK_BLP = 'BLP';
    public const TRACK_AE_ELEMENTARY = 'A&E_Elementary';
    public const TRACK_AE_JUNIOR_HIGH = 'A&E_Junior_High';

    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_ENROLLED = 'enrolled';
    public const STATUS_NEEDS_CORRECTION = 'needs_correction';
    public const STATUS_WITHDRAWN = 'withdrawn';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'calendar_year',
        'program_track',
        'clc_id',
        'assigned_teacher_id',
        'enrollment_status',
        'delivery_mode',
        'schedule_preference',
        'remarks',
        'enrolled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function clc(): BelongsTo
    {
        return $this->belongsTo(CommunityLearningCenter::class, 'clc_id');
    }

    public function assignedTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_teacher_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LearnerDocument::class, 'enrollment_id');
    }

    public function getTrackLabelAttribute(): string
    {
        return match ($this->program_track) {
            self::TRACK_BLP => 'Basic Literacy Program (BLP)',
            self::TRACK_AE_ELEMENTARY => 'A&E Elementary',
            self::TRACK_AE_JUNIOR_HIGH => 'A&E Junior High School',
            default => $this->program_track,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->enrollment_status) {
            self::STATUS_ENROLLED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::STATUS_PENDING => 'bg-amber-100 text-amber-800 border-amber-300',
            self::STATUS_UNDER_REVIEW => 'bg-blue-100 text-blue-800 border-blue-300',
            self::STATUS_NEEDS_CORRECTION => 'bg-rose-100 text-rose-800 border-rose-300',
            self::STATUS_COMPLETED => 'bg-purple-100 text-purple-800 border-purple-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function scopeForYear($query, string $year)
    {
        return $query->where('calendar_year', $year);
    }

    public function scopeByClc($query, int $clcId)
    {
        return $query->where('clc_id', $clcId);
    }

    public function scopePending($query)
    {
        return $query->where('enrollment_status', self::STATUS_PENDING);
    }

    public function scopeEnrolled($query)
    {
        return $query->where('enrollment_status', self::STATUS_ENROLLED);
    }
}
