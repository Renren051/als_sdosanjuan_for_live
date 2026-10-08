@extends('layouts.app')

@section('title', 'ALS Form 2 - Digital Learner Enrolment')

@section('body')
@php
    $initialStep = 1;
    if ($errors->hasAny(['username', 'email', 'password', 'password_confirmation'])) {
        $initialStep = 4;
    } elseif ($errors->hasAny(['program_track', 'clc_id', 'delivery_mode', 'schedule_preference'])) {
        $initialStep = 3;
    } elseif ($errors->hasAny([
        'highest_education_attained', 'mother_tongue', 'is_4ps_beneficiary',
        'is_ip', 'ip_community_name', 'is_pwd', 'pwd_disability_type',
        'father_name', 'mother_name', 'guardian_name', 'guardian_relationship',
        'emergency_contact_name', 'emergency_contact_number'
    ])) {
        $initialStep = 2;
    } elseif ($errors->any()) {
        $initialStep = 1;
    }

    $step1Errors = $errors->hasAny(['first_name', 'middle_name', 'last_name', 'name_extension', 'birthdate', 'gender', 'civil_status', 'contact_number', 'facebook_name', 'street_address', 'barangay']);
    $step2Errors = $errors->hasAny(['highest_education_attained', 'mother_tongue', 'is_4ps_beneficiary', 'is_ip', 'ip_community_name', 'is_pwd', 'pwd_disability_type', 'father_name', 'mother_name', 'guardian_name', 'guardian_relationship', 'emergency_contact_name', 'emergency_contact_number']);
    $step3Errors = $errors->hasAny(['program_track', 'clc_id', 'delivery_mode', 'schedule_preference']);
    $step4Errors = $errors->hasAny(['username', 'email', 'password', 'password_confirmation']);
@endphp

<style>
    /* Slowly drifting red -> blue -> green gradient */
    .als-animated-bg {
        background: linear-gradient(135deg, #CE1126 0%, #0038A8 50%, #047857 100%);
        background-size: 300% 300%;
        animation: alsGradientShift 16s ease-in-out infinite;
    }
    @keyframes alsGradientShift {
        0%   { background-position: 0% 50%; }
        50%  { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    @media (prefers-reduced-motion: reduce) {
        .als-animated-bg { animation: none; background-size: 100% 100%; }
    }
</style>

<div class="als-animated-bg min-h-full py-10 px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-center">
    <!-- Top Action Bar -->
    <div class="max-w-3xl w-full mb-6 flex items-center justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-deped-blue hover:border-deped-blue/40 text-xs sm:text-sm font-bold shadow-2xs transition-all hover:-translate-x-0.5">
            <svg class="w-4 h-4 text-deped-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7 7-7M3 12h18"/>
            </svg>
            <span>Back to Home</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs text-white font-semibold hidden sm:inline">Already registered?</span>
            <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-deped-blue text-xs sm:text-sm font-bold shadow-2xs transition-all">
                Sign In &rarr;
            </a>
        </div>
    </div>

    <!-- Header Banner -->
    <div class="max-w-3xl w-full text-center mb-6">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white text-deped-blue text-xs font-bold uppercase tracking-wider mb-2 shadow-sm">
            <span>DepEd SDO San Juan City &bull; ALS Form 2</span>
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight">
            Digital Learner Enrolment Intake
        </h1>
        <p class="text-sm text-white/90 mt-2">
            Alternative Learning System &bull; Enrolment for Out-of-School Youth and Adults (OSYA)
        </p>
    </div>

    <!-- Wizard Card with Alpine.js State Machine -->
    <div x-data="{
            step: {{ $initialStep }},
            totalSteps: 4,
            isSubmitting: false,
            isIp: {{ old('is_ip') == '1' ? 'true' : 'false' }},
            isPwd: {{ old('is_pwd') == '1' ? 'true' : 'false' }},
            nextStep() {
                const stepEl = this.$refs['step' + this.step];
                if (stepEl) {
                    const inputs = stepEl.querySelectorAll('input, select, textarea');
                    for (let i = 0; i < inputs.length; i++) {
                        if (!inputs[i].checkValidity()) {
                            inputs[i].reportValidity();
                            return;
                        }
                    }
                }
                if (this.step < this.totalSteps) this.step++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            prevStep() {
                if (this.step > 1) this.step--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            goToStep(targetStep) {
                this.step = targetStep;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
         }" 
         class="max-w-3xl w-full bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">

        <!-- Wizard Step Tracker Header -->
        <div class="bg-gradient-to-r from-deped-navy to-deped-blue px-6 py-6 text-white">
            <div class="flex items-center justify-between">
                <template x-for="i in 4" :key="i">
                    <div class="flex items-center" :class="i < 4 ? 'flex-1' : ''">
                        <button type="button" 
                                @click="goToStep(i)"
                                class="flex items-center gap-2 group text-left focus:outline-hidden">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all shadow-inner relative"
                                 :class="step === i ? 'bg-deped-gold text-slate-950 ring-4 ring-white/20' : (step > i ? 'bg-emerald-500 text-white' : 'bg-white/20 text-white/70 group-hover:bg-white/30')">
                                <span x-show="step <= i" x-text="i"></span>
                                <svg x-show="step > i" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                                <!-- Error indicator dot -->
                                <template x-if="(i === 1 && {{ $step1Errors ? 'true' : 'false' }}) ||
                                                (i === 2 && {{ $step2Errors ? 'true' : 'false' }}) ||
                                                (i === 3 && {{ $step3Errors ? 'true' : 'false' }}) ||
                                                (i === 4 && {{ $step4Errors ? 'true' : 'false' }})">
                                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white"></span>
                                </template>
                            </div>
                            <span class="text-xs font-semibold hidden sm:inline transition-colors" 
                                  :class="step === i ? 'text-white' : 'text-slate-300 group-hover:text-white'"
                                  x-text="['Personal Info', 'Education & Background', 'Program & CLC', 'Account Setup'][i-1]">
                            </span>
                        </button>
                        <div x-show="i < 4" class="flex-1 mx-3 h-0.5" :class="step > i ? 'bg-emerald-500' : 'bg-white/20'"></div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Global Error Notification -->
        @if($errors->any())
            <div class="mx-6 sm:mx-10 mt-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm flex items-start gap-3.5">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-sm font-bold text-rose-950">Incomplete or Invalid Submission</h3>
                    <p class="text-xs text-rose-700">
                        Please review and correct the marked items below. Your previously entered data has been preserved.
                    </p>
                    <ul class="mt-2 text-xs text-rose-800 list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Form Body -->
        <form action="{{ route('register') }}" 
              method="POST" 
              @submit="if (isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;"
              class="p-6 sm:p-10 space-y-6">
            @csrf

            <!-- STEP 1: Personal Demographics -->
            <div x-show="step === 1" x-ref="step1" x-cloak class="space-y-5">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-lg font-bold text-slate-900">Step 1: Learner Personal Information</h2>
                    <p class="text-xs text-slate-500">Provide legal name as indicated on your PSA Birth Certificate.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">First Name *</label>
                        <input type="text" name="first_name" required value="{{ old('first_name') }}" placeholder="e.g. Juan"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('first_name') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('first_name')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" placeholder="e.g. Protacio"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('middle_name') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('middle_name')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Last Name *</label>
                        <input type="text" name="last_name" required value="{{ old('last_name') }}" placeholder="e.g. Mercado"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('last_name') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('last_name')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Name Extension</label>
                        <input type="text" name="name_extension" value="{{ old('name_extension') }}" placeholder="Jr., III, etc."
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('name_extension') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('name_extension')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Birthdate *</label>
                        <input type="date" name="birthdate" required value="{{ old('birthdate') }}"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('birthdate') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('birthdate')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Sex *</label>
                        <select name="gender" required class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('gender') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            <option value="male" {{ old('gender', 'male') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Civil Status *</label>
                        <select name="civil_status" required class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('civil_status') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            <option value="single" {{ old('civil_status', 'single') === 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('civil_status') === 'married' ? 'selected' : '' }}>Married</option>
                            <option value="solo_parent" {{ old('civil_status') === 'solo_parent' ? 'selected' : '' }}>Solo Parent</option>
                            <option value="widowed" {{ old('civil_status') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                            <option value="separated" {{ old('civil_status') === 'separated' ? 'selected' : '' }}>Separated</option>
                        </select>
                        @error('civil_status')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Mobile Number *</label>
                        <input type="text" name="contact_number" required value="{{ old('contact_number') }}" placeholder="0917XXXXXXX"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('contact_number') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('contact_number')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Facebook / Social</label>
                        <input type="text" name="facebook_name" value="{{ old('facebook_name') }}" placeholder="Profile Name"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('facebook_name') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('facebook_name')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">House / Street Address *</label>
                        <input type="text" name="street_address" required value="{{ old('street_address') }}" placeholder="House #, Street"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('street_address') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('street_address')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">San Juan City Barangay *</label>
                        <select name="barangay" required class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('barangay') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            <option value="">Select Barangay in San Juan...</option>
                            @foreach([
                                'Addition Hills', 'Balong-Bato', 'Batis', 'Corazon de Jesus', 'Ermitaño',
                                'Salapan', 'San Perfecto', 'Santa Lucia', 'Tibagan', 'Progreso',
                                'Rivera', 'Pedro Cruz', 'Maytunas', 'Isabelita', 'Kabayanan',
                                'St. Joseph (Halo-halo)', 'Pasadena', 'Little Baguio', 'Greenhills',
                                'West Crame', 'Onse'
                            ] as $brgy)
                                <option value="{{ $brgy }}" {{ old('barangay') === $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                            @endforeach
                        </select>
                        @error('barangay')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <input type="hidden" name="city" value="{{ old('city', 'San Juan City') }}">
            </div>

            <!-- STEP 2: Educational Background & Social Inclusion -->
            <div x-show="step === 2" x-ref="step2" x-cloak class="space-y-5">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-lg font-bold text-slate-900">Step 2: Educational Background & Special Inclusions</h2>
                    <p class="text-xs text-slate-500">Official DepEd social protection and prior schooling profile.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase">Highest Education Attained Prior to ALS *</label>
                    <select name="highest_education_attained" required class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('highest_education_attained') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        <option value="">Select Highest Grade / Level Completed...</option>
                        @foreach([
                            'No Formal Schooling',
                            'Elementary Undergraduate (Grade 1-5)',
                            'Elementary Graduate (Grade 6)',
                            'Junior High Undergraduate (Grade 7-9)',
                            'Junior High Graduate (Grade 10)',
                            'Senior High Undergraduate (Grade 11)'
                        ] as $lvl)
                            <option value="{{ $lvl }}" {{ old('highest_education_attained') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                        @endforeach
                    </select>
                    @error('highest_education_attained')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Mother Tongue</label>
                        <input type="text" name="mother_tongue" value="{{ old('mother_tongue', 'Tagalog / Filipino') }}"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('mother_tongue') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('mother_tongue')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">4Ps / Pantawid Beneficiary?</label>
                        <select name="is_4ps_beneficiary" class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            <option value="0" {{ old('is_4ps_beneficiary', '0') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_4ps_beneficiary') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>

                <!-- Indigenous People (IP) Group with conditional subfield -->
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Member of Indigenous People (IP)?</label>
                            <select name="is_ip" 
                                    @change="isIp = ($event.target.value === '1')"
                                    class="mt-1 w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue">
                                <option value="0" {{ old('is_ip', '0') == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_ip') == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div x-show="isIp" x-cloak>
                            <label class="block text-xs font-bold text-slate-700 uppercase">IP Community / Ethnicity Name *</label>
                            <input type="text" name="ip_community_name" 
                                   :required="isIp"
                                   value="{{ old('ip_community_name') }}" 
                                   placeholder="e.g. Aeta, Mangyan, Badjao"
                                   class="mt-1 w-full px-3.5 py-2.5 bg-white border @error('ip_community_name') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue">
                            @error('ip_community_name')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Person with Disability (PWD) Group with conditional subfield -->
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Person with Disability (PWD)?</label>
                            <select name="is_pwd" 
                                    @change="isPwd = ($event.target.value === '1')"
                                    class="mt-1 w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue">
                                <option value="0" {{ old('is_pwd', '0') == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_pwd') == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div x-show="isPwd" x-cloak>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Disability Type / Specification *</label>
                            <input type="text" name="pwd_disability_type" 
                                   :required="isPwd"
                                   value="{{ old('pwd_disability_type') }}" 
                                   placeholder="e.g. Visual, Hearing, Mobility"
                                   class="mt-1 w-full px-3.5 py-2.5 bg-white border @error('pwd_disability_type') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue">
                            @error('pwd_disability_type')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Parents & Guardian Details (DepEd Form 2 Mandatory Sections) -->
                <div class="pt-2">
                    <div class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Parents & Guardian Information</div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Father's Full Name</label>
                            <input type="text" name="father_name" value="{{ old('father_name') }}" placeholder="Last Name, First Name, Middle Name"
                                   class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('father_name') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            @error('father_name')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Mother's Maiden Name</label>
                            <input type="text" name="mother_name" value="{{ old('mother_name') }}" placeholder="Maiden Last Name, First Name, Middle Name"
                                   class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('mother_name') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            @error('mother_name')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Guardian Name (If applicable)</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Full Name of Legal Guardian"
                                   class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('guardian_name') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            @error('guardian_name')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Guardian Relationship</label>
                            <input type="text" name="guardian_relationship" value="{{ old('guardian_relationship') }}" placeholder="e.g. Grandparent, Sibling, Aunt"
                                   class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('guardian_relationship') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            @error('guardian_relationship')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Emergency Contact Person</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Full Name of Contact Person"
                                   class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('emergency_contact_name') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            @error('emergency_contact_name')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Emergency Contact Number</label>
                            <input type="text" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}" placeholder="0917XXXXXXX"
                                   class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('emergency_contact_number') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            @error('emergency_contact_number')
                                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Program & CLC Selection -->
            <div x-show="step === 3" x-ref="step3" x-cloak class="space-y-5">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-lg font-bold text-slate-900">Step 3: ALS Program Track & Community Learning Center</h2>
                    <p class="text-xs text-slate-500">Choose your academic track and nearest San Juan learning center.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Program Track *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between has-[:checked]:border-deped-blue has-[:checked]:bg-blue-50/50 hover:border-deped-blue/60 {{ old('program_track', 'A&E_Junior_High') === 'BLP' ? 'border-deped-blue bg-blue-50/50' : 'border-slate-200' }}">
                            <input type="radio" name="program_track" value="BLP" class="sr-only" {{ old('program_track') === 'BLP' ? 'checked' : '' }}>
                            <div class="font-bold text-sm text-slate-900">Basic Literacy Program (BLP)</div>
                            <div class="text-xs text-slate-500 mt-1">For learners seeking foundational reading, writing, and numeracy.</div>
                        </label>
                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between has-[:checked]:border-deped-blue has-[:checked]:bg-blue-50/50 hover:border-deped-blue/60 {{ old('program_track') === 'A&E_Elementary' ? 'border-deped-blue bg-blue-50/50' : 'border-slate-200' }}">
                            <input type="radio" name="program_track" value="A&E_Elementary" class="sr-only" {{ old('program_track') === 'A&E_Elementary' ? 'checked' : '' }}>
                            <div class="font-bold text-sm text-slate-900">A&E Elementary</div>
                            <div class="text-xs text-slate-500 mt-1">Equivalent to formal elementary education graduation.</div>
                        </label>
                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between has-[:checked]:border-deped-blue has-[:checked]:bg-blue-50/50 hover:border-deped-blue/60 {{ old('program_track', 'A&E_Junior_High') === 'A&E_Junior_High' ? 'border-deped-blue bg-blue-50/50' : 'border-slate-200' }}">
                            <input type="radio" name="program_track" value="A&E_Junior_High" class="sr-only" {{ old('program_track', 'A&E_Junior_High') === 'A&E_Junior_High' ? 'checked' : '' }}>
                            <div class="font-bold text-sm text-slate-900">A&E Junior High School</div>
                            <div class="text-xs text-slate-500 mt-1">Equivalent to Grade 10 / Junior High School diploma.</div>
                        </label>
                    </div>
                    @error('program_track')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase">Nearest Community Learning Center (CLC) in San Juan *</label>
                    <select name="clc_id" required class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('clc_id') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        <option value="">Choose a Learning Center...</option>
                        @foreach($clcs as $clc)
                            <option value="{{ $clc->id }}" {{ old('clc_id') == $clc->id ? 'selected' : '' }}>
                                {{ $clc->clc_name }} (Brgy. {{ $clc->barangay }})
                            </option>
                        @endforeach
                    </select>
                    @error('clc_id')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Preferred Delivery Mode</label>
                        <select name="delivery_mode" class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            <option value="modular_offline" {{ old('delivery_mode', 'modular_offline') === 'modular_offline' ? 'selected' : '' }}>Modular (Offline Mobile LMS App)</option>
                            <option value="modular_print" {{ old('delivery_mode') === 'modular_print' ? 'selected' : '' }}>Modular (Printed DepEd Modules)</option>
                            <option value="blended" {{ old('delivery_mode') === 'blended' ? 'selected' : '' }}>Blended Learning (Offline App + Center Consultation)</option>
                            <option value="face_to_face" {{ old('delivery_mode') === 'face_to_face' ? 'selected' : '' }}>Face-to-Face Sessions</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Schedule Preference</label>
                        <select name="schedule_preference" class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            <option value="flexible" {{ old('schedule_preference', 'flexible') === 'flexible' ? 'selected' : '' }}>Flexible / Asynchronous Reading</option>
                            <option value="weekend" {{ old('schedule_preference') === 'weekend' ? 'selected' : '' }}>Weekend Consultations (Saturday/Sunday)</option>
                            <option value="weekday_am" {{ old('schedule_preference') === 'weekday_am' ? 'selected' : '' }}>Weekday Morning (8:00 AM - 12:00 PM)</option>
                            <option value="weekday_pm" {{ old('schedule_preference') === 'weekday_pm' ? 'selected' : '' }}>Weekday Afternoon (1:00 PM - 5:00 PM)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- STEP 4: Account Password & Submit -->
            <div x-show="step === 4" x-ref="step4" x-cloak class="space-y-5">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-lg font-bold text-slate-900">Step 4: Create Portal Account</h2>
                    <p class="text-xs text-slate-500">You will use this account to log in to the Web Portal and Android Mobile LMS App.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase">Username *</label>
                    <input type="text" name="username" required value="{{ old('username') }}" placeholder="e.g. juan.mercado"
                           class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('username') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                    @error('username')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase">Email Address (Optional)</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="juan@example.com"
                           class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('email') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Password * (Min 8 characters)</label>
                        <input type="password" name="password" required placeholder="••••••••••••"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border @error('password') border-rose-400 bg-rose-50/30 @else border-slate-300 @enderror rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••••••"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-xs text-blue-900">
                    <strong>Submission Notice:</strong> By submitting, your digital ALS Form 2 will be transmitted to SDO San Juan City. After registering, you will be prompted to upload clear copies of your PSA Birth Certificate and Barangay Clearance for teacher verification.
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="pt-6 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" x-show="step > 1" @click="prevStep()" 
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm hover:bg-slate-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7 7-7M3 12h18"/></svg>
                        <span>Back</span>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" x-show="step < totalSteps" @click="nextStep()" 
                            class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs sm:text-sm shadow-md transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <span>Continue</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                    <button type="submit" 
                            x-show="step === totalSteps" 
                            :disabled="isSubmitting"
                            :class="isSubmitting ? 'opacity-70 cursor-not-allowed' : 'hover:scale-[1.02] active:scale-[0.98]'"
                            class="inline-flex items-center gap-2 px-8 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-emerald-600/30 transition-all">
                        <svg x-show="isSubmitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Submitting ALS Form 2...' : 'Submit ALS Form 2'"></span>
                        <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection