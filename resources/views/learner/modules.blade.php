@extends('layouts.learner')

@section('title', 'Self-Learning Modules - SDO San Juan City ALS')

@section('content')
<div class="space-y-6 pt-4">

    <!-- Header Banner -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-deped-blue/10 text-deped-blue text-[11px] font-bold uppercase tracking-wider mb-2">
                <span>Official DepEd Self-Learning Modules (SLMs)</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Self-Learning Module Library</h1>
            <p class="text-xs text-slate-500 mt-1">
                Access verified learning materials across the 6 ALS Learning Strands. Read directly in your browser with automatic progress saving or download for offline study.
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="px-3.5 py-1.5 rounded-xl bg-blue-50 text-deped-blue font-bold text-xs border border-blue-200">
                {{ $modules->total() }} Published Modules
            </span>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <!-- Strand Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-100 text-xs custom-scrollbar">
            <a href="{{ request()->fullUrlWithQuery(['strand_id' => null, 'page' => null]) }}" 
               class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition-all {{ !request('strand_id') ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                All Learning Strands
            </a>
            @foreach($strands as $st)
                <a href="{{ request()->fullUrlWithQuery(['strand_id' => $st->id, 'page' => null]) }}" 
                   class="px-3.5 py-2 rounded-xl font-bold whitespace-nowrap transition-all {{ request('strand_id') == $st->id ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $st->code }} &bull; {{ Str::limit($st->name, 22) }}
                </a>
            @endforeach
        </div>

        <!-- Level, Sub-Level, Language and Search Filter -->
        <form method="GET" action="{{ route('learner.modules') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @if(request('strand_id'))
                <input type="hidden" name="strand_id" value="{{ request('strand_id') }}">
            @endif

            <div class="lg:col-span-2 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search module title, topic, or number..."
                       class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-deped-blue focus:bg-white focus:outline-none">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div>
                <select name="level" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-deped-blue">
                    <option value="">All Academic Levels</option>
                    <option value="Elementary" {{ request('level') == 'Elementary' ? 'selected' : '' }}>A&E Lower &amp; Advanced Elementary</option>
                    <option value="Junior_High" {{ request('level') == 'Junior_High' ? 'selected' : '' }}>A&E Secondary (Junior High)</option>
                    <option value="BLP" {{ request('level') == 'BLP' ? 'selected' : '' }}>Basic Literacy Program (BLP)</option>
                </select>
            </div>

            <div>
                <select name="sub_level" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-deped-blue">
                    <option value="">All Sub-Levels</option>
                    <option value="Lower Elementary" {{ request('sub_level') == 'Lower Elementary' ? 'selected' : '' }}>Lower Elementary (Grades 1-3)</option>
                    <option value="Advanced Elementary" {{ request('sub_level') == 'Advanced Elementary' ? 'selected' : '' }}>Advanced Elementary (Grades 4-6)</option>
                    <option value="Junior High School" {{ request('sub_level') == 'Junior High School' ? 'selected' : '' }}>Junior High School (Grades 7-10)</option>
                </select>
            </div>

            <div>
                <select name="language" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-deped-blue">
                    <option value="">All Language Tracks</option>
                    <option value="English" {{ request('language') == 'English' ? 'selected' : '' }}>English</option>
                    <option value="Filipino" {{ request('language') == 'Filipino' ? 'selected' : '' }}>Filipino</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($modules as $mod)
            @php
                $prog = $userProgress->get($mod->id);
            @endphp
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <!-- Strand, Sub-Level and Language Badges -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-white font-bold text-[10px] uppercase tracking-wide" style="background-color: {{ $mod->strand->color_code ?? '#2563eb' }}">
                            {{ $mod->strand->code }}
                        </span>
                        <div class="flex items-center gap-1.5">
                            @if($mod->language)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $mod->language === 'Filipino' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                    {{ $mod->language }}
                                </span>
                            @endif
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $mod->sub_level ?: str_replace('_', ' ', $mod->level) }}
                            </span>
                        </div>
                    </div>

                    <!-- Title & Module Info -->
                    <div class="mt-3">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            {{ $mod->module_number }}
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mt-0.5 leading-snug">
                            {{ $mod->title }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                            {{ $mod->description }}
                        </p>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        @if($prog)
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold {{ $prog->isCompleted() ? 'text-emerald-700' : ($prog->isInProgress() ? 'text-blue-700' : 'text-slate-600') }}">
                                    @if($prog->isCompleted())
                                        ✓ Completed
                                    @elseif($prog->isInProgress())
                                        In Progress (Page {{ $prog->last_page_read }} of {{ $prog->total_pages ?: '?' }})
                                    @else
                                        Downloaded
                                    @endif
                                </span>
                                <span class="font-bold text-slate-600 text-[11px]">{{ $prog->progress_percent }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ $prog->progress_percent }}%"></div>
                            </div>
                        @else
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-slate-400 font-medium">Not Started</span>
                                <span class="text-slate-400 text-[11px]">0%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-slate-300 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-[10px] text-slate-400 mt-2">
                            <span>Version {{ $mod->version }}</span>
                            <span class="font-mono">{{ $mod->formatted_file_size }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Read / Resume & Download -->
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if($prog && $prog->isInProgress() && $prog->last_page_read > 1)
                        <!-- Resume Button -->
                        <a href="{{ route('learner.modules.read', ['id' => $mod->id, 'page' => $prog->last_page_read]) }}"
                           class="flex-1 py-2 px-3 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4 text-deped-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Resume (P. {{ $prog->last_page_read }})</span>
                        </a>
                    @elseif($prog && $prog->isCompleted())
                        <!-- Review Button -->
                        <a href="{{ route('learner.modules.read', ['id' => $mod->id]) }}"
                           class="flex-1 py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Review Module</span>
                        </a>
                    @else
                        <!-- Start Reading Button -->
                        <a href="{{ route('learner.modules.read', ['id' => $mod->id]) }}"
                           class="flex-1 py-2 px-3 rounded-xl bg-deped-blue/10 hover:bg-deped-blue hover:text-white text-deped-blue font-bold text-xs transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Read Online</span>
                        </a>
                    @endif

                    <!-- Direct Download Link -->
                    <a href="{{ url('/api/v1/modules/' . $mod->id . '/download') }}" target="_blank" title="Download PDF for offline study"
                       class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-1.5 shrink-0">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span class="hidden sm:inline">Download</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-1 sm:col-span-2 lg:col-span-3 py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-200">
                <p class="font-bold text-sm">No published modules match your current filter.</p>
                <div class="mt-2">
                    <a href="{{ route('learner.modules') }}" class="text-xs text-deped-blue font-bold hover:underline">
                        Reset Filters
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($modules->hasPages())
        <div class="p-4 bg-white rounded-3xl border border-slate-200 shadow-sm">
            {{ $modules->links() }}
        </div>
    @endif
</div>
@endsection
