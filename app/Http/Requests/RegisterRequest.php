<?php

namespace App\Http\Requests;

use App\Models\Enrollment;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Account
            'username' => ['required', 'string', 'min:4', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // Demographics (ALS Form 2)
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:10'],
            'birthdate' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'civil_status' => ['required', 'in:single,married,widowed,separated,solo_parent,other'],
            'contact_number' => ['required', 'string', 'max:25'],
            'facebook_name' => ['nullable', 'string', 'max:100'],
            'street_address' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'highest_education_attained' => ['required', 'string', 'max:150'],

            // DepEd Form 2 Specific Inclusions
            'mother_tongue' => ['nullable', 'string', 'max:100'],
            'is_ip' => ['nullable', 'boolean'],
            'ip_community_name' => ['nullable', 'string', 'max:100'],
            'is_pwd' => ['nullable', 'boolean'],
            'pwd_disability_type' => ['nullable', 'string', 'max:150'],
            'is_4ps_beneficiary' => ['nullable', 'boolean'],
            'father_name' => ['nullable', 'string', 'max:150'],
            'mother_name' => ['nullable', 'string', 'max:150'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_relationship' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:25'],

            // Enrollment Selection
            'program_track' => ['required', 'in:' . implode(',', [
                Enrollment::TRACK_BLP,
                Enrollment::TRACK_AE_ELEMENTARY,
                Enrollment::TRACK_AE_JUNIOR_HIGH,
            ])],
            'clc_id' => ['required', 'exists:community_learning_centers,id'],
            'delivery_mode' => ['nullable', 'in:modular_offline,modular_print,face_to_face,blended'],
            'schedule_preference' => ['nullable', 'in:weekend,weekday_am,weekday_pm,flexible'],
        ];
    }
}
