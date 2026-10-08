@extends('layouts.learner')

@section('title', 'Learner Dashboard - SDO San Juan City ALS')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Status Banner Alert -->
    @if(!$enrollment)
        <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center font-bold text-amber-700 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold">No Active ALS Enrollment Record Found</h2>
                    <p class="text-xs text-amber-800 mt-0.5">Please consult an ALS Mobile Teacher or complete your online intake registration.</p>
                </div>
            </div>
            <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm self-start sm:self-center shrink-0">
                Submit Intake
            </a>
        </div>
    @elseif($enrollment->isEnrolled())
        <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center font-bold text-white shrink-0">
                    <svg class="w-8 h-8 text-deped-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider mb-1">
                        <span>Officially Enrolled &bull; CY {{ $enrollment->calendar_year }}</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black tracking-tight">
                        Welcome back, {{ $user->profile?->first_name ?: $user->username }}!
                    </h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-emerald-100">
                        <span>Track: <strong>{{ $enrollment->track_label }}</strong></span>
                        <span>&bull;</span>
                        <span>Mode: <strong>{{ $enrollment->delivery_mode_label }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-black/20 border border-white/10 text-center sm:text-right shrink-0">
                <div class="text-[10px] text-emerald-200 uppercase font-bold tracking-wider">Learner Reference Number (LRN)</div>
                <div class="font-mono text-xl sm:text-2xl font-black text-deped-gold tracking-widest mt-0.5">
                    {{ $user->lrn ?: 'ASSIGNED' }}
                </div>
                <div class="mt-2">
                    <a href="{{ route('learner.enrollment') }}" class="text-[11px] font-bold text-white hover:text-deped-gold underline">
                        View Enrollment Details &rarr;
                    </a>
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
                        Action Required: Document Correction
                    </span>
                    <h2 class="text-xl font-black mt-1">Correction Notice from Mobile Teacher</h2>
                    <div class="mt-2 p-3 rounded-xl bg-black/20 border border-white/10 text-xs text-rose-100 max-w-xl">
                        <strong>Teacher Remarks:</strong> {{ $enrollment->remarks }}
                    </div>
                </div>
            </div>
            <a href="{{ route('learner.documents') }}" class="px-5 py-3 rounded-xl bg-white text-rose-700 hover:bg-rose-50 font-bold text-xs uppercase tracking-wider shadow-lg shrink-0 text-center">
                Re-upload Documents &rarr;
            </a>
        </div>
    @elseif($enrollment->isRejected())
        <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-rose-800 to-red-900 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-start gap-5">
                <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center font-bold text-white shrink-0">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider">
                            Application Disapproved / Rejected
                        </span>
                        @if($enrollment?->tracking_number)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-black/25 text-white text-xs font-mono font-bold">
                                <span>REF: {{ $enrollment->tracking_number }}</span>
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl font-black mt-1">Enrolment Application Rejected</h2>
                    <p class="text-xs text-rose-100 mt-1 max-w-xl">
                        Your application could not be approved at this time. Please review the official review remarks below or consult with your designated ALS Mobile Teacher.
                    </p>
                    @if($enrollment->remarks)
                        <div class="mt-3 p-3.5 rounded-2xl bg-black/25 border border-white/15 text-xs text-rose-100 max-w-2xl">
                            <strong>Teacher / Evaluator Remarks:</strong> {{ $enrollment->remarks }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                <a href="{{ route('learner.enrollment') }}" class="px-5 py-3 rounded-xl bg-white text-rose-800 hover:bg-rose-50 font-bold text-xs uppercase tracking-wider shadow-lg shrink-0 text-center">
                    Review Dossier
                </a>
            </div>
        </div>
    @else
        <div class="p-6 sm:p-7 rounded-3xl bg-amber-500/10 border border-amber-300 text-amber-950 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-200/80 text-amber-800 text-[11px] font-bold uppercase tracking-wider">
                            Pending Document Verification
                        </span>
                        @if($enrollment?->tracking_number)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white border border-amber-300 text-slate-800 text-xs font-mono font-bold shadow-2xs">
                                <span class="text-slate-400 font-sans text-[10px] font-semibold">REF:</span>
                                <span>{{ $enrollment->tracking_number }}</span>
                            </span>
                        @endif
                    </div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900">Enrolment Application Under Review</h2>
                    <p class="text-xs text-slate-600 max-w-xl">
                        Your ALS Form 2 intake has been received. Your assigned Mobile Teacher will verify your PSA Birth Certificate and Barangay Clearance before official enrollment confirmation.
                    </p>
                    @if($enrollment?->created_at)
                        <div class="text-[11px] text-amber-800/80 font-medium pt-0.5">
                            Submitted on {{ $enrollment->created_at->timezone('Asia/Manila')->format('F d, Y \a\t h:i A') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                <a href="{{ route('learner.enrollment') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs shadow-sm text-center">
                    Track Journey
                </a>
                <a href="{{ route('learner.documents') }}" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs uppercase tracking-wider shadow-sm text-center transition-all hover:scale-[1.02] active:scale-[0.98]">
                    Upload Documents &rarr;
                </a>
            </div>
        </div>
    @endif

    <!-- Key Metrics & Journey Snapshot Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Enrollment Status -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-deped-blue flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] uppercase font-bold text-slate-400">Enrolment Status</div>
                <div class="text-sm font-bold text-slate-900 truncate mt-0.5">
                    {{ $enrollment ? $enrollment->status_label : 'Not Enrolled' }}
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5">
                    {{ $enrollment?->calendar_year ? 'CY ' . $enrollment->calendar_year : 'No Cycle' }}
                </div>
            </div>
        </div>

        <!-- Metric 2: Credentials Verified -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] uppercase font-bold text-slate-400">Mandatory Documents</div>
                <div class="text-sm font-bold text-slate-900 mt-0.5">
                    {{ $verifiedMandatoryCount }} of {{ $mandatoryCount }} Verified
                </div>
                <div class="text-[11px] {{ $rejectedCount > 0 ? 'text-rose-600 font-bold' : 'text-slate-500' }} mt-0.5">
                    {{ $rejectedCount > 0 ? $rejectedCount . ' Needs Correction' : 'Official Credentials' }}
                </div>
            </div>
        </div>

        <!-- Metric 3: Completed Modules -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] uppercase font-bold text-slate-400">Completed Modules</div>
                <div class="text-sm font-bold text-slate-900 mt-0.5">
                    {{ $completedCount }} Modules Finished
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5">
                    {{ $inProgressCount }} In Progress
                </div>
            </div>
        </div>

        <!-- Metric 4: Curriculum Progress -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div class="min-w-0 w-full">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Curriculum Progress</span>
                    <span class="text-xs font-bold text-emerald-700">{{ $overallProgressPercent }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 mt-1.5 overflow-hidden">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $overallProgressPercent }}%"></div>
                </div>
                <div class="text-[10px] text-slate-400 mt-1">
                    {{ $totalPublishedModules }} Published Modules
                </div>
            </div>
        </div>
    </div>

    <!-- Continue Studying Banner (if there's a module in progress) -->
    @if($continueReading)
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-900 text-white shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-deped-blue/30 border border-white/20 flex items-center justify-center font-bold text-deped-gold shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-deped-gold uppercase tracking-wider">Currently Reading</div>
                    <h3 class="text-base font-bold text-white mt-0.5">
                        {{ $continueReading->module?->title }}
                    </h3>
                    <div class="text-xs text-slate-300 mt-0.5">
                        {{ $continueReading->module?->strand?->code }} &bull; You were on <strong>Page {{ $continueReading->last_page_read }}</strong> of {{ $continueReading->total_pages ?: '?' }} ({{ $continueReading->progress_percent }}%)
                    </div>
                </div>
            </div>
            <a href="{{ route('learner.modules.read', ['id' => $continueReading->module_id, 'page' => $continueReading->last_page_read]) }}" 
               class="px-5 py-2.5 rounded-xl bg-deped-gold hover:bg-yellow-400 text-slate-950 font-bold text-xs uppercase tracking-wider shadow-md shrink-0 flex items-center justify-center gap-2">
                <span>Resume Reading (Page {{ $continueReading->last_page_read }}) &rarr;</span>
            </a>
        </div>
    @endif

    <!-- Center & Facilitator Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Assigned Learning Center -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="text-[10px] font-bold text-deped-blue uppercase tracking-wider mb-1">Assigned Community Center</div>
                <h3 class="text-lg font-bold text-slate-900">{{ $enrollment?->clc?->clc_name ?: 'Center Pending Assignment' }}</h3>
                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span>Brgy. {{ $enrollment?->clc?->barangay ?: 'San Juan City' }}</span>
                </div>
                @if($enrollment?->clc?->physical_location_notes)
                    <p class="text-xs text-slate-600 mt-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        {{ $enrollment->clc->physical_location_notes }}
                    </p>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500">
                Shift: <strong>{{ ucfirst(str_replace('_', ' ', $enrollment?->schedule_preference ?: 'flexible')) }}</strong>
            </div>
        </div>

        <!-- Assigned Mobile Teacher -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">ALS Facilitator</div>
                <h3 class="text-lg font-bold text-slate-900">
                    {{ $enrollment?->assignedTeacher ? $enrollment->assignedTeacher->full_name : 'SDO San Juan Teacher Pool' }}
                </h3>
                <p class="text-xs text-slate-500 mt-1">Official Mobile Teacher in your Barangay</p>

                <div class="mt-4 space-y-2 text-xs">
                    @if($enrollment?->assignedTeacher?->profile?->contact_number)
                        <div class="flex items-center gap-2 text-slate-700">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span class="font-mono font-bold">{{ $enrollment->assignedTeacher->profile->contact_number }}</span>
                        </div>
                    @endif
                    @if($enrollment?->assignedTeacher?->email)
                        <div class="flex items-center gap-2 text-slate-700">
                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="truncate">{{ $enrollment->assignedTeacher->email }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500">
                Division of San Juan City &bull; ALS Unit
            </div>
        </div>

        <!-- Document Progress Summary -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="text-[10px] font-bold text-purple-600 uppercase tracking-wider mb-1">Credential Check</div>
                <h3 class="text-lg font-bold text-slate-900">Enrolment Documents</h3>
                <p class="text-xs text-slate-500 mt-1">PSA Birth Cert, Barangay Clearance, 2x2 Photo</p>

                <div class="mt-4 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span>PSA Birth Certificate:</span>
                        @if(isset($documents['psa_birth_cert']))
                            <span class="font-bold {{ $documents['psa_birth_cert']->verification_status === 'verified' ? 'text-emerald-600' : ($documents['psa_birth_cert']->verification_status === 'rejected' ? 'text-rose-600' : 'text-amber-600') }}">
                                {{ ucfirst($documents['psa_birth_cert']->verification_status) }}
                            </span>
                        @else
                            <span class="text-rose-500 font-bold">Missing</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Barangay Clearance:</span>
                        @if(isset($documents['barangay_clearance']))
                            <span class="font-bold {{ $documents['barangay_clearance']->verification_status === 'verified' ? 'text-emerald-600' : ($documents['barangay_clearance']->verification_status === 'rejected' ? 'text-rose-600' : 'text-amber-600') }}">
                                {{ ucfirst($documents['barangay_clearance']->verification_status) }}
                            </span>
                        @else
                            <span class="text-rose-500 font-bold">Missing</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span>2x2 ID Photo:</span>
                        @if(isset($documents['photo_id']))
                            <span class="font-bold {{ $documents['photo_id']->verification_status === 'verified' ? 'text-emerald-600' : ($documents['photo_id']->verification_status === 'rejected' ? 'text-rose-600' : 'text-amber-600') }}">
                                {{ ucfirst($documents['photo_id']->verification_status) }}
                            </span>
                        @else
                            <span class="text-rose-500 font-bold">Missing</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('learner.documents') }}" class="text-xs font-bold text-deped-blue hover:underline">
                    Manage Documents &rarr;
                </a>
                <a href="{{ route('learner.enrollment') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                    Full Checklist &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Self-Learning Module Access & Telemetry Section -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Self-Learning Modules & Progress</h2>
                <p class="text-xs text-slate-500">Read online on your browser or download to study offline</p>
            </div>
            <a href="{{ route('learner.modules') }}" class="px-5 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md shadow-deped-blue/20 transition-all self-start sm:self-center">
                Browse All Modules &rarr;
            </a>
        </div>

        @if($progressRecords->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($progressRecords as $rec)
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold text-white uppercase" style="background-color: {{ $rec->module?->strand?->color_code ?? '#2563eb' }}">
                                    {{ $rec->module?->strand?->code }}
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $rec->isCompleted() ? 'bg-emerald-100 text-emerald-800' : ($rec->isInProgress() ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $rec->completion_status)) }}
                                </span>
                            </div>
                            <div class="text-xs font-bold text-slate-800 mt-2 line-clamp-2">
                                {{ $rec->module?->title }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                {{ $rec->module?->module_number }} &bull; {{ $rec->module?->level }}
                            </div>
                        </div>

                        <div class="mt-4 pt-2 border-t border-slate-200/60">
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden mb-1">
                                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ $rec->progress_percent }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400 mb-3">
                                <span>Page {{ $rec->last_page_read }} of {{ $rec->total_pages ?: '?' }}</span>
                                <span class="font-bold text-slate-600">{{ $rec->progress_percent }}%</span>
                            </div>

                            <a href="{{ route('learner.modules.read', ['id' => $rec->module_id, 'page' => $rec->last_page_read]) }}"
                               class="w-full block text-center py-2 rounded-xl {{ $rec->isCompleted() ? 'bg-slate-200 hover:bg-slate-300 text-slate-800' : 'bg-deped-blue hover:bg-deped-dark text-white' }} font-bold text-xs transition-colors">
                                {{ $rec->isCompleted() ? 'Review Module' : 'Resume (Page ' . $rec->last_page_read . ')' }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-500">
                You haven't opened any modules yet. Click "Browse All Modules" to begin studying!
            </div>
        @endif
    </div>
</div>
@endsection
