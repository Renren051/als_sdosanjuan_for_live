@extends('layouts.admin')

@section('title', 'Learner Dossier & Form 2 Verification - ' . $enrollment->user->full_name)
@section('page_title', 'Applicant Dossier & ALS Form 2 Verification')

@section('content')
@php
    $user = $enrollment->user;
    $profile = $user->profile;
@endphp

<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.enrollments.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-black text-slate-900">{{ $profile ? $profile->full_name : $user->username }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $enrollment->status_badge }}">
                        {{ ucfirst(str_replace('_', ' ', $enrollment->enrollment_status)) }}
                    </span>
                </div>
                <div class="text-xs text-slate-500 mt-0.5">
                    Username: <span class="font-mono font-bold">{{ $user->username }}</span> &bull; 
                    Applied: {{ $enrollment->created_at->format('M d, Y h:i A') }}
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Print/Download ALS Form 2 PDF -->
            <a href="{{ url('/api/v1/admin/reports/form-2/' . $user->id) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition-all">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Official Form 2</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Full Digital ALS Form 2 Dossier -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Part 1: Personal Demographics -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-deped-blue"></span>
                        <span>Part I: Learner Demographic Information</span>
                    </h2>
                    <span class="text-xs text-slate-400">DepEd Form 2 Data</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">First Name</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $profile?->first_name ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Middle Name</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $profile?->middle_name ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Last Name</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $profile?->last_name ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Name Extension</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $profile?->name_extension ?: 'N/A' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs pt-3 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Birthdate</span>
                        <span class="font-semibold text-slate-800">{{ $profile && $profile->birthdate ? $profile->birthdate->format('F d, Y') : '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Age</span>
                        <span class="font-semibold text-slate-800">{{ $profile?->age }} years old</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Sex</span>
                        <span class="font-semibold text-slate-800">{{ ucfirst($profile?->gender) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Civil Status</span>
                        <span class="font-semibold text-slate-800">{{ ucfirst($profile?->civil_status) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs pt-3 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Contact Number</span>
                        <span class="font-semibold text-slate-800 font-mono">{{ $profile?->contact_number ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Mother Tongue</span>
                        <span class="font-semibold text-slate-800">{{ $profile?->mother_tongue ?: 'Tagalog / Filipino' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Facebook Profile</span>
                        <span class="font-semibold text-slate-800">{{ $profile?->facebook_name ?: 'N/A' }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 text-xs">
                    <span class="text-slate-400 uppercase font-bold block">Complete Residence Address</span>
                    <span class="font-semibold text-slate-800 text-sm">{{ $profile?->full_address }}</span>
                </div>
            </div>

            <!-- Part 2: Educational Background & Social Protection -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-deped-gold"></span>
                        <span>Part II: Prior Education & Special Inclusion</span>
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Prior Educational Attainment</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $profile?->highest_education_attained ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">4Ps Beneficiary Status</span>
                        <span class="font-semibold {{ $profile?->is_4ps_beneficiary ? 'text-emerald-700' : 'text-slate-600' }}">
                            {{ $profile?->is_4ps_beneficiary ? 'Yes (Pantawid Pamilya)' : 'No' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-3 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Indigenous Peoples (IP)</span>
                        <span class="font-semibold text-slate-800">
                            {{ $profile?->is_ip ? 'Yes (' . $profile->ip_community_name . ')' : 'No' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Person with Disability (PWD)</span>
                        <span class="font-semibold text-slate-800">
                            {{ $profile?->is_pwd ? 'Yes (' . $profile->pwd_disability_type . ')' : 'No' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-3 border-t border-slate-100">
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Guardian / Parent</span>
                        <span class="font-semibold text-slate-800">{{ $profile?->guardian_name ?: ($profile?->mother_name ?: ($profile?->father_name ?: 'N/A')) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 uppercase font-bold block">Emergency Contact</span>
                        <span class="font-semibold text-slate-800">{{ $profile?->emergency_contact_name }} ({{ $profile?->emergency_contact_number ?: $profile?->contact_number }})</span>
                    </div>
                </div>
            </div>

            <!-- Part 3: Uploaded Credential Documents Review -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Submitted Documents & Credential Verification</span>
                    </h2>
                    <span class="text-xs text-slate-500">{{ $enrollment->documents->count() }} Attached</span>
                </div>

                <div class="space-y-3">
                    @forelse($enrollment->documents as $doc)
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                    <span>{{ $doc->document_type_label }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $doc->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : ($doc->verification_status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ ucfirst($doc->verification_status) }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-3">
                                    <span>File: {{ $doc->file_name }}</span>
                                    <span>Size: {{ $doc->formatted_file_size }}</span>
                                    @if($doc->reviewer)
                                        <span class="text-slate-400">Reviewed by: {{ $doc->reviewer->full_name }}</span>
                                    @endif
                                </div>
                                @if($doc->verification_notes)
                                    <div class="text-xs text-rose-700 bg-rose-50 p-2 rounded-lg mt-2 border border-rose-100">
                                        <strong>Feedback Note:</strong> {{ $doc->verification_notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                <!-- View File Link -->
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                   class="px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs shadow-sm">
                                    View File
                                </a>

                                <!-- Verification Action Buttons -->
                                <form action="{{ route('admin.documents.updateStatus', $doc->id) }}" method="POST" class="inline-flex gap-1">
                                    @csrf
                                    <input type="hidden" name="verification_status" value="verified">
                                    <button type="submit" title="Mark as Verified" 
                                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm">
                                        Verify
                                    </button>
                                </form>

                                <form action="{{ route('admin.documents.updateStatus', $doc->id) }}" method="POST" class="inline-flex gap-1"
                                      onsubmit="let reason = prompt('Please enter correction note for the learner:'); if(!reason) return false; this.verification_notes.value = reason;">
                                    @csrf
                                    <input type="hidden" name="verification_status" value="rejected">
                                    <input type="hidden" name="verification_notes" value="">
                                    <button type="submit" title="Reject with Correction Note" 
                                            class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs shadow-sm">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400 text-xs">
                            No credential documents have been uploaded for this intake yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Status Update, LRN Assignor, & Allocation Controls -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-5 sticky top-24">
                <div class="border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Enrolment Adjudication & Allocation
                    </h2>
                    <p class="text-xs text-slate-500">Official Mobile Teacher / Admin actions</p>
                </div>

                <form action="{{ route('admin.enrollments.updateStatus', $enrollment->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- 12-Digit LRN Assignor -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">
                            Learner Reference Number (LRN)
                        </label>
                        <p class="text-[11px] text-slate-500 mb-1.5">Official 12-digit DepEd identifier</p>
                        <input type="text" name="lrn" maxlength="12" 
                               value="{{ old('lrn', $user->lrn) }}"
                               placeholder="e.g. 136542000001"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono tracking-widest focus:ring-2 focus:ring-deped-blue focus:bg-white">
                    </div>

                    <!-- Enrolment Status -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Application Status *
                        </label>
                        <select name="enrollment_status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold">
                            <option value="enrolled" {{ $enrollment->enrollment_status === 'enrolled' ? 'selected' : '' }}>Officially Enrolled</option>
                            <option value="under_review" {{ $enrollment->enrollment_status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="needs_correction" {{ $enrollment->enrollment_status === 'needs_correction' ? 'selected' : '' }}>Needs Correction</option>
                            <option value="pending" {{ $enrollment->enrollment_status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="completed" {{ $enrollment->enrollment_status === 'completed' ? 'selected' : '' }}>Completed Program</option>
                            <option value="withdrawn" {{ $enrollment->enrollment_status === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                    </div>

                    <!-- Assigned CLC -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Assigned Learning Center (CLC)
                        </label>
                        <select name="clc_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                            @foreach($clcs as $clc)
                                <option value="{{ $clc->id }}" {{ $enrollment->clc_id == $clc->id ? 'selected' : '' }}>
                                    {{ $clc->clc_name }} (Brgy. {{ $clc->barangay }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assigned Mobile Teacher -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Assigned Mobile Teacher
                        </label>
                        <select name="assigned_teacher_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                            <option value="">Unassigned Pool</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ $enrollment->assigned_teacher_id == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Teacher Remarks & Feedback -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Teacher Remarks / Correction Instructions
                        </label>
                        <textarea name="remarks" rows="3" 
                                  placeholder="Provide specific notes or correction requirements that will appear on the learner's dashboard..."
                                  class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-deped-blue focus:bg-white">{{ old('remarks', $enrollment->remarks) }}</textarea>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-deped-blue/30 transition-all">
                        Update Enrolment Record
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
