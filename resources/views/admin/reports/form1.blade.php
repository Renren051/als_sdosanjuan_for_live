@extends('layouts.admin')

@section('title', 'DepEd ALS Form 1 (Masterlist) - SDO San Juan City')
@section('page_title', 'ALS Form 1: Masterlist of Enrolled Learners')

@push('styles')
<style>
    @media print {
        body {
            background-color: #ffffff !important;
            font-size: 8pt !important;
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
            size: A4 landscape;
            margin: 10mm 10mm;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Filter & Generator Controls -->
    <div class="no-print bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.reports.form1') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Select Center</label>
                <select name="clc_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-deped-blue/20">
                    <option value="">All San Juan CLCs (Consolidated)</option>
                    @foreach($clcs as $clc)
                        <option value="{{ $clc->id }}" {{ request('clc_id') == $clc->id ? 'selected' : '' }}>
                            {{ $clc->clc_name }} ({{ $clc->barangay }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Calendar Year</label>
                <select name="year" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-deped-blue/20">
                    <option value="2026" {{ $year == '2026' ? 'selected' : '' }}>CY 2026</option>
                    <option value="2025" {{ $year == '2025' ? 'selected' : '' }}>CY 2025</option>
                    <option value="all" {{ $year == 'all' ? 'selected' : '' }}>All Years</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Program Track</label>
                <select name="track" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-deped-blue/20">
                    <option value="">All Program Tracks</option>
                    <option value="{{ \App\Models\Enrollment::TRACK_BLP }}" {{ request('track') === \App\Models\Enrollment::TRACK_BLP ? 'selected' : '' }}>Basic Literacy (BLP)</option>
                    <option value="{{ \App\Models\Enrollment::TRACK_AE_ELEMENTARY }}" {{ request('track') === \App\Models\Enrollment::TRACK_AE_ELEMENTARY ? 'selected' : '' }}>A&amp;E Elementary</option>
                    <option value="{{ \App\Models\Enrollment::TRACK_AE_JUNIOR_HIGH }}" {{ request('track') === \App\Models\Enrollment::TRACK_AE_JUNIOR_HIGH ? 'selected' : '' }}>A&amp;E Junior High School</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-deped-blue/20">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="{{ \App\Models\Enrollment::STATUS_ENROLLED }}" {{ request('status', \App\Models\Enrollment::STATUS_ENROLLED) === \App\Models\Enrollment::STATUS_ENROLLED ? 'selected' : '' }}>Officially Enrolled</option>
                    <option value="{{ \App\Models\Enrollment::STATUS_UNDER_REVIEW }}" {{ request('status') === \App\Models\Enrollment::STATUS_UNDER_REVIEW ? 'selected' : '' }}>Under Review</option>
                    <option value="{{ \App\Models\Enrollment::STATUS_COMPLETED }}" {{ request('status') === \App\Models\Enrollment::STATUS_COMPLETED ? 'selected' : '' }}>Completed</option>
                    <option value="{{ \App\Models\Enrollment::STATUS_PENDING }}" {{ request('status') === \App\Models\Enrollment::STATUS_PENDING ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            @if(request('clc_id') || request('track') || request('status') || request('year') !== '2026')
                <div class="self-end pb-0.5">
                    <a href="{{ route('admin.reports.form1') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 underline">
                        Reset Filters
                    </a>
                </div>
            @endif
        </form>

        <div class="flex items-center gap-2 self-start lg:self-center">
            <!-- Official PDF Export Button -->
            <a href="{{ route('admin.reports.form1.pdf', request()->query()) }}" target="_blank"
               class="btn-export inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md shadow-deped-blue/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export DepEd Form 1 (PDF)</span>
            </a>

            <!-- Browser Print Trigger -->
            <button type="button" onclick="window.print()" 
                    class="btn-print px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 transition-colors">
                Print Sheet
            </button>
        </div>
    </div>

    <!-- Statistical Metric Cards (Acceptance Criterion 1) -->
    <div class="no-print grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Learners</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $summary['total'] ?? 0 }}</div>
            <span class="text-[10px] text-slate-500">Filtered Masterlist</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[10px] font-bold text-blue-500 uppercase tracking-wider block">Male</span>
            <div class="text-2xl font-black text-blue-700 mt-1">{{ $summary['male'] ?? 0 }}</div>
            <span class="text-[10px] text-slate-500">{{ $summary['total'] > 0 ? round(($summary['male'] / $summary['total']) * 100) : 0 }}% of Total</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[10px] font-bold text-pink-500 uppercase tracking-wider block">Female</span>
            <div class="text-2xl font-black text-pink-700 mt-1">{{ $summary['female'] ?? 0 }}</div>
            <span class="text-[10px] text-slate-500">{{ $summary['total'] > 0 ? round(($summary['female'] / $summary['total']) * 100) : 0 }}% of Total</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">BLP</span>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $summary['tracks']['BLP']['total'] ?? 0 }}</div>
            <span class="text-[10px] text-slate-500">Basic Literacy</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">Elementary</span>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ $summary['tracks']['A&E_Elementary']['total'] ?? 0 }}</div>
            <span class="text-[10px] text-slate-500">A&amp;E Track</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Junior High</span>
            <div class="text-2xl font-black text-indigo-700 mt-1">{{ $summary['tracks']['A&E_Junior_High']['total'] ?? 0 }}</div>
            <span class="text-[10px] text-slate-500">A&amp;E Track</span>
        </div>
    </div>

    <!-- Official DepEd ALS Form 1 Sheet Layout -->
    <div class="sheet-container bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-10">
        <!-- Official Document Header -->
        <div class="text-center pb-6 border-b-2 border-slate-900/10">
            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Republic of the Philippines &bull; Department of Education</p>
            <p class="text-sm font-black text-deped-blue uppercase tracking-wide mt-0.5">National Capital Region &bull; Schools Division Office of San Juan City</p>
            <h1 class="text-lg font-black text-slate-900 uppercase tracking-tight mt-1">MASTERLIST OF ENROLLED LEARNERS (ALS FORM 1)</h1>
            <p class="text-xs text-slate-500 italic">Alternative Learning System (ALS) &bull; Calendar Year {{ $year === 'all' ? 'Consolidated' : $year }}</p>

            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs text-left max-w-4xl mx-auto">
                <div><strong>Center (CLC):</strong> {{ $selectedClc ? $selectedClc->clc_name : 'All San Juan CLCs (Consolidated)' }}</div>
                <div><strong>Barangay:</strong> {{ $selectedClc ? $selectedClc->barangay : 'Division Wide' }}</div>
                <div><strong>Facilitator:</strong> {{ $selectedClc && $selectedClc->assignedTeacher ? $selectedClc->assignedTeacher->full_name : 'ALS Mobile Teacher Pool' }}</div>
            </div>
        </div>

        <!-- Masterlist Data Table -->
        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-left text-xs border border-slate-300 border-collapse">
                <thead class="bg-slate-100 text-slate-800 uppercase font-bold text-[10px] text-center border-b border-slate-300">
                    <tr>
                        <th class="border border-slate-300 px-2 py-2 w-10">No.</th>
                        <th class="border border-slate-300 px-3 py-2 w-28">LRN</th>
                        <th class="border border-slate-300 px-3 py-2 text-left">Name of Learner (Last, First, Ext., Middle)</th>
                        <th class="border border-slate-300 px-2 py-2 w-12">Sex</th>
                        <th class="border border-slate-300 px-2 py-2 w-20">Birthdate</th>
                        <th class="border border-slate-300 px-2 py-2 w-12">Age</th>
                        <th class="border border-slate-300 px-3 py-2 text-left">Address</th>
                        <th class="border border-slate-300 px-3 py-2">Prior Attainment</th>
                        <th class="border border-slate-300 px-2 py-2 w-24">Track</th>
                        <th class="border border-slate-300 px-2 py-2 w-20">Delivery</th>
                        <th class="border border-slate-300 px-2 py-2 w-24">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse($enrollments as $index => $enr)
                        @php
                            $profile = $enr->user?->profile;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="border border-slate-300 px-2 py-2 text-center font-bold">{{ $index + 1 }}</td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-mono font-bold">{{ $enr->user?->lrn ?: 'PENDING' }}</td>
                            <td class="border border-slate-300 px-3 py-2">
                                <strong>{{ $profile ? $profile->last_name : $enr->user?->username }}</strong>, 
                                {{ $profile ? $profile->first_name : '' }} 
                                {{ $profile && $profile->name_extension ? $profile->name_extension : '' }}
                                {{ $profile && $profile->middle_name ? mb_substr($profile->middle_name, 0, 1) . '.' : '' }}
                            </td>
                            <td class="border border-slate-300 px-2 py-2 text-center uppercase">{{ $profile ? substr($profile->gender, 0, 1) : '-' }}</td>
                            <td class="border border-slate-300 px-2 py-2 text-center">{{ $profile && $profile->birthdate ? $profile->birthdate->format('m/d/Y') : '-' }}</td>
                            <td class="border border-slate-300 px-2 py-2 text-center">{{ $profile ? $profile->age : '-' }}</td>
                            <td class="border border-slate-300 px-3 py-2">{{ $profile ? $profile->street_address . ', Brgy. ' . $profile->barangay : 'San Juan City' }}</td>
                            <td class="border border-slate-300 px-3 py-2">{{ $profile ? \Illuminate\Support\Str::limit($profile->highest_education_attained, 20) : '-' }}</td>
                            <td class="border border-slate-300 px-2 py-2 text-center font-bold">{{ $enr->program_track }}</td>
                            <td class="border border-slate-300 px-2 py-2 text-center">{{ ucfirst(str_replace('_', ' ', $enr->delivery_mode)) }}</td>
                            <td class="border border-slate-300 px-2 py-2 text-center">
                                <span class="font-bold text-[10px]">{{ ucfirst($enr->enrollment_status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="border border-slate-300 px-4 py-8 text-center text-slate-400">
                                No enrolled learners found for the selected filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Official DepEd Form 1 Summary Breakdown Table -->
        <div class="mt-8 page-break-avoid">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">Statistical Summary Breakdown (DepEd ALS Form 1)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-center text-xs border border-slate-300 border-collapse">
                    <thead class="bg-slate-100 text-slate-800 uppercase font-bold text-[10px]">
                        <tr>
                            <th rowspan="2" class="border border-slate-300 px-3 py-2 text-left w-1/3">Program Track Category</th>
                            <th colspan="3" class="border border-slate-300 px-3 py-1">Enrolment by Sex</th>
                            <th colspan="4" class="border border-slate-300 px-3 py-1">Status Distribution</th>
                        </tr>
                        <tr>
                            <th class="border border-slate-300 px-2 py-1 w-16">Male</th>
                            <th class="border border-slate-300 px-2 py-1 w-16">Female</th>
                            <th class="border border-slate-300 px-2 py-1 w-16">Total</th>
                            <th class="border border-slate-300 px-2 py-1">Enrolled</th>
                            <th class="border border-slate-300 px-2 py-1">Under Review</th>
                            <th class="border border-slate-300 px-2 py-1">Completed</th>
                            <th class="border border-slate-300 px-2 py-1">Pending/Other</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-slate-300 px-3 py-1.5 text-left font-semibold">Basic Literacy Program (BLP)</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['tracks']['BLP']['male'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['tracks']['BLP']['female'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5 font-bold">{{ $summary['tracks']['BLP']['total'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'BLP')->where('enrollment_status', 'enrolled')->count() }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'BLP')->where('enrollment_status', 'under_review')->count() }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'BLP')->where('enrollment_status', 'completed')->count() }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'BLP')->whereNotIn('enrollment_status', ['enrolled', 'under_review', 'completed'])->count() }}</td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-1.5 text-left font-semibold">A&amp;E Elementary</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['tracks']['A&E_Elementary']['male'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['tracks']['A&E_Elementary']['female'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5 font-bold">{{ $summary['tracks']['A&E_Elementary']['total'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'A&E_Elementary')->where('enrollment_status', 'enrolled')->count() }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'A&E_Elementary')->where('enrollment_status', 'under_review')->count() }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'A&E_Elementary')->where('enrollment_status', 'completed')->count() }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'A&E_Elementary')->whereNotIn('enrollment_status', ['enrolled', 'under_review', 'completed'])->count() }}</td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-1.5 text-left font-semibold">A&amp;E Junior High School</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['tracks']['A&E_Junior_High']['male'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['tracks']['A&E_Junior_High']['female'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5 font-bold">{{ $summary['tracks']['A&E_Junior_High']['total'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'A&E_Junior_High')->where('enrollment_status', 'enrolled')->count() }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'A&E_Junior_High')->where('enrollment_status', 'under_review')->count() }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'A&E_Junior_High')->where('enrollment_status', 'completed')->count() }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $enrollments->where('program_track', 'A&E_Junior_High')->whereNotIn('enrollment_status', ['enrolled', 'under_review', 'completed'])->count() }}</td>
                        </tr>
                        <tr class="bg-slate-100 font-bold">
                            <td class="border border-slate-300 px-3 py-1.5 text-left">GRAND TOTAL</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['male'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['female'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5 text-deped-blue">{{ $summary['total'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['status_breakdown']['enrolled'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['status_breakdown']['under_review'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ $summary['status_breakdown']['completed'] ?? 0 }}</td>
                            <td class="border border-slate-300 px-2 py-1.5">{{ ($summary['total'] ?? 0) - (($summary['status_breakdown']['enrolled'] ?? 0) + ($summary['status_breakdown']['under_review'] ?? 0) + ($summary['status_breakdown']['completed'] ?? 0)) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Official DepEd Signatures Block -->
        <div class="mt-12 pt-6 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center text-xs page-break-avoid">
            <div>
                <p class="text-slate-400 text-[10px] uppercase font-bold mb-8">Prepared by:</p>
                <div class="border-b border-slate-800 font-bold uppercase text-slate-900 pb-1">
                    {{ $selectedClc && $selectedClc->assignedTeacher ? $selectedClc->assignedTeacher->full_name : 'ALS Mobile Teacher' }}
                </div>
                <p class="text-slate-500 text-[10px] mt-1">ALS Mobile Teacher / Facilitator</p>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] uppercase font-bold mb-8">Certified Correct:</p>
                <div class="border-b border-slate-800 font-bold uppercase text-slate-900 pb-1">
                    Dr. Maria Elena Santos
                </div>
                <p class="text-slate-500 text-[10px] mt-1">Division ALS Focal Person / EPSA II</p>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] uppercase font-bold mb-8">Recommending Approval:</p>
                <div class="border-b border-slate-800 font-bold uppercase text-slate-900 pb-1">
                    Chief, CID
                </div>
                <p class="text-slate-500 text-[10px] mt-1">Curriculum Implementation Division</p>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] uppercase font-bold mb-8">Approved:</p>
                <div class="border-b border-slate-800 font-bold uppercase text-slate-900 pb-1">
                    Schools Division Superintendent
                </div>
                <p class="text-slate-500 text-[10px] mt-1">SDO San Juan City</p>
            </div>
        </div>
    </div>
</div>
@endsection
