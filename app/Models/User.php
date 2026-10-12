<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MOBILE_TEACHER = 'mobile_teacher';
    public const ROLE_LEARNER = 'learner';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'lrn',
        'username',
        'email',
        'password',
        'role',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* =========================================================================
     * RELATIONSHIPS
     * ========================================================================= */

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'user_id');
    }

    public function latestEnrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class, 'user_id')->latestOfMany();
    }

    public function assignedClcs(): HasMany
    {
        return $this->hasMany(CommunityLearningCenter::class, 'assigned_teacher_id');
    }

    public function assignedLearners(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'assigned_teacher_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LearnerDocument::class, 'user_id');
    }

    public function reviewedDocuments(): HasMany
    {
        return $this->hasMany(LearnerDocument::class, 'reviewed_by');
    }

    public function uploadedModules(): HasMany
    {
        return $this->hasMany(Module::class, 'uploaded_by');
    }

    public function moduleProgress(): HasMany
    {
        return $this->hasMany(ModuleProgress::class, 'user_id');
    }

    public function moduleGameAttempts(): HasMany
    {
        return $this->hasMany(ModuleGameAttempt::class, 'user_id');
    }

    /* =========================================================================
     * ROLE & PERMISSION HELPERS
     * ========================================================================= */

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN], true);
    }

    public function isDivisionAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMobileTeacher(): bool
    {
        return $this->role === self::ROLE_MOBILE_TEACHER;
    }

    public function isStaff(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_MOBILE_TEACHER], true);
    }

    public function isLearner(): bool
    {
        return $this->role === self::ROLE_LEARNER;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /* =========================================================================
     * ACCESSORS & SCOPES
     * ========================================================================= */

    public function getFullNameAttribute(): string
    {
        if ($this->profile) {
            return $this->profile->full_name;
        }
        return $this->username;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
