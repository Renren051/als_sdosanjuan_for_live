@extends('layouts.admin')

@section('title', 'Self-Learning Module Repository - SDO San Juan City ALS')
@section('page_title', 'Self-Learning Module Repository (LS 1–6)')

@section('content')
<div class="space-y-6" x-data="{ uploadModal: false }">

    <!-- Top Action & Upload Trigger -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Official DepEd Self-Learning Modules</h2>
            <p class="text-xs text-slate-500">Modules available for offline reading in the Android LMS mobile app and web portal</p>
        </div>
        <button type="button" @click="uploadModal = true" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md shadow-deped-blue/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            <span>Upload New Module (PDF)</span>
        </button>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.modules.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="sm:col-span-2 relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by Module Title or keywords..."
                       class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-deped-blue focus:bg-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Strand Filter -->
            <div>
                <select name="strand_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    <option value="">All Learning Strands</option>
                    @foreach($strands as $st)
                        <option value="{{ $st->id }}" {{ request('strand_id') == $st->id ? 'selected' : '' }}>
                            {{ $st->code }} - {{ Str::limit($st->name, 25) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Level Filter -->
            <div>
                <select name="level" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    <option value="">All Academic Levels</option>
                    <option value="Elementary" {{ request('level') == 'Elementary' ? 'selected' : '' }}>A&E Lower &amp; Advanced Elementary</option>
                    <option value="Junior_High" {{ request('level') == 'Junior_High' ? 'selected' : '' }}>A&E Secondary (Junior High)</option>
                    <option value="BLP" {{ request('level') == 'BLP' ? 'selected' : '' }}>Basic Literacy (BLP)</option>
                </select>
            </div>

            <!-- Sub-Level Filter -->
            <div>
                <select name="sub_level" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    <option value="">All Sub-Levels</option>
                    <option value="Lower Elementary" {{ request('sub_level') == 'Lower Elementary' ? 'selected' : '' }}>Lower Elementary (Grades 1-3)</option>
                    <option value="Advanced Elementary" {{ request('sub_level') == 'Advanced Elementary' ? 'selected' : '' }}>Advanced Elementary (Grades 4-6)</option>
                </select>
            </div>

            <!-- Language Track Filter -->
            <div>
                <select name="language" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
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
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
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
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $mod->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $mod->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <span>{{ $mod->module_number }}</span>
                            <span>&bull;</span>
                            <span class="text-slate-600 font-semibold">{{ $mod->sub_level ?: str_replace('_', ' ', $mod->level) }}</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mt-1 leading-snug">
                            {{ $mod->title }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                            {{ $mod->description }}
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Version {{ $mod->version }}</span>
                        <span class="font-mono font-semibold">{{ $mod->formatted_file_size }}</span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                    <!-- Download Link -->
                    <a href="{{ url('/api/v1/modules/' . $mod->id . '/download') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors">
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>PDF</span>
                    </a>

                    <div class="flex items-center gap-1.5">
                        <!-- Toggle Publish Form -->
                        <form action="{{ route('admin.modules.toggle', $mod->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="px-2.5 py-1.5 rounded-xl text-xs font-semibold {{ $mod->is_published ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                {{ $mod->is_published ? 'Unpublish' : 'Publish' }}
                            </button>
                        </form>

                        <!-- Delete Form -->
                        <form action="{{ route('admin.modules.destroy', $mod->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this module?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-200">
                <p class="font-bold text-sm">No self-learning modules found matching criteria.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($modules->hasPages())
        <div class="p-4 bg-white rounded-3xl border border-slate-200 shadow-sm">
            {{ $modules->links() }}
        </div>
    @endif

    <!-- Upload Module Modal -->
    <div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="uploadModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Upload Self-Learning Module</h3>
                        <p class="text-xs text-slate-500">Only PDF files up to 25MB are accepted</p>
                    </div>
                    <button @click="uploadModal = false" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
                </div>

                <form action="{{ route('admin.modules.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Learning Strand *</label>
                        <select name="strand_id" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                            @foreach($strands as $st)
                                <option value="{{ $st->id }}">{{ $st->code }} - {{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Academic Level *</label>
                            <select name="level" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                <option value="Elementary">A&E Lower &amp; Advanced Elementary</option>
                                <option value="Junior_High">A&E Secondary (Junior High)</option>
                                <option value="BLP">Basic Literacy (BLP)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Sub-Level</label>
                            <select name="sub_level" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                <option value="Lower Elementary">Lower Elementary (Grades 1-3)</option>
                                <option value="Advanced Elementary">Advanced Elementary (Grades 4-6)</option>
                                <option value="">None / Not Applicable</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Language Track *</label>
                            <select name="language" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                <option value="English">English</option>
                                <option value="Filipino">Filipino</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Module Number *</label>
                            <input type="text" name="module_number" required placeholder="e.g. M-LE-EN-01" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Module Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Addition and Subtraction" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Description / Competencies</label>
                        <textarea name="description" rows="2" placeholder="Brief lesson overview and learning goals..." class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Version</label>
                            <input type="text" name="version" value="1.0" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Publish State</label>
                            <select name="is_published" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                <option value="1">Publish Immediately</option>
                                <option value="0">Save as Draft</option>
                            </select>
                        </div>
                    </div>

                    <!-- PDF File Upload with 25MB validation -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Select PDF Document * (Max 25MB)</label>
                        <input type="file" name="pdf_file" required accept=".pdf,application/pdf"
                               class="mt-1 w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-deped-blue/10 file:text-deped-blue hover:file:bg-deped-blue/20">
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="uploadModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-deped-blue hover:bg-deped-dark rounded-xl shadow-md">Upload & Save Module</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
