@extends('layouts.admin')

@section('title', 'Dashboard - SDO San Juan City ALS')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8" 
     x-data="{ 
         walkinModal: false, 
         dictionaryModal: false,
         filterOpen: {{ !empty($activeFilters) ? 'true' : 'false' }},
         dateRangeSelect: '{{ $dateRange ?? 'all' }}'
     }" 
     @open-walkin-modal.window="walkinModal = true">

    <!-- Top Action & Filter Toolbar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-deped-blue/10 text-deped-blue">
                        {{ $isTeacher ? 'Mobile Teacher Scope' : 'Division-Wide Executive Overview' }}
                    </span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs font-medium text-slate-500">
                        {{ now()->format('F j, Y') }}
                    </span>
                </div>
                <h2 class="text-base font-bold text-slate-900 mt-1">Operational Metrics & Enrolment Summaries</h2>
            </div>

            <div class="flex items-center flex-wrap gap-2">
                <!-- Toggle Filter Panel Button -->
                <button type="button" 
                        @click="filterOpen = !filterOpen"
                        :class="filterOpen ? 'bg-slate-100 text-slate-900 border-slate-300' : 'bg-white text-slate-700 border-slate-200'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold border hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filters & Date Range</span>
                    @if(count($activeFilters) > 0)
                        <span class="w-5 h-5 rounded-full bg-deped-blue text-white text-[10px] flex items-center justify-center font-bold">
                            {{ count($activeFilters) }}
                        </span>
                    @endif
                </button>

                <!-- Metrics Dictionary / Methodology Button -->
                <button type="button" 
                        @click="dictionaryModal = true"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 hover:text-deped-blue transition-all flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4 text-deped-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Metric Definitions & Sources</span>
                </button>
            </div>
        </div>

        <!-- Filter Form Drawer -->
        <div x-show="filterOpen" x-collapse class="pt-4 border-t border-slate-100">
            <form action="{{ route('admin.dashboard') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Date Range Presets -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Time Period</label>
                        <select name="date_range" 
                                x-model="dateRangeSelect"
                                class="w-full text-xs rounded-xl border-slate-300 bg-slate-50 focus:border-deped-blue focus:ring-deped-blue py-2 px-3">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="7d">Past 7 Days</option>
                            <option value="30d">Past 30 Days</option>
                            <option value="this_month">This Month</option>
                            <option value="custom">Custom Date Range</option>
                        </select>
                    </div>

                    <!-- Custom Date From -->
                    <div x-show="dateRangeSelect === 'custom'" x-cloak>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Date From</label>
                        <input type="date" 
                               name="date_from" 
                               value="{{ $dateFrom }}"
                               class="w-full text-xs rounded-xl border-slate-300 bg-slate-50 focus:border-deped-blue focus:ring-deped-blue py-2 px-3">
                    </div>

                    <!-- Custom Date To -->
                    <div x-show="dateRangeSelect === 'custom'" x-cloak>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Date To</label>
                        <input type="date" 
                               name="date_to" 
                               value="{{ $dateTo }}"
                               class="w-full text-xs rounded-xl border-slate-300 bg-slate-50 focus:border-deped-blue focus:ring-deped-blue py-2 px-3">
                    </div>

                    <!-- Calendar Year -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Calendar Year</label>
                        <select name="calendar_year" 
                                class="w-full text-xs rounded-xl border-slate-300 bg-slate-50 focus:border-deped-blue focus:ring-deped-blue py-2 px-3">
                            <option value="">All Years</option>
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}" {{ (string)$calendarYear === (string)$yr ? 'selected' : '' }}>CY {{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Learning Center (CLC) -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Learning Center</label>
                        <select name="clc_id" 
                                class="w-full text-xs rounded-xl border-slate-300 bg-slate-50 focus:border-deped-blue focus:ring-deped-blue py-2 px-3">
                            <option value="">All Learning Centers</option>
                            @foreach($filterClcs as $c)
                                <option value="{{ $c->id }}" {{ (string)$clcId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->clc_name }} (Brgy. {{ $c->barangay }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Program Track -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Program Track</label>
                        <select name="track" 
                                class="w-full text-xs rounded-xl border-slate-300 bg-slate-50 focus:border-deped-blue focus:ring-deped-blue py-2 px-3">
                            <option value="">All Program Tracks</option>
                            <option value="BLP" {{ $track === 'BLP' ? 'selected' : '' }}>Basic Literacy (BLP)</option>
                            <option value="A&E_Elementary" {{ $track === 'A&E_Elementary' ? 'selected' : '' }}>A&E Elementary</option>
                            <option value="A&E_Junior_High" {{ $track === 'A&E_Junior_High' ? 'selected' : '' }}>A&E Junior High</option>
                        </select>
                    </div>

                    <!-- Enrollment Status -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Focus Status</label>
                        <select name="status" 
                                class="w-full text-xs rounded-xl border-slate-300 bg-slate-50 focus:border-deped-blue focus:ring-deped-blue py-2 px-3">
                            <option value="">All Statuses</option>
                            <option value="enrolled" {{ $status === 'enrolled' ? 'selected' : '' }}>Officially Enrolled</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="under_review" {{ $status === 'under_review' ? 'selected' : '' }}>Under Active Review</option>
                            <option value="needs_correction" {{ $status === 'needs_correction' ? 'selected' : '' }}>Needs Correction</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected / Ineligible</option>
                            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed / Graduated</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                        Reset Filters
                    </a>
                    <button type="submit" 
                            class="px-5 py-2 rounded-xl bg-deped-blue hover:bg-deped-blue/90 text-white font-bold text-xs shadow-md transition-all">
                        Apply Criteria
                    </button>
                </div>
            </form>
        </div>

        <!-- Active Filter Tags Display -->
        @if(count($activeFilters) > 0)
            <div class="pt-3 border-t border-slate-100 flex items-center flex-wrap gap-2 text-xs">
                <span class="text-slate-400 font-semibold text-[11px] uppercase tracking-wider">Active Filters:</span>
                @foreach($activeFilters as $key => $filter)
                    @php
                        $removeParams = request()->except($filter['param'], 'page');
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-800 text-xs font-medium">
                        <span>{{ $filter['label'] }}</span>
                        <a href="{{ route('admin.dashboard', $removeParams) }}" 
                           class="text-slate-400 hover:text-rose-600 font-bold ml-0.5" 
                           title="Remove this filter">&times;</a>
                    </span>
                @endforeach
                <a href="{{ route('admin.dashboard') }}" 
                   class="text-xs text-rose-600 font-bold hover:underline ml-2">
                    Clear all
                </a>
            </div>
        @endif
    </div>

    <!-- Learner Status KPI Metric Cards Grid (6 Status Columns) -->
    <div>
        <div class="flex items-center justify-between mb-3 px-1">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Learner Enrolment Status Totals</h3>
            <span class="text-xs text-slate-400">Total Recorded Intakes: <strong class="text-slate-800">{{ number_format($totalIntakes) }}</strong></span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- 1. Officially Enrolled -->
            <a href="{{ route('admin.enrollments.index', array_merge(request()->only(['clc_id', 'track', 'calendar_year']), ['status' => 'enrolled'])) }}" 
               class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md hover:border-emerald-300 transition-all group">
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-xs font-bold uppercase tracking-wider">Enrolled</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($totalEnrolled) }}</div>
                    <div class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center justify-between">
                        <span>Active Masterlist</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-emerald-600">&rarr;</span>
                    </div>
                </div>
            </a>

            <!-- 2. Pending Initial Review -->
            <a href="{{ route('admin.enrollments.index', array_merge(request()->only(['clc_id', 'track', 'calendar_year']), ['status' => 'pending'])) }}" 
               class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md hover:border-amber-300 transition-all group">
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-xs font-bold uppercase tracking-wider">Pending</span>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-3xl font-black text-amber-600">{{ number_format($totalPending) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1 flex items-center justify-between">
                        <span>Needs verification</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-amber-600">&rarr;</span>
                    </div>
                </div>
            </a>

            <!-- 3. Under Active Review -->
            <a href="{{ route('admin.enrollments.index', array_merge(request()->only(['clc_id', 'track', 'calendar_year']), ['status' => 'under_review'])) }}" 
               class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md hover:border-blue-300 transition-all group">
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-xs font-bold uppercase tracking-wider">Under Review</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-3xl font-black text-blue-600">{{ number_format($totalUnderReview) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1 flex items-center justify-between">
                        <span>Being evaluated</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-blue-600">&rarr;</span>
                    </div>
                </div>
            </a>

            <!-- 4. Needs Correction -->
            <a href="{{ route('admin.enrollments.index', array_merge(request()->only(['clc_id', 'track', 'calendar_year']), ['status' => 'needs_correction'])) }}" 
               class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md hover:border-rose-300 transition-all group">
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-xs font-bold uppercase tracking-wider">Correction</span>
                    <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-3xl font-black text-rose-600">{{ number_format($totalNeedsCorrection) }}</div>
                    <div class="text-[11px] text-rose-500 font-semibold mt-1 flex items-center justify-between">
                        <span>Waiting re-upload</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-rose-600">&rarr;</span>
                    </div>
                </div>
            </a>

            <!-- 5. Rejected / Ineligible -->
            <a href="{{ route('admin.enrollments.index', array_merge(request()->only(['clc_id', 'track', 'calendar_year']), ['status' => 'rejected'])) }}" 
               class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition-all group">
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-xs font-bold uppercase tracking-wider">Rejected</span>
                    <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-3xl font-black text-slate-700">{{ number_format($totalRejected) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1 flex items-center justify-between">
                        <span>Ineligible / Cancelled</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-slate-700">&rarr;</span>
                    </div>
                </div>
            </a>

            <!-- 6. Completed / Graduated -->
            <a href="{{ route('admin.enrollments.index', array_merge(request()->only(['clc_id', 'track', 'calendar_year']), ['status' => 'completed'])) }}" 
               class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md hover:border-purple-300 transition-all group">
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-xs font-bold uppercase tracking-wider">Completed</span>
                    <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl sm:text-3xl font-black text-purple-700">{{ number_format($totalCompleted) }}</div>
                    <div class="text-[11px] text-purple-600 font-semibold mt-1 flex items-center justify-between">
                        <span>A&E Passers / Grad</span>
                        <span class="text-[10px] text-slate-400 group-hover:text-purple-600">&rarr;</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Secondary Operational Summaries Row (Centers, Content, Telemetry) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Active CLCs -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-deped-blue flex items-center justify-center font-bold shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <div class="text-base font-bold text-slate-900">{{ number_format($totalClcs) }} Active Centers</div>
                    <div class="text-xs text-slate-500">{{ $isTeacher ? 'Assigned to your teaching scope' : 'Community Centers across San Juan City' }}</div>
                </div>
            </div>
            <a href="{{ route('admin.clcs.index') }}" class="text-xs font-bold text-deped-blue hover:underline">Manage &rarr;</a>
        </div>

        <!-- Published Modules -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <div class="text-base font-bold text-slate-900">{{ number_format($totalModules) }} Modules Published</div>
                    <div class="text-xs text-slate-500">Across 6 DepEd Learning Strands</div>
                </div>
            </div>
            <a href="{{ route('admin.modules.index') }}" class="text-xs font-bold text-deped-blue hover:underline">Modules &rarr;</a>
        </div>

        <!-- Telemetry Sync Events -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <div class="text-base font-bold text-slate-900">{{ number_format($totalTelemetrySyncs) }} Telemetry Syncs</div>
                    <div class="text-xs text-slate-500">Offline LMS Android Packets Received</div>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 text-xs text-emerald-600 font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Active
            </span>
        </div>
    </div>

    <!-- Middle Section: Program Track Distribution & CLC Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Program Track Distribution -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                            Enrolment by Program Track
                        </h2>
                        <p class="text-xs text-slate-500">Distribution across active filter scope</p>
                    </div>
                    <span class="text-xs text-deped-blue font-bold">
                        {{ $calendarYear ? 'CY ' . $calendarYear : 'All Years' }}
                    </span>
                </div>

                @if($totalTrackRecords > 0)
                    <div class="space-y-4">
                        <!-- Basic Literacy Program -->
                        <div>
                            <div class="flex justify-between text-xs font-bold text-slate-700 mb-1">
                                <span>Basic Literacy Program (BLP)</span>
                                <span>{{ number_format($trackCounts['BLP']) }} <span class="text-slate-400 font-normal">({{ $trackPercentages['BLP'] }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-amber-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $trackPercentages['BLP'] }}%"></div>
                            </div>
                        </div>

                        <!-- A&E Elementary -->
                        <div>
                            <div class="flex justify-between text-xs font-bold text-slate-700 mb-1">
                                <span>A&E Elementary</span>
                                <span>{{ number_format($trackCounts['Elementary']) }} <span class="text-slate-400 font-normal">({{ $trackPercentages['Elementary'] }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $trackPercentages['Elementary'] }}%"></div>
                            </div>
                        </div>

                        <!-- A&E Junior High -->
                        <div>
                            <div class="flex justify-between text-xs font-bold text-slate-700 mb-1">
                                <span>A&E Junior High School</span>
                                <span>{{ number_format($trackCounts['Junior_High']) }} <span class="text-slate-400 font-normal">({{ $trackPercentages['Junior_High'] }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $trackPercentages['Junior_High'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty Track State -->
                    <div class="py-8 text-center text-slate-400 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="text-xs font-semibold text-slate-600">No enrolment records in this selection</p>
                        <p class="text-[11px] text-slate-400 mt-1">Adjust filters or register new learners</p>
                    </div>
                @endif
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Need Form 1 Masterlist?</span>
                <a href="{{ route('admin.reports.form1') }}" class="font-bold text-deped-blue hover:underline">
                    View Masterlist &rarr;
                </a>
            </div>
        </div>

        <!-- CLC Allocation Snapshot -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                            Top Community Learning Centers (San Juan City)
                        </h2>
                        <p class="text-xs text-slate-500">Enrolled learners currently mapped per center</p>
                    </div>
                    <a href="{{ route('admin.clcs.index') }}" class="text-xs font-bold text-deped-blue hover:underline">
                        Manage All ({{ $totalClcs }}) &rarr;
                    </a>
                </div>

                @if($clcsSummary->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($clcsSummary as $clc)
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-center justify-between hover:bg-slate-100/60 transition-colors">
                                <div class="min-w-0 pr-2">
                                    <div class="text-xs font-bold text-slate-900 truncate">{{ $clc->clc_name }}</div>
                                    <div class="text-[11px] text-slate-500">Brgy. {{ $clc->barangay }}</div>
                                    <div class="text-[10px] text-deped-blue font-medium mt-0.5 truncate">
                                        Teacher: {{ $clc->assignedTeacher ? $clc->assignedTeacher->profile?->first_name . ' ' . $clc->assignedTeacher->profile?->last_name : 'Unassigned' }}
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-black text-xs inline-block">
                                        {{ number_format($clc->active_learners) }} Enrolled
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty CLC State -->
                    <div class="py-12 text-center text-slate-400 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <svg class="w-8 h-8 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-xs font-semibold text-slate-600">No active learning centers found</p>
                        <p class="text-[11px] text-slate-400 mt-1">No centers match the active filter criteria</p>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Active enrollments match currently selected calendar year and filters.</span>
                <a href="{{ route('admin.clcs.index') }}" class="font-bold text-deped-blue hover:underline">View Allocation Grid &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Recent Intakes Queue & Real-time Telemetry -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Enrolment Intakes -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Recent Enrolment Applications</h2>
                        <p class="text-xs text-slate-500">Learners applying via digital Form 2 or walk-in registration</p>
                    </div>
                    <a href="{{ route('admin.enrollments.index', request()->only(['clc_id', 'track', 'status', 'calendar_year'])) }}" class="text-xs font-bold text-deped-blue hover:underline">
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
                                        <span class="text-slate-600">{{ $enr->clc?->clc_name ?: 'Unassigned' }}</span>
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
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-9 h-9 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-xs font-semibold text-slate-600">No enrollment applications match your filter criteria.</p>
                                            <p class="text-[11px] text-slate-400 mt-1">Try clearing filters or recording walk-in intakes.</p>
                                            @if(count($activeFilters) > 0)
                                                <a href="{{ route('admin.dashboard') }}" class="mt-3 px-3 py-1.5 rounded-lg bg-slate-100 text-deped-blue font-bold text-xs hover:bg-slate-200">
                                                    Reset All Filters
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Showing up to 8 recent intakes</span>
                <a href="{{ route('admin.enrollments.index', request()->only(['clc_id', 'track', 'status', 'calendar_year'])) }}" class="font-bold text-deped-blue hover:underline">
                    View Verification Queue &rarr;
                </a>
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
                                <span class="truncate pr-2">{{ $tel->user?->full_name }}</span>
                                <span class="px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-800 text-[10px] shrink-0">
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
                        <div class="py-8 text-center text-slate-400 text-xs bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                            No telemetry sync records received yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('admin.module-progress.index') }}" class="text-xs font-bold text-deped-blue hover:underline">
                    View all learner module progress &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Offline Mini-game Scores -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-slate-900">Recent Mini-game Scores</h2>
                <p class="text-xs text-slate-500">Offline memory-match results appear here after learners reconnect.</p>
            </div>
            <span class="text-xs font-semibold text-slate-500">{{ $isTeacher ? 'Assigned learners' : 'All learners' }}</span>
        </div>

        @if($recentGameAttempts->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3">Learner</th>
                            <th class="px-6 py-3">Module</th>
                            <th class="px-6 py-3">Score</th>
                            <th class="px-6 py-3">Moves</th>
                            <th class="px-6 py-3">Time</th>
                            <th class="px-6 py-3">Played</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentGameAttempts as $attempt)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-6 py-3 font-semibold text-slate-800">{{ $attempt->user?->full_name ?? 'Learner' }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $attempt->module?->title ?? 'Module unavailable' }}</td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold">{{ $attempt->score }} / {{ $attempt->max_score }}</span>
                                </td>
                                <td class="px-6 py-3 text-slate-600">{{ $attempt->moves }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ intdiv($attempt->elapsed_seconds, 60) }}:{{ str_pad((string) ($attempt->elapsed_seconds % 60), 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-3 text-slate-500">{{ $attempt->attempted_at?->diffForHumans() ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="m-6 py-8 text-center text-slate-400 text-xs bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                No mini-game scores have synced yet.
            </div>
        @endif
    </div>

    <!-- Metrics Definitions & Methodology Modal -->
    <div x-show="dictionaryModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="dictionaryModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-deped-blue/10 text-deped-blue uppercase">Official Specification</span>
                            <span class="text-xs text-slate-400">&bull; DepEd ALS Standards</span>
                        </div>
                        <h2 class="text-lg font-black text-slate-900 mt-1">Dashboard Metrics Dictionary & Data Sources</h2>
                        <p class="text-xs text-slate-500">Every metric displayed on the dashboard has a validated definition and underlying source.</p>
                    </div>
                    <button @click="dictionaryModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                </div>

                <div class="mt-4 overflow-y-auto space-y-4 pr-1">
                    @foreach($metricsDefinitions as $code => $def)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-xs font-bold text-slate-900">{{ $def['name'] }}</div>
                                    <p class="text-xs text-slate-600 mt-1">{{ $def['description'] }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-semibold bg-slate-200 text-slate-700 shrink-0">
                                    {{ $code }}
                                </span>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/60 grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                                <div>
                                    <span class="text-slate-400 font-medium">Source / Formula:</span>
                                    <code class="block font-mono text-[10px] text-deped-blue bg-white p-1 rounded border border-slate-200 mt-0.5">{{ $def['source'] }}</code>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-medium">Policy / DepEd Basis:</span>
                                    <div class="text-slate-700 font-medium mt-0.5">{{ $def['policy_ref'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 shrink-0 mt-4">
                    <span>Complies with DepEd Order No. 27, s. 2017 & ALS Act (RA 11510) reporting requirements.</span>
                    <button type="button" @click="dictionaryModal = false" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800">
                        Close
                    </button>
                </div>
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

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
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
                            <label class="block text-xs font-bold text-slate-700 uppercase">Civil Status *</label>
                            <select name="civil_status" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="solo_parent">Solo Parent</option>
                                <option value="widowed">Widowed</option>
                                <option value="separated">Separated</option>
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Delivery Mode</label>
                            <select name="delivery_mode" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                <option value="modular_offline">Modular (Offline LMS / Print)</option>
                                <option value="modular_print">Modular (Printed DepEd Modules)</option>
                                <option value="blended">Blended Learning</option>
                                <option value="face_to_face">Face-to-Face Consultation</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Initial Status *</label>
                            <select name="enrollment_status" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                <option value="enrolled">Officially Enrolled</option>
                                <option value="under_review">Under Review / Verification</option>
                                <option value="pending">Pending Document Submission</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Intake Remarks / Notes</label>
                        <input type="text" name="remarks" placeholder="Optional notes regarding walk-in registration" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
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

