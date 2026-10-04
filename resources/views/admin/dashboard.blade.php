@extends('layouts.admin')

@section('title', 'Executive Dashboard - SDO San Juan City ALS')
@section('page_title', 'Executive Dashboard')

@section('content')
<div class="space-y-8" x-data="{ walkinModal: false }" @open-walkin-modal.window="walkinModal = true">

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Enrolled Learners -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">Officially Enrolled</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $totalEnrolled }}</div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-1">Active in Masterlist</div>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">Pending Review</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-amber-600">{{ $totalPending }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Needs verification</div>
            </div>
        </div>

        <!-- Needs Correction -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">Correction Needed</span>
                <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-rose-600">{{ $totalNeedsCorrection }}</div>
                <div class="text-[11px] text-rose-500 font-semibold mt-1">Waiting re-upload</div>
            </div>
        </div>

        <!-- Active CLCs -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">San Juan CLCs</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-deped-blue flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $totalClcs }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Community Centers</div>
            </div>
        </div>

        <!-- Published Modules -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">ALS Modules</span>
                <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $totalModules }}</div>
                <div class="text-[11px] text-purple-600 font-semibold mt-1">Across 6 Strands</div>
            </div>
        </div>

        <!-- Telemetry Sync Events -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider">Mobile Telemetry</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-2xl sm:text-3xl font-black text-indigo-600">{{ $totalTelemetrySyncs }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Offline Sync Events</div>
            </div>
        </div>
    </div>

    <!-- Middle Section: Program Track Distribution & CLC Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Program Track Distribution -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center justify-between">
                <span>Enrolment by Program Track</span>
                <span class="text-xs text-deped-blue font-semibold">CY 2026</span>
            </h2>

            <div class="space-y-4">
                <!-- Basic Literacy Program -->
                <div>
                    <div class="flex justify-between text-xs font-bold text-slate-700 mb-1">
                        <span>Basic Literacy Program (BLP)</span>
                        <span>{{ $trackCounts['BLP'] }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ $totalEnrolled > 0 ? ($trackCounts['BLP'] / max(1, array_sum($trackCounts))) * 100 : 25 }}%"></div>
                    </div>
                </div>

                <!-- A&E Elementary -->
                <div>
                    <div class="flex justify-between text-xs font-bold text-slate-700 mb-1">
                        <span>A&E Elementary</span>
                        <span>{{ $trackCounts['Elementary'] }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $totalEnrolled > 0 ? ($trackCounts['Elementary'] / max(1, array_sum($trackCounts))) * 100 : 35 }}%"></div>
                    </div>
                </div>

                <!-- A&E Junior High -->
                <div>
                    <div class="flex justify-between text-xs font-bold text-slate-700 mb-1">
                        <span>A&E Junior High School</span>
                        <span>{{ $trackCounts['Junior_High'] }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ $totalEnrolled > 0 ? ($trackCounts['Junior_High'] / max(1, array_sum($trackCounts))) * 100 : 60 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Need Form 1 Masterlist?</span>
                <a href="{{ route('admin.reports.form1') }}" class="font-bold text-deped-blue hover:underline">
                    View Masterlist &rarr;
                </a>
            </div>
        </div>

        <!-- CLC Allocation Snapshot -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                    Top Community Learning Centers (San Juan City)
                </h2>
                <a href="{{ route('admin.clcs.index') }}" class="text-xs font-bold text-deped-blue hover:underline">
                    Manage All ({{ $totalClcs }}) &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($clcsSummary as $clc)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-900">{{ $clc->clc_name }}</div>
                            <div class="text-[11px] text-slate-500">Brgy. {{ $clc->barangay }}</div>
                            <div class="text-[10px] text-deped-blue font-medium mt-0.5">
                                Teacher: {{ $clc->assignedTeacher ? $clc->assignedTeacher->profile?->first_name . ' ' . $clc->assignedTeacher->profile?->last_name : 'Unassigned' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-black text-xs">
                                {{ $clc->active_learners }} Enrolled
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bottom Section: Recent Intakes Queue & Real-time Telemetry -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Enrolment Intakes -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Recent Enrolment Applications</h2>
                    <p class="text-xs text-slate-500">Learners applying via digital Form 2 or walk-in registration</p>
                </div>
                <a href="{{ route('admin.enrollments.index') }}" class="text-xs font-bold text-deped-blue hover:underline">
                    View Full Queue &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3">Learner</th>
                            <th class="px-4 py-3">Program Track</th>
                            <th class="px-4 py-3">Assigned CLC</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($recentEnrollments as $enr)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-3.5">
                                    <div class="font-bold text-slate-900">{{ $enr->user->full_name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $enr->user->lrn ?: 'No LRN yet' }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-semibold text-slate-700">{{ $enr->program_track }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="text-slate-600">{{ $enr->clc?->clc_name }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $enr->status_badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $enr->enrollment_status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a href="{{ route('admin.enrollments.show', $enr->id) }}" 
                                       class="px-3 py-1 rounded-lg bg-deped-blue/10 text-deped-blue hover:bg-deped-blue hover:text-white font-bold transition-all text-xs">
                                        Review &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                    No enrollment applications recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Telemetry Feed (Android Offline Progress Events) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>Mobile Offline Telemetry</span>
                    </h2>
                    <span class="text-[10px] text-slate-400">Asynchronous</span>
                </div>
                <p class="text-xs text-slate-500 mb-4">
                    Real-time sync packets received from Android LMS app readers when reconnecting to Wi-Fi/Data.
                </p>

                <div class="space-y-3">
                    @forelse($recentTelemetry as $tel)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/70 text-xs">
                            <div class="flex items-center justify-between font-bold text-slate-800">
                                <span>{{ $tel->user?->full_name }}</span>
                                <span class="px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-800 text-[10px]">
                                    {{ ucfirst(str_replace('_', ' ', $tel->completion_status)) }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1 truncate">
                                {{ $tel->module?->title }}
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400 mt-2 pt-1 border-t border-slate-200/40">
                                <span>Page {{ $tel->last_page_read }} of {{ $tel->total_pages ?: '?' }}</span>
                                <span>{{ $tel->synced_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            No telemetry sync records received yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('admin.modules.index') }}" class="text-xs font-bold text-deped-blue hover:underline">
                    View Self-Learning Modules &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Walk-in Enrollment Modal (Global Accessible) -->
    <div x-show="walkinModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="walkinModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Walk-in Manual Intake</h2>
                        <p class="text-xs text-slate-500">Directly encode learner at Barangay Hall or CLC</p>
                    </div>
                    <button @click="walkinModal = false" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
                </div>

                <form action="{{ route('admin.enrollments.walkIn') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">First Name *</label>
                            <input type="text" name="first_name" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Middle Name</label>
                            <input type="text" name="middle_name" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Last Name *</label>
                            <input type="text" name="last_name" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Birthdate *</label>
                            <input type="date" name="birthdate" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Sex *</label>
                            <select name="gender" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Contact Number *</label>
                            <input type="text" name="contact_number" required placeholder="0917XXXXXXX" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Street Address *</label>
                            <input type="text" name="street_address" required placeholder="House #, Street" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">San Juan Barangay *</label>
                            <select name="barangay" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                @foreach(['Addition Hills', 'Balong-Bato', 'Batis', 'Corazon de Jesus', 'Ermitaño', 'Salapan', 'San Perfecto', 'Santa Lucia', 'Tibagan', 'Progreso', 'Rivera', 'Pedro Cruz', 'Maytunas', 'Isabelita', 'Kabayanan', 'St. Joseph (Halo-halo)', 'Pasadena', 'Little Baguio', 'Greenhills', 'West Crame', 'Onse'] as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Program Track *</label>
                            <select name="program_track" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                <option value="A&E_Junior_High">A&E Junior High School</option>
                                <option value="A&E_Elementary">A&E Elementary</option>
                                <option value="BLP">Basic Literacy Program (BLP)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Community Learning Center *</label>
                            <select name="clc_id" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                @php
                                    $allClcs = \App\Models\CommunityLearningCenter::where('status', 'active')->get();
                                @endphp
                                @foreach($allClcs as $c)
                                    <option value="{{ $c->id }}">{{ $c->clc_name }} (Brgy. {{ $c->barangay }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Prior Schooling Attainment *</label>
                            <input type="text" name="highest_education_attained" required placeholder="e.g. Grade 7" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Assign 12-Digit LRN (Optional)</label>
                            <input type="text" name="lrn" maxlength="12" placeholder="e.g. 136542000005" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Initial Status</label>
                        <select name="enrollment_status" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                            <option value="enrolled">Officially Enrolled</option>
                            <option value="under_review">Under Review / Verification</option>
                            <option value="pending">Pending Document Submission</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="walkinModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md">
                            Encode Walk-in Learner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
