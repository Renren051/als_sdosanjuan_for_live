@extends('layouts.app')

@section('title', 'Reading: ' . $module->title . ' - ALS SDO San Juan City')

@push('styles')
<style>
    /* Reader Canvas & Viewport Enhancements */
    .reader-viewport {
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
    }
    .reader-viewport::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .reader-viewport::-webkit-scrollbar-track {
        background: transparent;
    }
    .reader-viewport::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 9999px;
    }
    .reader-viewport::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.35);
    }

    /* Backdrop themes */
    .theme-dark {
        background-color: #0b1120;
        color: #f8fafc;
    }
    .theme-light {
        background-color: #e2e8f0;
        color: #0f172a;
    }
    .theme-sepia {
        background-color: #f4ecd8;
        color: #3d2e1e;
    }

    /* Canvas Shadow & Framing */
    .pdf-canvas-wrapper {
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        transform-origin: top center;
    }

    /* Hide floating chatbot widget while inside reader so it does not obstruct reading */
    [x-data="alsChatbot()"] {
        display: none !important;
    }
</style>
@endpush

@section('body')
<div class="h-screen w-screen flex flex-col select-none overflow-hidden"
     :class="'theme-' + theme"
     x-data="moduleReader({
         moduleId: {{ $module->id }},
         initialPage: {{ $resumePage }},
         totalPages: {{ $progress->total_pages ?: 2 }},
         isCompleted: {{ $progress->isCompleted() ? 'true' : 'false' }},
         pdfUrl: '{{ url('/api/v1/modules/' . $module->id . '/download') }}',
         progressUrl: '{{ route('learner.modules.progress', $module->id) }}',
         csrfToken: '{{ csrf_token() }}',
         workerUrl: '{{ asset('vendor/pdfjs/pdf.worker.min.js') }}'
     })"
     x-init="initViewer()"
     @keydown.window="handleKeyDown($event)"
     @wheel="handleWheel($event)">

    <!-- Top Reader Navigation Bar -->
    <header class="h-16 border-b px-3 sm:px-6 flex items-center justify-between shrink-0 z-30 transition-colors backdrop-blur-md"
            :class="theme === 'dark' ? 'bg-slate-950/95 border-white/10 text-white' : (theme === 'sepia' ? 'bg-[#ede3cc]/95 border-[#d6c4a5] text-[#3d2e1e]' : 'bg-white/95 border-slate-200 text-slate-800')">
        
        <!-- Left: Return & Module Title / Metadata -->
        <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0">
            <a href="{{ route('learner.modules') }}" 
               class="p-2 sm:p-2.5 rounded-xl transition-all flex items-center justify-center shrink-0"
               :class="theme === 'dark' ? 'bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white' : (theme === 'sepia' ? 'bg-[#dfd3b8] hover:bg-[#d4c5a3] text-[#3d2e1e]' : 'bg-slate-100 hover:bg-slate-200 text-slate-700')"
               title="Return to Module Library (Esc)">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>

            <div class="min-w-0">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider text-white shrink-0 shadow-sm" 
                          style="background-color: {{ $module->strand?->color_code ?? '#2563eb' }}">
                        {{ $module->strand?->code }}
                    </span>
                    <span class="text-[11px] font-bold uppercase tracking-wider truncate"
                          :class="theme === 'dark' ? 'text-slate-400' : (theme === 'sepia' ? 'text-[#7d6850]' : 'text-slate-500')">
                        {{ $module->module_number }} &bull; {{ $module->sub_level ?: str_replace('_', ' ', $module->level) }}
                    </span>
                </div>
                <h1 class="text-xs sm:text-sm font-bold truncate max-w-[200px] sm:max-w-md md:max-w-xl"
                    :class="theme === 'dark' ? 'text-white' : (theme === 'sepia' ? 'text-[#2e2113]' : 'text-slate-900')"
                    title="{{ $module->title }}">
                    {{ $module->title }}
                </h1>
            </div>
        </div>

        <!-- Center: Quick Page Navigation Controls -->
        <div class="flex items-center gap-1 sm:gap-2 shrink-0">
            <div class="flex items-center gap-1 p-1 rounded-xl border transition-colors"
                 :class="theme === 'dark' ? 'bg-white/5 border-white/10' : (theme === 'sepia' ? 'bg-[#e5dac0] border-[#d6c4a5]' : 'bg-slate-100 border-slate-200')">
                <!-- Previous Page Button -->
                <button type="button" @click="prevPage" :disabled="currentPage <= 1 || loading"
                        class="p-1.5 rounded-lg transition-all disabled:opacity-25"
                        :class="theme === 'dark' ? 'text-slate-300 hover:text-white hover:bg-white/10' : 'text-slate-700 hover:text-slate-900 hover:bg-white'"
                        title="Previous Page (Left Arrow or PgUp)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <!-- Page Input Box & Total Pages -->
                <div class="flex items-center gap-1 px-1.5 text-xs font-mono font-bold">
                    <input type="number" 
                           min="1" 
                           :max="totalPages"
                           x-model.lazy="pageInput"
                           @change="jumpToPage(pageInput)"
                           @keydown.enter="jumpToPage(pageInput)"
                           class="w-10 sm:w-12 text-center py-0.5 rounded-md text-xs font-bold font-mono focus:outline-none focus:ring-2 focus:ring-deped-blue transition-colors"
                           :class="theme === 'dark' ? 'bg-white/10 text-deped-gold border border-white/20' : (theme === 'sepia' ? 'bg-[#dfd3b8] text-[#543d22] border border-[#cfbe9c]' : 'bg-white text-deped-blue border border-slate-300')"
                           title="Type a page number and press Enter">
                    <span :class="theme === 'dark' ? 'text-slate-500' : 'text-slate-400'">/</span>
                    <span x-text="totalPages" :class="theme === 'dark' ? 'text-slate-400' : (theme === 'sepia' ? 'text-[#7d6850]' : 'text-slate-600')"></span>
                </div>

                <!-- Next Page Button -->
                <button type="button" @click="nextPage" :disabled="currentPage >= totalPages || loading"
                        class="p-1.5 rounded-lg transition-all disabled:opacity-25"
                        :class="theme === 'dark' ? 'text-slate-300 hover:text-white hover:bg-white/10' : 'text-slate-700 hover:text-slate-900 hover:bg-white'"
                        title="Next Page (Right Arrow, Space, or PgDn)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <!-- Right: Zoom, Display Themes, Completion & Tools -->
        <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">
            <!-- Zoom Controls (Desktop / Tablet) -->
            <div class="hidden md:flex items-center gap-1 p-1 rounded-xl border transition-colors"
                 :class="theme === 'dark' ? 'bg-white/5 border-white/10' : (theme === 'sepia' ? 'bg-[#e5dac0] border-[#d6c4a5]' : 'bg-slate-100 border-slate-200')">
                <button type="button" @click="zoomOut" :disabled="scale <= 0.6 || loading"
                        class="p-1.5 rounded-lg transition-all disabled:opacity-25"
                        :class="theme === 'dark' ? 'text-slate-300 hover:text-white hover:bg-white/10' : 'text-slate-700 hover:text-slate-900 hover:bg-white'"
                        title="Zoom Out (-)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </button>

                <button type="button" @click="fitWidth"
                        class="px-2 py-0.5 rounded text-[11px] font-mono font-bold transition-all"
                        :class="theme === 'dark' ? 'text-slate-300 hover:text-white hover:bg-white/10' : 'text-slate-700 hover:text-slate-900 hover:bg-white'"
                        title="Fit to Width / Reset">
                    <span x-text="Math.round(scale * 100) + '%'"></span>
                </button>

                <button type="button" @click="zoomIn" :disabled="scale >= 3.0 || loading"
                        class="p-1.5 rounded-lg transition-all disabled:opacity-25"
                        :class="theme === 'dark' ? 'text-slate-300 hover:text-white hover:bg-white/10' : 'text-slate-700 hover:text-slate-900 hover:bg-white'"
                        title="Zoom In (+)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
            </div>

            <!-- Rotate Page Button -->
            <button type="button" @click="rotatePage"
                    class="hidden sm:flex p-2 rounded-xl transition-all border"
                    :class="theme === 'dark' ? 'bg-white/5 border-white/10 text-slate-300 hover:text-white hover:bg-white/10' : (theme === 'sepia' ? 'bg-[#e5dac0] border-[#d6c4a5] text-[#3d2e1e] hover:bg-[#dfd3b8]' : 'bg-slate-100 border-slate-200 text-slate-700 hover:bg-slate-200')"
                    title="Rotate 90° Clockwise">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>

            <!-- Theme Toggle (Dark / Light / Sepia) -->
            <div class="flex items-center p-0.5 rounded-xl border"
                 :class="theme === 'dark' ? 'bg-white/5 border-white/10' : (theme === 'sepia' ? 'bg-[#e5dac0] border-[#d6c4a5]' : 'bg-slate-100 border-slate-200')">
                <button type="button" @click="setTheme('dark')" :class="theme === 'dark' ? 'bg-slate-800 text-deped-gold shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                        class="p-1.5 rounded-lg transition-all" title="Dark Background">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
                <button type="button" @click="setTheme('sepia')" :class="theme === 'sepia' ? 'bg-[#d8c8a6] text-[#3d2e1e] shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                        class="p-1.5 rounded-lg transition-all" title="Sepia Warm Book">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </button>
                <button type="button" @click="setTheme('light')" :class="theme === 'light' ? 'bg-white text-deped-blue shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                        class="p-1.5 rounded-lg transition-all" title="Light Background">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
            </div>

            <!-- Fullscreen Toggle Button -->
            <button type="button" @click="toggleFullscreen"
                    class="p-2 rounded-xl transition-all border"
                    :class="theme === 'dark' ? 'bg-white/5 border-white/10 text-slate-300 hover:text-white hover:bg-white/10' : (theme === 'sepia' ? 'bg-[#e5dac0] border-[#d6c4a5] text-[#3d2e1e] hover:bg-[#dfd3b8]' : 'bg-slate-100 border-slate-200 text-slate-700 hover:bg-slate-200')"
                    title="Toggle Fullscreen (F)">
                <svg x-show="!isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                <svg x-show="isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v5m0-5h5m6 0l5 5m0-5v5m0-5h-5m-6 16l-5-5m0 5v-5m0 5h5m6 0l5-5m0 5v-5m0 5h-5"/></svg>
            </button>

            <!-- Mark as Completed Button -->
            <button type="button" @click="markAsCompleted"
                    :class="completed ? 'bg-emerald-600/30 text-emerald-400 border border-emerald-500/40' : 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-900/20'"
                    class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-all shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span class="hidden sm:inline" x-text="completed ? 'Completed (100%)' : 'Mark Done'"></span>
            </button>

            <!-- Download PDF Button -->
            <a href="{{ url('/api/v1/modules/' . $module->id . '/download') }}" target="_blank"
               class="p-2 sm:p-2.5 rounded-xl transition-all border flex items-center justify-center shrink-0"
               :class="theme === 'dark' ? 'bg-white/5 border-white/10 text-slate-300 hover:text-white hover:bg-white/10' : (theme === 'sepia' ? 'bg-[#e5dac0] border-[#d6c4a5] text-[#3d2e1e] hover:bg-[#dfd3b8]' : 'bg-slate-100 border-slate-200 text-slate-700 hover:bg-slate-200')"
               title="Download PDF Copy">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </a>
        </div>
    </header>

    <!-- Reading Progress Bar (Top Accent) -->
    <div class="h-1 w-full shrink-0 transition-colors"
         :class="theme === 'dark' ? 'bg-slate-800' : (theme === 'sepia' ? 'bg-[#d8c8a6]' : 'bg-slate-200')">
        <div class="h-full bg-emerald-500 transition-all duration-300 shadow-sm shadow-emerald-500/50" 
             :style="'width: ' + progressPercent + '%'"></div>
    </div>

    <!-- Main Reading Viewport & Canvas Stage -->
    <main class="flex-1 w-full overflow-auto reader-viewport relative flex flex-col items-center justify-start p-3 sm:p-6"
          id="readerContainer"
          @touchstart="handleTouchStart($event)"
          @touchend="handleTouchEnd($event)">

        <!-- Loading State Spinner -->
        <div x-show="loading" 
             class="absolute inset-0 z-20 flex flex-col items-center justify-center backdrop-blur-sm transition-opacity"
             :class="theme === 'dark' ? 'bg-slate-950/80 text-white' : (theme === 'sepia' ? 'bg-[#f4ecd8]/90 text-[#3d2e1e]' : 'bg-slate-100/90 text-slate-800')">
            <div class="w-14 h-14 rounded-full border-4 border-deped-blue/30 border-t-deped-blue animate-spin mb-4"></div>
            <div class="text-sm font-bold tracking-wide" x-text="loadingText">Loading module...</div>
            <div class="text-xs text-slate-400 mt-1" x-text="'Please wait while the PDF is rendering'"></div>
        </div>

        <!-- Error State Card (Graceful Fallback) -->
        <div x-show="errorMessage" x-cloak
             class="my-auto max-w-md w-full p-6 sm:p-8 rounded-3xl border text-center shadow-2xl transition-all"
             :class="theme === 'dark' ? 'bg-slate-900 border-white/10 text-white' : 'bg-white border-slate-200 text-slate-800'">
            <div class="w-14 h-14 rounded-2xl bg-rose-500/10 text-rose-500 mx-auto flex items-center justify-center mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-bold mb-1">Unable to Render Page</h3>
            <p class="text-xs text-slate-400 mb-5" x-text="errorMessage"></p>
            <div class="flex items-center justify-center gap-3">
                <button type="button" @click="loadPdf()" 
                        class="px-4 py-2 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md transition-colors">
                    Try Reloading
                </button>
                <a :href="pdfUrl" target="_blank"
                   class="px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs transition-colors">
                    Open in New Tab
                </a>
            </div>
        </div>

        <!-- PDF Canvas Wrapper & Page Renderer -->
        <div x-show="!errorMessage" class="pdf-canvas-wrapper relative flex flex-col items-center">
            
            <!-- Floating Side Navigation Hover Zones (Desktop) -->
            <button type="button" @click="prevPage" 
                    x-show="currentPage > 1"
                    class="hidden lg:flex fixed left-6 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full backdrop-blur-md border shadow-2xl items-center justify-center transition-all z-20 group"
                    :class="theme === 'dark' ? 'bg-slate-950/70 border-white/20 text-white hover:bg-slate-900' : 'bg-white/80 border-slate-300 text-slate-800 hover:bg-white'"
                    title="Previous Page (Left Arrow)">
                <svg class="w-6 h-6 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>

            <button type="button" @click="nextPage" 
                    x-show="currentPage < totalPages"
                    class="hidden lg:flex fixed right-6 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full backdrop-blur-md border shadow-2xl items-center justify-center transition-all z-20 group"
                    :class="theme === 'dark' ? 'bg-slate-950/70 border-white/20 text-white hover:bg-slate-900' : 'bg-white/80 border-slate-300 text-slate-800 hover:bg-white'"
                    title="Next Page (Right Arrow or Space)">
                <svg class="w-6 h-6 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>

            <!-- Render Target Canvas -->
            <div class="rounded-xl overflow-hidden shadow-2xl border border-black/10 bg-white relative transition-shadow"
                 :style="'transform: rotate(' + rotation + 'deg)'">
                <canvas id="pdfCanvas" class="block max-w-full h-auto cursor-default"></canvas>
            </div>

            <!-- Page Number Overlay Tag below canvas -->
            <div class="mt-4 px-3 py-1 rounded-full text-[11px] font-bold font-mono tracking-wider shadow-sm transition-colors"
                 :class="theme === 'dark' ? 'bg-white/10 text-slate-300 border border-white/10' : (theme === 'sepia' ? 'bg-[#e5dac0] text-[#3d2e1e] border border-[#d6c4a5]' : 'bg-slate-200 text-slate-700 border border-slate-300')">
                Page <span x-text="currentPage" class="text-deped-blue font-black"></span> of <span x-text="totalPages"></span>
            </div>
        </div>

        <!-- Saving Feedback Toast -->
        <div x-show="toastMessage" x-transition.opacity.duration.200ms
             class="fixed bottom-14 right-6 z-40 px-4 py-2.5 rounded-2xl border text-xs font-bold shadow-2xl flex items-center gap-2.5 backdrop-blur-md"
             :class="theme === 'dark' ? 'bg-slate-950/95 border-white/20 text-white' : 'bg-white/95 border-slate-300 text-slate-800'">
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span x-text="toastMessage"></span>
        </div>
    </main>

    <!-- Bottom Scrubber & Telemetry Footer Bar -->
    <footer class="border-t px-4 sm:px-6 py-2.5 flex items-center justify-between text-[11px] shrink-0 transition-colors backdrop-blur-md"
            :class="theme === 'dark' ? 'bg-slate-950/95 border-white/10 text-slate-400' : (theme === 'sepia' ? 'bg-[#ede3cc]/95 border-[#d6c4a5] text-[#7d6850]' : 'bg-white/95 border-slate-200 text-slate-600')">
        
        <!-- Left: Quick Page Scrubber Slider -->
        <div class="flex items-center gap-3 w-48 sm:w-80">
            <span class="text-[10px] font-bold uppercase tracking-wider hidden sm:inline">Jump</span>
            <input type="range" 
                   min="1" 
                   :max="totalPages" 
                   :value="currentPage" 
                   @input="scrubPage($event.target.value)"
                   class="w-full accent-deped-blue cursor-pointer h-1.5 rounded-lg bg-slate-300/40">
        </div>

        <!-- Center: Shortcuts Legend (Desktop) -->
        <div class="hidden md:flex items-center gap-3 text-[10px] tracking-wide"
             :class="theme === 'dark' ? 'text-slate-500' : 'text-slate-400'">
            <span><kbd class="px-1.5 py-0.5 rounded bg-black/20 font-mono text-[9px]">←</kbd> / <kbd class="px-1.5 py-0.5 rounded bg-black/20 font-mono text-[9px]">→</kbd> Turn Page</span>
            <span>&bull;</span>
            <span><kbd class="px-1.5 py-0.5 rounded bg-black/20 font-mono text-[9px]">+</kbd> / <kbd class="px-1.5 py-0.5 rounded bg-black/20 font-mono text-[9px]">-</kbd> Zoom</span>
            <span>&bull;</span>
            <span><kbd class="px-1.5 py-0.5 rounded bg-black/20 font-mono text-[9px]">F</kbd> Fullscreen</span>
        </div>

        <!-- Right: Auto-Save Status -->
        <div class="flex items-center gap-2 font-medium">
            <span x-show="saving" class="text-amber-500 flex items-center gap-1.5 font-bold">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                Saving position...
            </span>
            <span x-show="!saving" class="flex items-center gap-1.5 text-emerald-600 font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Progress synced
            </span>
        </div>
    </footer>
</div>

<!-- Mozilla PDF.js Local Script with CDN Fallback -->
<script src="{{ asset('vendor/pdfjs/pdf.min.js') }}"></script>
<script>
    if (typeof pdfjsLib === 'undefined') {
        document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"><\/script>');
    }
</script>

<script>
function moduleReader(config) {
    return {
        moduleId: config.moduleId,
        currentPage: config.initialPage || 1,
        pageInput: config.initialPage || 1,
        totalPages: config.totalPages || 1,
        completed: config.isCompleted,
        pdfUrl: config.pdfUrl,
        progressUrl: config.progressUrl,
        csrfToken: config.csrfToken,
        workerUrl: config.workerUrl,

        // PDF.js Engine State
        pdfDoc: null,
        pageRendering: false,
        pageNumPending: null,
        scale: 1.15,
        rotation: 0,
        loading: true,
        loadingText: 'Loading PDF document...',
        errorMessage: '',

        // UI & Viewport State
        theme: localStorage.getItem('als_reader_theme') || 'dark',
        isFullscreen: false,
        saving: false,
        toastMessage: '',
        toastTimeout: null,
        syncTimeout: null,
        touchStartX: 0,
        touchEndX: 0,

        get progressPercent() {
            if (this.completed) return 100;
            if (this.totalPages > 0) {
                return Math.min(100, Math.round((this.currentPage / this.totalPages) * 100));
            }
            return 0;
        },

        initViewer() {
            if (typeof pdfjsLib !== 'undefined') {
                pdfjsLib.GlobalWorkerOptions.workerSrc = this.workerUrl;
            }
            this.loadPdf();

            // Track browser fullscreen events
            document.addEventListener('fullscreenchange', () => {
                this.isFullscreen = !!document.fullscreenElement;
            });

            // Adjust scale on window resize
            window.addEventListener('resize', () => {
                if (!this.loading && this.pdfDoc) {
                    this.autoScale();
                }
            });
        },

        async loadPdf() {
            this.loading = true;
            this.errorMessage = '';
            this.loadingText = 'Loading PDF document...';

            try {
                if (typeof pdfjsLib === 'undefined') {
                    throw new Error('PDF viewer library failed to initialize.');
                }

                const loadingTask = pdfjsLib.getDocument({
                    url: this.pdfUrl,
                    withCredentials: true,
                    cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
                    cMapPacked: true,
                });

                loadingTask.onProgress = (progress) => {
                    if (progress.total > 0) {
                        const pct = Math.round((progress.loaded / progress.total) * 100);
                        this.loadingText = 'Downloading module (' + pct + '%)...';
                    }
                };

                this.pdfDoc = await loadingTask.promise;
                this.totalPages = this.pdfDoc.numPages;

                // Clamp current page
                if (this.currentPage > this.totalPages) {
                    this.currentPage = this.totalPages;
                }
                if (this.currentPage < 1) {
                    this.currentPage = 1;
                }
                this.pageInput = this.currentPage;

                // Sync detected total pages if different from initial database count
                if (this.totalPages !== config.totalPages) {
                    this.debounceSyncProgress(this.currentPage, this.completed ? 'completed' : 'in_progress');
                }

                this.autoScale();
                this.renderPage(this.currentPage);
            } catch (err) {
                console.error('Failed to load PDF document:', err);
                this.errorMessage = 'Could not load the module PDF. It may still be transferring or requires direct browser viewing.';
                this.loading = false;
            }
        },

        autoScale() {
            const container = document.getElementById('readerContainer');
            if (!container) return;
            const containerWidth = container.clientWidth - 48; // padding
            if (containerWidth < 640) {
                // Mobile device: fit comfortably to width
                this.scale = Math.max(0.7, Math.min(1.0, containerWidth / 620));
            } else if (containerWidth < 1024) {
                // Tablet
                this.scale = 1.0;
            } else {
                // Desktop
                this.scale = 1.25;
            }
        },

        currentRenderTask: null,

        async renderPage(num) {
            if (!this.pdfDoc) return;
            if (this.currentRenderTask) {
                try {
                    this.currentRenderTask.cancel();
                } catch (e) {}
            }

            this.pageRendering = true;
            this.loading = true;
            this.loadingText = 'Rendering page ' + num + ' of ' + this.totalPages + '...';

            try {
                const page = await this.pdfDoc.getPage(num);
                const canvas = document.getElementById('pdfCanvas');
                if (!canvas) return;

                const context = canvas.getContext('2d');
                const viewport = page.getViewport({ scale: this.scale });

                // Render at high-DPI resolution for crisp text
                const outputScale = window.devicePixelRatio || 1;
                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                canvas.style.width = Math.floor(viewport.width) + "px";
                canvas.style.height = Math.floor(viewport.height) + "px";

                const transform = outputScale !== 1
                    ? [outputScale, 0, 0, outputScale, 0, 0]
                    : null;

                const renderContext = {
                    canvasContext: context,
                    transform: transform,
                    viewport: viewport
                };

                this.currentRenderTask = page.render(renderContext);
                await this.currentRenderTask.promise;
                this.currentRenderTask = null;

                this.pageRendering = false;
                this.loading = false;
                this.pageInput = num;

                // Reset scroll to top of page
                const container = document.getElementById('readerContainer');
                if (container) {
                    container.scrollTop = 0;
                }

                // If another page was requested while rendering, process it
                if (this.pageNumPending !== null) {
                    const nextNum = this.pageNumPending;
                    this.pageNumPending = null;
                    this.renderPage(nextNum);
                }
            } catch (err) {
                if (err && err.name === 'RenderingCancelledException') {
                    return;
                }
                console.error('Error rendering page ' + num + ':', err);
                this.pageRendering = false;
                this.loading = false;
            }
        },

        queueRenderPage(num) {
            if (this.pageRendering) {
                this.pageNumPending = num;
            } else {
                this.renderPage(num);
            }
        },

        prevPage() {
            if (this.currentPage <= 1) return;
            this.currentPage--;
            this.pageInput = this.currentPage;
            this.queueRenderPage(this.currentPage);
            this.debounceSyncProgress(this.currentPage, this.completed ? 'completed' : 'in_progress');
        },

        nextPage() {
            if (this.currentPage >= this.totalPages) return;
            this.currentPage++;
            this.pageInput = this.currentPage;
            this.queueRenderPage(this.currentPage);

            // If learner reached the final page, auto-complete
            const status = (this.currentPage >= this.totalPages) ? 'completed' : (this.completed ? 'completed' : 'in_progress');
            if (status === 'completed' && !this.completed) {
                this.completed = true;
                this.showToast('You reached the last page! Module marked completed.');
            }
            this.debounceSyncProgress(this.currentPage, status);
        },

        jumpToPage(num) {
            let page = parseInt(num, 10);
            if (isNaN(page)) page = this.currentPage;
            page = Math.max(1, Math.min(this.totalPages, page));
            this.currentPage = page;
            this.pageInput = page;
            this.queueRenderPage(page);
            this.debounceSyncProgress(page, this.completed ? 'completed' : 'in_progress');
        },

        scrubPage(val) {
            this.jumpToPage(val);
        },

        zoomIn() {
            if (this.scale >= 3.0) return;
            this.scale = Math.min(3.0, +(this.scale + 0.15).toFixed(2));
            this.queueRenderPage(this.currentPage);
        },

        zoomOut() {
            if (this.scale <= 0.6) return;
            this.scale = Math.max(0.6, +(this.scale - 0.15).toFixed(2));
            this.queueRenderPage(this.currentPage);
        },

        fitWidth() {
            this.autoScale();
            this.queueRenderPage(this.currentPage);
        },

        rotatePage() {
            this.rotation = (this.rotation + 90) % 360;
        },

        setTheme(themeName) {
            this.theme = themeName;
            localStorage.setItem('als_reader_theme', themeName);
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        },

        markAsCompleted() {
            this.completed = true;
            this.syncProgress(this.currentPage, 'completed');
            this.showToast('Module marked as Completed (100%)!');
        },

        showToast(msg) {
            this.toastMessage = msg;
            clearTimeout(this.toastTimeout);
            this.toastTimeout = setTimeout(() => {
                this.toastMessage = '';
            }, 3000);
        },

        debounceSyncProgress(page, status) {
            clearTimeout(this.syncTimeout);
            this.syncTimeout = setTimeout(() => {
                this.syncProgress(page, status);
            }, 500);
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
                }
            } catch (err) {
                console.error('Error saving reading telemetry:', err);
            } finally {
                this.saving = false;
            }
        },

        handleKeyDown(e) {
            // Ignore if active typing in input
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

            if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                e.preventDefault();
                this.prevPage();
            } else if (e.key === 'ArrowRight' || e.key === 'PageDown' || e.key === ' ') {
                e.preventDefault();
                this.nextPage();
            } else if (e.key === '+' || e.key === '=') {
                e.preventDefault();
                this.zoomIn();
            } else if (e.key === '-' || e.key === '_') {
                e.preventDefault();
                this.zoomOut();
            } else if (e.key === 'f' || e.key === 'F') {
                e.preventDefault();
                this.toggleFullscreen();
            }
        },

        handleWheel(e) {
            if (e.ctrlKey) {
                e.preventDefault();
                if (e.deltaY < 0) {
                    this.zoomIn();
                } else if (e.deltaY > 0) {
                    this.zoomOut();
                }
            }
        },

        handleTouchStart(e) {
            if (e.touches && e.touches.length === 1) {
                this.touchStartX = e.touches[0].clientX;
            }
        },

        handleTouchEnd(e) {
            if (e.changedTouches && e.changedTouches.length === 1) {
                this.touchEndX = e.changedTouches[0].clientX;
                const deltaX = this.touchEndX - this.touchStartX;
                if (deltaX > 75) {
                    // Swiped Right -> Previous Page
                    this.prevPage();
                } else if (deltaX < -75) {
                    // Swiped Left -> Next Page
                    this.nextPage();
                }
            }
        }
    };
}
</script>
@endsection
