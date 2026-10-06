@extends('layouts.app')

@section('title', 'ALS Form 2 - Digital Learner Enrolment')

@section('body')
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
    /* Respect users who turn off animations in their OS/browser */
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
    <div class="max-w-3xl w-full text-center mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white text-deped-blue text-xs font-bold uppercase tracking-wider mb-2 shadow-sm">
            <span>DepEd SDO San Juan City &bull; ALS Form 2</span>
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight">
            Digital Learner Enrolment Intake
        </h1>
        <p class="text-sm text-white mt-2">
            Alternative Learning System &bull; Enrolment for Out-of-School Youth and Adults (OSYA)
        </p>
    </div>

    <!-- Wizard Card with Alpine.js State Machine -->
    <div x-data="{
            step: 1,
            totalSteps: 4,
            nextStep() {
                if (this.step < this.totalSteps) this.step++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            prevStep() {
                if (this.step > 1) this.step--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
         }" 
         class="max-w-3xl w-full bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">

        <!-- Wizard Step Tracker Header -->
        <div class="bg-gradient-to-r from-deped-navy to-deped-blue px-6 py-6 text-white">
            <div class="flex items-center justify-between">
                <template x-for="i in 4">
                    <div class="flex items-center" :class="i < 4 ? 'flex-1' : ''">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all shadow-inner"
                                 :class="step === i ? 'bg-deped-gold text-slate-950 ring-4 ring-white/20' : (step > i ? 'bg-emerald-500 text-white' : 'bg-white/20 text-white/70')">
                                <span x-show="step <= i" x-text="i"></span>
                                <svg x-show="step > i" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold hidden sm:inline" 
                                  :class="step === i ? 'text-white' : 'text-slate-300'"
                                  x-text="['Personal Info', 'Education & Background', 'Program & CLC', 'Account Setup'][i-1]">
                            </span>
                        </div>
                        <div x-show="i < 4" class="flex-1 mx-3 h-0.5" :class="step > i ? 'bg-emerald-500' : 'bg-white/20'"></div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Form Body -->
        <form action="{{ route('register') }}" method="POST" class="p-6 sm:p-10 space-y-6">
            @csrf

            <!-- STEP 1: Personal Demographics -->
            <div x-show="step === 1" x-cloak class="space-y-5">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-lg font-bold text-slate-900">Step 1: Learner Personal Information</h2>
                    <p class="text-xs text-slate-500">Provide legal name as indicated on your PSA Birth Certificate.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">First Name *</label>
                        <input type="text" name="first_name" required value="{{ old('first_name') }}" placeholder="e.g. Juan"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" placeholder="e.g. Protacio"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Last Name *</label>
                        <input type="text" name="last_name" required value="{{ old('last_name') }}" placeholder="e.g. Mercado"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Name Extension</label>
                        <input type="text" name="name_extension" value="{{ old('name_extension') }}" placeholder="Jr., III, etc."
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Birthdate *</label>
                        <input type="date" name="birthdate" required value="{{ old('birthdate') }}"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Sex *</label>
                        <select name="gender" required class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Civil Status *</label>
                        <select name="civil_status" required class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                            <option value="single">Single</option>
                            <option value="married">Married</option>
                            <option value="solo_parent">Solo Parent</option>
                            <option value="widowed">Widowed</option>
                            <option value="separated">Separated</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Mobile Number *</label>
                        <input type="text" name="contact_number" required value="{{ old('contact_number') }}" placeholder="0917XXXXXXX"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Facebook / Social</label>
                        <input type="text" name="facebook_name" value="{{ old('facebook_name') }}" placeholder="Profile Name"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">House / Street Address *</label>
                        <input type="text" name="street_address" required value="{{ old('street_address') }}" placeholder="House #, Street"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">San Juan City Barangay *</label>
                        <select name="barangay" required class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                            <option value="">Select Barangay in San Juan...</option>
                            @foreach([
                                'Addition Hills', 'Balong-Bato', 'Batis', 'Corazon de Jesus', 'Ermitaño',
                                'Salapan', 'San Perfecto', 'Santa Lucia', 'Tibagan', 'Progreso',
                                'Rivera', 'Pedro Cruz', 'Maytunas', 'Isabelita', 'Kabayanan',
                                'St. Joseph (Halo-halo)', 'Pasadena', 'Little Baguio', 'Greenhills',
                                'West Crame', 'Onse'
                            ] as $brgy)
                                <option value="{{ $brgy }}" {{ old('barangay') == $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Educational Background & Social Inclusion -->
            <div x-show="step === 2" x-cloak class="space-y-5">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-lg font-bold text-slate-900">Step 2: Educational Background & Special Inclusions</h2>
                    <p class="text-xs text-slate-500">Official DepEd social protection and prior schooling profile.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase">Highest Education Attained Prior to ALS *</label>
                    <select name="highest_education_attained" required class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                        <option value="No Formal Schooling">No Formal Schooling</option>
                        <option value="Elementary Undergraduate (Grade 1-5)">Elementary Undergraduate (Grade 1-5)</option>
                        <option value="Elementary Graduate (Grade 6)">Elementary Graduate (Grade 6)</option>
                        <option value="Junior High Undergraduate (Grade 7-9)">Junior High Undergraduate (Grade 7-9)</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Mother Tongue</label>
                        <input type="text" name="mother_tongue" value="Tagalog / Filipino"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">4Ps / Pantawid Beneficiary?</label>
                        <select name="is_4ps_beneficiary" class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Member of Indigenous People (IP)?</label>
                        <select name="is_ip" class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Person with Disability (PWD)?</label>
                        <select name="is_pwd" class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Parent / Guardian Name</label>
                        <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Full Name of Guardian"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Emergency Contact Number</label>
                        <input type="text" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}" placeholder="0917XXXXXXX"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                </div>
            </div>

            <!-- STEP 3: Program & CLC Selection -->
            <div x-show="step === 3" x-cloak class="space-y-5">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-lg font-bold text-slate-900">Step 3: ALS Program Track & Community Learning Center</h2>
                    <p class="text-xs text-slate-500">Choose your academic track and nearest San Juan learning center.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Program Track *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="p-4 rounded-2xl border-2 border-slate-200 hover:border-deped-blue cursor-pointer transition-all flex flex-col justify-between has-[:checked]:border-deped-blue has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="program_track" value="BLP" class="sr-only">
                            <div class="font-bold text-sm text-slate-900">Basic Literacy Program (BLP)</div>
                            <div class="text-xs text-slate-500 mt-1">For learners seeking foundational reading, writing, and numeracy.</div>
                        </label>
                        <label class="p-4 rounded-2xl border-2 border-slate-200 hover:border-deped-blue cursor-pointer transition-all flex flex-col justify-between has-[:checked]:border-deped-blue has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="program_track" value="A&E_Elementary" class="sr-only">
                            <div class="font-bold text-sm text-slate-900">A&E Elementary</div>
                            <div class="text-xs text-slate-500 mt-1">Equivalent to formal elementary education graduation.</div>
                        </label>
                        <label class="p-4 rounded-2xl border-2 border-slate-200 hover:border-deped-blue cursor-pointer transition-all flex flex-col justify-between has-[:checked]:border-deped-blue has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="program_track" value="A&E_Junior_High" checked class="sr-only">
                            <div class="font-bold text-sm text-slate-900">A&E Junior High School</div>
                            <div class="text-xs text-slate-500 mt-1">Equivalent to Grade 10 / Junior High School diploma.</div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase">Nearest Community Learning Center (CLC) in San Juan *</label>
                    <select name="clc_id" required class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                        <option value="">Choose a Learning Center...</option>
                        @foreach($clcs as $clc)
                            <option value="{{ $clc->id }}" {{ old('clc_id') == $clc->id ? 'selected' : '' }}>
                                {{ $clc->clc_name }} (Brgy. {{ $clc->barangay }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Preferred Delivery Mode</label>
                        <select name="delivery_mode" class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                            <option value="modular_offline">Modular (Offline Mobile LMS App)</option>
                            <option value="modular_print">Modular (Printed DepEd Modules)</option>
                            <option value="blended">Blended Learning (Offline App + Center Consultation)</option>
                            <option value="face_to_face">Face-to-Face Sessions</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Schedule Preference</label>
                        <select name="schedule_preference" class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                            <option value="flexible">Flexible / Asynchronous Reading</option>
                            <option value="weekend">Weekend Consultations (Saturday/Sunday)</option>
                            <option value="weekday_am">Weekday Morning (8:00 AM - 12:00 PM)</option>
                            <option value="weekday_pm">Weekday Afternoon (1:00 PM - 5:00 PM)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- STEP 4: Account Password & Submit -->
            <div x-show="step === 4" x-cloak class="space-y-5">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-lg font-bold text-slate-900">Step 4: Create Portal Account</h2>
                    <p class="text-xs text-slate-500">You will use this account to log in to the Web Portal and Android Mobile LMS App.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase">Username *</label>
                    <input type="text" name="username" required value="{{ old('username') }}" placeholder="e.g. juan.mercado"
                           class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase">Email Address (Optional)</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="juan@example.com"
                           class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Password * (Min 8 characters)</label>
                        <input type="password" name="password" required placeholder="••••••••••••"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••••••"
                               class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm">
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
                    <button type="submit" x-show="step === totalSteps" 
                            class="inline-flex items-center gap-2 px-8 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-emerald-600/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <span>Submit ALS Form 2</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection