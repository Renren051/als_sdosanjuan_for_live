@extends('layouts.admin')

@section('title', 'Enrollment Intake & Verification - SDO San Juan City ALS')
@section('page_title', 'Enrollment Intake & Verification Queue')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <!-- Status Tabs Bar -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-2 border-b border-slate-100 text-xs custom-scrollbar">
            @php
                $currentStatus = request('status');
                $statuses = [
                    '' => 'All Applications',
                    'pending' => 'Pending Review',
                    'under_review' => 'Under Review',
                    'enrolled' => 'Officially Enrolled',
                    'needs_correction' => 'Needs Correction',
                    'completed' => 'Completed',
                ];
            @endphp
            @foreach($statuses as $key => $label)
                <a href="{{ request()->fullUrlWithQuery(['status' => $key ?: null, 'page' => null]) }}" 
                   class="px-3.5 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all {{ ($currentStatus === $key || (!$currentStatus && $key === '')) ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Filter Dropdowns & Search Form -->
        <form method="GET" action="{{ route('admin.enrollments.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <!-- Search Query -->
            <div class="sm:col-span-2 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by Learner Name, LRN, or Barangay..."
                       class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-deped-blue focus:bg-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- CLC Filter -->
            <div>
                <select name="clc_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    <option value="">All San Juan CLCs</option>
                    @foreach($clcs as $clc)
                        <option value="{{ $clc->id }}" {{ request('clc_id') == $clc->id ? 'selected' : '' }}>
                            {{ $clc->clc_name }} ({{ $clc->barangay }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Program Track Filter -->
            <div>
                <select name="track" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    <option value="">All Program Tracks</option>
                    <option value="BLP" {{ request('track') == 'BLP' ? 'selected' : '' }}>Basic Literacy (BLP)</option>
                    <option value="A&E_Elementary" {{ request('track') == 'A&E_Elementary' ? 'selected' : '' }}>A&E Elementary</option>
                    <option value="A&E_Junior_High" {{ request('track') == 'A&E_Junior_High' ? 'selected' : '' }}>A&E Junior High</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Enrolments Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">LRN / ID</th>
                        <th class="px-4 py-3.5">Learner Name</th>
                        <th class="px-4 py-3.5">Demographics</th>
                        <th class="px-4 py-3.5">San Juan CLC</th>
                        <th class="px-4 py-3.5">Track & Mode</th>
                        <th class="px-4 py-3.5">Credentials</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($enrollments as $enr)
                        @php
                            $profile = $enr->user->profile;
                            $verifiedCount = $enr->documents->where('verification_status', 'verified')->count();
                            $totalDocs = $enr->documents->count();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- LRN -->
                            <td class="px-6 py-4 font-mono">
                                @if($enr->user->lrn)
                                    <span class="px-2 py-0.5 rounded bg-slate-100 font-bold text-slate-800">{{ $enr->user->lrn }}</span>
                                @else
                                    <span class="text-amber-600 font-bold italic">Unassigned</span>
                                @endif
                            </td>

                            <!-- Name -->
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $profile ? $profile->formal_name : $enr->user->username }}
                                </div>
                                <div class="text-[11px] text-slate-400">@ {{ $enr->user->username }}</div>
                            </td>

                            <!-- Demographics -->
                            <td class="px-4 py-4">
                                <div>{{ $profile ? ucfirst($profile->gender) : '-' }}, {{ $profile ? $profile->age . ' yrs' : '-' }}</div>
                                <div class="text-[11px] text-slate-500">Brgy. {{ $profile ? $profile->barangay : 'San Juan' }}</div>
                            </td>

                            <!-- CLC -->
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-800">{{ $enr->clc?->clc_name }}</div>
                                <div class="text-[10px] text-slate-400">Teacher: {{ $enr->assignedTeacher ? $enr->assignedTeacher->profile?->first_name . ' ' . $enr->assignedTeacher->profile?->last_name : 'Pool' }}</div>
                            </td>

                            <!-- Track -->
                            <td class="px-4 py-4">
                                <span class="font-bold text-slate-800">{{ $enr->program_track }}</span>
                                <div class="text-[11px] text-slate-500">{{ ucfirst(str_replace('_', ' ', $enr->delivery_mode)) }}</div>
                            </td>

                            <!-- Credentials -->
                            <td class="px-4 py-4">
                                @if($totalDocs > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $verifiedCount === $totalDocs ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $verifiedCount }}/{{ $totalDocs }} Verified
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">No docs uploaded</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $enr->status_badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $enr->enrollment_status)) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.enrollments.show', $enr->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-deped-blue text-white hover:bg-deped-dark font-bold text-xs shadow-sm transition-all">
                                    <span>Review Dossier</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <p class="text-sm font-semibold">No enrollment records match the selected filters.</p>
                                <p class="text-xs mt-1">Try resetting the status filter or search keywords.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($enrollments->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
