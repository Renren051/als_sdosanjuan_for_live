@extends('layouts.learner')

@section('title', 'My ALS Profile - SDO San Juan City ALS')
@section('page_title', 'My Profile')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Page Header -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-deped-blue/10 text-deped-blue text-[11px] font-bold uppercase tracking-wider mb-2">
                <span>ALS Form 2 &bull; Digital Learner Record</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">My Learner Profile</h1>
            <p class="text-xs text-slate-500 mt-1">
                Official student record encoded in the DepEd SDO San Juan Alternative Learning System.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-right">
                <div class="text-xs text-slate-400 uppercase font-bold">Learner Reference Number</div>
                <div class="font-mono text-base font-black text-deped-blue">
                    {{ $user->lrn ?: 'Intake In Progress' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Identity & Account Card -->
        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="text-center pb-6 border-b border-slate-100">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-deped-blue to-teal-500 text-white flex items-center justify-center font-black text-2xl mx-auto shadow-md">
                    {{ strtoupper(substr($user->profile?->first_name ?: $user->username, 0, 1)) }}{{ strtoupper(substr($user->profile?->last_name ?: '', 0, 1)) }}
                </div>
                <h2 class="text-lg font-bold text-slate-900 mt-3">{{ $user->full_name }}</h2>
                <div class="text-xs text-slate-500 font-mono mt-0.5">{{ '@' . $user->username }}</div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold mt-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Account Active</span>
                </div>
            </div>

            <div class="mt-6 space-y-3 text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-slate-500">Account Role:</span>
                    <strong class="text-slate-800 uppercase text-[11px]">ALS Learner</strong>
                </div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-slate-500">Official Email:</span>
                    <span class="text-slate-800 font-medium truncate max-w-[180px]">{{ $user->email }}</span>
                </div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <span class="text-slate-500">Registered On:</span>
                    <span class="text-slate-700">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Assigned CLC:</span>
                    <span class="font-bold text-deped-blue">{{ $enrollment?->clc?->clc_name ?: 'Pending' }}</span>
                </div>
            </div>
        </div>

        <!-- Personal & Demographic Details -->
        <div class="lg:col-span-2 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                    Personal Information (ALS Form 2)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">First Name</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $user->profile?->first_name ?: '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Middle Name</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $user->profile?->middle_name ?: '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Last Name</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $user->profile?->last_name ?: '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Name Extension</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $user->profile?->name_extension ?: 'None' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Birthdate & Age</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">
                            {{ $user->profile?->birthdate ? $user->profile->birthdate->format('F d, Y') : '—' }}
                            ({{ $user->profile?->age ?? '—' }} years old)
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Gender & Civil Status</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">
                            {{ ucfirst($user->profile?->gender ?: '—') }} &bull; {{ ucfirst($user->profile?->civil_status ?: 'Single') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Mother Tongue</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">{{ $user->profile?->mother_tongue ?: 'Tagalog / Filipino' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Highest Education Attained</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">{{ $user->profile?->highest_education_attained ?: '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Address and Contact Details -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                    Permanent Address & Contact Details
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="sm:col-span-2">
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Street Address</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">{{ $user->profile?->street_address ?: '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Barangay</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">Brgy. {{ $user->profile?->barangay ?: '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">City / Municipality</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">{{ $user->profile?->city ?: 'San Juan City' }}, {{ $user->profile?->province ?: 'Metro Manila' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Contact Number</span>
                        <span class="font-mono font-bold text-slate-900 mt-0.5 block">{{ $user->profile?->contact_number ?: '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Facebook / Social Account</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">{{ $user->profile?->facebook_name ?: '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Special Demographics & Emergency Contact -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                    Special Classifications & Emergency Contacts
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">4Ps Beneficiary</span>
                        <span class="font-bold {{ $user->profile?->is_4ps_beneficiary ? 'text-emerald-700' : 'text-slate-600' }}">
                            {{ $user->profile?->is_4ps_beneficiary ? 'Yes (4Ps Registered)' : 'No' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">IP Community Member</span>
                        <span class="font-bold {{ $user->profile?->is_ip ? 'text-emerald-700' : 'text-slate-600' }}">
                            {{ $user->profile?->is_ip ? ($user->profile->ip_community_name ?: 'Yes') : 'No' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">PWD Status</span>
                        <span class="font-bold {{ $user->profile?->is_pwd ? 'text-purple-700' : 'text-slate-600' }}">
                            {{ $user->profile?->is_pwd ? ($user->profile->pwd_disability_type ?: 'Yes') : 'No' }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Emergency Contact Name</span>
                        <span class="font-bold text-slate-900 mt-0.5 block">{{ $user->profile?->emergency_contact_name ?: ($user->profile?->guardian_name ?: '—') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px] uppercase font-bold">Emergency Contact Number</span>
                        <span class="font-mono font-bold text-slate-900 mt-0.5 block">{{ $user->profile?->emergency_contact_number ?: '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
