@extends('layouts.admin')

@section('title', 'DepEd ALS Form 2 (Learner Enrolment Form) - SDO San Juan City')
@section('page_title', 'ALS Form 2: Learner Enrolment Form Generator')

@push('styles')
<style>
    @media print {
        body {
            background-color: #ffffff !important;
            font-size: 8.5pt !important;
            color: #000000 !important;
        }
        aside, header, nav, footer, .no-print, #chat-widget-container, .btn-print, .btn-export {
            display: none !important;
        }
        main {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }
        .sheet-container {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
        .page-break-avoid {
            page-break-inside: avoid !important;
        }
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Learner Selector & PDF Export Bar -->
    <div class="no-print bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.reports.form2') }}" class="flex flex-wrap items-center gap-3">
            @if($clcs->isNotEmpty())
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filter Center</label>
                    <select name="clc_id" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-deped-blue/20">
                        <option value="">All Assigned Centers</option>
                        @foreach($clcs as $clc)
                            <option value="{{ $clc->id }}" {{ request('clc_id') == $clc->id ? 'selected' : '' }}>
                                {{ $clc->clc_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="min-w-[280px]">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Select Learner</label>
                <select name="learner_id" onchange="this.form.submit()" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-deped-blue/20">
                    @forelse($learners as $l)
                        <option value="{{ $l->id }}" {{ $selectedLearner && $selectedLearner->id == $l->id ? 'selected' : '' }}>
                            {{ $l->profile ? $l->profile->last_name . ', ' . $l->profile->first_name : $l->username }} 
                            ({{ $l->lrn ?: 'No LRN' }}) &bull; {{ $l->latestEnrollment?->clc?->clc_name ?: 'No CLC' }}
                        </option>
                    @empty
                        <option value="">No learners found matching criteria</option>
                    @endforelse
                </select>
            </div>
        </form>

        @if($selectedLearner)
            <div class="flex items-center gap-2 self-start lg:self-center">
                <a href="{{ route('admin.reports.form2.pdf', $selectedLearner->id) }}" target="_blank"
                   class="btn-export inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md shadow-deped-blue/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Download DepEd Form 2 (PDF)</span>
                </a>
                <button type="button" onclick="window.print()" class="btn-print px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 transition-colors">
                    Print Form
                </button>
            </div>
        @endif
    </div>

    <!-- Official DepEd Form 2 Preview Canvas -->
    @if($selectedLearner)
        @php
            $profile = $selectedLearner->profile;
            $enr = $selectedLearner->latestEnrollment;
            $submittedDocTypes = $selectedLearner->documents ? $selectedLearner->documents->pluck('document_type')->toArray() : [];
            $hasPsa = in_array(\App\Models\LearnerDocument::TYPE_PSA_BIRTH_CERT, $submittedDocTypes);
            $hasBrgy = in_array(\App\Models\LearnerDocument::TYPE_BARANGAY_CLEARANCE, $submittedDocTypes);
            $hasSf10 = in_array(\App\Models\LearnerDocument::TYPE_FORM_137_SF10, $submittedDocTypes);
            $hasPhoto = in_array(\App\Models\LearnerDocument::TYPE_PHOTO_ID, $submittedDocTypes);
        @endphp
        <div class="sheet-container bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-12 max-w-4xl mx-auto space-y-6">
            <!-- Header Table -->
            <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                <div>
                    <div class="text-[11px] font-bold text-deped-blue uppercase tracking-wider">ANNEX 2 &bull; ALS FORM 2</div>
                    <div class="text-[9px] text-slate-400">(Revised 2019)</div>
                </div>
                <div class="text-center">
                    <p class="text-xs font-bold text-slate-700 uppercase">Republic of the Philippines &bull; Department of Education</p>
                    <p class="text-sm font-black text-deped-blue uppercase tracking-wide">Schools Division Office &bull; San Juan City</p>
                    <h2 class="text-base font-black text-slate-900 uppercase mt-0.5">LEARNER ENROLMENT FORM (ALS FORM 2)</h2>
                    <p class="text-[11px] text-slate-500 italic">Alternative Learning System (ALS)</p>
                </div>
                <div class="w-20 h-20 border-2 border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center text-[9px] text-slate-400 text-center font-bold">
                    <span>2x2 ID Photo</span>
                </div>
            </div>

            <!-- Identifiers Strip -->
            <div class="grid grid-cols-3 gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <div>
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">Learner Reference Number (LRN)</span>
                    <span class="font-mono font-bold text-slate-900 text-sm tracking-wider">{{ $selectedLearner->lrn ?: 'NOT YET ASSIGNED' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">Division / Municipality</span>
                    <span class="font-bold text-slate-900">SDO San Juan City</span>
                </div>
                <div>
                    <span class="text-slate-400 font-bold block text-[10px] uppercase">Calendar Year</span>
                    <span class="font-bold text-slate-900">{{ $enr ? $enr->calendar_year : '2026' }}</span>
                </div>
            </div>

            <!-- Part 1: Basic Information -->
            <div>
                <div class="bg-deped-blue text-white px-3 py-1.5 font-bold text-xs uppercase tracking-wider rounded-t-lg">
                    Part I: Learner's Personal Information
                </div>
                <div class="border border-t-0 border-slate-200 p-4 rounded-b-lg space-y-3 text-xs">
                    <div class="grid grid-cols-4 gap-3">
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Last Name</span>
                            <span class="font-bold text-slate-800">{{ $profile?->last_name ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">First Name</span>
                            <span class="font-bold text-slate-800">{{ $profile?->first_name ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Middle Name</span>
                            <span class="font-bold text-slate-800">{{ $profile?->middle_name ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Name Ext.</span>
                            <span class="font-bold text-slate-800">{{ $profile?->name_extension ?: 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-3 pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Birthdate</span>
                            <span class="font-semibold text-slate-800">{{ $profile && $profile->birthdate ? $profile->birthdate->format('M d, Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Age</span>
                            <span class="font-semibold text-slate-800">{{ $profile?->age ? $profile->age . ' yrs old' : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Sex</span>
                            <span class="font-semibold text-slate-800">{{ $profile?->gender ? ucfirst($profile->gender) : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Civil Status</span>
                            <span class="font-semibold text-slate-800">{{ $profile?->civil_status ? ucfirst($profile->civil_status) : '-' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Mother Tongue</span>
                            <span class="font-semibold text-slate-800">{{ $profile?->mother_tongue ?: 'Tagalog / Filipino' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Contact Number</span>
                            <span class="font-semibold text-slate-800 font-mono">{{ $profile?->contact_number ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Social Media</span>
                            <span class="font-semibold text-slate-800">{{ $profile?->facebook_name ?: 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-400 uppercase text-[10px] font-bold block">Complete Residential Address</span>
                        <span class="font-bold text-slate-800">{{ $profile?->full_address ?: ($profile ? $profile->street_address . ', Brgy. ' . $profile->barangay . ', ' . $profile->city : 'San Juan City') }}</span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">4Ps Beneficiary</span>
                            <span class="font-semibold">{{ $profile?->is_4ps_beneficiary ? 'YES' : 'NO' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Indigenous People</span>
                            <span class="font-semibold">{{ $profile?->is_ip ? 'YES (' . $profile->ip_community_name . ')' : 'NO' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 uppercase text-[10px] font-bold block">Person with Disability</span>
                            <span class="font-semibold">{{ $profile?->is_pwd ? 'YES (' . $profile->pwd_disability_type . ')' : 'NO' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Part 2: Educational Background -->
            <div>
                <div class="bg-deped-blue text-white px-3 py-1.5 font-bold text-xs uppercase tracking-wider rounded-t-lg">
                    Part II: Educational Background
                </div>
                <div class="border border-t-0 border-slate-200 p-4 rounded-b-lg text-xs grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-slate-400 uppercase text-[10px] font-bold block">Highest Grade / Level Completed</span>
                        <span class="font-bold text-slate-800">{{ $profile?->highest_education_attained ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase text-[10px] font-bold block">Prior Formal School</span>
                        <span class="font-semibold text-slate-800">San Juan Elementary / High School (DepEd NCR)</span>
                    </div>
                </div>
            </div>

            <!-- Part 3: Program Track & Learning Center -->
            <div>
                <div class="bg-deped-blue text-white px-3 py-1.5 font-bold text-xs uppercase tracking-wider rounded-t-lg">
                    Part III: Program Track & Learning Center Allocation
                </div>
                <div class="border border-t-0 border-slate-200 p-4 rounded-b-lg text-xs grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-slate-400 uppercase text-[10px] font-bold block">Program Track Applied</span>
                        <span class="font-black text-deped-blue text-sm">{{ $enr?->track_label ?: 'A&E Junior High School' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase text-[10px] font-bold block">Community Learning Center (CLC)</span>
                        <span class="font-bold text-slate-800">{{ $enr?->clc?->clc_name ?: 'To be assigned' }} (Brgy. {{ $enr?->clc?->barangay ?: 'San Juan' }})</span>
                    </div>
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-400 uppercase text-[10px] font-bold block">Preferred Delivery Mode</span>
                        <span class="font-semibold text-slate-800">{{ $enr ? ucfirst(str_replace('_', ' ', $enr->delivery_mode)) : 'Modular' }}</span>
                    </div>
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-400 uppercase text-[10px] font-bold block">Assigned Mobile Teacher</span>
                        <span class="font-bold text-slate-800">{{ $enr?->assignedTeacher?->full_name ?: 'ALS Mobile Teacher Pool' }}</span>
                    </div>
                </div>
            </div>

            <!-- Part 4: Submitted Documentation Requirements -->
            <div>
                <div class="bg-deped-blue text-white px-3 py-1.5 font-bold text-xs uppercase tracking-wider rounded-t-lg">
                    Part IV: Submitted Document Requirements & Verification Status
                </div>
                <div class="border border-t-0 border-slate-200 p-4 rounded-b-lg text-xs grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-2.5 rounded-lg border {{ $hasPsa ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                        <span class="text-[10px] font-bold block uppercase">PSA Birth Certificate</span>
                        <span class="font-bold text-xs mt-1 block">{{ $hasPsa ? '✓ Submitted' : 'Pending' }}</span>
                    </div>
                    <div class="p-2.5 rounded-lg border {{ $hasBrgy ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                        <span class="text-[10px] font-bold block uppercase">Barangay Clearance</span>
                        <span class="font-bold text-xs mt-1 block">{{ $hasBrgy ? '✓ Submitted' : 'Pending' }}</span>
                    </div>
                    <div class="p-2.5 rounded-lg border {{ $hasSf10 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                        <span class="text-[10px] font-bold block uppercase">Form 137 / SF10</span>
                        <span class="font-bold text-xs mt-1 block">{{ $hasSf10 ? '✓ Submitted' : 'Pending' }}</span>
                    </div>
                    <div class="p-2.5 rounded-lg border {{ $hasPhoto ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                        <span class="text-[10px] font-bold block uppercase">2x2 ID Photo</span>
                        <span class="font-bold text-xs mt-1 block">{{ $hasPhoto ? '✓ Submitted' : 'Pending' }}</span>
                    </div>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="pt-8 border-t border-slate-200 grid grid-cols-2 gap-8 text-center text-xs page-break-avoid">
                <div>
                    <div class="border-b border-slate-800 font-bold uppercase pb-1 text-slate-900">
                        {{ $profile?->full_name ?: $selectedLearner->username }}
                    </div>
                    <p class="text-slate-500 text-[10px] mt-1">Signature over Printed Name of Learner / Parent</p>
                </div>
                <div>
                    <div class="border-b border-slate-800 font-bold uppercase pb-1 text-slate-900">
                        {{ $enr?->assignedTeacher?->full_name ?: 'ALS Mobile Teacher' }}
                    </div>
                    <p class="text-slate-500 text-[10px] mt-1">ALS Mobile Teacher / Facilitator</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-12 text-center text-slate-400">
            No learner selected or no enrolled learners available.
        </div>
    @endif
</div>
@endsection
