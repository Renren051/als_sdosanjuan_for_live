<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'birthdate',
        'gender',
        'civil_status',
        'religion',
        'contact_number',
        'facebook_name',
        'street_address',
        'barangay',
        'city',
        'province',
        'highest_education_attained',
        'mother_tongue',
        'is_ip',
        'ip_community_name',
        'is_pwd',
        'pwd_disability_type',
        'is_4ps_beneficiary',
        'father_name',
        'mother_name',
        'guardian_name',
        'guardian_relationship',
        'emergency_contact_name',
        'emergency_contact_number',
        'profile_photo_path',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'is_ip' => 'boolean',
            'is_pwd' => 'boolean',
            'is_4ps_beneficiary' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name ? mb_substr($this->middle_name, 0, 1) . '.' : null,
            $this->last_name,
            $this->name_extension,
        ]);

        return implode(' ', $parts);
    }

    public function getFormalNameAttribute(): string
    {
        $name = "{$this->last_name}, {$this->first_name}";
        if ($this->middle_name) {
            $name .= ' ' . mb_substr($this->middle_name, 0, 1) . '.';
        }
        if ($this->name_extension) {
            $name .= ' ' . $this->name_extension;
        }
        return $name;
    }

    public function getAgeAttribute(): int
    {
        return $this->birthdate ? Carbon::parse($this->birthdate)->age : 0;
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->street_address}, Brgy. {$this->barangay}, {$this->city}, {$this->province}";
    }
}
