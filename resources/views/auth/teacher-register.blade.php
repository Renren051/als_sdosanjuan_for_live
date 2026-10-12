@extends('layouts.app')

@section('title', 'ALS Mobile Teacher Registration - SDO San Juan City')

@section('body')
@php
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
    ];
@endphp

<div class="relative isolate min-h-[100dvh] overflow-x-hidden bg-slate-950 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <!-- Ambient Background Glow -->
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-deped-blue/25 blur-3xl"></div>
        <div class="absolute -bottom-44 -right-36 h-[30rem] w-[30rem] rounded-full bg-amber-500/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.08]" style="background-image: linear-gradient(rgba(255,255,255,.16) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.16) 1px, transparent 1px); background-size: 32px 32px;"></div>
    </div>

    <div class="relative mx-auto max-w-4xl">
        <!-- Top Navigation -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-300 backdrop-blur-md transition hover:bg-white/20 hover:text-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18"/>
                </svg>
                Back to ALS Home
            </a>

            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 font-semibold hidden sm:inline">Already have an account?</span>
                <a href="{{ route('login') }}" class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-xs sm:text-sm font-bold text-deped-gold backdrop-blur-md transition hover:bg-white hover:text-deped-navy">
                    Sign In &rarr;
                </a>
            </div>
        </div>

        <!-- Role Selector Switcher -->
        <div class="mb-6 flex rounded-2xl bg-slate-900/90 p-1.5 border border-white/10 backdrop-blur-md max-w-md mx-auto shadow-xl">
            <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 px-3 rounded-xl text-xs font-bold text-slate-400 hover:text-white hover:bg-white/5 transition">
                Learner Registration (Form 2)
            </a>
            <div class="flex-1 text-center py-2.5 px-3 rounded-xl text-xs font-bold bg-deped-blue text-white shadow-md">
                Mobile Teacher Registration
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="overflow-hidden rounded-3xl border border-white/15 bg-white text-slate-900 shadow-2xl shadow-black/40">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-deped-navy via-slate-900 to-deped-dark p-6 sm:p-8 text-white relative">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/20 p-2 flex items-center justify-center shrink-0 backdrop-blur-md shadow-inner">
                            <svg class="w-full h-full text-deped-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-deped-gold">
                                DepEd SDO San Juan &bull; Instructional Staff
                            </span>
                            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight mt-0.5">
                                ALS Mobile Teacher Onboarding
                            </h1>
                            <p class="text-xs text-slate-300 mt-1 max-w-xl">
                                Register your instructional manager account to facilitate intake reviews, monitor learner telemetry, and handle assigned Community Learning Centers.
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-xs font-bold self-start sm:self-center shrink-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Instant Activation
                    </span>
                </div>
            </div>

            <!-- Form Content -->
            <form action="{{ route('teacher.register.submit') }}" method="POST" class="p-6 sm:p-8 space-y-8">
                @csrf

                @if($errors->any())
                    <div role="alert" class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                        <div class="font-bold flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Please correct the errors below before proceeding:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Section 1: Personal Information -->
                <div>
                    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 mb-4">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-deped-blue text-white text-xs font-black">1</span>
                        <h2 class="text-sm font-black uppercase tracking-wider text-slate-800">Personal Information</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">First Name *</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="e.g. Maria Clara"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name') }}" placeholder="Optional"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Last Name *</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="e.g. Santos"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Name Extension</label>
                            <input type="text" name="name_extension" value="{{ old('name_extension') }}" placeholder="Jr., Sr., III"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Gender *</label>
                            <select name="gender" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Civil Status</label>
                            <select name="civil_status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                                <option value="single" {{ old('civil_status') === 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('civil_status') === 'married' ? 'selected' : '' }}>Married</option>
                                <option value="widowed" {{ old('civil_status') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="separated" {{ old('civil_status') === 'separated' ? 'selected' : '' }}>Separated</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Birthdate</label>
                            <input type="date" name="birthdate" value="{{ old('birthdate', '1992-05-15') }}"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Contact & Station Details -->
                <div>
                    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 mb-4">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-deped-blue text-white text-xs font-black">2</span>
                        <h2 class="text-sm font-black uppercase tracking-wider text-slate-800">Station & Contact Information</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Contact Number *</label>
                            <input type="tel" name="contact_number" value="{{ old('contact_number') }}" required placeholder="0917-123-4567"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">San Juan Barangay</label>
                            <select name="barangay" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                                @foreach($sanJuanBarangays as $bgy)
                                    <option value="{{ $bgy }}" {{ old('barangay') === $bgy ? 'selected' : '' }}>{{ $bgy }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Street Address</label>
                            <input type="text" name="street_address" value="{{ old('street_address') }}" placeholder="Street name / Purok"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Assigned Community Learning Center (CLC)</label>
                            <select name="clc_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                                <option value="">-- No specific center (General Mobile Pool) --</option>
                                @foreach($clcs as $clc)
                                    <option value="{{ $clc->id }}" {{ old('clc_id') == $clc->id ? 'selected' : '' }}>
                                        {{ $clc->clc_name }} (Brgy. {{ $clc->barangay }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-500 mt-1">You can request or adjust CLC assignments through the Division Administrator anytime.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Highest Educational Attainment</label>
                            <input type="text" name="highest_education_attained" value="{{ old('highest_education_attained', 'Bachelor of Secondary Education') }}" placeholder="e.g. BSED, MAEd, BEED"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Account Credentials -->
                <div>
                    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 mb-4">
                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-deped-blue text-white text-xs font-black">3</span>
                        <h2 class="text-sm font-black uppercase tracking-wider text-slate-800">Account Credentials</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username *</label>
                            <input type="text" name="username" value="{{ old('username') }}" required placeholder="e.g. teacher_maria"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                            <p class="text-[11px] text-slate-500 mt-1">Used for signing in across both Web Portal and Android LMS.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address (DepEd or Personal) *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. maria.santos@deped.gov.ph"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password * (Minimum 8 Characters)</label>
                            <input type="password" name="password" required placeholder="••••••••"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Confirm Password *</label>
                            <input type="password" name="password_confirmation" required placeholder="••••••••"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-deped-blue focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- Terms & Privacy -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 leading-relaxed">
                    By registering as an ALS Mobile Teacher, you confirm your adherence to DepEd Order No. 27, s. 2017, the Data Privacy Act of 2012 (RA 10173), and the ethical stewardship of learner records within the Schools Division Office of San Juan City.
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                        &larr; Already have an account? Sign in
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-sm shadow-xl shadow-deped-blue/20 transition-all hover:-translate-y-0.5 active:translate-y-0">
                        Complete Teacher Registration &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
