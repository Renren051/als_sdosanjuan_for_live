@extends('layouts.app')

@section('title', ($pageTitles[$pageKey] ?? 'Home') . ' | SDO San Juan City - Alternative Learning System (ALS)')

@php
    $publicNavigation = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'About ALS', 'route' => 'about'],
        ['label' => 'Online Registration', 'route' => 'register'],
        ['label' => 'A & E Program', 'route' => 'programs'],
        ['label' => 'Requirements', 'route' => 'requirements'],
        ['label' => 'Learning Centers', 'route' => 'centers'],
        ['label' => 'Contact Us', 'route' => 'contact'],
    ];
@endphp

@push('styles')
<style>
    /* Bento Grid Modern Glass & Ambient Glow Tokens */
    .bento-card {
        background-color: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 1.5rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .bento-card:hover {
        border-color: rgba(0, 56, 168, 0.3);
        box-shadow: 0 16px 36px -10px rgba(0, 56, 168, 0.1);
        transform: translateY(-2px);
    }
    .bento-card.bento-dark,
    .bento-dark {
        background-color: #0b193d !important;
        background: linear-gradient(135deg, #0b193d 0%, #0f172a 60%, #020617 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        color: #ffffff !important;
    }
    .bento-card.bento-dark:hover,
    .bento-dark:hover {
        border-color: rgba(252, 209, 22, 0.5) !important;
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.6) !important;
    }
    .bento-glow-gold {
        box-shadow: 0 0 50px -15px rgba(252, 209, 22, 0.25);
    }
    .bento-glow-blue {
        box-shadow: 0 0 50px -15px rgba(0, 56, 168, 0.3);
    }
    .bento-grid-bg {
        background-size: 28px 28px;
        background-image: 
            linear-gradient(to right, rgba(0, 56, 168, 0.04) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(0, 56, 168, 0.04) 1px, transparent 1px);
    }
    .bento-grid-dark-bg {
        background-size: 28px 28px;
        background-image: 
            linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    }
    .phone-mockup-frame {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.08);
    }
</style>
@endpush

@section('body')
<div class="min-h-full flex flex-col bg-slate-50/70 text-slate-800 font-sans selection:bg-deped-blue selection:text-white" 
     x-data="{ 
         mobileNav: false,
         clcSearch: '',
         clcFilter: 'all',
         scrolled: false
     }"
     @scroll.window="scrolled = (window.pageYOffset > 15)">

    <!-- Modern Bento Frosted Glass Navigation Bar -->
    <header class="sticky top-0 z-40 transition-all duration-300 backdrop-blur-md backdrop-saturate-150"
            :class="scrolled ? 'bg-white/75 border-b border-slate-200/80 shadow-sm' : 'bg-white/85 border-b border-slate-200/60 shadow-xs'">
        <div class="max-w-[1920px] mx-auto px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between min-h-[72px] sm:min-h-[78px] py-2 gap-2 sm:gap-4">
                
                <!-- Brand -->
                <div class="flex min-w-0 flex-1 items-center 2xl:flex-none">
                    <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-1.5 sm:gap-3 group">
                        <!-- School / SDO San Juan Logo Container -->
                        <div class="w-8 h-8 sm:w-11 sm:h-11 p-1 rounded-lg sm:rounded-xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center shrink-0 transition-transform group-hover:scale-105 overflow-hidden">
                            <img src="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : (file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('images/school_logo.svg')) }}" 
                                 alt="SDO San Juan City Logo" 
                                 width="44" height="44"
                                 class="w-full h-full object-contain max-w-full max-h-full">
                        </div>

                        <!-- DepEd ALS Official Logo Container -->
                        <div class="w-8 h-8 sm:w-11 sm:h-11 p-1 rounded-lg sm:rounded-xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center shrink-0 transition-transform group-hover:scale-105 overflow-hidden">
                            <img src="{{ file_exists(public_path('images/als_logo.png')) ? asset('images/als_logo.png') : asset('images/als_logo.svg') }}" 
                                 alt="DepEd ALS Logo" 
                                 width="44" height="44"
                                 class="w-full h-full object-contain max-w-full max-h-full">
                        </div>

                        <!-- Typography Titles -->
                        <div class="min-w-0 leading-tight">
                            <span class="text-[8px] sm:text-[10px] font-black uppercase tracking-[0.08em] sm:tracking-widest text-deped-blue block sm:whitespace-nowrap">
                                SDO San Juan City
                            </span>
                            <span class="text-[11px] leading-tight sm:text-base font-black text-slate-900 tracking-tight block sm:whitespace-nowrap">
                                Alternative Learning System
                            </span>
                            <span class="hidden sm:block text-[10px] font-semibold text-slate-500 tracking-wide whitespace-nowrap">
                                Modular LMS &bull; SIS Portal
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Desktop navigation centered between the brand and portal actions. -->
                <div class="hidden min-w-0 flex-1 items-center justify-center px-1 2xl:flex">
                    <nav aria-label="Main navigation" class="flex max-w-full items-center justify-center gap-0.5 rounded-full border border-slate-200/90 bg-slate-100/80 p-1 text-xs font-bold uppercase tracking-wide text-slate-700 shadow-2xs">
                        @foreach($publicNavigation as $item)
                            @php $isActive = request()->routeIs($item['route']); @endphp
                            <a href="{{ route($item['route']) }}"
                               @if($isActive) aria-current="page" @endif
                               class="shrink-0 rounded-full px-2.5 py-2 transition-all whitespace-nowrap hover:bg-white hover:text-deped-blue focus:outline-none focus:ring-2 focus:ring-deped-blue/30 {{ $isActive ? 'bg-white text-deped-blue shadow-sm' : '' }} {{ $item['route'] === 'register' ? 'font-black' : '' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>

                <!-- Portal Sign In and Responsive Menu -->
                <div class="flex shrink-0 items-center justify-end gap-1 sm:gap-3">
                    <a href="{{ route('login') }}" 
                       class="hidden sm:inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 rounded-full bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs uppercase tracking-wider shadow-sm hover:shadow-md transition-all hover:scale-[1.02] active:scale-[0.98] whitespace-nowrap">
                        <span>Portal Sign In</span>
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>

                    <!-- Mobile Hamburger Button -->
                    <div class="2xl:hidden flex items-center gap-2">
                        <a href="{{ route('login') }}" class="sm:hidden px-3 py-1.5 bg-deped-blue text-white text-xs font-bold rounded-lg whitespace-nowrap">
                            Sign In
                        </a>
                        <button @click="mobileNav = !mobileNav" 
                                class="p-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 hover:bg-slate-100 transition-colors"
                                :aria-expanded="mobileNav.toString()"
                                aria-controls="mobile-public-navigation"
                                :aria-label="mobileNav ? 'Close navigation menu' : 'Open navigation menu'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileNav" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             id="mobile-public-navigation"
             class="2xl:hidden border-b border-slate-200 bg-white px-6 py-5 space-y-3 font-bold text-xs uppercase tracking-wider shadow-xl" 
             x-cloak>
            <nav aria-label="Main navigation" class="space-y-1">
                @foreach($publicNavigation as $item)
                    @php $isActive = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       @click="mobileNav = false"
                       @if($isActive) aria-current="page" @endif
                       class="block py-2 px-3 rounded-lg {{ $isActive ? 'bg-blue-50 text-deped-blue' : 'text-slate-800 hover:text-deped-blue hover:bg-slate-50' }} {{ $item['route'] === 'register' ? 'font-black' : '' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
            <div class="pt-3 border-t border-slate-100">
                <a href="{{ route('login') }}" class="w-full py-3 text-center block rounded-xl bg-deped-blue text-white font-bold">
                    Learner &amp; Teacher Portal Sign In
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN BENTO CONTAINER -->
    <main class="flex-grow max-w-[1920px] mx-auto w-full px-4 sm:px-6 lg:px-8 py-7 sm:py-12 space-y-10 sm:space-y-12">

        <!-- ==========================================
             HERO BENTO GRID SECTION (#home)
             ========================================== -->
        @if($pageKey === 'home')
        <section id="home" class="scroll-mt-28">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- BENTO HERO CARD 1: PRIMARY MANIFESTO (Span 8) -->
                <div class="lg:col-span-8 bento-card bento-dark bento-glow-blue p-5 sm:p-12 flex flex-col justify-between relative overflow-hidden bento-grid-dark-bg min-h-[440px] sm:min-h-[480px]">
                    <!-- Ambient Glow Blobs -->
                    <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-deped-blue/30 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-deped-gold/15 blur-3xl pointer-events-none"></div>

                    <!-- Header Badges -->
                    <div class="relative z-10 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-deped-gold">
                            <span class="w-2 h-2 rounded-full bg-deped-gold animate-ping"></span>
                            <span>SDO San Juan City &bull; Division ALS Network</span>
                        </div>

                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.08] uppercase">
                            Build Knowledge.<br/>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-deped-gold via-amber-300 to-amber-500">
                                Rebuild Your Future.
                            </span>
                        </h1>

                        <p class="text-sm sm:text-base text-slate-300 font-normal max-w-2xl leading-relaxed pt-1">
                            The Alternative Learning System (ALS) provides out-of-school youth and adults in San Juan City with quality, 100% tuition-free modular education, neighborhood Community Learning Centers, and official DepEd accreditation.
                        </p>
                    </div>

                    <!-- Call To Action & Pill Badges -->
                    <div class="relative z-10 pt-8 sm:pt-10 flex flex-wrap items-center gap-4">
                        <a href="{{ route('register') }}" 
                           class="inline-flex w-full items-center justify-center gap-2.5 px-4 py-3.5 sm:w-auto sm:px-7 sm:py-4 rounded-2xl bg-deped-gold hover:bg-amber-400 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-lg hover:shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <span>ALS Online Registration (Form 2)</span>
                            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>

                        <a href="{{ route('centers') }}" 
                           class="inline-flex w-full items-center justify-center gap-2 px-4 py-3.5 sm:w-auto sm:px-6 sm:py-4 rounded-2xl bg-slate-900/80 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm uppercase tracking-wider border border-white/30 backdrop-blur-md transition-all shadow-md hover:border-deped-gold/50">
                            <span>Locate Learning Centers</span>
                            <svg class="w-4 h-4 text-deped-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                    </div>

                    <!-- Bottom Trust Strip -->
                    <div class="relative z-10 pt-8 mt-6 border-t border-white/10 flex flex-wrap items-center gap-y-2 gap-x-6 text-[11px] font-semibold text-slate-300">
                        <div class="flex items-center gap-1.5">
                            <span class="text-emerald-400">✓</span> 100% Free Tuition &amp; Modules
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-emerald-400">✓</span> Official DepEd A&amp;E Diploma
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-emerald-400">✓</span> 21 Barangays Covered
                        </div>
                    </div>
                </div>

                <!-- BENTO HERO CARD 2: ANDROID MODULAR LMS SHOWCASE (Span 4) -->
                <div class="lg:col-span-4 bento-card p-6 sm:p-7 flex flex-col justify-between bg-gradient-to-b from-white to-slate-50 border border-slate-200/90 shadow-sm group">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Android LMS
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Offline-First Engine</span>
                        </div>

                        <h3 class="text-xl font-black text-slate-900 tracking-tight">
                            Learn Anytime. Anywhere. Even Without Internet.
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Modular LMS app designed specifically for learners with limited connectivity. Download modules at your CLC and study completely offline.
                        </p>
                    </div>

                    <!-- Realistic Mobile Phone Screen Mockup -->
                    <div class="my-5 p-4 rounded-2xl bg-slate-950 text-white phone-mockup-frame border border-slate-800 transition-transform group-hover:scale-[1.01]">
                        <!-- Simulated Android Top Status Bar -->
                        <div class="flex items-center justify-between text-[9px] text-slate-400 font-mono pb-2.5 border-b border-slate-800">
                            <span>ALS San Juan Mobile</span>
                            <div class="flex items-center gap-1.5">
                                <span class="text-emerald-400">● Offline Active</span>
                                <span>100%</span>
                            </div>
                        </div>

                        <!-- Simulated App Content -->
                        <div class="pt-3 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <div class="text-[11px] font-black text-white">Learner Mark (LRN: 136542...)</div>
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-deped-blue/40 text-blue-300 font-bold">Grade 10</span>
                            </div>

                            <!-- Progress Bar -->
                            <div>
                                <div class="flex justify-between text-[10px] text-slate-300 font-semibold mb-1">
                                    <span>Modular Completion</span>
                                    <span class="text-deped-gold font-bold">78%</span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-deped-blue via-deped-gold to-emerald-400 rounded-full" style="width: 78%"></div>
                                </div>
                            </div>

                            <!-- Cached Modules Snippets -->
                            <div class="space-y-1.5 pt-1">
                                <div class="p-2 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-between text-[10px]">
                                    <span class="font-bold text-slate-200">LS1: Communication Arts</span>
                                    <span class="text-[9px] text-emerald-400 font-semibold">Cached</span>
                                </div>
                                <div class="p-2 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-between text-[10px]">
                                    <span class="font-bold text-slate-200">LS3: Math Problem Solving</span>
                                    <span class="text-[9px] text-emerald-400 font-semibold">Cached</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Feature Pill -->
                    <div class="flex items-center justify-between pt-3 border-t border-slate-200/80 text-xs font-bold text-slate-600">
                        <span>Background Telemetry Sync</span>
                        <span class="text-deped-blue">Zero Mobile Data &rarr;</span>
                    </div>
                </div>

            </div>

            <!-- ROW OF 4 BENTO METRIC TILES -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-5 mt-6">
                <!-- Stat Bento 1 -->
                <div class="bento-card p-5 bg-white flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-xl bg-blue-50 text-deped-blue flex items-center justify-center font-black text-sm">
                            📍
                        </span>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Active</span>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $totalClcs }}</div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wide mt-0.5">Community Learning Centers</div>
                    </div>
                </div>

                <!-- Stat Bento 2 -->
                <div class="bento-card p-5 bg-white flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-sm">
                            🏛️
                        </span>
                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">100% Citywide</span>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $barangayCount }}</div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wide mt-0.5">San Juan Barangays</div>
                    </div>
                </div>

                <!-- Stat Bento 3 -->
                <div class="bento-card p-5 bg-white flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-xl bg-rose-50 text-deped-red flex items-center justify-center font-black text-sm">
                            📚
                        </span>
                        <span class="text-[10px] font-bold text-deped-blue bg-blue-50 px-2 py-0.5 rounded-full">DepEd K to 12</span>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-slate-900 tracking-tight">6</div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wide mt-0.5">Core Learning Strands</div>
                    </div>
                </div>

                <!-- Stat Bento 4 -->
                <div class="bento-card p-5 bg-white flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-sm">
                            ⚡
                        </span>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">Self-Learning</span>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-slate-900 tracking-tight">{{ $totalModules }}</div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wide mt-0.5">Synthesized PDF Modules</div>
                    </div>
                </div>
            </div>
        </section>
        @endif


        <!-- ==========================================
             SECTION 2: ABOUT ALS BENTO (#about)
             ========================================== -->
        @if($pageKey === 'about')
        <section id="about" class="scroll-mt-28 space-y-6">
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-deped-blue">
                        <span class="w-1.5 h-1.5 rounded-full bg-deped-blue"></span>
                        Section 01 &bull; About ALS
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">
                        About Alternative Learning System
                    </h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    DepEd SDO San Juan City &bull; RA 11510
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
                <article class="lg:col-span-2 bento-card p-6 sm:p-8 bg-gradient-to-r from-blue-50 via-white to-amber-50 border border-slate-200 shadow-sm">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white text-deped-blue text-xs font-bold border border-blue-100">A flexible path to basic education</span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-3">What is the Alternative Learning System (ALS)?</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mt-2">ALS is a parallel learning system in the Philippines that provides a practical alternative to formal instruction. When a person has not had the opportunity or cannot access schooling, ALS offers another way to learn. It draws on non-formal and informal sources of knowledge and skills.</p>
                </article>

                <article class="bento-card p-6 sm:p-7 bg-white border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Why is ALS needed in the Philippines?</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mt-3">Many Filipinos are unable to attend or complete basic education because of different life circumstances, or because a school is not accessible in their community. Since every Filipino has the right to free basic education, ALS gives out-of-school children, youth, and adults a way to continue learning that can fit their situations and needs.</p>
                </article>

                <article class="bento-card p-6 sm:p-7 bg-white border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">What is the basis for ALS?</h3>
                    <div class="space-y-3 mt-3 text-sm text-slate-600 leading-relaxed">
                        <p>The <strong>1987 Philippine Constitution</strong>, Article XIV, Section 2, recognizes and encourages non-formal, informal, and indigenous learning systems, as well as self-learning and out-of-school study programs that respond to community needs.</p>
                        <p><strong>Republic Act No. 9155</strong>, the Governance of Basic Education Act, provides for an Alternative Learning System to serve out-of-school children, youth, and adults.</p>
                    </div>
                </article>

                <article class="bento-card p-6 sm:p-7 bg-white border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">How does ALS work?</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mt-3">The Department of Education offers the <strong>Basic Literacy Program</strong> and the <strong>Continuing Education Program, including Accreditation and Equivalency (A&amp;E)</strong>. Learning materials and activities are modular and flexible, so learning can happen at different times and places according to learners’ needs and the arrangements made with their facilitators.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                        <div class="rounded-xl bg-amber-50 border border-amber-100 p-4">
                            <h4 class="text-sm font-black text-amber-800">Basic Literacy Program</h4>
                            <p class="text-xs text-slate-600 mt-1">Builds foundational literacy and numeracy skills.</p>
                        </div>
                        <div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
                            <h4 class="text-sm font-black text-deped-blue">Continuing Education / A&amp;E</h4>
                            <p class="text-xs text-slate-600 mt-1">Supports continued learning and accreditation and equivalency.</p>
                        </div>
                    </div>
                </article>

                <article class="bento-card p-6 sm:p-7 bg-white border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">How is ALS different from formal education?</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                            <h4 class="text-sm font-black text-slate-800">Formal education</h4>
                            <p class="text-xs text-slate-600 leading-relaxed mt-2">Usually takes place in a classroom and is managed by trained formal school teachers.</p>
                        </div>
                        <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                            <h4 class="text-sm font-black text-emerald-800">ALS non-formal education</h4>
                            <p class="text-xs text-slate-600 leading-relaxed mt-2">Takes place outside the formal classroom in community learning centers, barangay halls, libraries, or learners’ homes. ALS mobile teachers, coordinators, and instructional managers agree with learners on a suitable schedule and venue.</p>
                        </div>
                    </div>
                </article>
            </div>
        </section>
        @endif


        <!-- ==========================================
             SECTION 3: A & E PROGRAM BENTO (#programs)
             ========================================== -->
        @if($pageKey === 'programs')
        <section id="programs" class="scroll-mt-28 space-y-6">
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-deped-red">
                        <span class="w-1.5 h-1.5 rounded-full bg-deped-red"></span>
                        Section 02 &bull; Academic Programs &amp; Strands
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">
                        Accreditation &amp; Equivalency (A &amp; E)
                    </h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    3 Core Tracks &bull; 6 Learning Strands
                </div>
            </div>

            <!-- 3 Program Bento Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Track 1: Basic Literacy Program (BLP) -->
                <div class="bento-card p-6 bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 font-black text-base flex items-center justify-center shadow-sm">
                                BLP
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-50 text-amber-700">
                                Literacy Track
                            </span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 uppercase">Basic Literacy Program</h3>
                        <p class="text-xs text-slate-600 font-normal mt-2 leading-relaxed">
                            Foundational course for non-literate youth and adults. Focuses on basic reading, writing, and numeracy skills essential to daily community living.
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-400">Target Level:</span>
                        <span class="text-amber-700 font-extrabold">Zero Prerequisite</span>
                    </div>
                </div>

                <!-- Track 2: A&E Elementary Level -->
                <div class="bento-card p-6 bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-deped-blue text-white font-black text-base flex items-center justify-center shadow-sm">
                                ELEM
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-blue-50 text-deped-blue">
                                Grade 6 Equivalency
                            </span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 uppercase">A &amp; E Elementary Level</h3>
                        <p class="text-xs text-slate-600 font-normal mt-2 leading-relaxed">
                            Equivalent to the formal elementary curriculum. Completers qualify for an official elementary school diploma and advance to Junior High School.
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-400">Eligibility:</span>
                        <span class="text-deped-blue font-extrabold">Ages 12 &amp; Above</span>
                    </div>
                </div>

                <!-- Track 3: A&E Junior High School Level -->
                <div class="bento-card p-6 bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-deped-red text-white font-black text-base flex items-center justify-center shadow-sm">
                                JHS
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-rose-50 text-deped-red">
                                Grade 10 Equivalency
                            </span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 uppercase">A &amp; E Junior High School</h3>
                        <p class="text-xs text-slate-600 font-normal mt-2 leading-relaxed">
                            Comprehensive secondary competencies. Qualifies completers for Senior High School (Grades 11-12), college admissions, or technical vocational tracks.
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-400">Eligibility:</span>
                        <span class="text-deped-red font-extrabold">Ages 16 &amp; Above</span>
                    </div>
                </div>

            </div>

            <!-- BENTO CURRICULUM STRANDS TILE (Wide Dark Bento) -->
            <div class="bento-card bento-dark bg-slate-950 text-white p-5 sm:p-10 border border-slate-800 shadow-xl space-y-6 sm:space-y-8 bento-grid-dark-bg">
                <div class="border-b border-white/10 pb-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-deped-gold text-xs font-bold mb-2">
                        <span>DepEd K to 12 Basic Education Curriculum for ALS</span>
                    </div>
                    <h3 class="text-2xl font-black text-white tracking-tight uppercase">
                        The 6 ALS Learning Strands (LS 1 to 6)
                    </h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-2xl">
                        Integrated competencies preparing learners for 21st-century livelihood, digital citizenship, and higher learning.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($strands as $st)
                        <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-700/80 hover:border-deped-gold/60 hover:bg-slate-850 transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-2.5 py-1 rounded-lg font-black text-[11px] text-white uppercase shadow-sm" 
                                          style="background-color: {{ $st->color_code ?? '#0038A8' }}">
                                        {{ $st->code }}
                                    </span>
                                    <span class="text-[11px] font-bold text-deped-gold bg-deped-gold/10 px-2.5 py-0.5 rounded-full border border-deped-gold/20">
                                        {{ $st->published_modules_count }} Modules
                                    </span>
                                </div>
                                <h4 class="text-sm font-black text-white uppercase tracking-tight">{{ $st->name }}</h4>
                                <p class="text-xs text-slate-300 mt-2 line-clamp-3 font-normal leading-relaxed">{{ $st->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif


        <!-- ==========================================
             SECTION 4: REQUIREMENTS BENTO (#requirements)
             ========================================== -->
        @if($pageKey === 'requirements')
        <section id="requirements" class="scroll-mt-28 space-y-8">
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-deped-blue">
                        <span class="w-1.5 h-1.5 rounded-full bg-deped-blue"></span>
                        Section 03 &bull; DepEd Intake Checklist & Qualifications
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">
                        Enrolment Requirements
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">
                        Opisyal na pamantayan at mga dokumentong kinakailangan para sa ALS Accreditation & Equivalency (A&E).
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700 shrink-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Reference: <strong>DepEd.gov.ph</strong></span>
                </div>
            </div>

            <!-- PART 1: REQUIREMENTS (MGA DOKUMENTONG KINAKAILANGAN) -->
            <div class="space-y-4">
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-deped-navy to-deped-blue text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-[10px] font-black uppercase tracking-wider text-deped-gold">
                            Opisyal na Dokumento
                        </span>
                        <h3 class="text-lg sm:text-xl font-black mt-1">Requirements sa Pagpaparehistro</h3>
                        <p class="text-xs text-white/90 mt-0.5">
                            Ang mga magpapa-register para sa ALS A&E Test ay kinakailangang magpasa ng mga sumusunod na documents:
                        </p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-xl bg-white/10 border border-white/20 text-white shrink-0 self-start sm:self-center">
                        4 Mandatory Items
                    </span>
                </div>

                <!-- 4 Requirement Bento Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    <!-- Req 1: 1x1 Pictures with Name Tag -->
                    <div class="bento-card p-6 bg-white border-2 border-deped-blue/30 rounded-3xl shadow-sm flex flex-col justify-between hover:border-deped-blue transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-mono font-bold text-deped-blue bg-blue-50 px-2.5 py-0.5 rounded-md">DOC-01</span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">2 Piraso</span>
                            </div>
                            <h4 class="text-base font-black text-slate-900">
                                Dalawang (2) Piraso ng (1x1) Picture with Name Tag
                            </h4>
                            <p class="text-xs text-slate-600 mt-2 font-medium leading-relaxed">
                                White background na may nakalimbag na name tag sa ilalim:
                            </p>
                            <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-[11px] font-mono leading-tight">
                                <div class="font-bold text-slate-500 text-[10px] uppercase">Format:</div>
                                <div class="font-black text-slate-900 mt-0.5">SURNAME, FIRST NAME &amp; MIDDLE NAME</div>
                                <div class="text-slate-500 text-[10px] mt-1.5">Example:</div>
                                <div class="font-bold text-deped-blue">DELA CRUZ, JUAN SANTOS</div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700">
                            <span>White Background</span>
                            <span>✓</span>
                        </div>
                    </div>

                    <!-- Req 2: PSA Birth Certificate -->
                    <div class="bento-card p-6 bg-white border border-slate-200 rounded-3xl shadow-sm flex flex-col justify-between hover:border-deped-blue/50 transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-mono font-bold text-deped-blue bg-blue-50 px-2.5 py-0.5 rounded-md">DOC-02</span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Mandatory</span>
                            </div>
                            <h4 class="text-base font-black text-slate-900">
                                PSA Birth Certificate
                            </h4>
                            <p class="text-xs text-slate-600 mt-2 font-normal leading-relaxed">
                                Philippine Statistics Authority (PSA) Birth Certificate.
                            </p>
                            <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 font-medium">
                                <div class="flex items-center gap-1.5 text-slate-900 font-bold">
                                    <svg class="w-4 h-4 text-deped-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Original and Photocopy</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1">
                                    (O kopya mula sa Local Civil Registrar kung wala pa ang PSA)
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700">
                            <span>Proof of Identity &amp; Age</span>
                            <span>✓</span>
                        </div>
                    </div>

                    <!-- Req 3: Brgy. Certificate of Residency -->
                    <div class="bento-card p-6 bg-white border border-slate-200 rounded-3xl shadow-sm flex flex-col justify-between hover:border-deped-blue/50 transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-mono font-bold text-deped-blue bg-blue-50 px-2.5 py-0.5 rounded-md">DOC-03</span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Mandatory</span>
                            </div>
                            <h4 class="text-base font-black text-slate-900">
                                Brgy. Certificate of Residency
                            </h4>
                            <p class="text-xs text-slate-600 mt-2 font-normal leading-relaxed">
                                Katunayan ng paninirahan mula sa inyong kasalukuyang barangay sa San Juan City.
                            </p>
                            <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 font-medium">
                                <div class="flex items-center gap-1.5 text-slate-900 font-bold">
                                    <svg class="w-4 h-4 text-deped-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    <span>Certificate of Residency</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1">
                                    Naglalaman ng kasalukuyang tirahan sa alinman sa 21 barangay ng San Juan.
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700">
                            <span>Proof of Residence</span>
                            <span>✓</span>
                        </div>
                    </div>

                    <!-- Req 4: 1 Valid ID -->
                    <div class="bento-card p-6 bg-white border border-slate-200 rounded-3xl shadow-sm flex flex-col justify-between hover:border-deped-blue/50 transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-xs font-mono font-bold text-deped-blue bg-blue-50 px-2.5 py-0.5 rounded-md">DOC-04</span>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Mandatory</span>
                            </div>
                            <h4 class="text-base font-black text-slate-900">
                                1 Valid ID
                            </h4>
                            <p class="text-xs text-slate-600 mt-2 font-normal leading-relaxed">
                                Isang (1) opisyal o valid identification card.
                            </p>
                            <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 font-medium">
                                <div class="flex items-center gap-1.5 text-slate-900 font-bold">
                                    <svg class="w-4 h-4 text-deped-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                    <span>Original and Photocopy</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1">
                                    Halimbawa: Postal ID, PhilSys / National ID, Barangay ID, Student / School ID, Voter's Certificate, o Company ID.
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700">
                            <span>Official Identification</span>
                            <span>✓</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- PART 2: SIMPLIFIED VERSION (ANTAS AT EDAD SA ALS A&E TEST) -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-deped-gold"></span>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                        Simplified Version: Antas at Kuwalipikasyon ayon sa Edad
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- Elementary Level Card -->
                    <div class="bento-card p-6 sm:p-7 bg-white border border-slate-200 rounded-3xl shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider mb-3">
                                <span>12 Taong Gulang Pataas</span>
                            </div>
                            <h4 class="text-xl font-black text-slate-900">
                                Elementary Level ALS A&E Test
                            </h4>
                            <div class="mt-3 p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 text-xs sm:text-sm text-slate-800 leading-relaxed font-normal">
                                Kung hindi mo natapos ang elementary sa anumang kadahilanan at ikaw ay <strong>12 taong gulang pataas</strong> bago ang takdang araw ng itinakdang petsa ng Accreditation and Equivalency (A&E) Test, ikaw ay <strong>qualified kumuha ng Elementary Level ALS A&E Test</strong>.
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-500">
                            <span>Kwalipikasyon: Hindi nakatapos ng Grade 6</span>
                            <span class="text-emerald-700">Qualified ✓</span>
                        </div>
                    </div>

                    <!-- Secondary Level Card -->
                    <div class="bento-card p-6 sm:p-7 bg-white border border-slate-200 rounded-3xl shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-deped-blue/10 text-deped-blue text-xs font-black uppercase tracking-wider mb-3">
                                <span>16 Taong Gulang Pataas</span>
                            </div>
                            <h4 class="text-xl font-black text-slate-900">
                                Secondary Level ALS A&E Test (Junior High School)
                            </h4>
                            <div class="mt-3 p-4 rounded-2xl bg-blue-50/60 border border-blue-200 text-xs sm:text-sm text-slate-800 leading-relaxed font-normal">
                                Kung ikaw ay nakatapos ng elementary ngunit hindi nakapagpatuloy ng Junior High School sa anumang kadahilanan at ikaw ay <strong>16 taong gulang pataas</strong> bago ang itinakdang petsa ng Accreditation and Equivalency (A&E) Test, ikaw ay <strong>qualified kumuha ng Junior High School ALS A&E Test</strong>.
                            </div>

                            <ul class="mt-3 space-y-2 text-xs text-slate-700">
                                <li class="flex items-start gap-2">
                                    <span class="text-deped-blue font-bold">•</span>
                                    <span><strong>Retakers:</strong> Ang mga hindi pinalad makapasa sa mga nakaraang taon ng ALS Test ay pinapahintulutang mag-retake.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-deped-blue font-bold">•</span>
                                    <span><strong>Nakaraang Reviewees:</strong> Ang mga nagpa-register at nag-review sa nakaraang ALS program, ngunit hindi nakapunta sa itinakdang petsa ng ALS test ay inaanyayahang magpa-register muli.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-deped-blue font-bold">•</span>
                                    <span><strong>Overage Learners:</strong> Sinumang mga kabataan o mga matatanda na kasalukuyang nag-aaral ngunit overage para sa elementary level (mahigit sa 12 taong gulang) o para sa Junior High School (mahigit 16 taong gulang) ay inaanyayahang magpa-register.</span>
                                </li>
                            </ul>
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-500">
                            <span>Kwalipikasyon: Elementarya graduate / JHS drop-out</span>
                            <span class="text-deped-blue">Qualified ✓</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- PART 3: SINU-SINO ANG MGA QUALIFIED KUMUHA NG TEST? -->
            <div class="bento-card p-6 sm:p-8 bg-white border border-slate-200 rounded-3xl shadow-sm space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-black uppercase tracking-wider mb-2">
                        DepEd SDO San Juan City Scope
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Sinu-sino ang mga Qualified Kumuha ng Test?
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">
                        Ang mga sumusunod ay inaanyayahang mag-register para sa ALS A&E Test:
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm text-slate-700">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center gap-2 font-bold text-slate-900">
                            <span class="w-2 h-2 rounded-full bg-deped-blue"></span>
                            <span>Out-of-School Children, Youth and Adults</span>
                        </div>
                        <p class="text-xs text-slate-600 pl-4 leading-relaxed">
                            School leavers o non-attendees ng formal school system na nagnanais makatapos ng kanilang pag-aaral.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center gap-2 font-bold text-slate-900">
                            <span class="w-2 h-2 rounded-full bg-deped-blue"></span>
                            <span>Visually-Impaired Learners</span>
                        </div>
                        <p class="text-xs text-slate-600 pl-4 leading-relaxed">
                            Out-of-school youth and adults / school leavers / non-attendees of the formal school system who are visually-impaired but braille literate (contracted).
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center gap-2 font-bold text-slate-900">
                            <span class="w-2 h-2 rounded-full bg-deped-blue"></span>
                            <span>Homeschoolers &amp; Home Education</span>
                        </div>
                        <p class="text-xs text-slate-600 pl-4 leading-relaxed">
                            Learners of home education or homeschoolers na nangangailangan ng accreditation at opisyal na equivalency rating.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center gap-2 font-bold text-slate-900">
                            <span class="w-2 h-2 rounded-full bg-deped-blue"></span>
                            <span>Non-Passers ng Nakaraang A&E Test</span>
                        </div>
                        <p class="text-xs text-slate-600 pl-4 leading-relaxed">
                            Non-passer of previous years' A&E Test na handang sumubok muli at kumuha ng remediation sessions.
                        </p>
                    </div>
                </div>

                <!-- Others who may be -->
                <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-50/50 to-slate-50 border border-blue-100">
                    <div class="font-bold text-slate-900 text-xs sm:text-sm uppercase tracking-wider mb-2">
                        Others who may be eligible:
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs text-slate-700">
                        <div class="p-3 rounded-xl bg-white border border-slate-200">
                            <strong class="text-slate-900 block mb-0.5">Unemployed OSYAs</strong>
                            Unemployed out-of-school youth and adults.
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-slate-200">
                            <strong class="text-slate-900 block mb-0.5">Manggagawa &amp; Mothers</strong>
                            Industry-based workers, housewives, kasambahay/maids, factory workers, drivers, etc.
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-slate-200">
                            <strong class="text-slate-900 block mb-0.5">Cultural Minorities / IPs</strong>
                            Members of cultural minorities / indigenous peoples (IPs).
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-slate-200">
                            <strong class="text-slate-900 block mb-0.5">Inmates &amp; Integrees</strong>
                            Inmates sa piitan (BJMP), rebel / soldier integrees.
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-between text-xs text-slate-500">
                    <span class="italic font-medium">Department of Education &bull; Alternative Learning System (ALS)</span>
                    <a href="https://www.deped.gov.ph" target="_blank" rel="noopener noreferrer" class="font-bold text-deped-blue hover:underline">
                        Reference: DepEd.gov.ph &rarr;
                    </a>
                </div>
            </div>

            <!-- Two Ways to Enroll Bento Banner -->
            <div class="bento-card p-5 sm:p-8 bg-gradient-to-r from-amber-50 via-white to-blue-50 border border-slate-200/90 rounded-3xl shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                    <div class="md:col-span-8 space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-extrabold uppercase">
                            <span>Flexible DepEd Intake Methods</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 uppercase">Two Easy Ways to Submit Your Registration:</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 text-xs text-slate-700">
                            <div class="p-3.5 rounded-xl bg-white/90 border border-slate-200">
                                <strong class="text-deped-blue block mb-1 text-sm font-black">1. Online Digital Intake</strong>
                                Complete the 4-step registration wizard on your phone or PC, select your CLC, and submit your initial ALS Form 2.
                            </div>
                            <div class="p-3.5 rounded-xl bg-white/90 border border-slate-200">
                                <strong class="text-amber-700 block mb-1 text-sm font-black">2. In-Person Walk-In</strong>
                                Visit any San Juan Community Learning Center or Barangay Hall where a Mobile Teacher encodes your Form 2 directly.
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-4 flex justify-end">
                        <a href="{{ route('register') }}" 
                           class="w-full sm:w-auto px-4 sm:px-8 py-4 rounded-2xl bg-deped-blue hover:bg-deped-dark text-white font-black text-xs uppercase tracking-wider text-center transition-all shadow-md hover:shadow-lg hover:scale-105">
                            Start Online Registration &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </section>
        @endif


        <!-- ==========================================
             SECTION 5: LEARNING CENTERS BENTO (#centers)
             ========================================== -->
        @if($pageKey === 'centers')
        <section id="centers" class="scroll-mt-28 space-y-6">
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-deped-blue">
                        <span class="w-1.5 h-1.5 rounded-full bg-deped-blue"></span>
                        Section 04 &bull; Community Network
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">
                        San Juan Learning Centers (CLCs)
                    </h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    12 Active Locations Across San Juan City
                </div>
            </div>

            <!-- Bento Filter & Search Toolbar (Alpine.js Interactive) -->
            <div class="bento-card p-4 sm:p-5 bg-white border border-slate-200 shadow-sm flex flex-col items-stretch lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Search Input -->
                <div class="relative w-full lg:w-96">
                    <input type="text" 
                           x-model="clcSearch" 
                           placeholder="Search by center name, barangay, or teacher..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-deped-blue/30 focus:border-deped-blue transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Category Filter Pills -->
                <div class="flex w-full flex-wrap items-center justify-start gap-1.5 text-xs font-bold lg:w-auto">
                    <button @click="clcFilter = 'all'" 
                            :class="clcFilter === 'all' ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3.5 py-2 rounded-xl transition-all">
                        All Centers
                    </button>
                    <button @click="clcFilter = 'School-Based'" 
                            :class="clcFilter === 'School-Based' ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3.5 py-2 rounded-xl transition-all">
                        School-Based
                    </button>
                    <button @click="clcFilter = 'Barangay Hall'" 
                            :class="clcFilter === 'Barangay Hall' ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3.5 py-2 rounded-xl transition-all">
                        Barangay Halls
                    </button>
                    <button @click="clcFilter = 'Community Center'" 
                            :class="clcFilter === 'Community Center' ? 'bg-deped-blue text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3.5 py-2 rounded-xl transition-all">
                        Community Centers
                    </button>
                </div>
            </div>

            <!-- Bento CLC Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($clcs as $clc)
                    <div class="bento-card p-6 bg-white border border-slate-200 shadow-sm flex flex-col justify-between"
                         x-show="(clcFilter === 'all' || clcFilter === '{{ $clc->clc_type }}') && 
                                 ('{{ strtolower($clc->clc_name) }}'.includes(clcSearch.toLowerCase()) || 
                                  '{{ strtolower($clc->barangay) }}'.includes(clcSearch.toLowerCase()) || 
                                  '{{ strtolower($clc->assignedTeacher?->profile?->first_name ?? '') }}'.includes(clcSearch.toLowerCase()))"
                         x-transition>
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-mono font-bold text-[10px] text-deped-blue border border-slate-200">
                                    {{ $clc->clc_code }}
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 uppercase bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                    {{ $clc->clc_type }}
                                </span>
                            </div>

                            <h3 class="text-base font-black text-slate-900 uppercase leading-snug">
                                {{ $clc->clc_name }}
                            </h3>
                            <div class="text-xs text-deped-blue font-bold mt-1">
                                Barangay {{ $clc->barangay }} &bull; San Juan City
                            </div>

                            @if($clc->physical_location_notes)
                                <p class="text-xs text-slate-600 mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100 leading-relaxed">
                                    {{ $clc->physical_location_notes }}
                                </p>
                            @endif
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-medium">Assigned Teacher:</span>
                            <span class="text-slate-800 font-bold">
                                {{ $clc->assignedTeacher ? $clc->assignedTeacher->profile?->first_name . ' ' . $clc->assignedTeacher->profile?->last_name : 'ALS Mobile Teacher Pool' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif


        <!-- ==========================================
             SECTION 6: FAST INTAKE BENTO CTA BANNER
             ========================================== -->
        @if($pageKey === 'home')
        <section id="register-cta" class="bento-card bento-dark bg-slate-950 text-white p-5 sm:p-12 relative overflow-hidden bento-grid-dark-bg border border-slate-800 shadow-xl">
            <div class="relative z-10 max-w-3xl mx-auto text-center space-y-4">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-deped-gold text-slate-950 font-black text-xs uppercase tracking-wider">
                    <span>100% Free Public Education &bull; Open Intake</span>
                </span>

                <h2 class="text-3xl sm:text-5xl font-black uppercase text-white tracking-tight leading-tight">
                    Start Your DepEd Journey in San Juan Today
                </h2>

                <p class="text-sm sm:text-base text-slate-300 font-normal max-w-xl mx-auto leading-relaxed">
                    Complete your learner intake form online in 5 minutes. No tuition fees, no rigid schedules, and full division support from certified ALS teachers.
                </p>

                <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('register') }}" 
                       class="inline-flex w-full items-center justify-center gap-2.5 px-4 py-4 sm:w-auto sm:px-8 rounded-2xl bg-deped-gold hover:bg-amber-400 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-lg hover:shadow-xl transition-all hover:scale-105 active:scale-95">
                        <span>Fill Out ALS Form 2 Online Now</span>
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </section>
        @endif


        <!-- ==========================================
             SECTION 7: CONTACT US BENTO (#contact)
             ========================================== -->
        @if($pageKey === 'contact')
        <section id="contact" class="scroll-mt-28 space-y-6">
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-deped-blue">
                        <span class="w-1.5 h-1.5 rounded-full bg-deped-blue"></span>
                        Section 05 &bull; Inquiries &amp; Assistance
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">
                        Contact SDO San Juan City ALS
                    </h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    Division Office &bull; Pinaglabanan St.
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-7 bento-card bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <iframe
                        title="Map to Division of City Schools - San Juan City"
                        src="https://maps.google.com/maps?q=Division%20of%20City%20Schools%20San%20Juan%20City%2C%20Pinaglabanan%20Street&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed"
                        class="w-full h-72 sm:h-96 border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen>
                    </iframe>
                </div>

                <div class="lg:col-span-5 bento-card p-6 sm:p-8 bg-white border border-slate-200 shadow-sm">
                    <span class="text-[10px] font-black uppercase tracking-widest text-deped-blue">Division Office</span>
                    <h3 class="text-xl font-black text-slate-900 mt-1">Division of City Schools - San Juan City</h3>
                    <address class="not-italic text-sm text-slate-600 leading-relaxed mt-3">Pinaglabanan St., San Juan City 1500</address>

                    <div class="space-y-3 mt-5 pt-5 border-t border-slate-100 text-sm">
                        <p><span class="font-bold text-slate-700">Telephone:</span> <a class="text-deped-blue hover:underline" href="tel:+6324512699">+63 2 451 2699</a></p>
                        <p><span class="font-bold text-slate-700">Fax:</span> <a class="text-deped-blue hover:underline" href="tel:+6324512383">+63 2 451 2383</a></p>
                        <p><span class="font-bold text-slate-700">Email:</span> <a class="break-all text-deped-blue hover:underline" href="mailto:depedsanjuanrecords@gmail.com">depedsanjuanrecords@gmail.com</a></p>
                    </div>

                    <a href="https://www.google.com/maps/search/?api=1&amp;query=Division%20of%20City%20Schools%20San%20Juan%20City%20Pinaglabanan%20St" target="_blank" rel="noopener noreferrer" class="inline-flex mt-6 px-4 py-3 rounded-xl bg-blue-50 text-deped-blue font-bold text-xs uppercase tracking-wide hover:bg-blue-100 transition-colors">
                        Open directions in Google Maps &rarr;
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                <div class="border-b border-slate-200 pb-3">
                    <span class="text-[10px] font-black uppercase tracking-widest text-deped-blue">ALS Contact Persons</span>
                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">Get in touch with the San Juan City ALS team</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach([
                        ['name' => 'Mr. Anselmo B. Joven', 'role' => 'Education Program Supervisor – ALS', 'phone' => '0917-053-0466', 'email' => 'anselmo.joven001@deped.gov.ph'],
                        ['name' => 'Mr. Erwin C. dela Cruz', 'role' => 'Education Program Specialist II – ALS', 'phone' => '0922-219-3356', 'email' => 'erwin.delacruz@deped.gov.ph'],
                        ['name' => 'Mrs. Michelle Faye V. Salamera', 'role' => 'ALS Mobile Teacher II', 'phone' => '0915-624-6983'],
                        ['name' => 'Mrs. Hergelyn G. Cerredo', 'role' => 'ALS Mobile Teacher I', 'phone' => '0933-820-0019'],
                        ['name' => 'Mr. Emil O. Melchor', 'role' => 'ALS Mobile Teacher I', 'phone' => '0975-983-9507'],
                        ['name' => 'Mr. Arniel Lago', 'role' => 'Instructional Manager', 'phone' => '0938-251-1138'],
                        ['name' => 'Mr. Charles Andrew Cacajon', 'role' => 'Instructional Manager', 'phone' => '0908-878-5235'],
                    ] as $person)
                        <article class="bento-card p-5 bg-white border border-slate-200 shadow-sm">
                            <h4 class="text-sm font-black text-slate-900">{{ $person['name'] }}</h4>
                            <p class="text-xs text-slate-500 mt-1">{{ $person['role'] }}</p>
                            <a class="inline-flex mt-4 text-sm font-bold text-deped-blue hover:underline" href="tel:{{ preg_replace('/[^+0-9]/', '', $person['phone']) }}">{{ $person['phone'] }}</a>
                            @if(isset($person['email']))
                                <a class="block break-all text-xs text-slate-600 hover:text-deped-blue hover:underline mt-1" href="mailto:{{ $person['email'] }}">{{ $person['email'] }}</a>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

    </main>

    <!-- MODERN BENTO-INSPIRED FOOTER -->
    <footer class="mt-12 bg-slate-950 text-white border-t border-slate-800 py-12">
        <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-white/10">
                <!-- Logos & Title -->
                <div class="flex w-full min-w-0 items-center gap-2 sm:w-auto sm:gap-3">
                    <div class="w-12 h-12 p-1 rounded-xl bg-white flex items-center justify-center shrink-0 overflow-hidden">
                        <img src="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : (file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('images/school_logo.svg')) }}" 
                             alt="SDO San Juan" width="48" height="48" class="w-full h-full object-contain max-w-full max-h-full">
                    </div>
                    <div class="w-12 h-12 p-1 rounded-xl bg-white flex items-center justify-center shrink-0 overflow-hidden">
                        <img src="{{ file_exists(public_path('images/als_logo.png')) ? asset('images/als_logo.png') : asset('images/als_logo.svg') }}" 
                             alt="DepEd ALS" width="48" height="48" class="w-full h-full object-contain max-w-full max-h-full">
                    </div>
                    <div class="min-w-0 leading-tight pl-1 sm:pl-2">
                        <div class="text-[10px] font-black uppercase leading-snug text-deped-gold sm:text-xs">SDO San Juan City &bull; Alternative Learning System</div>
                        <div class="mt-1 text-[10px] leading-snug text-slate-400 sm:text-[11px]">Department of Education &bull; National Capital Region</div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="flex w-full flex-wrap items-center justify-center gap-x-4 gap-y-2 text-[10px] font-bold text-slate-400 uppercase sm:text-xs md:w-auto">
                    @foreach($publicNavigation as $item)
                        <a href="{{ route($item['route']) }}" class="{{ $item['route'] === 'register' ? 'text-deped-gold hover:underline' : 'hover:text-white transition-colors' }}">
                            {{ $item['label'] === 'Online Registration' ? 'Register' : $item['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('login') }}" class="text-white hover:text-deped-gold transition-colors">Portal Sign In</a>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-center sm:text-left text-[11px] text-slate-500 gap-2">
                <p>&copy; 2026 Department of Education &bull; Schools Division Office of San Juan City. All rights reserved.</p>
                <p class="text-slate-400">Pursuant to Republic Act No. 11510 and the Data Privacy Act of 2012.</p>
            </div>
        </div>
    </footer>

</div>
@endsection
