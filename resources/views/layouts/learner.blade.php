@extends('layouts.app')

@section('body')
<div x-data="{ mobileMenuOpen: false }" class="min-h-full flex flex-col bg-slate-50">
    <!-- Top Navigation Bar -->
    <header class="bg-gradient-to-r from-deped-navy via-slate-900 to-deped-dark text-white sticky top-0 z-40 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Identity -->
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-deped-blue/40 border border-white/20 flex items-center justify-center p-1.5 shadow-inner">
                        <svg class="w-full h-full text-deped-gold" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2zm0 8.27L4.74 7 12 3.73 19.26 7 12 10.27zM3 10.05v5.86L12 21l9-5.09v-5.86L12 15l-9-4.95z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-deped-gold uppercase tracking-wider">DepEd SDO San Juan City</div>
                        <div class="text-base font-bold text-white tracking-tight">ALS Learner Portal</div>
                    </div>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('learner.dashboard') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('learner.dashboard') ? 'bg-deped-blue text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('learner.enrollment') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('learner.enrollment') ? 'bg-deped-blue text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        My Enrollment
                    </a>
                    <a href="{{ route('learner.documents') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('learner.documents*') ? 'bg-deped-blue text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        My Documents
                    </a>
                    <a href="{{ route('learner.modules') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('learner.modules*') ? 'bg-deped-blue text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        ALS Modules
                    </a>
                    <a href="{{ route('learner.profile') }}" 
                       class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('learner.profile') ? 'bg-deped-blue text-white shadow-md' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                        Profile
                    </a>
                </nav>

                <!-- User Profile & Logout -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('learner.profile') }}" class="text-right group hover:opacity-90 transition-opacity">
                        <div class="text-sm font-bold text-white group-hover:text-deped-gold transition-colors">{{ auth()->user()->full_name }}</div>
                        <div class="text-xs text-slate-300 flex items-center justify-end gap-1.5">
                            @if(auth()->user()->lrn)
                                <span class="font-mono text-deped-gold font-bold">LRN: {{ auth()->user()->lrn }}</span>
                            @else
                                <span class="inline-block w-2 h-2 rounded-full bg-amber-400"></span>
                                <span>Intake Pending</span>
                            @endif
                        </div>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 rounded-xl bg-white/5 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="md:hidden flex items-center gap-2">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" class="md:hidden border-t border-white/10 px-4 pt-3 pb-4 space-y-2 bg-slate-900" x-cloak>
            <a href="{{ route('learner.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('learner.dashboard') ? 'bg-deped-blue text-white' : 'text-slate-300' }}">Dashboard</a>
            <a href="{{ route('learner.enrollment') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('learner.enrollment') ? 'bg-deped-blue text-white' : 'text-slate-300' }}">My Enrollment</a>
            <a href="{{ route('learner.documents') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('learner.documents*') ? 'bg-deped-blue text-white' : 'text-slate-300' }}">My Documents</a>
            <a href="{{ route('learner.modules') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('learner.modules*') ? 'bg-deped-blue text-white' : 'text-slate-300' }}">ALS Modules</a>
            <a href="{{ route('learner.profile') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('learner.profile') ? 'bg-deped-blue text-white' : 'text-slate-300' }}">My Profile</a>
            <div class="pt-3 border-t border-white/10 flex items-center justify-between">
                <span class="text-xs text-slate-400">{{ auth()->user()->full_name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-rose-400 font-semibold">Sign Out</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Flash Messages Container -->
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-6">
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
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <p>&copy; 2026 Department of Education &bull; Schools Division Office of San Juan City &bull; Alternative Learning System</p>
    </footer>
</div>
@endsection
