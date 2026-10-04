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
         inquirySuccess: false
     }">

    <!-- Top DepEd Official Identification Ribbon -->
    <div class="bg-deped-navy text-white text-[11px] font-semibold py-1.5 border-b border-white/10 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center justify-center w-2 h-2 rounded-full bg-deped-gold animate-pulse"></span>
                <span class="tracking-wider uppercase font-bold text-slate-200">Republic of the Philippines &bull; Department of Education</span>
            </div>
            <div class="flex items-center gap-4 text-slate-300">
                <span>Schools Division Office of San Juan City</span>
                <span class="hidden sm:inline text-slate-500">&bull;</span>
                <span class="hidden sm:inline font-bold text-deped-gold">Republic Act 11510 (The ALS Act)</span>
            </div>
        </div>
    </div>

    <!-- Modern Bento Glass Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-xl border-b border-slate-200/90 shadow-xs transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between min-h-[72px] sm:min-h-[78px] py-2 gap-3 sm:gap-4">
                
                <!-- Brand -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 sm:gap-3 group">
                        <!-- School / SDO San Juan Logo Container -->
                        <div class="w-10 h-10 sm:w-11 sm:h-11 p-1 rounded-xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
                            <img src="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : (file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('images/school_logo.svg')) }}" 
                                 alt="SDO San Juan City Logo" 
                                 class="w-full h-full object-contain">
                        </div>

                        <!-- DepEd ALS Official Logo Container -->
                        <div class="w-10 h-10 sm:w-11 sm:h-11 p-1 rounded-xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
                            <img src="{{ file_exists(public_path('images/als_logo.png')) ? asset('images/als_logo.png') : asset('images/als_logo.svg') }}" 
                                 alt="DepEd ALS Logo" 
                                 class="w-full h-full object-contain">
                        </div>

                        <!-- Typography Titles -->
                        <div class="leading-tight">
                            <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-deped-blue block whitespace-nowrap">
                                SDO San Juan City
                            </span>
                            <span class="text-sm sm:text-base font-black text-slate-900 tracking-tight block whitespace-nowrap">
                                Alternative Learning System
                            </span>
                            <span class="text-[9px] sm:text-[10px] font-semibold text-slate-500 tracking-wide block whitespace-nowrap">
                                Modular LMS &bull; SIS Portal
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden 2xl:flex flex-1 min-w-0 items-center justify-center px-2">
                    <nav aria-label="Main navigation" class="flex items-center gap-0.5 font-bold text-[10px] uppercase tracking-wide text-slate-600 bg-slate-100/80 p-1 rounded-full border border-slate-200/90 shadow-2xs">
                        @foreach($publicNavigation as $item)
                            @php $isActive = request()->routeIs($item['route']); @endphp
                            <a href="{{ route($item['route']) }}"
                               @if($isActive) aria-current="page" @endif
                               class="px-2.5 py-1.5 rounded-full transition-all whitespace-nowrap {{ $isActive ? 'bg-white text-deped-blue shadow-sm' : 'hover:text-deped-blue hover:bg-white' }} {{ $item['route'] === 'register' ? 'font-black' : '' }}">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>

                <!-- Portal Sign In and Responsive Menu -->
                <div class="flex items-center justify-end gap-3 shrink-0">
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
                                aria-label="Toggle Navigation">
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
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-12">

        <!-- ==========================================
             HERO BENTO GRID SECTION (#home)
             ========================================== -->
        @if($pageKey === 'home')
        <section id="home" class="scroll-mt-28">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- BENTO HERO CARD 1: PRIMARY MANIFESTO (Span 8) -->
                <div class="lg:col-span-8 bento-card bento-dark bento-glow-blue p-8 sm:p-12 flex flex-col justify-between relative overflow-hidden bento-grid-dark-bg min-h-[480px]">
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
                           class="inline-flex items-center gap-2.5 px-7 py-4 rounded-2xl bg-deped-gold hover:bg-amber-400 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-lg hover:shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <span>ALS Online Registration (Form 2)</span>
                            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>

                        <a href="{{ route('centers') }}" 
                           class="inline-flex items-center gap-2 px-6 py-4 rounded-2xl bg-slate-900/80 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm uppercase tracking-wider border border-white/30 backdrop-blur-md transition-all shadow-md hover:border-deped-gold/50">
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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mt-6">
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
                        Section 01 &bull; Mandate &amp; Philosophy
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">
                        About Alternative Learning System
                    </h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    DepEd SDO San Juan City &bull; RA 11510
                </div>
            </div>

            <!-- Bento Layout for About -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Bento Tile A: Constructivist Modular Philosophy (Span 7) -->
                <div class="lg:col-span-7 bento-card p-8 bg-white border border-slate-200/90 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-deped-blue text-xs font-bold">
                            <span>Constructivism Learning Architecture</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">
                            Learners Construct Meaning from Everyday Life Experiences
                        </h3>
                        <p class="text-sm text-slate-600 leading-relaxed font-normal">
                            ALS is not a traditional passive lecture hall. Rooted in <strong>Constructivist educational philosophy</strong>, learners actively engage with modular materials, bridging their personal background, jobs, and community realities with formal academic competencies.
                        </p>
                        <p class="text-sm text-slate-600 leading-relaxed font-normal">
                            Under the guidance of certified <strong>DepEd Mobile Teachers</strong>, students navigate self-paced modules, attend targeted group consultations at their local Community Learning Center, and prepare for national certification.
                        </p>
                    </div>

                    <!-- 3 Constructivist Micro Pillars -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-black text-deped-blue block">01 &bull; Experience</span>
                            <p class="text-[11px] text-slate-600 mt-1">Practical problem-solving rooted in real work and family contexts.</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-black text-amber-600 block">02 &bull; Self-Pacing</span>
                            <p class="text-[11px] text-slate-600 mt-1">Learners master competencies at their own speed without shame.</p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                            <span class="text-xs font-black text-emerald-600 block">03 &bull; Mentorship</span>
                            <p class="text-[11px] text-slate-600 mt-1">Continuous coaching from division learning facilitators.</p>
                        </div>
                    </div>
                </div>

                <!-- Bento Tile B: Republic Act 11510 Legal Mandate (Span 5) -->
                <div class="lg:col-span-5 bento-card bento-dark bg-slate-950 text-white p-8 flex flex-col justify-between relative overflow-hidden bento-grid-dark-bg">
                    <div class="relative z-10 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-deped-gold text-xs font-bold">
                            <span>Institutionalized Under Philippine Law</span>
                        </div>
                        <h3 class="text-2xl font-black text-white tracking-tight">
                            Republic Act No. 11510 (The ALS Act)
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                            Signed into law to strengthen and institutionalize the Alternative Learning System in basic education. It guarantees that out-of-school youth and adult completers receive standard equivalency recognized by civil service, universities, and private employers.
                        </p>

                        <div class="space-y-2 pt-2 text-xs text-slate-300">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-deped-gold"></span>
                                <span>Zero cost to learners — funded by DepEd national budget</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-deped-gold"></span>
                                <span>Community Learning Centers established in all barangays</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-deped-gold"></span>
                                <span>Direct equivalency to Elementary and Junior High School</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 pt-6 mt-6 border-t border-white/10">
                        <a href="{{ route('register') }}" 
                           class="w-full py-3.5 px-4 rounded-xl bg-deped-blue hover:bg-blue-600 text-white font-bold text-xs uppercase tracking-wider text-center block transition-all shadow-md">
                            Register as an ALS Learner Today &rarr;
                        </a>
                    </div>
                </div>

                <!-- Bento Tile C: Flexible Schedules (Span 4) -->
                <div class="lg:col-span-4 bento-card p-6 bg-white border border-slate-200/90 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-lg mb-4">
                            ⏰
                        </div>
                        <h4 class="text-base font-black text-slate-900 uppercase">Flexible Schedules</h4>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            No daily 8 AM to 5 PM attendance lock. Study modules at home, during work breaks, or attend weekend and evening consultations with your Mobile Teacher.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-bold text-deped-blue">
                        Works for working youth &amp; parents
                    </div>
                </div>

                <!-- Bento Tile D: No Age Ceiling (Span 4) -->
                <div class="lg:col-span-4 bento-card p-6 bg-white border border-slate-200/90 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-deped-blue flex items-center justify-center font-black text-lg mb-4">
                            🌱
                        </div>
                        <h4 class="text-base font-black text-slate-900 uppercase">Zero Age Ceiling</h4>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Education is a lifelong right. ALS San Juan welcomes learners aged 12 to 65+, working adults, solo parents, and persons with special educational needs.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-bold text-deped-blue">
                        Second-chance pathways for all
                    </div>
                </div>

                <!-- Bento Tile E: Dedicated Mobile Teachers (Span 4) -->
                <div class="lg:col-span-4 bento-card p-6 bg-white border border-slate-200/90 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-lg mb-4">
                            🧑‍🏫
                        </div>
                        <h4 class="text-base font-black text-slate-900 uppercase">Dedicated Mobile Teachers</h4>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Fully certified DepEd teachers deploy directly to San Juan barangays, bringing instructional materials, formative assessments, and moral support directly to learners.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-bold text-emerald-600">
                        12 Centers with assigned mentors
                    </div>
                </div>

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
            <div class="bento-card bento-dark bg-slate-950 text-white p-8 sm:p-10 border border-slate-800 shadow-xl space-y-8 bento-grid-dark-bg">
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
        <section id="requirements" class="scroll-mt-28 space-y-6">
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-deped-blue">
                        <span class="w-1.5 h-1.5 rounded-full bg-deped-blue"></span>
                        Section 03 &bull; Intake Checklist
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight mt-1">
                        Enrolment Requirements
                    </h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    Mandatory for ALS Form 2 Validation
                </div>
            </div>

            <!-- 4 Requirement Bento Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- Req 1: PSA Birth Cert -->
                <div class="bento-card p-6 bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-mono font-bold text-deped-blue mb-1">REQ-01</div>
                        <h3 class="text-base font-black text-slate-900 uppercase">PSA Birth Certificate</h3>
                        <p class="text-xs text-slate-600 mt-2 font-normal leading-relaxed">
                            Original or clear copy of Philippine Statistics Authority (PSA) Birth Certificate (or Local Civil Registrar copy if PSA is unavailable).
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700">
                        <span>Mandatory Document</span>
                        <span>✓</span>
                    </div>
                </div>

                <!-- Req 2: Barangay Clearance -->
                <div class="bento-card p-6 bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-mono font-bold text-deped-blue mb-1">REQ-02</div>
                        <h3 class="text-base font-black text-slate-900 uppercase">Barangay Clearance</h3>
                        <p class="text-xs text-slate-600 mt-2 font-normal leading-relaxed">
                            Certificate of Residency from your current San Juan City barangay verifying local address within the division's catchment area.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700">
                        <span>Mandatory Document</span>
                        <span>✓</span>
                    </div>
                </div>

                <!-- Req 3: 2x2 ID Photos -->
                <div class="bento-card p-6 bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-mono font-bold text-deped-blue mb-1">REQ-03</div>
                        <h3 class="text-base font-black text-slate-900 uppercase">2x2 ID Photos</h3>
                        <p class="text-xs text-slate-600 mt-2 font-normal leading-relaxed">
                            2 copies of recent colored 2x2 ID pictures with white background and printed name tag (LastName, FirstName, MI).
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-emerald-700">
                        <span>Mandatory Document</span>
                        <span>✓</span>
                    </div>
                </div>

                <!-- Req 4: Form 137 / SF10 -->
                <div class="bento-card p-6 bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-mono font-bold text-slate-400 mb-1">REQ-04</div>
                        <h3 class="text-base font-black text-slate-900 uppercase">Form 137 / SF10</h3>
                        <p class="text-xs text-slate-600 mt-2 font-normal leading-relaxed">
                            School permanent record from the last formal elementary or high school attended (if applicable for school dropouts).
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-slate-500">
                        <span>Optional / If Applicable</span>
                        <span>○</span>
                    </div>
                </div>

            </div>

            <!-- Two Ways to Enroll Bento Banner -->
            <div class="bento-card p-8 bg-gradient-to-r from-amber-50 via-white to-blue-50 border border-slate-200/90 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                    <div class="md:col-span-8 space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-extrabold uppercase">
                            <span>Flexible DepEd Intake Methods</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 uppercase">Two Easy Ways to Submit Your Registration:</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 text-xs text-slate-700">
                            <div class="p-3.5 rounded-xl bg-white/90 border border-slate-200">
                                <strong class="text-deped-blue block mb-1 text-sm font-black">1. Online Digital Intake</strong>
                                Complete the 5-step registration wizard on your phone or PC, select your CLC, and upload PDF/photos directly.
                            </div>
                            <div class="p-3.5 rounded-xl bg-white/90 border border-slate-200">
                                <strong class="text-amber-700 block mb-1 text-sm font-black">2. In-Person Walk-In</strong>
                                Visit any San Juan Community Learning Center or Barangay Hall where a Mobile Teacher encodes your Form 2 directly.
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-4 flex justify-end">
                        <a href="{{ route('register') }}" 
                           class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-deped-blue hover:bg-deped-dark text-white font-black text-xs uppercase tracking-wider text-center transition-all shadow-md hover:shadow-lg hover:scale-105">
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
            <div class="bento-card p-4 sm:p-5 bg-white border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Search Input -->
                <div class="relative w-full md:w-96">
                    <input type="text" 
                           x-model="clcSearch" 
                           placeholder="Search by center name, barangay, or teacher..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-deped-blue/30 focus:border-deped-blue transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Category Filter Pills -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold">
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
        <section id="register-cta" class="bento-card bento-dark bg-slate-950 text-white p-8 sm:p-12 relative overflow-hidden bento-grid-dark-bg border border-slate-800 shadow-xl">
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
                       class="inline-flex items-center gap-2.5 px-8 py-4 rounded-2xl bg-deped-gold hover:bg-amber-400 text-slate-950 font-black text-sm uppercase tracking-wider shadow-lg hover:shadow-xl transition-all hover:scale-105 active:scale-95">
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

            <!-- Bento Contact Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Contact Details Bento (Span 5) -->
                <div class="lg:col-span-5 bento-card p-6 sm:p-8 bg-white border border-slate-200 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-5">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-deped-blue block">Division Office Address</span>
                            <h3 class="text-base font-black text-slate-900 mt-1">SDO San Juan City</h3>
                            <p class="text-xs text-slate-600 mt-1 font-normal leading-relaxed">
                                Schools Division Office Building, Pinaglabanan Street, Barangay Pedro Cruz, San Juan City, Metro Manila
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-[10px] font-black uppercase tracking-widest text-deped-blue block">ALS Unit Operating Hours</span>
                            <p class="text-xs text-slate-800 font-bold mt-1">Monday – Friday: 8:00 AM – 5:00 PM</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Weekend CLC consultations vary per center schedule.</p>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-[10px] font-black uppercase tracking-widest text-deped-blue block">Direct Assistance Channels</span>
                            <p class="text-xs text-slate-800 font-mono font-bold mt-1">Mobile Teacher Hotline: 0917-123-4567</p>
                            <p class="text-xs text-slate-800 font-mono font-bold">Email: als.sanjuan@deped.gov.ph</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <a href="{{ route('register') }}" class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-deped-blue font-bold text-xs uppercase tracking-wider text-center block transition-all">
                            Proceed to Digital Registration &rarr;
                        </a>
                    </div>
                </div>

                <!-- Inquiry Form Bento (Span 7) -->
                <div class="lg:col-span-7 bento-card p-6 sm:p-8 bg-white border border-slate-200 shadow-sm">
                    <div class="border-b border-slate-100 pb-3 mb-5">
                        <span class="text-[10px] font-black uppercase tracking-widest text-deped-blue">Send a Message</span>
                        <h3 class="text-lg font-black text-slate-900 uppercase mt-0.5">
                            Ask an ALS Mobile Teacher
                        </h3>
                    </div>

                    <form @submit.prevent="inquirySuccess = true" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Your Name *</label>
                                <input type="text" required placeholder="Full Name" 
                                       class="mt-1 w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-deped-blue/30 focus:border-deped-blue transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Mobile Number *</label>
                                <input type="text" required placeholder="0917XXXXXXX" 
                                       class="mt-1 w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-deped-blue/30 focus:border-deped-blue transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Your San Juan Barangay</label>
                            <select class="mt-1 w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-deped-blue/30 focus:border-deped-blue transition-all">
                                <option value="">Select your barangay...</option>
                                @foreach([
                                    'Addition Hills', 'Balong-Bato', 'Batis', 'Corazon de Jesus', 'Ermitaño',
                                    'Salapan', 'San Perfecto', 'Santa Lucia', 'Tibagan', 'Progreso',
                                    'Rivera', 'Pedro Cruz', 'Maytunas', 'Isabelita', 'Kabayanan',
                                    'St. Joseph (Halo-halo)', 'Pasadena', 'Little Baguio', 'Greenhills',
                                    'West Crame', 'Onse'
                                ] as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Your Question / Inquiry *</label>
                            <textarea rows="3" required placeholder="Tell us about your educational background or ask about nearest center schedules..." 
                                      class="mt-1 w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-deped-blue/30 focus:border-deped-blue transition-all"></textarea>
                        </div>

                        <!-- Feedback Notification -->
                        <div x-show="inquirySuccess" x-cloak class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                            <span>✓</span>
                            <span>Thank you! An ALS Mobile Teacher from SDO San Juan City will contact you shortly via SMS.</span>
                        </div>

                        <button type="submit" 
                                class="w-full py-3.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-black text-xs uppercase tracking-wider transition-all shadow-sm hover:shadow-md">
                            Submit Inquiry to ALS Mobile Teacher
                        </button>
                    </form>
                </div>

            </div>
        </section>
        @endif

    </main>

    <!-- MODERN BENTO-INSPIRED FOOTER -->
    <footer class="mt-12 bg-slate-950 text-white border-t border-slate-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-white/10">
                <!-- Logos & Title -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 p-1 rounded-xl bg-white flex items-center justify-center">
                        <img src="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : (file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('images/school_logo.svg')) }}" 
                             alt="SDO San Juan" class="w-full h-full object-contain">
                    </div>
                    <div class="w-12 h-12 p-1 rounded-xl bg-white flex items-center justify-center">
                        <img src="{{ file_exists(public_path('images/als_logo.png')) ? asset('images/als_logo.png') : asset('images/als_logo.svg') }}" 
                             alt="DepEd ALS" class="w-full h-full object-contain">
                    </div>
                    <div class="leading-tight pl-2">
                        <div class="text-xs font-black uppercase text-deped-gold">SDO San Juan City &bull; Alternative Learning System</div>
                        <div class="text-[11px] text-slate-400">Department of Education &bull; National Capital Region</div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="flex flex-wrap items-center gap-4 text-xs font-bold text-slate-400 uppercase">
                    @foreach($publicNavigation as $item)
                        <a href="{{ route($item['route']) }}" class="{{ $item['route'] === 'register' ? 'text-deped-gold hover:underline' : 'hover:text-white transition-colors' }}">
                            {{ $item['label'] === 'Online Registration' ? 'Register' : $item['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('login') }}" class="text-white hover:text-deped-gold transition-colors">Portal Sign In</a>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-2">
                <p>&copy; 2026 Department of Education &bull; Schools Division Office of San Juan City. All rights reserved.</p>
                <p class="text-slate-400">Pursuant to Republic Act No. 11510 and the Data Privacy Act of 2012.</p>
            </div>
        </div>
    </footer>

</div>
@endsection
