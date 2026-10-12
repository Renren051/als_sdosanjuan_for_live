@extends('layouts.app')

@section('body')
<div x-data="{ sidebarOpen: false }" class="min-h-full flex flex-col lg:flex-row bg-slate-50">
    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden" 
         x-cloak></div>

    <!-- Sidebar Navigation -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-deped-navy via-slate-900 to-slate-950 text-white flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-2xl">
        
        <!-- Brand Header (Matches Top Dashboard Header Design) -->
        <div class="h-20 flex items-center px-6 border-b border-white/10 gap-3">
            <div class="w-11 h-11 rounded-xl bg-deped-blue/30 border border-white/20 flex items-center justify-center p-1.5 shadow-inner">
                <svg class="w-full h-full text-deped-gold" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2zm0 8.27L4.74 7 12 3.73 19.26 7 12 10.27zM3 10.05v5.86L12 21l9-5.09v-5.86L12 15l-9-4.95z"/>
                </svg>
            </div>
            <div class="leading-tight">
                <div class="text-xs font-semibold tracking-wider text-deped-gold uppercase">DepEd SDO San Juan</div>
                <div class="text-base font-bold text-white tracking-tight">ALS SIS & LMS</div>
                <div class="text-[10px] text-slate-400">Alternative Learning System</div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">
            <div class="px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Learner Portal</div>

            <!-- Dashboard -->
            <a href="{{ route('learner.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('learner.dashboard') ? 'bg-deped-blue text-white shadow-lg shadow-deped-blue/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- My Enrollment -->
            <a href="{{ route('learner.enrollment') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('learner.enrollment') ? 'bg-deped-blue text-white shadow-lg shadow-deped-blue/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>My Enrollment</span>
            </a>

            <!-- My Documents -->
            <a href="{{ route('learner.documents') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('learner.documents*') ? 'bg-deped-blue text-white shadow-lg shadow-deped-blue/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <span>My Documents</span>
            </a>

            <!-- ALS Modules -->
            <a href="{{ route('learner.modules') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('learner.modules*') ? 'bg-deped-blue text-white shadow-lg shadow-deped-blue/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>ALS Modules</span>
            </a>

            <!-- My Profile -->
            <a href="{{ route('learner.profile') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('learner.profile') ? 'bg-deped-blue text-white shadow-lg shadow-deped-blue/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Profile</span>
            </a>
        </nav>

        <!-- Current User Profile & Logout -->
        <div class="p-4 border-t border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-deped-blue/40 border border-white/20 flex items-center justify-center font-bold text-deped-gold shrink-0">
                    {{ strtoupper(substr(auth()->user()->profile?->first_name ?: auth()->user()->username, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-semibold truncate text-white leading-tight">
                        {{ auth()->user()->full_name }}
                    </div>
                    <div class="text-xs text-slate-400">
                        @if(auth()->user()->lrn)
                            <span class="text-deped-gold font-mono font-bold">LRN: {{ auth()->user()->lrn }}</span>
                        @else
                            <span>Learner Account</span>
                        @endif
                    </div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-white/5 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top App Bar (Unified Header matching screenshot) -->
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-8">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight">@yield('page_title', 'Dashboard')</h1>
                    <p class="text-xs text-slate-500 hidden sm:block">SDO San Juan City &bull; Alternative Learning System Portal</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Walk-In Quick Intake Button (Matches Screenshot Exactly) -->
                <a href="{{ route('learner.enrollment') }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm hover:shadow transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Encode Walk-In</span>
                </a>

                <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                <!-- Academic Year Badge (Matches Screenshot Exactly) -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>CY 2026</span>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-4 sm:px-8 pt-6">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-emerald-600 hover:text-emerald-900">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-rose-600 hover:text-rose-900">&times;</button>
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <main class="flex-1 px-4 sm:px-8 py-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
            <p>&copy; 2026 Department of Education &bull; Schools Division Office of San Juan City &bull; Alternative Learning System</p>
        </footer>
    </div>
</div>
@endsection
