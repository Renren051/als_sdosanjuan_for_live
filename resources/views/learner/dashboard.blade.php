@extends('layouts.learner')

@section('title', 'Learner Dashboard - SDO San Juan City ALS')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Status Banner Alert -->
    @if(!$enrollment)
        <div class="p-6 rounded-3xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center font-bold text-amber-700 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold">No Active ALS Enrolment Found</h2>
                    <p class="text-xs text-amber-800 mt-0.5">Please consult with an ALS Mobile Teacher or complete your online intake registration.</p>
                </div>
            </div>
            <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm">
                Submit Intake
            </a>
        </div>
    @elseif($enrollment->enrollment_status === 'enrolled')
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
                        Welcome, {{ $user->profile?->first_name }}!
                    </h2>
                    <div class="flex items-center gap-2 mt-1 text-xs text-emerald-100">
                        <span>Track: <strong>{{ $enrollment->track_label }}</strong></span>
                        <span>&bull;</span>
                        <span>Mode: <strong>{{ ucfirst(str_replace('_', ' ', $enrollment->delivery_mode)) }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-black/20 border border-white/10 text-center sm:text-right">
                <div class="text-[10px] text-emerald-200 uppercase font-bold tracking-wider">Learner Reference Number (LRN)</div>
                <div class="font-mono text-xl sm:text-2xl font-black text-deped-gold tracking-widest mt-0.5">
                    {{ $user->lrn ?: 'ASSIGNED' }}
                </div>
            </div>
        </div>
    @elseif($enrollment->enrollment_status === 'needs_correction')
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
    @else
        <div class="p-6 rounded-3xl bg-amber-500/10 border border-amber-300 text-amber-900 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Enrolment Application Under Review</h2>
                    <p class="text-xs text-slate-600 mt-0.5">Your ALS Form 2 intake has been received. Your assigned Mobile Teacher will verify your credential documents.</p>
                </div>
            </div>
            <a href="{{ route('learner.documents') }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm self-start sm:self-center shrink-0">
                Check Documents &rarr;
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
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
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
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span class="font-mono font-bold">{{ $enrollment->assignedTeacher->profile->contact_number }}</span>
                        </div>
                    @endif
                    @if($enrollment?->assignedTeacher?->email)
                        <div class="flex items-center gap-2 text-slate-700">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
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
                    @php
                        $docs = $user->documents->keyBy('document_type');
                    @endphp
                    <div class="flex items-center justify-between">
                        <span>PSA Birth Certificate:</span>
                        @if(isset($docs['psa_birth_cert']))
                            <span class="font-bold text-emerald-600">{{ ucfirst($docs['psa_birth_cert']->verification_status) }}</span>
                        @else
                            <span class="text-rose-500 font-bold">Missing</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Barangay Clearance:</span>
                        @if(isset($docs['barangay_clearance']))
                            <span class="font-bold text-emerald-600">{{ ucfirst($docs['barangay_clearance']->verification_status) }}</span>
                        @else
                            <span class="text-rose-500 font-bold">Missing</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span>2x2 ID Photo:</span>
                        @if(isset($docs['photo_id']))
                            <span class="font-bold text-emerald-600">{{ ucfirst($docs['photo_id']->verification_status) }}</span>
                        @else
                            <span class="text-rose-500 font-bold">Missing</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('learner.documents') }}" class="text-xs font-bold text-deped-blue hover:underline">
                    Manage Documents &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Self-Learning Module Access & Telemetry Section -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Self-Learning Modules & Progress</h2>
                <p class="text-xs text-slate-500">Read online on your browser or download to your Android phone for offline studying</p>
            </div>
            <a href="{{ route('learner.modules') }}" class="px-5 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md shadow-deped-blue/20 transition-all self-start sm:self-center">
                Browse All Modules &rarr;
            </a>
        </div>

        @if($progressRecords->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($progressRecords as $rec)
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                        <div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold text-white uppercase" style="background-color: {{ $rec->module?->strand?->color_code ?? '#2563eb' }}">
                                {{ $rec->module?->strand?->code }}
                            </span>
                            <div class="text-xs font-bold text-slate-800 mt-2 line-clamp-1">
                                {{ $rec->module?->title }}
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1">
                                Status: <strong class="text-slate-800">{{ ucfirst(str_replace('_', ' ', $rec->completion_status)) }}</strong>
                            </div>
                        </div>

                        <div class="mt-4 pt-2 border-t border-slate-200/60">
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden mb-1">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $rec->progress_percent }}%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span>Page {{ $rec->last_page_read }} of {{ $rec->total_pages ?: '?' }}</span>
                                <span>{{ $rec->progress_percent }}%</span>
                            </div>
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
