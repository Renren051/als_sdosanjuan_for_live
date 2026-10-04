@extends('layouts.admin')

@section('title', 'DepEd ALS Form 1 (Masterlist) - SDO San Juan City')
@section('page_title', 'ALS Form 1: Masterlist of Enrolled Learners')

@section('content')
<div class="space-y-6">

    <!-- Filter & Generator Controls -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.reports.form1') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Select Center</label>
                <select name="clc_id" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
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
                <select name="year" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold">
                    <option value="2026" {{ $year == '2026' ? 'selected' : '' }}>CY 2026</option>
                    <option value="2025" {{ $year == '2025' ? 'selected' : '' }}>CY 2025</option>
                </select>
            </div>
        </form>

        <div class="flex items-center gap-2">
            <!-- Direct PDF Export Button -->
            <a href="{{ url('/api/v1/admin/reports/form-1?clc_id=' . request('clc_id') . '&year=' . $year) }}" target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md shadow-deped-blue/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export Official DepEd Form 1 (PDF)</span>
            </a>

            <!-- Browser Print Trigger -->
            <button type="button" onclick="window.print()" 
                    class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300 transition-colors">
                Print Sheet
            </button>
        </div>
    </div>

    <!-- Official DepEd ALS Form 1 Sheet Layout -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-10">
        <!-- Official Document Header -->
        <div class="text-center pb-6 border-b-2 border-slate-900/10">
            <p class="text-xs font-bold text-slate-700 uppercase">Republic of the Philippines &bull; Department of Education</p>
            <p class="text-sm font-black text-deped-blue uppercase tracking-wide mt-0.5">National Capital Region &bull; Schools Division Office of San Juan City</p>
            <h1 class="text-lg font-black text-slate-900 uppercase tracking-tight mt-1">MASTERLIST OF ENROLLED LEARNERS (ALS FORM 1)</h1>
            <p class="text-xs text-slate-500 italic">Alternative Learning System &bull; Calendar Year {{ $year }}</p>

            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs text-left max-w-4xl mx-auto">
                <div><strong>Center (CLC):</strong> {{ $selectedClc ? $selectedClc->clc_name : 'All San Juan CLCs' }}</div>
                <div><strong>Barangay:</strong> {{ $selectedClc ? $selectedClc->barangay : 'Division Wide' }}</div>
                <div><strong>Teacher:</strong> {{ $selectedClc && $selectedClc->assignedTeacher ? $selectedClc->assignedTeacher->full_name : 'Mobile Teacher Pool' }}</div>
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
                            $profile = $enr->user->profile;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="border border-slate-300 px-2 py-2 text-center font-bold">{{ $index + 1 }}</td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-mono font-bold">{{ $enr->user->lrn ?: 'PENDING' }}</td>
                            <td class="border border-slate-300 px-3 py-2">
                                <strong>{{ $profile ? $profile->last_name : $enr->user->username }}</strong>, 
                                {{ $profile ? $profile->first_name : '' }} 
                                {{ $profile && $profile->name_extension ? $profile->name_extension : '' }}
                                {{ $profile && $profile->middle_name ? mb_substr($profile->middle_name, 0, 1) . '.' : '' }}
                            </td>
                            <td class="border border-slate-300 px-2 py-2 text-center uppercase">{{ $profile ? substr($profile->gender, 0, 1) : '-' }}</td>
                            <td class="border border-slate-300 px-2 py-2 text-center">{{ $profile && $profile->birthdate ? $profile->birthdate->format('m/d/Y') : '-' }}</td>
                            <td class="border border-slate-300 px-2 py-2 text-center">{{ $profile ? $profile->age : '-' }}</td>
                            <td class="border border-slate-300 px-3 py-2">{{ $profile ? $profile->street_address . ', Brgy. ' . $profile->barangay : 'San Juan' }}</td>
                            <td class="border border-slate-300 px-3 py-2">{{ $profile ? Str::limit($profile->highest_education_attained, 20) : '-' }}</td>
                            <td class="border border-slate-300 px-2 py-2 text-center font-bold">{{ $enr->program_track }}</td>
                            <td class="border border-slate-300 px-2 py-2 text-center">{{ ucfirst(str_replace('_', ' ', $enr->delivery_mode)) }}</td>
                            <td class="border border-slate-300 px-2 py-2 text-center">
                                <span class="font-bold text-[10px]">{{ ucfirst($enr->enrollment_status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="border border-slate-300 px-4 py-8 text-center text-slate-400">
                                No enrolled learners found for the selected center and calendar year.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Official DepEd Signatures Block -->
        <div class="mt-12 pt-6 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center text-xs">
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
                <p class="text-slate-500 text-[10px] mt-1">Division ALS Focal Person</p>
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
