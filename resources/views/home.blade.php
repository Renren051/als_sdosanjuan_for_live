@extends('layouts.app')

@section('title', 'SDO San Juan City - Alternative Learning System (ALS) | Official Portal')

@push('styles')
<style>
    /* Constructivist Design Tokens & Micro-Styling */
    .constructivist-grid {
        background-size: 32px 32px;
        background-image: 
            linear-gradient(to right, rgba(0, 56, 168, 0.05) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(0, 56, 168, 0.05) 1px, transparent 1px);
    }
    .constructivist-grid-dark {
        background-size: 32px 32px;
        background-image: 
            linear-gradient(to right, rgba(255, 255, 255, 0.06) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255, 255, 255, 0.06) 1px, transparent 1px);
    }
    .brutalist-shadow {
        box-shadow: 6px 6px 0px 0px #0f172a;
    }
    .brutalist-shadow-blue {
        box-shadow: 6px 6px 0px 0px #0038A8;
    }
    .brutalist-shadow-gold {
        box-shadow: 6px 6px 0px 0px #FCD116;
    }
    .brutalist-shadow-red {
        box-shadow: 6px 6px 0px 0px #CE1126;
    }
    .diagonal-stripes {
        background: repeating-linear-gradient(
            45deg,
            #FCD116,
            #FCD116 12px,
            #0f172a 12px,
            #0f172a 24px
        );
    }
    .diagonal-accent-blue {
        background: repeating-linear-gradient(
            -45deg,
            rgba(0, 56, 168, 0.08),
            rgba(0, 56, 168, 0.08) 10px,
            transparent 10px,
            transparent 20px
        );
    }
</style>
@endpush

@section('body')
<div class="min-h-full flex flex-col bg-slate-50 text-slate-900 selection:bg-deped-blue selection:text-white" x-data="{ mobileNav: false }">

    <!-- Top Official Banner Strip -->
    <div class="bg-deped-navy text-white text-[11px] font-bold py-1.5 px-4 sm:px-8 border-b-2 border-slate-900 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-deped-gold animate-pulse"></span>
            <span class="tracking-wider uppercase">Republic of the Philippines &bull; Department of Education</span>
        </div>
        <div class="flex items-center gap-4 text-slate-300">
            <span>Schools Division Office of San Juan City</span>
            <span class="hidden sm:inline">&bull;</span>
            <span class="hidden sm:inline text-deped-gold">Republic Act 11510 (ALS Act)</span>
        </div>
    </div>

    <!-- Main Navigation Bar (Constructivist Header) -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b-4 border-slate-900 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
                
                <!-- Dual Brand Logos (School Logo + ALS Logo) -->
                <a href="#home" class="flex items-center gap-3.5 group">
                    <!-- School / SDO San Juan Logo -->
                    <div class="relative w-14 h-14 sm:w-16 sm:h-16 p-1 bg-white border-2 border-slate-900 brutalist-shadow transition-transform group-hover:-translate-y-0.5">
                        <img src="{{ file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('images/school_logo.svg') }}" 
                             alt="SDO San Juan City Logo" 
                             class="w-full h-full object-contain">
                    </div>

                    <!-- DepEd ALS Official Logo -->
                    <div class="relative w-14 h-14 sm:w-16 sm:h-16 p-1 bg-white border-2 border-slate-900 brutalist-shadow-blue transition-transform group-hover:-translate-y-0.5">
                        <img src="{{ file_exists(public_path('images/als_logo.png')) ? asset('images/als_logo.png') : asset('images/als_logo.svg') }}" 
                             alt="DepEd ALS Logo" 
                             class="w-full h-full object-contain">
                    </div>

                    <!-- Typography Brand Title -->
                    <div class="leading-none pl-1">
                        <div class="text-[10px] font-black uppercase tracking-widest text-deped-blue">
                            SDO San Juan City
                        </div>
                        <div class="text-base sm:text-xl font-black text-slate-950 uppercase tracking-tight mt-0.5">
                            Alternative Learning System
                        </div>
                        <div class="text-[10px] font-bold text-deped-red tracking-wider uppercase mt-0.5">
                            Community-Based Education
                        </div>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden xl:flex items-center gap-1 font-bold text-xs uppercase tracking-wider text-slate-700">
                    <a href="#home" class="px-3 py-2 hover:text-deped-blue hover:bg-slate-100 rounded-lg transition-colors">Home</a>
                    <a href="#about" class="px-3 py-2 hover:text-deped-blue hover:bg-slate-100 rounded-lg transition-colors">About ALS</a>
                    <a href="{{ route('register') }}" class="px-3 py-2 text-deped-blue hover:text-white hover:bg-deped-blue rounded-lg transition-colors">ALS Registration</a>
                    <a href="#programs" class="px-3 py-2 hover:text-deped-blue hover:bg-slate-100 rounded-lg transition-colors">A &amp; E Program</a>
                    <a href="#requirements" class="px-3 py-2 hover:text-deped-blue hover:bg-slate-100 rounded-lg transition-colors">Requirements</a>
                    <a href="#centers" class="px-3 py-2 hover:text-deped-blue hover:bg-slate-100 rounded-lg transition-colors">Learning Centers</a>
                    <a href="#contact" class="px-3 py-2 hover:text-deped-blue hover:bg-slate-100 rounded-lg transition-colors">Contact Us</a>
                </nav>

                <!-- Actions: Portal Login -->
                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ route('login') }}" 
                       class="px-5 py-2.5 rounded-none bg-deped-blue hover:bg-deped-dark text-white font-black text-xs uppercase tracking-wider border-2 border-slate-900 brutalist-shadow transition-all hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none flex items-center gap-2">
                        <span>Portal Login</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- Hamburger Button -->
                <div class="xl:hidden flex items-center gap-2">
                    <a href="{{ route('login') }}" class="sm:hidden px-3 py-1.5 bg-deped-blue text-white text-[11px] font-bold uppercase border-2 border-slate-900">
                        Login
                    </a>
                    <button @click="mobileNav = !mobileNav" class="p-2 border-2 border-slate-900 bg-slate-100 text-slate-900 hover:bg-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileNav" class="xl:hidden border-t-2 border-slate-900 bg-white px-6 py-4 space-y-3 font-bold text-xs uppercase tracking-wider shadow-xl" x-cloak>
            <a href="#home" @click="mobileNav = false" class="block py-2 border-b border-slate-100 hover:text-deped-blue">Home</a>
            <a href="#about" @click="mobileNav = false" class="block py-2 border-b border-slate-100 hover:text-deped-blue">About ALS</a>
            <a href="{{ route('register') }}" class="block py-2 border-b border-slate-100 text-deped-blue font-black">ALS Online Registration</a>
            <a href="#programs" @click="mobileNav = false" class="block py-2 border-b border-slate-100 hover:text-deped-blue">A &amp; E Program</a>
            <a href="#requirements" @click="mobileNav = false" class="block py-2 border-b border-slate-100 hover:text-deped-blue">Requirements</a>
            <a href="#centers" @click="mobileNav = false" class="block py-2 border-b border-slate-100 hover:text-deped-blue">Learning Centers</a>
            <a href="#contact" @click="mobileNav = false" class="block py-2 hover:text-deped-blue">Contact Us</a>
            <div class="pt-2">
                <a href="{{ route('login') }}" class="w-full py-2.5 text-center block bg-deped-blue text-white font-black border-2 border-slate-900">
                    Learner &amp; Teacher Portal Login
                </a>
            </div>
        </div>
    </header>

    <!-- SECTION 1: HERO (CONSTRUCTIVIST MANIFESTO) -->
    <section id="home" class="relative bg-slate-900 text-white constructivist-grid-dark py-16 sm:py-24 overflow-hidden border-b-8 border-deped-red">
        <!-- Diagonal Accent Ribbon -->
        <div class="absolute -top-12 -right-16 w-80 h-32 diagonal-stripes rotate-12 opacity-80 pointer-events-none hidden md:block"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <!-- Left Constructivist Manifesto -->
                <div class="lg:col-span-8 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-deped-blue text-deped-gold border-2 border-deped-gold text-xs font-black uppercase tracking-widest brutalist-shadow-gold">
                        <span>Official SDO San Juan City ALS Division</span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight text-white leading-none">
                        BUILD KNOWLEDGE.<br/>
                        <span class="text-deped-gold underline decoration-deped-red decoration-8 underline-offset-8">REBUILD YOUR FUTURE.</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-300 font-medium max-w-2xl leading-relaxed">
                        The Alternative Learning System (ALS) provides out-of-school youth and adults (OSYA) in San Juan City with quality, tuition-free modular education, Community Learning Center consultations, and official Department of Education accreditation.
                    </p>

                    <!-- Constructivist Action Buttons -->
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('register') }}" 
                           class="px-8 py-4 bg-deped-gold hover:bg-amber-400 text-slate-950 font-black text-sm uppercase tracking-wider border-4 border-slate-900 brutalist-shadow-blue transition-all hover:translate-x-1 hover:translate-y-1 hover:shadow-none flex items-center gap-2">
                            <span>ALS Online Registration (Form 2)</span>
                            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="#centers" 
                           class="px-6 py-4 bg-slate-800 hover:bg-slate-700 text-white font-bold text-sm uppercase tracking-wider border-2 border-white/30 transition-all flex items-center gap-2">
                            <span>Locate Learning Centers</span>
                            <svg class="w-4 h-4 text-deped-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Right Constructivist Geometric Emblem -->
                <div class="lg:col-span-4 flex justify-center">
                    <div class="relative w-72 sm:w-80 bg-white text-slate-900 border-4 border-slate-950 p-6 brutalist-shadow-gold rotate-1 hover:rotate-0 transition-transform">
                        <div class="border-b-4 border-deped-blue pb-3 mb-4">
                            <span class="text-[10px] font-black text-deped-red uppercase tracking-widest">Republic of the Philippines</span>
                            <div class="text-xl font-black text-slate-900 uppercase">DEPED ALS SAN JUAN</div>
                        </div>

                        <div class="space-y-3 text-xs font-bold text-slate-700">
                            <div class="flex items-center gap-3 p-2 bg-slate-100 border border-slate-300">
                                <span class="w-7 h-7 bg-deped-blue text-white font-black flex items-center justify-center text-xs">01</span>
                                <div>100% FREE &bull; Zero Tuition Fee</div>
                            </div>
                            <div class="flex items-center gap-3 p-2 bg-slate-100 border border-slate-300">
                                <span class="w-7 h-7 bg-deped-red text-white font-black flex items-center justify-center text-xs">02</span>
                                <div>21 Barangays Fully Covered</div>
                            </div>
                            <div class="flex items-center gap-3 p-2 bg-slate-100 border border-slate-300">
                                <span class="w-7 h-7 bg-deped-gold text-slate-950 font-black flex items-center justify-center text-xs">03</span>
                                <div>Offline Mobile LMS &amp; Printed Modules</div>
                            </div>
                            <div class="flex items-center gap-3 p-2 bg-slate-100 border border-slate-300">
                                <span class="w-7 h-7 bg-emerald-600 text-white font-black flex items-center justify-center text-xs">04</span>
                                <div>DepEd Certificate / High School Diploma</div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t-2 border-slate-200 text-center">
                            <span class="text-[10px] text-slate-500 font-bold uppercase">Ready to restart your education?</span>
                            <a href="{{ route('register') }}" class="block mt-1 text-xs font-black text-deped-blue hover:underline uppercase">
                                Fill out ALS Form 2 Now &rarr;
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Telemetry Statistics Strip -->
        <div class="mt-16 border-t-4 border-slate-800 bg-slate-950/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="border-r-0 md:border-r border-white/10">
                        <div class="text-3xl sm:text-4xl font-black text-deped-gold">{{ $totalClcs }}</div>
                        <div class="text-xs uppercase font-bold text-slate-400 mt-0.5">Community Learning Centers</div>
                    </div>
                    <div class="border-r-0 md:border-r border-white/10">
                        <div class="text-3xl sm:text-4xl font-black text-white">{{ $barangayCount }}</div>
                        <div class="text-xs uppercase font-bold text-slate-400 mt-0.5">San Juan Barangays Served</div>
                    </div>
                    <div class="border-r-0 md:border-r border-white/10">
                        <div class="text-3xl sm:text-4xl font-black text-deped-gold">6</div>
                        <div class="text-xs uppercase font-bold text-slate-400 mt-0.5">Core Learning Strands</div>
                    </div>
                    <div>
                        <div class="text-3xl sm:text-4xl font-black text-emerald-400">{{ $totalModules }}</div>
                        <div class="text-xs uppercase font-bold text-slate-400 mt-0.5">Official ALS Modules</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: ABOUT ALS -->
    <section id="about" class="py-20 bg-white constructivist-grid border-b-4 border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between border-b-4 border-slate-900 pb-4 mb-12 gap-4">
                <div>
                    <div class="text-xs font-black text-deped-red uppercase tracking-widest">Section 01 &bull; Mandate &amp; Philosophy</div>
                    <h2 class="text-3xl sm:text-4xl font-black uppercase text-slate-950 tracking-tight">About Alternative Learning System</h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    Republic Act No. 11510 (The ALS Act)
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                <div class="lg:col-span-7 space-y-5 text-sm text-slate-700 leading-relaxed font-medium">
                    <p class="text-base text-slate-900 font-bold border-l-4 border-deped-blue pl-4 py-1">
                        The Alternative Learning System (ALS) is a parallel learning system in the Philippines that provides a practical option to the existing formal instruction.
                    </p>
                    <p>
                        When one does not have or cannot access formal education in school, ALS offers an alternate avenue for learners of all ages. Under the supervision of the <strong>Schools Division Office (SDO) of San Juan City</strong>, mobile teachers and learning facilitators conduct classes in Community Learning Centers (CLCs) located in barangay halls, covered courts, local public schools, and community centers.
                    </p>
                    <p>
                        Rooted in <strong>Constructivist Learning Philosophy</strong>, ALS emphasizes active, hands-on self-learning modules where learners build knowledge from their lived everyday experiences in their communities, progressing at their own pace without rigid classroom scheduling.
                    </p>

                    <!-- Pillar Callout Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                        <div class="p-4 bg-slate-50 border-2 border-slate-900 brutalist-shadow">
                            <span class="text-xs font-black text-deped-blue uppercase tracking-wider block">01 &bull; Flexible Schedule</span>
                            <p class="text-xs text-slate-600 mt-1">Study around your employment, household chores, or family responsibilities with weekend and evening consultation options.</p>
                        </div>
                        <div class="p-4 bg-slate-50 border-2 border-slate-900 brutalist-shadow-gold">
                            <span class="text-xs font-black text-amber-700 uppercase tracking-wider block">02 &bull; No Age Ceiling</span>
                            <p class="text-xs text-slate-600 mt-1">Open to youth aged 12 and above, working adults, senior citizens, and persons deprived of liberty (PDL).</p>
                        </div>
                    </div>
                </div>

                <!-- Right Feature Box -->
                <div class="lg:col-span-5 bg-slate-900 text-white p-8 border-4 border-slate-950 brutalist-shadow-blue space-y-6">
                    <div class="border-b-2 border-white/20 pb-3">
                        <div class="text-xs font-black text-deped-gold uppercase tracking-wider">Why Choose ALS San Juan?</div>
                        <h3 class="text-xl font-black uppercase text-white mt-1">Education Within Your Reach</h3>
                    </div>

                    <ul class="space-y-4 text-xs font-semibold text-slate-300">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-deped-gold shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span><strong>Free DepEd Learning Modules:</strong> Printed modules and offline mobile LMS available at zero cost.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-deped-gold shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span><strong>Dedicated Mobile Teachers:</strong> Certified DepEd ALS educators assigned to each San Juan barangay center.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-deped-gold shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span><strong>Valid Official Diploma:</strong> Pass the A&amp;E assessment to receive an official DepEd diploma recognized for college admission or employment.</span>
                        </li>
                    </ul>

                    <div class="pt-4 border-t border-white/20">
                        <a href="{{ route('register') }}" class="w-full py-3 block text-center bg-deped-blue hover:bg-blue-600 text-white font-black text-xs uppercase tracking-wider border-2 border-white">
                            Start Registration Now &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: A & E PROGRAM (ACCREDITATION & EQUIVALENCY) -->
    <section id="programs" class="py-20 bg-slate-100 border-b-4 border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between border-b-4 border-slate-900 pb-4 mb-12 gap-4">
                <div>
                    <div class="text-xs font-black text-deped-red uppercase tracking-widest">Section 02 &bull; Academic Tracks</div>
                    <h2 class="text-3xl sm:text-4xl font-black uppercase text-slate-950 tracking-tight">Accreditation &amp; Equivalency (A &amp; E)</h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    3 Learning Programs &bull; 6 Core Strands
                </div>
            </div>

            <!-- 3 Core Programs Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                <!-- Program 1: BLP -->
                <div class="bg-white border-4 border-slate-900 p-6 brutalist-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-amber-500 text-slate-950 font-black text-lg flex items-center justify-center border-2 border-slate-900 mb-4">
                            BLP
                        </div>
                        <h3 class="text-lg font-black text-slate-900 uppercase">Basic Literacy Program</h3>
                        <p class="text-xs text-slate-600 font-medium mt-2 leading-relaxed">
                            A foundational program aimed at eradicating illiteracy. Helps non-literate out-of-school youth and adults master basic reading, writing, and arithmetic skills.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t-2 border-slate-100 flex items-center justify-between text-xs font-bold text-slate-500">
                        <span>Foundation Track</span>
                        <span class="text-deped-blue">No age limit</span>
                    </div>
                </div>

                <!-- Program 2: A&E Elementary -->
                <div class="bg-white border-4 border-slate-900 p-6 brutalist-shadow-blue flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-deped-blue text-white font-black text-lg flex items-center justify-center border-2 border-slate-900 mb-4">
                            ELEM
                        </div>
                        <h3 class="text-lg font-black text-slate-900 uppercase">A &amp; E Elementary</h3>
                        <p class="text-xs text-slate-600 font-medium mt-2 leading-relaxed">
                            Provides education comparable to the formal elementary system. Prepares learners to obtain a certificate equivalent to an elementary school diploma.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t-2 border-slate-100 flex items-center justify-between text-xs font-bold text-slate-500">
                        <span>Grade 6 Equivalency</span>
                        <span class="text-deped-blue">Ages 12 &amp; above</span>
                    </div>
                </div>

                <!-- Program 3: A&E Junior High -->
                <div class="bg-white border-4 border-slate-900 p-6 brutalist-shadow-red flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-deped-red text-white font-black text-lg flex items-center justify-center border-2 border-slate-900 mb-4">
                            JHS
                        </div>
                        <h3 class="text-lg font-black text-slate-900 uppercase">A &amp; E Junior High School</h3>
                        <p class="text-xs text-slate-600 font-medium mt-2 leading-relaxed">
                            Provides competencies comparable to formal junior high education. Completers qualify for Senior High School, Technical-Vocational courses, or college.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t-2 border-slate-100 flex items-center justify-between text-xs font-bold text-slate-500">
                        <span>Grade 10 Equivalency</span>
                        <span class="text-deped-blue">Ages 16 &amp; above</span>
                    </div>
                </div>
            </div>

            <!-- The 6 ALS Learning Strands (Constructivist Curriculum) -->
            <div class="bg-slate-900 text-white p-8 sm:p-12 border-4 border-slate-950 brutalist-shadow">
                <div class="border-b-2 border-white/20 pb-4 mb-8">
                    <span class="text-xs font-black text-deped-gold uppercase tracking-widest">Constructivist Curriculum</span>
                    <h3 class="text-2xl font-black uppercase text-white mt-1">The 6 ALS Learning Strands (LS 1–6)</h3>
                    <p class="text-xs text-slate-400 mt-1">Integrated competencies for lifelong learning and 21st-century workplace readiness.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($strands as $st)
                        <div class="p-5 bg-slate-800 border-2 border-white/20 flex flex-col justify-between hover:border-deped-gold transition-colors">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-0.5 font-black text-[10px] text-white uppercase rounded" style="background-color: {{ $st->color_code ?? '#2563eb' }}">
                                        {{ $st->code }}
                                    </span>
                                    <span class="text-[10px] text-deped-gold font-bold">{{ $st->published_modules_count }} Modules</span>
                                </div>
                                <h4 class="text-sm font-black text-white uppercase">{{ $st->name }}</h4>
                                <p class="text-xs text-slate-300 mt-2 line-clamp-3">{{ $st->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION 4: REQUIREMENTS -->
    <section id="requirements" class="py-20 bg-white constructivist-grid border-b-4 border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row md:items-end justify-between border-b-4 border-slate-900 pb-4 mb-12 gap-4">
                <div>
                    <div class="text-xs font-black text-deped-red uppercase tracking-widest">Section 03 &bull; Intake Documents</div>
                    <h2 class="text-3xl sm:text-4xl font-black uppercase text-slate-950 tracking-tight">Enrolment Requirements</h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    Mandatory for ALS Form 2 Validation
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Requirement 1: PSA Birth Cert -->
                <div class="p-6 bg-slate-50 border-4 border-slate-900 brutalist-shadow flex flex-col justify-between">
                    <div>
                        <div class="text-2xl font-black text-deped-blue mb-2 font-mono">01</div>
                        <h3 class="text-base font-black text-slate-900 uppercase">PSA Birth Certificate</h3>
                        <p class="text-xs text-slate-600 font-medium mt-2">
                            Photocopy or digital scan of Philippine Statistics Authority (PSA) Birth Certificate (or LCR copy if PSA unavailable).
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t-2 border-slate-200 text-[10px] font-bold text-emerald-700 uppercase">
                        Mandatory Document
                    </div>
                </div>

                <!-- Requirement 2: Barangay Clearance -->
                <div class="p-6 bg-slate-50 border-4 border-slate-900 brutalist-shadow-blue flex flex-col justify-between">
                    <div>
                        <div class="text-2xl font-black text-deped-blue mb-2 font-mono">02</div>
                        <h3 class="text-base font-black text-slate-900 uppercase">Barangay Clearance</h3>
                        <p class="text-xs text-slate-600 font-medium mt-2">
                            Certificate of Residency from your current San Juan City barangay verifying your local residence.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t-2 border-slate-200 text-[10px] font-bold text-emerald-700 uppercase">
                        Mandatory Document
                    </div>
                </div>

                <!-- Requirement 3: 2x2 Photo -->
                <div class="p-6 bg-slate-50 border-4 border-slate-900 brutalist-shadow-gold flex flex-col justify-between">
                    <div>
                        <div class="text-2xl font-black text-deped-blue mb-2 font-mono">03</div>
                        <h3 class="text-base font-black text-slate-900 uppercase">2x2 ID Photos</h3>
                        <p class="text-xs text-slate-600 font-medium mt-2">
                            2 pieces recent 2x2 colored identification photos with white background and name tag.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t-2 border-slate-200 text-[10px] font-bold text-emerald-700 uppercase">
                        Mandatory Document
                    </div>
                </div>

                <!-- Requirement 4: Form 137 / SF10 -->
                <div class="p-6 bg-slate-50 border-4 border-slate-900 brutalist-shadow-red flex flex-col justify-between">
                    <div>
                        <div class="text-2xl font-black text-deped-blue mb-2 font-mono">04</div>
                        <h3 class="text-base font-black text-slate-900 uppercase">Form 137 / SF10</h3>
                        <p class="text-xs text-slate-600 font-medium mt-2">
                            School permanent record from the last formal elementary or high school attended (if available / for dropouts).
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t-2 border-slate-200 text-[10px] font-bold text-slate-500 uppercase">
                        Optional / If Applicable
                    </div>
                </div>
            </div>

            <!-- Two Ways to Enroll Banner -->
            <div class="mt-12 p-8 bg-amber-50 border-4 border-slate-900 brutalist-shadow flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase">Two Flexible Ways to Enroll:</h3>
                    <p class="text-xs text-slate-700 font-medium mt-1">
                        <strong>Online:</strong> Fill out the digital ALS Form 2 intake wizard and upload your files directly.<br/>
                        <strong>Walk-In:</strong> Visit any San Juan Community Learning Center or Barangay Hall to encode in person with an ALS Mobile Teacher.
                    </p>
                </div>
                <a href="{{ route('register') }}" 
                   class="px-6 py-3 bg-deped-blue hover:bg-deped-dark text-white font-black text-xs uppercase tracking-wider border-2 border-slate-900 brutalist-shadow transition-all shrink-0">
                    Proceed to Digital Registration &rarr;
                </a>
            </div>

        </div>
    </section>

    <!-- SECTION 5: LEARNING CENTERS (SAN JUAN CLC DIRECTORY) -->
    <section id="centers" class="py-20 bg-slate-100 border-b-4 border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row md:items-end justify-between border-b-4 border-slate-900 pb-4 mb-12 gap-4">
                <div>
                    <div class="text-xs font-black text-deped-red uppercase tracking-widest">Section 04 &bull; Community Network</div>
                    <h2 class="text-3xl sm:text-4xl font-black uppercase text-slate-950 tracking-tight">San Juan Learning Centers (CLCs)</h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    12 Active Learning Locations Across San Juan City
                </div>
            </div>

            <!-- CLC Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($clcs as $clc)
                    <div class="bg-white border-4 border-slate-900 p-6 brutalist-shadow flex flex-col justify-between hover:translate-x-0.5 hover:translate-y-0.5 transition-all">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 bg-slate-100 border border-slate-300 font-mono font-bold text-[10px] text-deped-blue uppercase">
                                    {{ $clc->clc_code }}
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 uppercase bg-emerald-50 px-2 py-0.5 border border-emerald-200">
                                    Active Center
                                </span>
                            </div>

                            <h3 class="text-base font-black text-slate-900 uppercase mt-1 leading-snug">
                                {{ $clc->clc_name }}
                            </h3>
                            <div class="text-xs text-deped-red font-bold mt-1">
                                Barangay {{ $clc->barangay }} &bull; {{ $clc->clc_type }}
                            </div>

                            @if($clc->physical_location_notes)
                                <p class="text-xs text-slate-600 mt-3 p-3 bg-slate-50 border border-slate-200">
                                    {{ $clc->physical_location_notes }}
                                </p>
                            @endif
                        </div>

                        <div class="mt-6 pt-4 border-t-2 border-slate-100 flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-400">Assigned Teacher:</span>
                            <span class="text-slate-800">
                                {{ $clc->assignedTeacher ? $clc->assignedTeacher->profile?->first_name . ' ' . $clc->assignedTeacher->profile?->last_name : 'ALS Mobile Teacher Pool' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- SECTION 6: ONLINE REGISTRATION CALL TO ACTION -->
    <section class="py-20 bg-deped-blue text-white constructivist-grid-dark border-b-8 border-deped-gold relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            <span class="inline-block px-3 py-1 bg-deped-gold text-slate-950 font-black text-xs uppercase tracking-widest border-2 border-slate-900 brutalist-shadow">
                Official DepEd ALS Form 2 Digital Intake
            </span>

            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight text-white">
                Start Your Education Journey in San Juan City Today
            </h2>

            <p class="text-base text-blue-100 max-w-2xl mx-auto font-medium">
                Registration is completely free. Complete your learner intake form online in 5 minutes and choose your nearest Community Learning Center.
            </p>

            <div class="pt-4">
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center gap-3 px-10 py-5 bg-deped-gold hover:bg-amber-400 text-slate-950 font-black text-base uppercase tracking-wider border-4 border-slate-900 brutalist-shadow transition-all hover:translate-x-1 hover:translate-y-1 hover:shadow-none">
                    <span>Fill Out ALS Online Registration &rarr;</span>
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION 7: CONTACT US -->
    <section id="contact" class="py-20 bg-white constructivist-grid border-b-4 border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row md:items-end justify-between border-b-4 border-slate-900 pb-4 mb-12 gap-4">
                <div>
                    <div class="text-xs font-black text-deped-red uppercase tracking-widest">Section 05 &bull; Inquiries &amp; Assistance</div>
                    <h2 class="text-3xl sm:text-4xl font-black uppercase text-slate-950 tracking-tight">Contact SDO San Juan City ALS</h2>
                </div>
                <div class="text-xs font-bold text-slate-500 uppercase">
                    Division Office &bull; Pinaglabanan St.
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Contact Details -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="p-6 bg-slate-50 border-4 border-slate-900 brutalist-shadow space-y-4">
                        <div>
                            <div class="text-xs font-black text-deped-blue uppercase">Division Office Location</div>
                            <h3 class="text-base font-black text-slate-900 mt-1">SDO San Juan City</h3>
                            <p class="text-xs text-slate-600 mt-1 font-medium">
                                Schools Division Office Building, Pinaglabanan Street, Barangay Pedro Cruz, San Juan City, Metro Manila
                            </p>
                        </div>

                        <div class="pt-3 border-t-2 border-slate-200">
                            <div class="text-xs font-black text-deped-blue uppercase">ALS Unit Office Hours</div>
                            <p class="text-xs text-slate-700 font-bold mt-1">Monday – Friday: 8:00 AM – 5:00 PM</p>
                            <p class="text-[11px] text-slate-500 font-medium">Weekend CLC consultations vary per center schedule.</p>
                        </div>

                        <div class="pt-3 border-t-2 border-slate-200">
                            <div class="text-xs font-black text-deped-blue uppercase">Direct Assistance Hotlines</div>
                            <p class="text-xs text-slate-800 font-mono font-bold mt-1">Mobile Teacher Hotline: 0917-123-4567</p>
                            <p class="text-xs text-slate-800 font-mono font-bold">Email: als.sanjuan@deped.gov.ph</p>
                        </div>
                    </div>
                </div>

                <!-- Inquiry Form -->
                <div class="lg:col-span-7 bg-white p-6 sm:p-8 border-4 border-slate-900 brutalist-shadow-blue">
                    <h3 class="text-lg font-black text-slate-900 uppercase border-b-2 border-slate-200 pb-3 mb-4">
                        Send a Message / Ask a Teacher
                    </h3>

                    <form onsubmit="alert('Thank you for your message! An ALS Mobile Teacher from SDO San Juan City will contact you shortly.'); return false;" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Your Name *</label>
                                <input type="text" required placeholder="Full Name" class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border-2 border-slate-900 text-xs font-semibold focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Mobile Number *</label>
                                <input type="text" required placeholder="0917XXXXXXX" class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border-2 border-slate-900 text-xs font-semibold focus:bg-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Barangay in San Juan City</label>
                            <select class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border-2 border-slate-900 text-xs font-semibold focus:bg-white">
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
                            <label class="block text-xs font-bold text-slate-700 uppercase">Message / Questions</label>
                            <textarea rows="3" required placeholder="Tell us about your educational background or which learning center you would like to attend..." class="mt-1 w-full px-3.5 py-2.5 bg-slate-50 border-2 border-slate-900 text-xs font-semibold focus:bg-white"></textarea>
                        </div>

                        <button type="submit" 
                                class="w-full py-3.5 bg-deped-blue hover:bg-deped-dark text-white font-black text-xs uppercase tracking-wider border-2 border-slate-900 brutalist-shadow transition-all">
                            Submit Inquiry to ALS Mobile Teacher
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </section>

    <!-- CONSTRUCTIVIST FOOTER -->
    <footer class="bg-slate-950 text-white py-12 border-t-8 border-deped-blue">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <img src="{{ file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('images/school_logo.svg') }}" 
                         alt="SDO San Juan" class="w-12 h-12 bg-white p-1 border border-white/30">
                    <img src="{{ file_exists(public_path('images/als_logo.png')) ? asset('images/als_logo.png') : asset('images/als_logo.svg') }}" 
                         alt="ALS Logo" class="w-12 h-12 bg-white p-1 border border-white/30">
                    <div class="leading-tight pl-2">
                        <div class="text-xs font-black uppercase text-deped-gold">SDO San Juan City &bull; Alternative Learning System</div>
                        <div class="text-[11px] text-slate-400">Department of Education &bull; National Capital Region</div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 text-xs font-bold text-slate-400 uppercase">
                    <a href="#home" class="hover:text-white">Home</a>
                    <a href="#about" class="hover:text-white">About ALS</a>
                    <a href="{{ route('register') }}" class="text-deped-gold hover:underline">Register</a>
                    <a href="#programs" class="hover:text-white">Programs</a>
                    <a href="#centers" class="hover:text-white">Centers</a>
                    <a href="{{ route('login') }}" class="text-white hover:text-deped-gold">Portal Sign In</a>
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
