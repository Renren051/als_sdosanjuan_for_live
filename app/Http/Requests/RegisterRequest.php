<?php

namespace App\Http\Requests;

use App\Models\Enrollment;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data for validation (sanitize strings, format booleans).
     */
    protected function prepareForValidation(): void
    {
        $gender = $this->gender ? mb_strtolower(trim($this->gender)) : null;
        $civilStatus = $this->civil_status ? mb_strtolower(trim($this->civil_status)) : null;
        if ($civilStatus) {
            if (str_contains($civilStatus, 'widow')) {
                $civilStatus = 'widowed';
            } elseif (str_contains($civilStatus, 'solo')) {
                $civilStatus = 'solo_parent';
            }
        }

        $this->merge([
            'username' => $this->username ? trim(mb_strtolower($this->username)) : null,
            'email' => $this->email ? trim(mb_strtolower($this->email)) : null,
            'first_name' => $this->first_name ? trim($this->first_name) : null,
            'middle_name' => $this->middle_name ? trim($this->middle_name) : null,
            'last_name' => $this->last_name ? trim($this->last_name) : null,
            'name_extension' => $this->name_extension ? trim($this->name_extension) : null,
            'gender' => $gender,
            'civil_status' => $civilStatus,
            'religion' => $this->religion ? trim($this->religion) : null,
            'age' => $this->age ? intval($this->age) : null,
            'contact_number' => $this->contact_number ? trim($this->contact_number) : null,
            'facebook_name' => $this->facebook_name ? trim($this->facebook_name) : null,
            'street_address' => $this->street_address ? trim($this->street_address) : null,
            'barangay' => $this->barangay ? trim($this->barangay) : null,
            'city' => $this->city ? trim($this->city) : 'San Juan City',
            'mother_tongue' => $this->mother_tongue ? trim($this->mother_tongue) : null,
            'father_name' => $this->father_name ? trim($this->father_name) : null,
            'mother_name' => $this->mother_name ? trim($this->mother_name) : null,
            'guardian_name' => $this->guardian_name ? trim($this->guardian_name) : null,
            'guardian_relationship' => $this->guardian_relationship ? trim($this->guardian_relationship) : null,
            'emergency_contact_name' => $this->emergency_contact_name ? trim($this->emergency_contact_name) : null,
            'emergency_contact_number' => $this->emergency_contact_number ? trim($this->emergency_contact_number) : null,
            'dropout_reason' => $this->dropout_reason ? trim($this->dropout_reason) : null,
            'has_attended_als' => filter_var($this->has_attended_als, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'previous_als_program' => $this->previous_als_program ? trim($this->previous_als_program) : null,
            'previous_als_year' => $this->previous_als_year ? trim($this->previous_als_year) : null,
            'previous_als_completed' => filter_var($this->previous_als_completed, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'previous_als_incomplete_reason' => $this->previous_als_incomplete_reason ? trim($this->previous_als_incomplete_reason) : null,
            'ip_community_name' => $this->ip_community_name ? trim($this->ip_community_name) : null,
            'pwd_disability_type' => $this->pwd_disability_type ? trim($this->pwd_disability_type) : null,
            'is_4ps_beneficiary' => filter_var($this->is_4ps_beneficiary, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'is_ip' => filter_var($this->is_ip, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'is_pwd' => filter_var($this->is_pwd, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
        ]);
    }

    /**
     * Validation rules matching approved ALS Form 2 intake requirements.
     */
    public function rules(): array
    {
        return [
            // Account Credentials
            'username' => [
                'required',
                'string',
                'min:4',
                'max:50',
                'regex:/^[a-zA-Z0-9._-]+$/',
                'unique:users,username',
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            // Demographics (Part 1: Personal Information)
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'name_extension' => ['nullable', 'string', 'max:10'],
            'birthdate' => ['required', 'date', 'before:today'],
            'age' => ['nullable', 'numeric', 'min:5', 'max:120'],
            'gender' => ['required', 'in:male,female,other'],
            'civil_status' => ['required', 'in:single,married,widowed,separated,solo_parent,other'],
            'religion' => ['nullable', 'string', 'max:100'],
            'mother_tongue' => ['nullable', 'string', 'max:100'],
            'is_pwd' => ['nullable', 'boolean'],
            'contact_number' => ['required', 'string', 'max:25'],
            'facebook_name' => ['nullable', 'string', 'max:100'],
            'street_address' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],

            // Parents Information (Part 1)
            'father_name' => ['nullable', 'string', 'max:150'],
            'mother_name' => ['nullable', 'string', 'max:150'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_relationship' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:25'],

            // Educational Background (Part 2: Educational Information)
            'highest_education_attained' => ['required', 'string', 'max:150'],
            'dropout_reason' => ['nullable', 'string', 'max:255'],
            'has_attended_als' => ['nullable', 'boolean'],
            'previous_als_program' => ['nullable', 'string', 'max:100'],
            'previous_als_year' => ['nullable', 'string', 'max:50'],
            'previous_als_completed' => ['nullable', 'boolean'],
            'previous_als_incomplete_reason' => ['nullable', 'string', 'max:255'],

            // Social Inclusion (Part 2)
            'is_4ps_beneficiary' => ['nullable', 'boolean'],
            'is_ip' => ['nullable', 'boolean'],
            'ip_community_name' => [
                'nullable',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::requiredIf(fn () => (bool) $this->is_ip),
            ],
            'pwd_disability_type' => [
                'nullable',
                'string',
                'max:150',
                \Illuminate\Validation\Rule::requiredIf(fn () => (bool) $this->is_pwd),
            ],

            // Program Track & CLC Allocation (ALS Form 2 Part III)
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

    /**
     * Clear user-friendly error guidance messages.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Please create a username for your account.',
            'username.min' => 'Username must be at least 4 characters long.',
            'username.regex' => 'Username may only contain letters, numbers, periods, dashes, or underscores.',
            'username.unique' => 'This username is already taken. If you already created an account, please sign in.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered. If you already have an account, please sign in.',
            'password.required' => 'A password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'first_name.required' => 'Learner first name is required (as shown on PSA Birth Certificate).',
            'last_name.required' => 'Learner last name is required.',
            'birthdate.required' => 'Birthdate is required.',
            'birthdate.before' => 'Birthdate must be a valid date in the past.',
            'gender.required' => 'Please select sex/gender.',
            'gender.in' => 'Please select a valid gender option.',
            'civil_status.required' => 'Please select civil status.',
            'contact_number.required' => 'Active mobile or contact number is required.',
            'street_address.required' => 'House / street address is required.',
            'barangay.required' => 'Please select your barangay in San Juan City.',
            'highest_education_attained.required' => 'Please select your highest educational attainment prior to ALS.',
            'ip_community_name.required' => 'Please specify your Indigenous Cultural Community name.',
            'ip_community_name.required_if' => 'Please specify your Indigenous Cultural Community name.',
            'pwd_disability_type.required' => 'Please specify your disability type.',
            'pwd_disability_type.required_if' => 'Please specify your disability type.',
            'program_track.required' => 'Please select an ALS Program Track (BLP, A&E Elementary, or A&E Junior High).',
            'clc_id.required' => 'Please select your nearest Community Learning Center in San Juan City.',
            'clc_id.exists' => 'The selected Community Learning Center does not exist.',
        ];
    }
}
