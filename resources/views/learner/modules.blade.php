@extends('layouts.learner')

@section('title', 'Self-Learning Modules - SDO San Juan City ALS')

@section('content')
<div class="space-y-6 pt-4" x-data="{ readerModal: false, activePdfUrl: '', activeModuleTitle: '' }">

    <!-- Header Banner -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Self-Learning Module Library</h1>
            <p class="text-xs text-slate-500 mt-1">
                Official DepEd learning materials across the 6 ALS Strands. Read directly in your browser or download to study offline.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-blue-50 text-deped-blue font-bold text-xs border border-blue-200">
                {{ $modules->total() }} Published Modules
            </span>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <!-- Strand Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-100 text-xs custom-scrollbar">
            <a href="{{ request()->fullUrlWithQuery(['strand_id' => null, 'page' => null]) }}" 
               class="px-3.5 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all {{ !request('strand_id') ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                All Strands
            </a>
            @foreach($strands as $st)
                <a href="{{ request()->fullUrlWithQuery(['strand_id' => $st->id, 'page' => null]) }}" 
                   class="px-3.5 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all {{ request('strand_id') == $st->id ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $st->code }} &bull; {{ Str::limit($st->name, 22) }}
                </a>
            @endforeach
        </div>

        <!-- Level and Search Filter -->
        <form method="GET" action="{{ route('learner.modules') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @if(request('strand_id'))
                <input type="hidden" name="strand_id" value="{{ request('strand_id') }}">
            @endif

            <div class="sm:col-span-2 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search module title or topic..."
                       class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-deped-blue focus:bg-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div>
                <select name="level" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    <option value="">All Academic Levels</option>
                    <option value="BLP" {{ request('level') == 'BLP' ? 'selected' : '' }}>Basic Literacy Program (BLP)</option>
                    <option value="Elementary" {{ request('level') == 'Elementary' ? 'selected' : '' }}>A&E Elementary</option>
                    <option value="Junior_High" {{ request('level') == 'Junior_High' ? 'selected' : '' }}>A&E Junior High</option>
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
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-white font-bold text-[10px] uppercase tracking-wide" style="background-color: {{ $mod->strand->color_code ?? '#2563eb' }}">
                            {{ $mod->strand->code }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                            {{ $mod->level }}
                        </span>
                    </div>

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

                    <!-- Progress or File Info -->
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        @if($prog)
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-emerald-700">Progress: {{ ucfirst(str_replace('_', ' ', $prog->completion_status)) }}</span>
                                <span class="text-slate-400 text-[10px]">{{ $prog->progress_percent }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $prog->progress_percent }}%"></div>
                            </div>
                        @else
                            <div class="flex items-center justify-between text-[11px] text-slate-400">
                                <span>Version {{ $mod->version }}</span>
                                <span class="font-mono">{{ $mod->formatted_file_size }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                    <!-- Read in-browser PDF Modal Button -->
                    <button type="button" 
                            @click="activePdfUrl = '{{ url('/api/v1/modules/' . $mod->id . '/download') }}'; activeModuleTitle = '{{ addslashes($mod->title) }}'; readerModal = true"
                            class="px-3.5 py-2 rounded-xl bg-deped-blue/10 hover:bg-deped-blue hover:text-white text-deped-blue font-bold text-xs transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Read Online</span>
                    </button>

                    <!-- Direct Download Link -->
                    <a href="{{ url('/api/v1/modules/' . $mod->id . '/download') }}" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Download</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-200">
                <p class="font-bold text-sm">No modules match your current filter.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($modules->hasPages())
        <div class="p-4 bg-white rounded-3xl border border-slate-200 shadow-sm">
            {{ $modules->links() }}
        </div>
    @endif

    <!-- In-Browser Embedded PDF Reader Modal -->
    <div x-show="readerModal" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="readerModal = false"></div>
        <div class="relative min-h-screen flex flex-col p-2 sm:p-6">
            <div class="bg-white rounded-3xl shadow-2xl flex-1 flex flex-col overflow-hidden max-w-6xl w-full mx-auto border border-white/20">
                <!-- Modal Bar -->
                <div class="h-16 px-6 bg-slate-900 text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <h3 class="text-sm font-bold truncate text-white" x-text="activeModuleTitle"></h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <a :href="activePdfUrl" target="_blank" class="text-xs font-bold text-deped-gold hover:underline">
                            Open in New Tab &rarr;
                        </a>
                        <button @click="readerModal = false" class="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-white/10">
                            &times;
                        </button>
                    </div>
                </div>

                <!-- Embedded PDF Viewer -->
                <div class="flex-1 bg-slate-800">
                    <iframe :src="activePdfUrl" class="w-full h-full border-0" title="PDF Viewer"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
