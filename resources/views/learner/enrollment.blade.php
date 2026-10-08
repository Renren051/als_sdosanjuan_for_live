@extends('layouts.learner')

@section('title', 'My Enrollment Status & Review - SDO San Juan City ALS')

@section('content')
<div class="space-y-6 pt-4" x-data="{ uploadModal: false, activeDocType: '', activeDocTitle: '' }">

    <!-- Page Title Bar -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-deped-blue/10 text-deped-blue text-[11px] font-bold uppercase tracking-wider mb-2">
                <span>ALS Form 2 Intake &bull; Enrolment Tracking</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">My Enrollment Status & Review</h1>
            <p class="text-xs text-slate-500 mt-1">
                Track your official DepEd registration milestones, teacher evaluation notes, and credential checklist.
            </p>
        </div>

        @if($enrollment)
            <div class="flex items-center gap-2 shrink-0">
                <span class="px-3.5 py-1.5 rounded-xl border text-xs font-bold {{ $enrollment->status_badge }}">
                    {{ $enrollment->status_label }}
                </span>
            </div>
        @endif
    </div>

    @if(!$enrollment)
        <!-- No Enrollment Record -->
        <div class="p-8 rounded-3xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 flex items-center justify-center font-bold text-amber-700 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h2 class="text-lg font-bold">No Active ALS Enrollment Record Found</h2>
            <p class="text-xs text-amber-800 mt-1 max-w-md mx-auto">
                You do not have an active enrollment record on file. Please complete your digital intake application or consult your barangay Mobile Teacher.
            </p>
            <div class="mt-5">
                <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md">
                    Submit Intake Application &rarr;
                </a>
            </div>
        </div>
    @else

        <!-- Status Context Banner -->
        @if($enrollment->isEnrolled())
            <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center font-bold text-white shrink-0">
                        <svg class="w-8 h-8 text-deped-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider mb-1">
                            <span>Officially Enrolled &bull; School Year {{ $enrollment->calendar_year }}</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight">
                            Enrolment Approved & Active!
                        </h2>
                        <p class="text-xs text-emerald-100 mt-1">
                            Your credentials have been verified by SDO San Juan City. You are cleared to study official modules and attend CLC sessions.
                        </p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-black/20 border border-white/10 text-center sm:text-right shrink-0">
                    <div class="text-[10px] text-emerald-200 uppercase font-bold tracking-wider">Learner Reference Number (LRN)</div>
                    <div class="font-mono text-xl sm:text-2xl font-black text-deped-gold tracking-widest mt-0.5">
                        {{ $user->lrn ?: 'ASSIGNED' }}
                    </div>
                    <div class="text-[10px] text-emerald-200 mt-1">
                        Enrolled: {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'Active' }}
                    </div>
                </div>
            </div>
        @elseif($enrollment->needsCorrection())
            <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-rose-600 to-red-700 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-start gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center font-bold text-white shrink-0">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider">
                            Action Required &bull; Staff Review Notice
                        </span>
                        <h2 class="text-xl font-black mt-1">Document Corrections Needed</h2>
                        <p class="text-xs text-rose-100 mt-1">
                            Your Mobile Teacher reviewed your submission and flagged one or more documents requiring correction.
                        </p>
                        @if($enrollment->remarks)
                            <div class="mt-3 p-3.5 rounded-2xl bg-black/25 border border-white/15 text-xs text-rose-100 max-w-2xl">
                                <strong>Teacher Remarks:</strong> {{ $enrollment->remarks }}
                            </div>
                        @endif
                    </div>
                </div>
                <a href="{{ route('learner.documents') }}" class="px-5 py-3 rounded-xl bg-white text-rose-700 hover:bg-rose-50 font-bold text-xs uppercase tracking-wider shadow-lg shrink-0 text-center">
                    Correct Documents Now &rarr;
                </a>
            </div>
        @elseif($enrollment->isRejected())
            <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-rose-800 to-red-900 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-start gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center font-bold text-white shrink-0">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider">
                            Application Disapproved / Rejected
                        </span>
                        <h2 class="text-xl font-black mt-1">Application Disapproved</h2>
                        <p class="text-xs text-rose-100 mt-1 max-w-2xl">
                            Your intake application could not be approved for the selected cycle. Please review the evaluator's explanation below.
                        </p>
                        @if($enrollment->remarks)
                            <div class="mt-3 p-3.5 rounded-2xl bg-black/25 border border-white/15 text-xs text-rose-100 max-w-2xl">
                                <strong>Teacher / Evaluator Remarks:</strong> {{ $enrollment->remarks }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($enrollment->isUnderReview())
            <div class="p-6 rounded-3xl bg-blue-50 border border-blue-200 text-blue-950 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Application Under Staff Review</h2>
                        <p class="text-xs text-slate-600 mt-0.5">Your submitted credentials are being reviewed by your assigned Mobile Teacher in SDO San Juan City.</p>
                    </div>
                </div>
                <div class="text-xs font-bold text-blue-800 bg-blue-100/70 px-4 py-2 rounded-xl">
                    {{ $verifiedCount }} of {{ $mandatoryCount }} Mandatory Docs Verified
                </div>
            </div>
        @else
            <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 text-amber-950 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Intake Application Received (Pending Review)</h2>
                        <p class="text-xs text-slate-600 mt-0.5">Your ALS Form 2 registration has been submitted. Please upload your mandatory credentials to speed up review.</p>
                    </div>
                </div>
                <a href="{{ route('learner.documents') }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm shrink-0">
                    Upload Documents &rarr;
                </a>
            </div>
        @endif

        <!-- 5-Step Enrollment Workflow Stepper -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6">ALS Enrollment Journey & Review Milestones</h2>

            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 relative">
                <!-- Step 1: Intake -->
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold mb-2">✓</div>
                    <div class="text-xs font-bold text-emerald-900">1. Intake Submitted</div>
                    <div class="text-[11px] text-emerald-700 mt-1">ALS Form 2 registration recorded</div>
                    <div class="text-[10px] text-slate-400 mt-2">CY {{ $enrollment->calendar_year }}</div>
                </div>

                <!-- Step 2: Upload Documents -->
                @php
                    $step2Complete = $verifiedCount >= $mandatoryCount;
                    $step2HasRejection = $rejectedCount > 0;
                @endphp
                <div class="p-4 rounded-2xl {{ $step2Complete ? 'bg-emerald-50 border border-emerald-200' : ($step2HasRejection ? 'bg-rose-50 border border-rose-200' : 'bg-blue-50 border border-blue-200') }}">
                    <div class="w-8 h-8 rounded-full {{ $step2Complete ? 'bg-emerald-600 text-white' : ($step2HasRejection ? 'bg-rose-600 text-white' : 'bg-blue-600 text-white') }} flex items-center justify-center text-xs font-bold mb-2">
                        {{ $step2Complete ? '✓' : ($step2HasRejection ? '!' : '2') }}
                    </div>
                    <div class="text-xs font-bold {{ $step2Complete ? 'text-emerald-900' : ($step2HasRejection ? 'text-rose-900' : 'text-blue-900') }}">
                        2. Credentials Upload
                    </div>
                    <div class="text-[11px] {{ $step2Complete ? 'text-emerald-700' : ($step2HasRejection ? 'text-rose-700' : 'text-blue-700') }} mt-1">
                        {{ $verifiedCount }} of {{ $mandatoryCount }} mandatory verified
                    </div>
                    <div class="text-[10px] text-slate-400 mt-2">PSA, Brgy, 2x2 Photo</div>
                </div>

                <!-- Step 3: Teacher Review -->
                @php
                    $step3Complete = $enrollment->isEnrolled() || $enrollment->enrollment_status === 'completed';
                    $step3Current = $enrollment->isUnderReview() || $enrollment->needsCorrection();
                @endphp
                <div class="p-4 rounded-2xl {{ $step3Complete ? 'bg-emerald-50 border border-emerald-200' : ($step3Current ? ($enrollment->needsCorrection() ? 'bg-rose-50 border border-rose-200' : 'bg-blue-50 border border-blue-200') : 'bg-slate-50 border border-slate-200') }}">
                    <div class="w-8 h-8 rounded-full {{ $step3Complete ? 'bg-emerald-600 text-white' : ($step3Current ? ($enrollment->needsCorrection() ? 'bg-rose-600 text-white' : 'bg-blue-600 text-white') : 'bg-slate-300 text-slate-700') }} flex items-center justify-center text-xs font-bold mb-2">
                        {{ $step3Complete ? '✓' : '3' }}
                    </div>
                    <div class="text-xs font-bold {{ $step3Complete ? 'text-emerald-900' : ($step3Current ? 'text-blue-900' : 'text-slate-700') }}">
                        3. Teacher Evaluation
                    </div>
                    <div class="text-[11px] {{ $step3Complete ? 'text-emerald-700' : ($step3Current ? 'text-blue-700' : 'text-slate-500') }} mt-1">
                        {{ $enrollment->status_label }}
                    </div>
                    <div class="text-[10px] text-slate-400 mt-2">Document validation</div>
                </div>

                <!-- Step 4: CLC & Facilitator -->
                <div class="p-4 rounded-2xl {{ $enrollment->clc_id ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200' }}">
                    <div class="w-8 h-8 rounded-full {{ $enrollment->clc_id ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700' }} flex items-center justify-center text-xs font-bold mb-2">
                        {{ $enrollment->clc_id ? '✓' : '4' }}
                    </div>
                    <div class="text-xs font-bold {{ $enrollment->clc_id ? 'text-emerald-900' : 'text-slate-700' }}">
                        4. Center & Teacher
                    </div>
                    <div class="text-[11px] {{ $enrollment->clc_id ? 'text-emerald-700' : 'text-slate-500' }} mt-1">
                        {{ $enrollment->clc?->clc_name ? Str::limit($enrollment->clc->clc_name, 20) : 'Pending' }}
                    </div>
                    <div class="text-[10px] text-slate-400 mt-2">Community allocation</div>
                </div>

                <!-- Step 5: Official LRN & Enrolled -->
                <div class="p-4 rounded-2xl {{ $enrollment->isEnrolled() ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200' }}">
                    <div class="w-8 h-8 rounded-full {{ $enrollment->isEnrolled() ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-700' }} flex items-center justify-center text-xs font-bold mb-2">
                        {{ $enrollment->isEnrolled() ? '✓' : '5' }}
                    </div>
                    <div class="text-xs font-bold {{ $enrollment->isEnrolled() ? 'text-emerald-900' : 'text-slate-700' }}">
                        5. Official Enrolment
                    </div>
                    <div class="text-[11px] {{ $enrollment->isEnrolled() ? 'text-emerald-700' : 'text-slate-500' }} mt-1">
                        {{ $user->lrn ? 'LRN: ' . $user->lrn : 'LRN Assigned on Enrolment' }}
                    </div>
                    <div class="text-[10px] text-slate-400 mt-2">ALS Masterlist Form 1</div>
                </div>
            </div>
        </div>

        <!-- Enrollment Details & Center Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Program Track & Application Info -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="text-[10px] font-bold text-deped-blue uppercase tracking-wider mb-1">Academic Program</div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $enrollment->track_label }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Department of Education ALS Curriculum</p>

                    <div class="mt-5 space-y-3 text-xs">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-slate-500">School Calendar Year:</span>
                            <strong class="text-slate-900">{{ $enrollment->calendar_year }}</strong>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-slate-500">Delivery Mode:</span>
                            <strong class="text-slate-900">{{ $enrollment->delivery_mode_label }}</strong>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-slate-500">Schedule Preference:</span>
                            <strong class="text-slate-900">{{ ucfirst(str_replace('_', ' ', $enrollment->schedule_preference ?: 'flexible')) }}</strong>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-slate-500">Intake Registration Date:</span>
                            <span class="text-slate-700">{{ $enrollment->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Official Enrolment Date:</span>
                            <span class="font-bold {{ $enrollment->enrolled_at ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'Pending verification' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($enrollment->remarks)
                    <div class="mt-5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Teacher Review Remarks</div>
                        <p class="text-slate-700 mt-1">{{ $enrollment->remarks }}</p>
                    </div>
                @endif
            </div>

            <!-- Assigned Community Learning Center -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Learning Facility</div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $enrollment->clc?->clc_name ?: 'Center Pending Assignment' }}</h3>
                    <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span>Brgy. {{ $enrollment->clc?->barangay ?: 'San Juan City' }}</span>
                    </div>

                    @if($enrollment->clc)
                        <div class="mt-4 space-y-2.5 text-xs text-slate-600">
                            @if($enrollment->clc->physical_location_notes)
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 text-slate-700">
                                    <span class="font-bold block text-[10px] uppercase text-slate-400 mb-0.5">Location Notes:</span>
                                    {{ $enrollment->clc->physical_location_notes }}
                                </div>
                            @endif
                            <div class="flex items-center justify-between text-xs pt-1">
                                <span class="text-slate-500">Center Code:</span>
                                <span class="font-mono font-bold text-slate-800">{{ $enrollment->clc->clc_code }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Facility Status:</span>
                                <span class="font-bold text-emerald-600 uppercase text-[11px]">{{ $enrollment->clc->status }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 mt-4 p-3 rounded-2xl bg-slate-50">
                            Our ALS coordinator will assign you to the nearest Community Learning Center based on your barangay of residence.
                        </p>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500">
                    Schools Division of San Juan City
                </div>
            </div>

            <!-- Assigned Mobile Teacher -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="text-[10px] font-bold text-purple-600 uppercase tracking-wider mb-1">Instructional Facilitator</div>
                    <h3 class="text-lg font-bold text-slate-900">
                        {{ $enrollment->assignedTeacher ? $enrollment->assignedTeacher->full_name : 'SDO San Juan Teacher Pool' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Official Mobile Teacher / ALS Implementer</p>

                    @if($enrollment->assignedTeacher)
                        <div class="mt-4 space-y-3 text-xs">
                            @if($enrollment->assignedTeacher->profile?->contact_number)
                                <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 text-slate-700">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <div>
                                        <div class="text-[10px] text-slate-400 uppercase font-bold">Contact Number</div>
                                        <div class="font-mono font-bold">{{ $enrollment->assignedTeacher->profile->contact_number }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($enrollment->assignedTeacher->email)
                                <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 text-slate-700">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <div>
                                        <div class="text-[10px] text-slate-400 uppercase font-bold">Official DepEd Email</div>
                                        <div class="font-medium truncate">{{ $enrollment->assignedTeacher->email }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-slate-400 mt-4 p-3 rounded-2xl bg-slate-50">
                            A Mobile Teacher will be designated upon document evaluation.
                        </p>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500">
                    San Juan City Mobile Teacher Network
                </div>
            </div>
        </div>

        <!-- Credential Checklist & Verification Queue Table -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Credential Document Checklist</h2>
                    <p class="text-xs text-slate-500">
                        Staff review status for all mandatory and optional enrollment records.
                    </p>
                </div>
                <a href="{{ route('learner.documents') }}" class="px-4 py-2 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-sm self-start sm:self-center">
                    Manage Documents &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase font-bold border-y border-slate-200/80">
                        <tr>
                            <th class="py-3.5 px-4">Requirement</th>
                            <th class="py-3.5 px-4">Priority</th>
                            <th class="py-3.5 px-4">Verification Status</th>
                            <th class="py-3.5 px-4">Reviewer & Notes</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($requiredDocs as $type => $info)
                            @php
                                $doc = $documents->get($type);
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900">{{ $info['title'] }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $info['description'] }}</div>
                                    @if($doc)
                                        <div class="text-[10px] text-slate-500 font-mono mt-1">
                                            {{ $doc->file_name }} &bull; {{ $doc->formatted_file_size }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $info['required'] ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $info['required'] ? 'Mandatory' : 'Optional' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($doc)
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $doc->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : ($doc->verification_status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                            {{ ucfirst($doc->verification_status) }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">
                                            Not Uploaded
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @if($doc && $doc->verification_notes)
                                        <div class="p-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px]">
                                            <strong>Correction Note:</strong> {{ $doc->verification_notes }}
                                        </div>
                                    @elseif($doc && $doc->reviewer)
                                        <div class="text-[11px] text-emerald-700 font-medium">
                                            Verified by {{ $doc->reviewer->full_name }}
                                            @if($doc->reviewed_at)
                                                <span class="text-slate-400">({{ $doc->reviewed_at->format('M d, Y') }})</span>
                                            @endif
                                        </div>
                                    @elseif($doc)
                                        <span class="text-slate-400 text-[11px]">Pending Mobile Teacher review</span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">—</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($doc)
                                            <a href="{{ route('learner.documents.file', $doc->id) }}" target="_blank"
                                               class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px]">
                                                View File
                                            </a>
                                        @endif
                                        <button type="button"
                                                @click="activeDocType = '{{ $type }}'; activeDocTitle = '{{ $info['title'] }}'; uploadModal = true"
                                                class="px-3 py-1 rounded-lg {{ $doc ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-deped-blue hover:bg-deped-dark text-white' }} font-bold text-[11px]">
                                            {{ $doc ? 'Replace' : 'Upload' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @endif

    <!-- Upload / Replace Modal -->
    <div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="uploadModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900" x-text="'Upload: ' + activeDocTitle"></h3>
                    <button @click="uploadModal = false" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
                </div>

                <form action="{{ route('learner.documents.upload') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="document_type" :value="activeDocType">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Select File (PDF, JPG, PNG)</label>
                        <p class="text-[11px] text-slate-500 mb-2">Ensure text, dry seal, and signatures are legible.</p>
                        <input type="file" name="document_file" required accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-deped-blue/10 file:text-deped-blue hover:file:bg-deped-blue/20">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="uploadModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-deped-blue hover:bg-deped-dark rounded-xl shadow-md">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
