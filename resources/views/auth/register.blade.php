@extends('layouts.app')

@section('title', 'ALS Online Registration - Part 1 & 2')

@section('body')
@php
    $initialStep = 1;
    if ($errors->hasAny(['username', 'email', 'password', 'password_confirmation', 'highest_education_attained', 'program_track', 'clc_id', 'delivery_mode', 'schedule_preference'])) {
        $initialStep = 2;
    } elseif ($errors->any()) {
        $initialStep = 1;
    }

    $step1Errors = $errors->hasAny([
        'last_name', 'first_name', 'middle_name', 'name_extension',
        'street_address', 'barangay', 'gender', 'civil_status',
        'birthdate', 'age', 'religion', 'mother_tongue', 'is_pwd',
        'father_name', 'mother_name', 'contact_number'
    ]);
    $step2Errors = $errors->hasAny([
        'highest_education_attained', 'program_track', 'clc_id',
        'delivery_mode', 'schedule_preference', 'username', 'email', 'password', 'password_confirmation'
    ]);

    $sanJuanBarangays = [
        'Additionhills',
        'Balong-Bato',
        'Batis',
        'Corazon De Jesus',
        'Ermitaño',
        'Greenhills',
        'St. Joseph',
        'Isabelita',
        'Kabayanan',
        'Little Baguio',
        'Maytunas',
        'Onse',
        'Pasadena',
        'Pedro Cruz',
        'Progreso',
        'Rivera',
        'Salapan',
        'San Perfecto',
        'Santa Lucia',
        'Tibagan',
        'West Crame',
        'San Juan City Jail (Male Dorm)',
        'San Juan City Jail (Female Dorm)',
    ];

    $oldBarangay = old('barangay', '');
    $isCustomBarangay = $oldBarangay && !in_array($oldBarangay, $sanJuanBarangays);

    $religionList = [
        'Roman Catholic',
        'Muslim',
        'Protestant',
        'Iglesia Ni Cristo',
        'Born Again Christian',
        'Evangelical',
    ];
    $oldReligion = old('religion', '');
    $isCustomReligion = $oldReligion && !in_array($oldReligion, $religionList);
@endphp

<div class="min-h-full py-8 sm:py-12 px-3 sm:px-6 lg:px-8 bg-slate-100 flex flex-col justify-center items-center">
    
    <!-- Top Action Bar -->
    <div class="max-w-2xl w-full mb-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-deped-blue hover:border-deped-blue/40 text-xs sm:text-sm font-bold shadow-xs transition-all hover:-translate-x-0.5">
            <svg class="w-4 h-4 text-deped-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7 7-7M3 12h18"/>
            </svg>
            <span>Back to Home</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-semibold hidden sm:inline">Already registered?</span>
            <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-deped-blue text-xs sm:text-sm font-bold shadow-xs transition-all">
                Sign In &rarr;
            </a>
        </div>
    </div>

    <!-- Main Form Container with Alpine.js State Machine -->
    <div x-data="{
            step: {{ $initialStep }},
            totalSteps: 2,
            isSubmitting: false,
            birthdate: '{{ old('birthdate', '') }}',
            age: '{{ old('age', '') }}',
            selectedBarangay: '{{ $isCustomBarangay ? '__other__' : $oldBarangay }}',
            customBarangay: '{{ $isCustomBarangay ? $oldBarangay : '' }}',
            selectedReligion: '{{ $isCustomReligion ? '__other__' : $oldReligion }}',
            customReligion: '{{ $isCustomReligion ? $oldReligion : '' }}',
            calculateAge() {
                if (!this.birthdate) return;
                const bdate = new Date(this.birthdate);
                const today = new Date();
                let calcAge = today.getFullYear() - bdate.getFullYear();
                const m = today.getMonth() - bdate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < bdate.getDate())) {
                    calcAge--;
                }
                if (calcAge >= 0 && calcAge <= 120) {
                    this.age = calcAge;
                }
            },
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
                if (targetStep > this.step) {
                    this.nextStep();
                    return;
                }
                this.step = targetStep;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
         }"
         x-init="if (birthdate && !age) calculateAge()"
         class="max-w-2xl w-full space-y-4">

        <!-- Form Top Header Card -->
        <div class="bg-white rounded-2xl border-t-8 border-t-rose-400 border-x border-b border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-3">
            <div class="flex items-center justify-between gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-deped-blue text-[11px] font-black uppercase tracking-wider">
                    DepEd SDO San Juan City
                </span>
                <span class="text-xs font-bold text-slate-400">ALS Form 2 Intake</span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                ALS Online Registration
            </h1>
            
            <p class="text-sm font-medium text-slate-600">
                Alternative Learning System - San Juan City
            </p>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-rose-600 font-semibold">
                <span>* Indicates required question</span>
                <span class="text-slate-400 font-normal">Official DepEd Registration Portal</span>
            </div>
        </div>

        <!-- 2-Part Stepper Tabs -->
        <div class="bg-white rounded-2xl border border-slate-200/90 p-3 shadow-xs">
            <div class="grid grid-cols-2 gap-2">
                <button type="button" 
                        @click="goToStep(1)"
                        class="p-3 rounded-xl text-left transition-all flex items-center justify-between"
                        :class="step === 1 ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100'">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black"
                              :class="step === 1 ? 'bg-white text-deped-blue' : 'bg-slate-200 text-slate-700'">
                            1
                        </span>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider opacity-80">Part 1</div>
                            <div class="text-xs sm:text-sm font-black truncate">Personal Information</div>
                        </div>
                    </div>
                    @if($step1Errors)
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                    @endif
                </button>

                <button type="button" 
                        @click="goToStep(2)"
                        class="p-3 rounded-xl text-left transition-all flex items-center justify-between"
                        :class="step === 2 ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100'">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black"
                              :class="step === 2 ? 'bg-white text-deped-blue' : 'bg-slate-200 text-slate-700'">
                            2
                        </span>
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider opacity-80">Part 2</div>
                            <div class="text-xs sm:text-sm font-black truncate">Program &amp; Account Setup</div>
                        </div>
                    </div>
                    @if($step2Errors)
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                    @endif
                </button>
            </div>
        </div>

        <!-- Global Error Notification -->
        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-xs flex items-start gap-3">
                <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5 font-bold">
                    !
                </div>
                <div class="space-y-1 text-xs">
                    <h3 class="font-bold text-rose-950">Incomplete or Invalid Submission</h3>
                    <p class="text-rose-700">Please review and complete all required questions marked with red asterisk (*).</p>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Form Element -->
        <form action="{{ route('register') }}" 
              method="POST" 
              @submit="if (isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;"
              class="space-y-4">
            @csrf

            <!-- ==========================================
                 PART 1: PERSONAL INFORMATION
                 ========================================== -->
            <div x-show="step === 1" x-ref="step1" class="space-y-4">

                <!-- Section Card 1: Personal Information Part 1 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs">
                    <h2 class="text-xl font-black text-slate-900">Personal Information</h2>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Part 1</p>
                </div>

                <!-- Last Name * -->
                <div class="bg-white rounded-2xl p-6 border @error('last_name') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Last Name <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" 
                           name="last_name" 
                           required 
                           value="{{ old('last_name') }}" 
                           placeholder="Your answer"
                           class="w-full px-0 py-2 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400 transition-colors">
                    @error('last_name')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- First Name * -->
                <div class="bg-white rounded-2xl p-6 border @error('first_name') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        First Name <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" 
                           name="first_name" 
                           required 
                           value="{{ old('first_name') }}" 
                           placeholder="Your answer"
                           class="w-full px-0 py-2 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400 transition-colors">
                    @error('first_name')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Middle Name -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Middle Name
                    </label>
                    <input type="text" 
                           name="middle_name" 
                           value="{{ old('middle_name') }}" 
                           placeholder="Your answer"
                           class="w-full px-0 py-2 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400 transition-colors">
                </div>

                <!-- Name Extension (Jr. / Sr. / III) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Name Extension (Jr. / Sr. / III)
                    </label>
                    <input type="text" 
                           name="name_extension" 
                           value="{{ old('name_extension') }}" 
                           placeholder="Your answer"
                           class="w-full px-0 py-2 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400 transition-colors">
                </div>

                <!-- Section Card 2: COMPLETE HOME ADDRESS -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs">
                    <h2 class="text-xl font-black text-slate-900 uppercase">COMPLETE HOME ADDRESS</h2>
                    <p class="text-xs font-semibold text-slate-500 mt-1">San Juan City Address</p>
                </div>

                <!-- House Number / Street / Sitio / Purok * -->
                <div class="bg-white rounded-2xl p-6 border @error('street_address') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        House Number / Street / Sitio / Purok <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" 
                           name="street_address" 
                           required 
                           value="{{ old('street_address') }}" 
                           placeholder="Your answer"
                           class="w-full px-0 py-2 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400 transition-colors">
                    @error('street_address')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Barangay * -->
                <div class="bg-white rounded-2xl p-6 border @error('barangay') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Barangay <span class="text-rose-600">*</span>
                    </label>

                    <!-- Hidden submitted input bound to Alpine state -->
                    <input type="hidden" 
                           name="barangay" 
                           :value="selectedBarangay === '__other__' ? customBarangay : selectedBarangay">

                    <div class="space-y-2 pt-1 max-h-96 overflow-y-auto pr-1">
                        @foreach($sanJuanBarangays as $b)
                            <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
                                <input type="radio" 
                                       value="{{ $b }}" 
                                       x-model="selectedBarangay" 
                                       class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                                <span class="text-sm font-medium text-slate-800">{{ $b }}</span>
                            </label>
                        @endforeach

                        <!-- Other: text option -->
                        <div class="p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" 
                                       value="__other__" 
                                       x-model="selectedBarangay" 
                                       class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                                <span class="text-sm font-medium text-slate-800">Other:</span>
                            </label>
                            <div x-show="selectedBarangay === '__other__'" class="mt-2 pl-7">
                                <input type="text" 
                                       x-model="customBarangay" 
                                       placeholder="Specify barangay"
                                       class="w-full px-0 py-1.5 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400">
                            </div>
                        </div>
                    </div>
                    @error('barangay')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Sex * -->
                <div class="bg-white rounded-2xl p-6 border @error('gender') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Sex <span class="text-rose-600">*</span>
                    </label>
                    <div class="space-y-2 pt-1">
                        <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="gender" value="male" required {{ old('gender') === 'male' ? 'checked' : '' }}
                                   class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                            <span class="text-sm font-medium text-slate-800">Male</span>
                        </label>
                        <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="gender" value="female" required {{ old('gender') === 'female' ? 'checked' : '' }}
                                   class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                            <span class="text-sm font-medium text-slate-800">Female</span>
                        </label>
                    </div>
                    @error('gender')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Civil Status * -->
                <div class="bg-white rounded-2xl p-6 border @error('civil_status') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Civil Status <span class="text-rose-600">*</span>
                    </label>
                    <div class="space-y-2 pt-1">
                        @foreach([
                            'single' => 'Single',
                            'married' => 'Married',
                            'widowed' => 'Widow/er',
                            'separated' => 'Separated',
                            'solo_parent' => 'Solo Parent',
                        ] as $val => $lbl)
                            <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 cursor-pointer">
                                <input type="radio" name="civil_status" value="{{ $val }}" required {{ old('civil_status') === $val ? 'checked' : '' }}
                                       class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                                <span class="text-sm font-medium text-slate-800">{{ $lbl }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('civil_status')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Date of Birth * -->
                <div class="bg-white rounded-2xl p-6 border @error('birthdate') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Date of Birth <span class="text-rose-600">*</span>
                    </label>
                    <div class="text-xs text-slate-500 font-medium">Date</div>
                    <input type="date" 
                           name="birthdate" 
                           required 
                           x-model="birthdate" 
                           @change="calculateAge()"
                           value="{{ old('birthdate') }}" 
                           class="w-full sm:w-64 px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-deped-blue focus:bg-white focus:outline-hidden">
                    @error('birthdate')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Age * -->
                <div class="bg-white rounded-2xl p-6 border @error('age') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Age <span class="text-rose-600">*</span>
                    </label>
                    <input type="number" 
                           name="age" 
                           x-model="age"
                           value="{{ old('age') }}" 
                           placeholder="Your answer"
                           min="5" 
                           max="120"
                           class="w-full sm:w-48 px-0 py-2 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400 transition-colors">
                    @error('age')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Religion (No asterisk) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Religion
                    </label>

                    <input type="hidden" 
                           name="religion" 
                           :value="selectedReligion === '__other__' ? customReligion : selectedReligion">

                    <div class="space-y-2 pt-1">
                        @foreach($religionList as $rel)
                            <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 cursor-pointer">
                                <input type="radio" 
                                       value="{{ $rel }}" 
                                       x-model="selectedReligion" 
                                       class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                                <span class="text-sm font-medium text-slate-800">{{ $rel }}</span>
                            </label>
                        @endforeach

                        <div class="p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" 
                                       value="__other__" 
                                       x-model="selectedReligion" 
                                       class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                                <span class="text-sm font-medium text-slate-800">Other:</span>
                            </label>
                            <div x-show="selectedReligion === '__other__'" class="mt-2 pl-7">
                                <input type="text" 
                                       x-model="customReligion" 
                                       placeholder="Specify religion"
                                       class="w-full px-0 py-1.5 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mother Tongue * -->
                <div class="bg-white rounded-2xl p-6 border @error('mother_tongue') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Mother Tongue <span class="text-rose-600">*</span>
                    </label>
                    <select name="mother_tongue" 
                            required 
                            class="w-full sm:w-72 px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:ring-2 focus:ring-deped-blue focus:bg-white focus:outline-hidden">
                        <option value="" disabled {{ old('mother_tongue') ? '' : 'selected' }}>Choose</option>
                        @foreach([
                            'Tagalog / Filipino',
                            'English',
                            'Cebuano / Bisaya',
                            'Ilocano',
                            'Hiligaynon / Ilonggo',
                            'Bikolano',
                            'Waray',
                            'Kapampangan',
                            'Pangasinense',
                            'Maranao',
                            'Maguindanaon',
                            'Tausug',
                            'Chavacano',
                            'Other'
                        ] as $mt)
                            <option value="{{ $mt }}" {{ old('mother_tongue', 'Tagalog / Filipino') === $mt ? 'selected' : '' }}>
                                {{ $mt }}
                            </option>
                        @endforeach
                    </select>
                    @error('mother_tongue')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>This is a required question</span>
                        </p>
                    @enderror
                </div>

                <!-- Person with Disability? -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Person with Disability?
                    </label>
                    <div class="space-y-2 pt-1">
                        <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="is_pwd" value="1" {{ old('is_pwd') == '1' ? 'checked' : '' }}
                                   class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                            <span class="text-sm font-medium text-slate-800">Yes</span>
                        </label>
                        <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="is_pwd" value="0" {{ old('is_pwd', '0') == '0' ? 'checked' : '' }}
                                   class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                            <span class="text-sm font-medium text-slate-800">No</span>
                        </label>
                    </div>
                </div>

                <!-- Section Card 3: PARENTS INFORMATION -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs">
                    <h2 class="text-xl font-black text-slate-900 uppercase">PARENTS INFORMATION</h2>
                </div>

                <!-- Father's Name * -->
                <div class="bg-white rounded-2xl p-6 border @error('father_name') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Father's Name (Last Name, First Name, Middle Name) <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" 
                           name="father_name" 
                           required 
                           value="{{ old('father_name') }}" 
                           placeholder="Your answer"
                           class="w-full px-0 py-2 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400 transition-colors">
                    @error('father_name')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Mother's Name * -->
                <div class="bg-white rounded-2xl p-6 border @error('mother_name') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Mother's Name (Last Name, First Name, Middle Name) <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" 
                           name="mother_name" 
                           required 
                           value="{{ old('mother_name') }}" 
                           placeholder="Your answer"
                           class="w-full px-0 py-2 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400 transition-colors">
                    @error('mother_name')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Contact Number * -->
                <div class="bg-white rounded-2xl p-6 border @error('contact_number') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Contact Number <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" 
                           name="contact_number" 
                           required 
                           value="{{ old('contact_number') }}" 
                           placeholder="Your answer"
                           class="w-full px-0 py-2 bg-transparent border-b-2 border-slate-200 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 placeholder-slate-400 transition-colors">
                    @error('contact_number')
                        <p class="text-xs text-rose-600 font-bold flex items-center gap-1">
                            <span>⚠</span> <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Navigation Action: Proceed to Part 2 -->
                <div class="pt-4 flex items-center justify-between">
                    <span class="text-xs text-slate-400 font-medium">Page 1 of 2</span>
                    <button type="button" 
                            @click="nextStep()" 
                            class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-sm shadow-md transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <span>Next &rarr; Proceed to Part 2</span>
                    </button>
                </div>
            </div>


            <!-- ==========================================
                 PART 2: PROGRAM TRACK & ENROLLMENT SETUP
                 ========================================== -->
            <div x-show="step === 2" x-ref="step2" x-cloak class="space-y-4">

                <!-- Section Card: Part 2 Header -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-black text-slate-900">Program Track &amp; Account Setup</h2>
                        <span class="text-xs font-bold text-deped-blue bg-blue-50 px-2.5 py-1 rounded-full border border-blue-100">
                            Part 2
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Complete your prior schooling level, preferred ALS learning center, and portal login account.
                    </p>
                </div>

                <!-- Highest Education Attained Prior to ALS * -->
                <div class="bg-white rounded-2xl p-6 border @error('highest_education_attained') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Highest Education Attained Prior to ALS <span class="text-rose-600">*</span>
                    </label>
                    <select name="highest_education_attained" 
                            required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        <option value="">Select Highest Grade / Level Completed...</option>
                        @foreach([
                            'No Formal Schooling',
                            'Elementary Undergraduate (Grade 1-5)',
                            'Elementary Graduate (Grade 6)',
                            'Junior High Undergraduate (Grade 7-9)',
                            'Junior High Graduate (Grade 10)',
                            'Senior High Undergraduate (Grade 11)'
                        ] as $lvl)
                            <option value="{{ $lvl }}" {{ old('highest_education_attained', 'Junior High Undergraduate (Grade 7-9)') === $lvl ? 'selected' : '' }}>
                                {{ $lvl }}
                            </option>
                        @endforeach
                    </select>
                    @error('highest_education_attained')
                        <p class="text-xs text-rose-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Program Track * -->
                <div class="bg-white rounded-2xl p-6 border @error('program_track') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        ALS Program Track <span class="text-rose-600">*</span>
                    </label>
                    <div class="space-y-2 pt-1">
                        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="program_track" value="BLP" {{ old('program_track') === 'BLP' ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                            <div>
                                <span class="block text-sm font-bold text-slate-900">Basic Literacy Program (BLP)</span>
                                <span class="block text-xs text-slate-500 mt-0.5">Foundational reading, writing, and numeracy.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="program_track" value="A&E_Elementary" {{ old('program_track') === 'A&E_Elementary' ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                            <div>
                                <span class="block text-sm font-bold text-slate-900">A&amp;E Elementary Level</span>
                                <span class="block text-xs text-slate-500 mt-0.5">Grade 6 equivalency &bull; Leads to Elementary Diploma.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="program_track" value="A&E_Junior_High" {{ old('program_track', 'A&E_Junior_High') === 'A&E_Junior_High' ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                            <div>
                                <span class="block text-sm font-bold text-slate-900">A&amp;E Junior High School</span>
                                <span class="block text-xs text-slate-500 mt-0.5">Grade 10 equivalency &bull; Qualifies for Senior High / College.</span>
                            </div>
                        </label>
                    </div>
                    @error('program_track')
                        <p class="text-xs text-rose-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Community Learning Center (CLC) * -->
                <div class="bg-white rounded-2xl p-6 border @error('clc_id') border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-3">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Nearest Community Learning Center (CLC) in San Juan City <span class="text-rose-600">*</span>
                    </label>
                    <select name="clc_id" 
                            required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        <option value="">Choose a Learning Center...</option>
                        @foreach($clcs as $clc)
                            <option value="{{ $clc->id }}" {{ old('clc_id', $clcs->first()->id ?? '') == $clc->id ? 'selected' : '' }}>
                                {{ $clc->clc_name }} (Brgy. {{ $clc->barangay }})
                            </option>
                        @endforeach
                    </select>
                    @error('clc_id')
                        <p class="text-xs text-rose-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preferred Delivery Mode & Schedule Preference -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                    <label class="block text-sm sm:text-base font-bold text-slate-900">
                        Learning Preferences
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs font-bold text-slate-600 uppercase mb-1">Delivery Mode</span>
                            <select name="delivery_mode" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                                <option value="modular_offline" {{ old('delivery_mode', 'modular_offline') === 'modular_offline' ? 'selected' : '' }}>Modular (Offline Mobile LMS App)</option>
                                <option value="modular_print" {{ old('delivery_mode') === 'modular_print' ? 'selected' : '' }}>Modular (Printed Modules)</option>
                                <option value="blended" {{ old('delivery_mode') === 'blended' ? 'selected' : '' }}>Blended Learning</option>
                                <option value="face_to_face" {{ old('delivery_mode') === 'face_to_face' ? 'selected' : '' }}>Face-to-Face Sessions</option>
                            </select>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-600 uppercase mb-1">Schedule Preference</span>
                            <select name="schedule_preference" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                                <option value="flexible" {{ old('schedule_preference', 'flexible') === 'flexible' ? 'selected' : '' }}>Flexible / Asynchronous Reading</option>
                                <option value="weekend" {{ old('schedule_preference') === 'weekend' ? 'selected' : '' }}>Weekend Consultations</option>
                                <option value="weekday_am" {{ old('schedule_preference') === 'weekday_am' ? 'selected' : '' }}>Weekday Morning</option>
                                <option value="weekday_pm" {{ old('schedule_preference') === 'weekday_pm' ? 'selected' : '' }}>Weekday Afternoon</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Portal Account Credentials * -->
                <div class="bg-white rounded-2xl p-6 border @error('username') border-rose-400 bg-rose-50/20 @elseif($errors->hasAny(['password', 'email'])) border-rose-400 bg-rose-50/20 @else border-slate-200/90 @enderror shadow-xs space-y-4">
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">Portal Account Setup</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Use these credentials to sign in to the Learner Portal and Mobile App.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username <span class="text-rose-600">*</span></label>
                        <input type="text" 
                               name="username" 
                               required 
                               value="{{ old('username') }}" 
                               placeholder="e.g. juan.dela.cruz"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('username')
                            <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address (Optional)</label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="your.email@example.com"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        @error('email')
                            <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password <span class="text-rose-600">*</span></label>
                            <input type="password" 
                                   name="password" 
                                   required 
                                   placeholder="Min. 8 characters"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            @error('password')
                                <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Confirm Password <span class="text-rose-600">*</span></label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   required 
                                   placeholder="Re-type password"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- Navigation Action: Back & Submit -->
                <div class="pt-4 flex flex-wrap items-center justify-between gap-3">
                    <button type="button" 
                            @click="prevStep()" 
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs sm:text-sm hover:bg-slate-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7 7-7M3 12h18"/></svg>
                        <span>&larr; Back to Part 1</span>
                    </button>

                    <button type="submit" 
                            :disabled="isSubmitting"
                            :class="isSubmitting ? 'opacity-70 cursor-not-allowed' : 'hover:scale-[1.02] active:scale-[0.98]'"
                            class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/30 transition-all">
                        <svg x-show="isSubmitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Submitting Registration...' : 'Submit ALS Registration ✓'"></span>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection