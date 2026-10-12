@extends('layouts.app')

@section('title', 'ALS Online Registration - Part 1 & 2')

@section('body')
@php
    $initialStep = 1;
    if ($errors->hasAny([
        'highest_education_attained', 'dropout_reason', 'has_attended_als',
        'previous_als_program', 'previous_als_year', 'previous_als_completed', 'previous_als_incomplete_reason',
        'program_track', 'clc_id', 'delivery_mode', 'schedule_preference',
        'username', 'email', 'password', 'password_confirmation'
    ])) {
        $initialStep = 2;
    } elseif ($errors->any()) {
        $initialStep = 1;
    }

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

    $dropoutOptions = [
        'No school in Barangay',
        'Unable to pay for miscellaneous and other expenses',
        'School too far from home',
    ];
    $oldDropout = old('dropout_reason', '');
    $isCustomDropout = $oldDropout && !in_array($oldDropout, $dropoutOptions);

    $gradeLevels = [
        'Kinder',
        'Grade 1',
        'Grade 2',
        'Grade 3',
        'Grade 4',
        'Grade 5',
        'Grade 6',
        'Grade 7',
        'Grade 8',
        'Grade 9',
    ];

    $motherTongues = [
        'Aklanon',
        'Alta, Northern',
        'Alta, Southern',
        'Atta',
        'Bicol',
        'Cebuano',
        'Chavacano',
        "Ga'dang",
        'Gaddang',
        'Hiligaynon',
        'Ibaloy',
        'Ibanag',
        'Ilocano',
        'Isinai',
        'Isnag',
        'Itawis',
        'Itneg',
        'Ivatan',
        'Kankanaey',
        'Kapampangan',
        'Kinaray-a',
        'Labin Agta',
        'Maguindanao',
        'Manobo',
        'Marano',
        'Pangasinan',
        'Sambal',
        'Sugiraonon',
        'Tagalog',
        'Tausug',
        'Waray',
        'Yakan',
        'Yogad',
    ];
@endphp

<div class="min-h-screen py-8 sm:py-12 px-3 sm:px-6 lg:px-8 bg-[#edf2f7] flex flex-col justify-center items-center">
    
    <!-- Top Action Bar -->
    <div class="max-w-[1040px] w-full mb-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-deped-blue hover:border-deped-blue/40 text-xs sm:text-sm font-bold shadow-xs transition-all hover:-translate-x-0.5">
            <svg class="w-4 h-4 text-deped-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7 7-7M3 12h18"/>
            </svg>
            <span>Back to Home</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('teacher.register') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-deped-blue/10 border border-deped-blue/20 hover:bg-deped-blue hover:text-white text-deped-blue text-xs sm:text-sm font-bold shadow-xs transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Teacher Register</span>
            </a>
            <span class="text-xs text-slate-500 font-semibold hidden sm:inline">Already registered?</span>
            <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-deped-blue text-xs sm:text-sm font-bold shadow-xs transition-all">
                Sign In &rarr;
            </a>
        </div>
    </div>

    <!-- Dedicated High-Visibility Scrollbar for Part 1 and Part 2 -->
    <style>
        .form-panel-scroll {
            overflow-y: scroll !important;
            scrollbar-width: thin !important;
            scrollbar-color: #1b387a #e2e8f0 !important;
            scrollbar-gutter: stable;
            -webkit-overflow-scrolling: touch;
        }

        .form-panel-scroll::-webkit-scrollbar {
            width: 10px !important;
            height: 10px !important;
            display: block !important;
        }

        .form-panel-scroll::-webkit-scrollbar-track {
            background: #f1f5f9 !important;
            border-left: 1px solid #cbd5e1 !important;
        }

        .form-panel-scroll::-webkit-scrollbar-thumb {
            background: #1b387a !important;
            border-radius: 9999px !important;
            border: 2px solid #f1f5f9 !important;
            min-height: 48px !important;
        }

        .form-panel-scroll::-webkit-scrollbar-thumb:hover {
            background: #0f2042 !important;
        }

        .form-panel-scroll::-webkit-scrollbar-thumb:active {
            background: #c53030 !important;
        }

        /* Ensure the left sidebar never displays any scrollbars */
        .register-sidebar-rail {
            overflow: hidden !important;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        .register-sidebar-rail::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
    </style>

    <!-- Colorlib V02 Multi-Step Form Container with Alpine.js -->
    <div x-data="{
            step: {{ $initialStep }},
            totalSteps: 2,
            isSubmitting: false,
            showPw: false,
            showConfirmPw: false,
            birthdate: '{{ old('birthdate', '') }}',
            age: '{{ old('age', '') }}',
            selectedBarangay: '{{ $isCustomBarangay ? '__other__' : $oldBarangay }}',
            customBarangay: '{{ $isCustomBarangay ? $oldBarangay : '' }}',
            selectedReligion: '{{ $isCustomReligion ? '__other__' : $oldReligion }}',
            customReligion: '{{ $isCustomReligion ? $oldReligion : '' }}',
            hasAttendedAls: '{{ old('has_attended_als', '') }}',
            selectedDropout: '{{ $isCustomDropout ? '__other__' : $oldDropout }}',
            customDropout: '{{ $isCustomDropout ? $oldDropout : '' }}',
            canScrollMore: true,
            checkScroll() {
                const scroller = this.$refs.formScrollContainer;
                if (!scroller) return;
                this.canScrollMore = (scroller.scrollHeight - scroller.scrollTop - scroller.clientHeight > 30);
            },
            scrollDown() {
                const scroller = this.$refs.formScrollContainer;
                if (!scroller) return;
                scroller.scrollBy({ top: 320, behavior: 'smooth' });
            },
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
                this.$nextTick(() => {
                    const scroller = this.$refs.formScrollContainer;
                    if (scroller) {
                        scroller.scrollTo({ top: 0, behavior: 'smooth' });
                        setTimeout(() => this.checkScroll(), 120);
                    }
                });
            },
            prevStep() {
                if (this.step > 1) this.step--;
                this.$nextTick(() => {
                    const scroller = this.$refs.formScrollContainer;
                    if (scroller) {
                        scroller.scrollTo({ top: 0, behavior: 'smooth' });
                        setTimeout(() => this.checkScroll(), 120);
                    }
                });
            },
            goToStep(targetStep) {
                if (targetStep > this.step) {
                    this.nextStep();
                    return;
                }
                this.step = targetStep;
                this.$nextTick(() => {
                    const scroller = this.$refs.formScrollContainer;
                    if (scroller) {
                        scroller.scrollTo({ top: 0, behavior: 'smooth' });
                        setTimeout(() => this.checkScroll(), 120);
                    }
                });
            }
         }"
         x-init="
            if (birthdate && !age) calculateAge();
            $nextTick(() => {
                setTimeout(() => checkScroll(), 150);
            });
         "
         @resize.window.passive="checkScroll()"
         class="w-full max-w-[1040px] rounded-3xl bg-white shadow-[0_25px_60px_-15px_rgba(15,32,66,0.22)] overflow-hidden border border-slate-200/80 grid grid-cols-1 lg:grid-cols-[330px_minmax(0,1fr)] max-h-[92vh] lg:h-[840px]">

        <!-- =========================================================
             LEFT RAIL: Brand & Stepper Navigation (DepEd Blue Gradient)
             ========================================================= -->
        <aside class="register-sidebar-rail bg-gradient-to-b from-[#1b387a] via-[#12285a] to-[#0a1838] text-white p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden h-full">
            <!-- Background subtle crest decoration -->
            <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-white/5 pointer-events-none blur-2xl"></div>
            <div class="absolute -left-12 -bottom-12 w-48 h-48 rounded-full bg-deped-red/10 pointer-events-none blur-2xl"></div>

            <div class="relative z-10">
                <!-- Brand Header -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center p-2 border border-white/20 shadow-xs">
                        <img src="{{ asset('images/deped-logo.png') }}" alt="DepEd Logo" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span style="display:none" class="text-base font-black text-amber-300">ALS</span>
                    </div>
                    <div>
                        <span class="block text-xs font-black uppercase tracking-wider text-amber-300">SDO San Juan City</span>
                        <span class="block text-sm font-bold text-white leading-tight">Alternative Learning System</span>
                    </div>
                </div>

                <!-- Page Title & Lead -->
                <h1 class="text-2xl sm:text-[28px] font-black text-white tracking-tight leading-tight mb-2">
                    ALS Online Registration
                </h1>
                <p class="text-blue-100/80 text-xs sm:text-sm mb-6 leading-relaxed">
                    Official DepEd Form 2 Learner Intake Portal for San Juan City. Complete your 2-step profile to enroll.
                </p>

                <!-- Colorlib V02 Vertical Stepper -->
                <ol class="space-y-5 relative" aria-label="Registration Progress">
                    <!-- Step 1 -->
                    <li class="relative flex items-start gap-4 cursor-pointer group" @click="goToStep(1)">
                        <!-- Connector Line -->
                        <div class="absolute left-4 top-9 bottom-[-20px] w-0.5"
                             :class="step > 1 ? 'bg-emerald-400' : 'bg-white/20'"></div>

                        <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all"
                             :class="{
                                 'bg-white text-deped-blue shadow-lg ring-4 ring-white/20': step === 1,
                                 'bg-emerald-400 text-slate-950 font-black shadow-md': step > 1,
                                 'border-2 border-white/30 text-white/70': step < 1
                             }">
                            <template x-if="step > 1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="step <= 1">
                                <span>1</span>
                            </template>
                        </div>
                        <div class="pt-0.5">
                            <strong class="block text-sm font-bold text-white tracking-tight">Personal Information</strong>
                            <small class="block text-xs text-blue-200/80 mt-0.5">Part 1 &bull; Basic details, address &amp; parents</small>
                        </div>
                    </li>

                    <!-- Step 2 -->
                    <li class="relative flex items-start gap-4 cursor-pointer group" @click="goToStep(2)">
                        <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all"
                             :class="{
                                 'bg-white text-deped-blue shadow-lg ring-4 ring-white/20': step === 2,
                                 'border-2 border-white/30 text-white/70': step !== 2
                             }">
                            <span>2</span>
                        </div>
                        <div class="pt-0.5">
                            <strong class="block text-sm font-bold text-white tracking-tight">Educational Information</strong>
                            <small class="block text-xs text-blue-200/80 mt-0.5">Part 2 &bull; School history, track &amp; credentials</small>
                        </div>
                    </li>
                </ol>
            </div>

            <!-- Rail Footer Help -->
            <div class="relative z-10 pt-4 mt-6 border-t border-white/10 text-xs text-blue-200/80 flex items-center justify-between shrink-0">
                <div>
                    <span class="block font-semibold text-white">Need help?</span>
                    <span>Chat with <strong class="text-amber-300">ALSJay</strong> assistant</span>
                </div>
                <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center text-amber-300 font-bold">
                    ?
                </div>
            </div>
        </aside>

        <!-- =========================================================
             RIGHT BODY: Form Content & Controls with Dedicated Scrollbar
             ========================================================= -->
        <div class="relative flex flex-col bg-white h-full min-h-0 overflow-hidden">
            <!-- Top Step Progress Bar (Fixed at top of right panel) -->
            <div class="px-6 pt-6 pb-4 sm:px-9 sm:pt-7 sm:pb-4 border-b border-slate-100 bg-white z-10 shrink-0">
                <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-2">
                    <span x-text="'Step ' + step + ' of ' + totalSteps"></span>
                    <span class="text-[#d93025] font-semibold">* Indicates required question</span>
                </div>
                <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-deped-blue to-deped-red transition-all duration-300"
                         :style="'width: ' + (step === 1 ? '50%' : '100%')"></div>
                </div>
            </div>

            <!-- Scrollable Form Container with Dedicated High-Visibility Scrollbar for Part 1 & Part 2 -->
            <div x-ref="formScrollContainer" 
                 @scroll.passive="checkScroll()"
                 class="flex-1 min-h-0 overflow-y-scroll form-panel-scroll px-6 py-6 sm:px-9 sm:py-8">

                <!-- Global Server-Side Validation Errors -->
                @if($errors->any())
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-xs flex items-start gap-3 mb-6">
                        <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                            !
                        </div>
                        <div class="space-y-1 text-xs">
                            <h3 class="font-bold text-rose-950">Incomplete or Invalid Submission</h3>
                            <p class="text-rose-700">Please review all required fields marked with an asterisk (*).</p>
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
                      class="space-y-6">
                    @csrf

                    <!-- ==========================================
                         PART 1: PERSONAL INFORMATION
                         ========================================== -->
                    <div x-show="step === 1" x-ref="step1" class="space-y-6">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-deped-blue">Part 1</span>
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Personal Information</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Official learner identification and demographic records</p>
                        </div>

                        <!-- Name Fields Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Last Name <span class="text-[#d93025]">*</span>
                                </label>
                                <input type="text" 
                                       name="last_name" 
                                       required 
                                       value="{{ old('last_name') }}" 
                                       placeholder="e.g. Dela Cruz"
                                       class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('last_name') border-rose-400 bg-rose-50/20 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                                @error('last_name')
                                    <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    First Name <span class="text-[#d93025]">*</span>
                                </label>
                                <input type="text" 
                                       name="first_name" 
                                       required 
                                       value="{{ old('first_name') }}" 
                                       placeholder="e.g. Juan"
                                       class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('first_name') border-rose-400 bg-rose-50/20 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                                @error('first_name')
                                    <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Middle Name
                                </label>
                                <input type="text" 
                                       name="middle_name" 
                                       value="{{ old('middle_name') }}" 
                                       placeholder="e.g. Santos (Optional)"
                                       class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Name Extension
                                </label>
                                <input type="text" 
                                       name="name_extension" 
                                       value="{{ old('name_extension') }}" 
                                       placeholder="e.g. Jr., III (Optional)"
                                       class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                            </div>
                        </div>

                        <!-- COMPLETE HOME ADDRESS Section -->
                        <div class="pt-4 border-t border-slate-100 space-y-4">
                            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                                COMPLETE HOME ADDRESS
                            </h3>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    House Number / Street / Sitio / Purok <span class="text-[#d93025]">*</span>
                                </label>
                                <input type="text" 
                                       name="street_address" 
                                       required 
                                       value="{{ old('street_address') }}" 
                                       placeholder="e.g. Block 4 Lot 12 F. Manalo St."
                                       class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('street_address') border-rose-400 bg-rose-50/20 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                                @error('street_address')
                                    <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <input type="hidden" 
                                   name="barangay" 
                                   :value="selectedBarangay === '__other__' ? customBarangay : selectedBarangay">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Barangay in San Juan City <span class="text-[#d93025]">*</span>
                                    </label>
                                    <div class="relative">
                                        <select x-model="selectedBarangay" 
                                                required 
                                                class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('barangay') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 appearance-none pr-10">
                                            <option value="" disabled>Choose Barangay</option>
                                            @foreach($sanJuanBarangays as $b)
                                                <option value="{{ $b }}">{{ $b }}</option>
                                            @endforeach
                                            <option value="__other__">Other / Outside San Juan</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                        </div>
                                    </div>
                                    @error('barangay')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div x-show="selectedBarangay === '__other__'" x-cloak>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Specify Barangay / Municipality <span class="text-[#d93025]">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="customBarangay" 
                                           placeholder="Enter barangay name"
                                           class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15">
                                </div>
                            </div>
                        </div>

                        <!-- Demographics & Inclusion -->
                        <div class="pt-4 border-t border-slate-100 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Sex (Pill Radios) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Sex <span class="text-[#d93025]">*</span>
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <label class="relative cursor-pointer grow">
                                            <input type="radio" name="gender" value="male" required {{ old('gender') === 'male' ? 'checked' : '' }} class="peer sr-only">
                                            <span class="flex items-center justify-center min-h-[42px] px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 peer-checked:border-deped-blue peer-checked:bg-deped-blue peer-checked:text-white transition-all">
                                                Male
                                            </span>
                                        </label>
                                        <label class="relative cursor-pointer grow">
                                            <input type="radio" name="gender" value="female" required {{ old('gender') === 'female' ? 'checked' : '' }} class="peer sr-only">
                                            <span class="flex items-center justify-center min-h-[42px] px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 peer-checked:border-deped-blue peer-checked:bg-deped-blue peer-checked:text-white transition-all">
                                                Female
                                            </span>
                                        </label>
                                    </div>
                                    @error('gender')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Civil Status (Pill Radios) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Civil Status <span class="text-[#d93025]">*</span>
                                    </label>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach([
                                            'single' => 'Single',
                                            'married' => 'Married',
                                            'widowed' => 'Widow/er',
                                            'separated' => 'Separated',
                                            'solo_parent' => 'Solo Parent',
                                        ] as $val => $lbl)
                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="civil_status" value="{{ $val }}" required {{ old('civil_status') === $val ? 'checked' : '' }} class="peer sr-only">
                                                <span class="inline-flex items-center justify-center px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-bold text-slate-700 peer-checked:border-deped-blue peer-checked:bg-deped-blue peer-checked:text-white transition-all">
                                                    {{ $lbl }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('civil_status')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Birthdate & Age Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Date of Birth <span class="text-[#d93025]">*</span>
                                    </label>
                                    <input type="date" 
                                           name="birthdate" 
                                           required 
                                           x-model="birthdate" 
                                           @change="calculateAge()"
                                           value="{{ old('birthdate') }}" 
                                           class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('birthdate') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15">
                                    @error('birthdate')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Age <span class="text-[#d93025]">*</span>
                                    </label>
                                    <input type="number" 
                                           name="age" 
                                           x-model="age"
                                           value="{{ old('age') }}" 
                                           placeholder="e.g. 19"
                                           min="5" 
                                           max="120"
                                           class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('age') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15">
                                    @error('age')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Religion & Mother Tongue (Uncontrolled) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Religion
                                    </label>
                                    <input type="hidden" 
                                           name="religion" 
                                           :value="selectedReligion === '__other__' ? customReligion : selectedReligion">
                                    <div class="relative">
                                        <select x-model="selectedReligion" 
                                                class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 appearance-none pr-10">
                                            <option value="">Choose Religion (Optional)</option>
                                            @foreach($religionList as $rel)
                                                <option value="{{ $rel }}">{{ $rel }}</option>
                                            @endforeach
                                            <option value="__other__">Other</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                        </div>
                                    </div>
                                    <div x-show="selectedReligion === '__other__'" class="mt-2" x-cloak>
                                        <input type="text" 
                                               x-model="customReligion" 
                                               placeholder="Specify religion"
                                               class="w-full min-h-[40px] px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-deped-blue">
                                    </div>
                                </div>

                                <!-- Mother Tongue (Uncontrolled selection) -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Mother Tongue <span class="text-[#d93025]">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="mother_tongue" 
                                                required 
                                                class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('mother_tongue') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 appearance-none pr-10">
                                            <option value="" disabled {{ old('mother_tongue') ? '' : 'selected' }}>Choose</option>
                                            @foreach($motherTongues as $mt)
                                                <option value="{{ $mt }}" {{ old('mother_tongue') === $mt ? 'selected' : '' }}>
                                                    {{ $mt }}
                                                </option>
                                            @endforeach
                                            @if(old('mother_tongue') && !in_array(old('mother_tongue'), $motherTongues))
                                                <option value="{{ old('mother_tongue') }}" selected>
                                                    {{ old('mother_tongue') }}
                                                </option>
                                            @endif
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                        </div>
                                    </div>
                                    @error('mother_tongue')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Person with Disability (Pill) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Person with Disability (PWD)?
                                </label>
                                <div class="flex items-center gap-3">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="is_pwd" value="1" {{ old('is_pwd') == '1' ? 'checked' : '' }} class="peer sr-only">
                                        <span class="inline-flex items-center justify-center min-h-[40px] px-5 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 peer-checked:border-deped-blue peer-checked:bg-deped-blue peer-checked:text-white transition-all">
                                            Yes
                                        </span>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="is_pwd" value="0" {{ old('is_pwd', '0') == '0' ? 'checked' : '' }} class="peer sr-only">
                                        <span class="inline-flex items-center justify-center min-h-[40px] px-5 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 peer-checked:border-deped-blue peer-checked:bg-deped-blue peer-checked:text-white transition-all">
                                            No
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- PARENTS INFORMATION Section -->
                        <div class="pt-4 border-t border-slate-100 space-y-4">
                            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                                PARENTS INFORMATION
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Father's Name <span class="text-[#d93025]">*</span>
                                    </label>
                                    <input type="text" 
                                           name="father_name" 
                                           required 
                                           value="{{ old('father_name') }}" 
                                           placeholder="Last Name, First Name, Middle Name"
                                           class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('father_name') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                                    @error('father_name')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Mother's Name <span class="text-[#d93025]">*</span>
                                    </label>
                                    <input type="text" 
                                           name="mother_name" 
                                           required 
                                           value="{{ old('mother_name') }}" 
                                           placeholder="Last Name, First Name, Middle Name"
                                           class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('mother_name') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                                    @error('mother_name')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Contact Number <span class="text-[#d93025]">*</span>
                                </label>
                                <input type="text" 
                                       name="contact_number" 
                                       required 
                                       value="{{ old('contact_number') }}" 
                                       placeholder="e.g. 09171234567"
                                       class="w-full sm:w-1/2 min-h-[46px] px-3.5 py-2.5 bg-white border @error('contact_number') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                                @error('contact_number')
                                    <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Step 1 Bottom Action -->
                        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-400 font-semibold">Page 1 of 2</span>
                            <button type="button" 
                                    @click="nextStep()" 
                                    class="inline-flex items-center gap-2 min-h-[48px] px-8 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-sm shadow-md transition-all hover:scale-[1.01] active:scale-[0.99]">
                                <span>Continue &rarr; Proceed to Part 2</span>
                            </button>
                        </div>
                    </div>


                    <!-- ==========================================
                         PART 2: EDUCATIONAL INFORMATION & SETUP
                         ========================================== -->
                    <div x-show="step === 2" x-ref="step2" x-cloak class="space-y-6">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-widest text-deped-red">Part 2</span>
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Educational Information</h2>
                            <p class="text-xs text-slate-500 mt-0.5">School history, ALS program track, and portal account credentials</p>
                        </div>

                        <!-- Last Grade Level Completed in Formal School * -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Last Grade Level Completed in Formal School <span class="text-[#d93025]">*</span>
                            </label>
                            <div class="relative w-full sm:w-80">
                                <select name="highest_education_attained" 
                                        required 
                                        class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('highest_education_attained') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 appearance-none pr-10">
                                    <option value="" disabled {{ old('highest_education_attained') ? '' : 'selected' }}>Choose</option>
                                    @foreach($gradeLevels as $lvl)
                                        <option value="{{ $lvl }}" {{ old('highest_education_attained') === $lvl ? 'selected' : '' }}>
                                            {{ $lvl }}
                                        </option>
                                    @endforeach
                                    @if(old('highest_education_attained') && !in_array(old('highest_education_attained'), $gradeLevels))
                                        <option value="{{ old('highest_education_attained') }}" selected>
                                            {{ old('highest_education_attained') }}
                                        </option>
                                    @endif
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                </div>
                            </div>
                            @error('highest_education_attained')
                                <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Why did you drop out of school? -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Why did you drop out of school?
                            </label>
                            <input type="hidden" 
                                   name="dropout_reason" 
                                   :value="selectedDropout === '__other__' ? customDropout : selectedDropout">

                            <div class="space-y-2">
                                @foreach($dropoutOptions as $opt)
                                    <label class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                                        <input type="radio" 
                                               value="{{ $opt }}" 
                                               x-model="selectedDropout" 
                                               class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                                        <span class="text-sm font-medium text-slate-800">{{ $opt }}</span>
                                    </label>
                                @endforeach

                                <!-- Other: inline text field option -->
                                <div class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors">
                                    <input type="radio" 
                                           value="__other__" 
                                           x-model="selectedDropout" 
                                           class="w-4 h-4 text-deped-blue focus:ring-deped-blue border-slate-300">
                                    <span class="text-sm font-medium text-slate-800 shrink-0">Other:</span>
                                    <input type="text" 
                                           x-model="customDropout" 
                                           @focus="selectedDropout = '__other__'"
                                           placeholder="Specify reason"
                                           class="grow px-2 py-1 bg-transparent border-b border-slate-300 focus:border-deped-blue focus:outline-hidden text-sm text-slate-900 transition-colors">
                                </div>
                            </div>
                        </div>

                        <!-- Have you attended ALS learning sessions before? * -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Have you attended ALS learning sessions before? <span class="text-[#d93025]">*</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" 
                                           name="has_attended_als" 
                                           value="1" 
                                           required 
                                           x-model="hasAttendedAls"
                                           {{ old('has_attended_als') == '1' ? 'checked' : '' }}
                                           class="peer sr-only">
                                    <span class="inline-flex items-center justify-center min-h-[40px] px-6 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 peer-checked:border-deped-blue peer-checked:bg-deped-blue peer-checked:text-white transition-all">
                                        Yes
                                    </span>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" 
                                           name="has_attended_als" 
                                           value="0" 
                                           required 
                                           x-model="hasAttendedAls"
                                           {{ old('has_attended_als', '0') == '0' ? 'checked' : '' }}
                                           class="peer sr-only">
                                    <span class="inline-flex items-center justify-center min-h-[40px] px-6 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 peer-checked:border-deped-blue peer-checked:bg-deped-blue peer-checked:text-white transition-all">
                                        No
                                    </span>
                                </label>
                            </div>
                            @error('has_attended_als')
                                <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notice Callout Box -->
                        <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-100 text-slate-700 text-xs space-y-1">
                            <p class="font-bold text-slate-900">If YES on the previous question, please answer the follow-up details below.</p>
                            <p class="text-slate-500">If you answered "No", you may skip directly to Program Track selection.</p>
                        </div>

                        <!-- Conditional Follow-up Questions -->
                        <div class="space-y-4 p-4 rounded-2xl bg-slate-50 border border-slate-200/80" 
                             :class="hasAttendedAls === '1' ? 'opacity-100' : 'opacity-70'">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Name of the Program
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach([
                                        'Basic Literacy Program',
                                        'A&E Elementary',
                                        'A&E Junior High School',
                                    ] as $prog)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" 
                                                   name="previous_als_program" 
                                                   value="{{ $prog }}" 
                                                   {{ old('previous_als_program') === $prog ? 'checked' : '' }}
                                                   class="peer sr-only">
                                            <span class="inline-flex items-center justify-center px-3.5 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 peer-checked:border-deped-blue peer-checked:bg-deped-blue peer-checked:text-white transition-all">
                                                {{ $prog }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Year Attended
                                    </label>
                                    <input type="text" 
                                           name="previous_als_year" 
                                           value="{{ old('previous_als_year') }}" 
                                           placeholder="e.g. 2023"
                                           class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Have you completed the program?
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <label class="relative cursor-pointer">
                                            <input type="radio" 
                                                   name="previous_als_completed" 
                                                   value="1" 
                                                   {{ old('previous_als_completed') == '1' ? 'checked' : '' }}
                                                   class="peer sr-only">
                                            <span class="inline-flex items-center justify-center min-h-[38px] px-4 py-1.5 border border-slate-300 rounded-lg text-xs font-bold text-slate-700 peer-checked:border-deped-blue peer-checked:bg-deped-blue peer-checked:text-white transition-all">
                                                Yes
                                            </span>
                                        </label>
                                        <label class="relative cursor-pointer">
                                            <input type="radio" 
                                                   name="previous_als_completed" 
                                                   value="0" 
                                                   {{ old('previous_als_completed') == '0' ? 'checked' : '' }}
                                                   class="peer sr-only">
                                            <span class="inline-flex items-center justify-center min-h-[38px] px-4 py-1.5 border border-slate-300 rounded-lg text-xs font-bold text-slate-700 peer-checked:border-deped-blue peer-checked:bg-deped-blue peer-checked:text-white transition-all">
                                                No
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    If No, state the reason:
                                </label>
                                <input type="text" 
                                       name="previous_als_incomplete_reason" 
                                       value="{{ old('previous_als_incomplete_reason') }}" 
                                       placeholder="Enter reason if incomplete"
                                       class="w-full min-h-[42px] px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue">
                            </div>
                        </div>

                        <!-- Current ALS Program Track * (Colorlib 02 Plan Cards Style) -->
                        <div class="pt-4 border-t border-slate-100 space-y-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Current ALS Program Track to Enroll <span class="text-[#d93025]">*</span>
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- BLP Card -->
                                <label class="relative flex flex-col justify-between p-4 border-2 rounded-2xl cursor-pointer transition-all hover:border-blue-300"
                                       :class="'{{ old('program_track') }}' === 'BLP' ? 'border-deped-blue bg-blue-50/40' : 'border-slate-200'">
                                    <input type="radio" name="program_track" value="BLP" {{ old('program_track') === 'BLP' ? 'checked' : '' }} class="peer absolute top-3 right-3 text-deped-blue focus:ring-deped-blue">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-500 uppercase">Track 1</span>
                                        <h4 class="text-sm font-black text-slate-900 mt-1">Basic Literacy (BLP)</h4>
                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Foundational reading, writing, and arithmetic skills.</p>
                                    </div>
                                </label>

                                <!-- Elementary Card -->
                                <label class="relative flex flex-col justify-between p-4 border-2 rounded-2xl cursor-pointer transition-all hover:border-blue-300"
                                       :class="'{{ old('program_track') }}' === 'A&E_Elementary' ? 'border-deped-blue bg-blue-50/40' : 'border-slate-200'">
                                    <input type="radio" name="program_track" value="A&E_Elementary" {{ old('program_track') === 'A&E_Elementary' ? 'checked' : '' }} class="peer absolute top-3 right-3 text-deped-blue focus:ring-deped-blue">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-500 uppercase">Track 2</span>
                                        <h4 class="text-sm font-black text-slate-900 mt-1">A&amp;E Elementary</h4>
                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Grade 6 equivalency &bull; Elementary diploma.</p>
                                    </div>
                                </label>

                                <!-- Junior High Card -->
                                <label class="relative flex flex-col justify-between p-4 border-2 rounded-2xl cursor-pointer transition-all hover:border-blue-300"
                                       :class="'{{ old('program_track', 'A&E_Junior_High') }}' === 'A&E_Junior_High' ? 'border-deped-blue bg-blue-50/40' : 'border-slate-200'">
                                    <input type="radio" name="program_track" value="A&E_Junior_High" {{ old('program_track', 'A&E_Junior_High') === 'A&E_Junior_High' ? 'checked' : '' }} class="peer absolute top-3 right-3 text-deped-blue focus:ring-deped-blue">
                                    <div>
                                        <span class="inline-block px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase mb-1">Most Popular</span>
                                        <h4 class="text-sm font-black text-slate-900">A&amp;E Junior High</h4>
                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Grade 10 equivalency &bull; Senior High qualifier.</p>
                                    </div>
                                </label>
                            </div>
                            @error('program_track')
                                <p class="text-xs text-[#d93025] font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- CLC & Preferences Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nearest CLC in San Juan City <span class="text-[#d93025]">*</span>
                                </label>
                                <div class="relative">
                                    <select name="clc_id" 
                                            required 
                                            class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('clc_id') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 appearance-none pr-10">
                                        <option value="">Choose a Learning Center...</option>
                                        @foreach($clcs as $clc)
                                            <option value="{{ $clc->id }}" {{ old('clc_id', $clcs->first()->id ?? '') == $clc->id ? 'selected' : '' }}>
                                                {{ $clc->clc_name }} (Brgy. {{ $clc->barangay }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                    </div>
                                </div>
                                @error('clc_id')
                                    <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Delivery Mode
                                </label>
                                <div class="relative">
                                    <select name="delivery_mode" class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 appearance-none pr-10">
                                        <option value="modular_offline" {{ old('delivery_mode', 'modular_offline') === 'modular_offline' ? 'selected' : '' }}>Modular (Offline Mobile LMS App)</option>
                                        <option value="modular_print" {{ old('delivery_mode') === 'modular_print' ? 'selected' : '' }}>Modular (Printed Modules)</option>
                                        <option value="blended" {{ old('delivery_mode') === 'blended' ? 'selected' : '' }}>Blended Learning</option>
                                        <option value="face_to_face" {{ old('delivery_mode') === 'face_to_face' ? 'selected' : '' }}>Face-to-Face Sessions</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Portal Account Setup (Colorlib 02 Styled with Eye Toggle) -->
                        <div class="pt-4 border-t border-slate-100 space-y-4">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                                    Portal Account Setup
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Use these credentials to sign in to the web portal and Android Mobile LMS app.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Username <span class="text-[#d93025]">*</span>
                                    </label>
                                    <input type="text" 
                                           name="username" 
                                           required 
                                           value="{{ old('username') }}" 
                                           placeholder="e.g. juan.delacruz"
                                           class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('username') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                                    @error('username')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Email Address (Optional)
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="your.email@example.com"
                                           class="w-full min-h-[46px] px-3.5 py-2.5 bg-white border @error('email') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                                    @error('email')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Password <span class="text-[#d93025]">*</span>
                                    </label>
                                    <div class="relative">
                                        <input :type="showPw ? 'text' : 'password'" 
                                               name="password" 
                                               required 
                                               placeholder="Min. 8 characters"
                                               class="w-full min-h-[46px] px-3.5 py-2.5 pr-11 bg-white border @error('password') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                                        <button type="button" 
                                                @click="showPw = !showPw" 
                                                class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-hidden">
                                            <svg x-show="!showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showPw" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="text-xs text-[#d93025] font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                        Confirm Password <span class="text-[#d93025]">*</span>
                                    </label>
                                    <div class="relative">
                                        <input :type="showConfirmPw ? 'text' : 'password'" 
                                               name="password_confirmation" 
                                               required 
                                               placeholder="Re-type password"
                                               class="w-full min-h-[46px] px-3.5 py-2.5 pr-11 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-hidden focus:border-deped-blue focus:ring-3 focus:ring-deped-blue/15 transition-all">
                                        <button type="button" 
                                                @click="showConfirmPw = !showConfirmPw" 
                                                class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-hidden">
                                            <svg x-show="!showConfirmPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showConfirmPw" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 Bottom Navigation Actions -->
                        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                            <button type="button" 
                                    @click="prevStep()" 
                                    class="inline-flex items-center gap-2 min-h-[48px] px-6 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7 7-7M3 12h18"/></svg>
                                <span>Back</span>
                            </button>

                            <button type="submit" 
                                    :disabled="isSubmitting"
                                    :class="isSubmitting ? 'opacity-70 cursor-not-allowed' : 'hover:scale-[1.01] active:scale-[0.99]'"
                                    class="inline-flex items-center gap-2 min-h-[48px] px-8 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/30 transition-all">
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

            <!-- Floating Scroll Down Helper Indicator for Part 1 & Part 2 -->
            <div x-show="canScrollMore"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 @click="scrollDown()"
                 class="absolute bottom-4 right-8 z-20 cursor-pointer flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-deped-blue hover:bg-deped-dark text-white text-xs font-bold shadow-lg shadow-deped-blue/30 backdrop-blur-xs transition-all hover:scale-105 select-none animate-bounce"
                 title="Click to scroll down">
                <span>Scroll for more details</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </div>
    </div>
</div>
@endsection