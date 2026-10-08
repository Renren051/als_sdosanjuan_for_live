@extends('layouts.app')

@section('title', 'Reading: ' . $module->title . ' - ALS SDO San Juan City')

@section('body')
<div class="h-screen flex flex-col bg-slate-900 text-white select-none"
     x-data="moduleReader({
         moduleId: {{ $module->id }},
         initialPage: {{ $resumePage }},
         totalPages: {{ $progress->total_pages ?: 2 }},
         isCompleted: {{ $progress->isCompleted() ? 'true' : 'false' }},
         pdfBaseUrl: '{{ url('/api/v1/modules/' . $module->id . '/download') }}',
         progressUrl: '{{ route('learner.modules.progress', $module->id) }}',
         csrfToken: '{{ csrf_token() }}'
     })">

    <!-- Top Reader Navigation Bar -->
    <header class="h-16 bg-slate-950 border-b border-white/10 px-4 sm:px-6 flex items-center justify-between shrink-0 z-30">
        <!-- Left: Back & Title -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('learner.modules') }}" 
               class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-colors"
               title="Return to Module Repository">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold text-white uppercase tracking-wider" style="background-color: {{ $module->strand?->color_code ?? '#2563eb' }}">
                        {{ $module->strand?->code }}
                    </span>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider truncate">
                        {{ $module->module_number }} &bull; {{ $module->level }}
                    </span>
                </div>
                <h1 class="text-sm font-bold text-white truncate max-w-md sm:max-w-xl">
                    {{ $module->title }}
                </h1>
            </div>
        </div>

        <!-- Center / Right: Progress Indicator & Reader Controls -->
        <div class="flex items-center gap-2 sm:gap-4 shrink-0">
            <!-- Progress Status Badge -->
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-xs">
                <span class="w-2 h-2 rounded-full" :class="completed ? 'bg-emerald-400' : 'bg-deped-gold animate-pulse'"></span>
                <span class="font-bold" x-text="completed ? 'Completed (100%)' : 'Page ' + currentPage + ' of ' + totalPages + ' (' + progressPercent + '%)'"></span>
            </div>

            <!-- Page Navigation Buttons -->
            <div class="flex items-center gap-1 bg-white/5 border border-white/10 p-1 rounded-xl">
                <button type="button" @click="prevPage" :disabled="currentPage <= 1"
                        class="p-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-white/10 disabled:opacity-30 disabled:hover:bg-transparent"
                        title="Previous Page">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <div class="flex items-center gap-1 px-2 text-xs font-mono font-bold">
                    <span x-text="currentPage" class="text-deped-gold"></span>
                    <span class="text-slate-500">/</span>
                    <span x-text="totalPages" class="text-slate-400"></span>
                </div>

                <button type="button" @click="nextPage" :disabled="currentPage >= totalPages"
                        class="p-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-white/10 disabled:opacity-30 disabled:hover:bg-transparent"
                        title="Next Page">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- Mark as Completed Button -->
            <button type="button" @click="markAsCompleted"
                    :class="completed ? 'bg-emerald-600/30 text-emerald-300 border border-emerald-500/40' : 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-md'"
                    class="px-3.5 py-1.5 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span class="hidden sm:inline" x-text="completed ? 'Completed' : 'Mark Completed'"></span>
            </button>

            <!-- Download Button -->
            <a href="{{ url('/api/v1/modules/' . $module->id . '/download') }}" target="_blank"
               class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-colors"
               title="Download PDF Copy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
        </div>
    </header>

    <!-- Reading Progress Bar (Top Accent) -->
    <div class="h-1 bg-slate-800 w-full shrink-0">
        <div class="h-full bg-emerald-500 transition-all duration-300" :style="'width: ' + progressPercent + '%'"></div>
    </div>

    <!-- Reader Frame Viewport -->
    <main class="flex-1 w-full bg-slate-900 overflow-hidden relative">
        <iframe id="modulePdfFrame"
                :src="currentFrameUrl"
                class="w-full h-full border-0 select-text bg-slate-900"
                title="{{ $module->title }}">
        </iframe>

        <!-- Saving Feedback Toast -->
        <div x-show="toastMessage" x-transition
             class="absolute bottom-6 right-6 z-40 px-4 py-2 rounded-2xl bg-slate-950/90 border border-white/20 text-white text-xs font-bold shadow-2xl flex items-center gap-2 backdrop-blur-md">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span x-text="toastMessage"></span>
        </div>
    </main>

    <!-- Reader Footer Bar -->
    <footer class="h-10 bg-slate-950 border-t border-white/10 px-6 flex items-center justify-between text-[11px] text-slate-400 shrink-0">
        <div>
            DepEd SDO San Juan City &bull; Alternative Learning System (ALS)
        </div>
        <div class="flex items-center gap-3">
            <span x-show="saving" class="text-amber-400 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                Saving progress...
            </span>
            <span x-show="!saving" class="text-slate-400">
                Progress automatically saved to your ALS account
            </span>
        </div>
    </footer>
</div>

<script>
function moduleReader(config) {
    return {
        moduleId: config.moduleId,
        currentPage: config.initialPage || 1,
        totalPages: config.totalPages || 2,
        completed: config.isCompleted,
        pdfBaseUrl: config.pdfBaseUrl,
        progressUrl: config.progressUrl,
        csrfToken: config.csrfToken,
        saving: false,
        toastMessage: '',
        toastTimeout: null,

        get progressPercent() {
            if (this.completed) return 100;
            if (this.totalPages > 0) {
                return Math.min(100, Math.round((this.currentPage / this.totalPages) * 100));
            }
            return 50;
        },

        get currentFrameUrl() {
            return this.pdfBaseUrl + '#page=' + this.currentPage;
        },

        showToast(msg) {
            this.toastMessage = msg;
            clearTimeout(this.toastTimeout);
            this.toastTimeout = setTimeout(() => {
                this.toastMessage = '';
            }, 3000);
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.setPage(this.currentPage - 1);
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.setPage(this.currentPage + 1);
            }
        },

        setPage(page) {
            this.currentPage = Math.max(1, Math.min(this.totalPages, page));
            const frame = document.getElementById('modulePdfFrame');
            if (frame) {
                frame.src = this.currentFrameUrl;
            }
            this.syncProgress(this.currentPage, this.completed ? 'completed' : 'in_progress');
        },

        markAsCompleted() {
            this.completed = true;
            this.currentPage = this.totalPages;
            const frame = document.getElementById('modulePdfFrame');
            if (frame) {
                frame.src = this.currentFrameUrl;
            }
            this.syncProgress(this.totalPages, 'completed');
            this.showToast('Congratulations! Module completed!');
        },

        async syncProgress(page, status) {
            this.saving = true;
            try {
                const response = await fetch(this.progressUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        last_page_read: page,
                        total_pages: this.totalPages,
                        completion_status: status
                    })
                });

                const data = await response.json();
                if (data.success) {
                    if (status === 'completed') {
                        this.completed = true;
                    }
                    this.showToast('Reading position saved: Page ' + page);
                }
            } catch (err) {
                console.error('Error saving reading telemetry:', err);
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
@endsection
