@extends('layouts.app')

@section('title', 'Sign In - SDO San Juan City ALS')

@section('body')
<div class="relative isolate min-h-[100dvh] overflow-x-hidden bg-slate-950 text-slate-100">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-deped-blue/25 blur-3xl"></div>
        <div class="absolute -bottom-44 -right-36 h-[30rem] w-[30rem] rounded-full bg-amber-500/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.08]" style="background-image: linear-gradient(rgba(255,255,255,.16) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.16) 1px, transparent 1px); background-size: 32px 32px;"></div>
    </div>

    <main class="relative mx-auto flex min-h-[100dvh] w-full max-w-6xl flex-col px-3 py-5 sm:px-6 sm:py-8 lg:px-8">
        <a href="{{ route('home') }}" class="inline-flex min-h-10 w-fit items-center gap-2 rounded-xl px-2 text-sm font-semibold text-slate-300 transition hover:bg-white/5 hover:text-white focus:outline-none focus:ring-2 focus:ring-deped-gold/70">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18"/>
            </svg>
            Back to ALS home
        </a>

        <div class="my-auto py-6 sm:py-8">
            <div class="grid overflow-hidden rounded-3xl border border-white/10 bg-white shadow-2xl shadow-black/30 lg:grid-cols-2">
                <!-- Portal introduction -->
                <section class="relative overflow-hidden bg-gradient-to-br from-deped-navy via-slate-900 to-slate-950 p-6 sm:p-9 lg:p-12">
                    <div aria-hidden="true" class="absolute -right-16 -top-16 h-56 w-56 rounded-full border border-white/10"></div>
                    <div aria-hidden="true" class="absolute -right-8 -top-8 h-40 w-40 rounded-full border border-white/10"></div>

                    <div class="relative">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white p-1.5 shadow-lg overflow-hidden">
                                <img src="{{ file_exists(public_path('images/sdosj.svg')) ? asset('images/sdosj.svg') : (file_exists(public_path('images/school_logo.png')) ? asset('images/school_logo.png') : asset('images/school_logo.svg')) }}" alt="SDO San Juan City logo" width="44" height="44" class="h-full w-full object-contain max-h-full max-w-full">
                            </div>
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white p-1.5 shadow-lg overflow-hidden">
                                <img src="{{ file_exists(public_path('images/als_logo.png')) ? asset('images/als_logo.png') : asset('images/als_logo.svg') }}" alt="DepEd ALS logo" width="44" height="44" class="h-full w-full object-contain max-h-full max-w-full">
                            </div>
                            <div class="min-w-0 leading-tight">
                                <span class="block text-[10px] font-black uppercase tracking-[0.14em] text-deped-gold">SDO San Juan City</span>
                                <span class="mt-1 block text-xs font-bold text-white sm:text-sm">Alternative Learning System</span>
                            </div>
                        </div>

                        <div class="mt-8 sm:mt-12">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-200 sm:text-xs">
                                <span class="h-2 w-2 rounded-full bg-deped-gold"></span>
                                Official learner and teacher portal
                            </span>
                            <h1 class="mt-5 text-3xl font-black leading-tight tracking-tight text-white sm:text-4xl">
                                Your learning journey, all in one place.
                            </h1>
                            <p class="mt-4 max-w-md text-sm leading-relaxed text-slate-300 sm:text-base">
                                Sign in to continue your ALS learning, check enrollment updates, or manage division services.
                            </p>
                        </div>

                        <div class="mt-7 grid gap-3 text-sm sm:mt-9">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-400/15 text-emerald-300" aria-hidden="true">✓</span>
                                <span class="text-slate-200">Access learning modules and track your progress</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-400/15 text-emerald-300" aria-hidden="true">✓</span>
                                <span class="text-slate-200">Review enrollment and document status</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-400/15 text-emerald-300" aria-hidden="true">✓</span>
                                <span class="text-slate-200">Continue with support from your ALS team</span>
                            </div>
                        </div>

                        <p class="mt-8 border-t border-white/10 pt-5 text-xs leading-relaxed text-slate-400 sm:mt-10">
                            Alternative Learning System &bull; Schools Division Office of San Juan City
                        </p>
                    </div>
                </section>

                <!-- Sign-in form -->
                <section class="bg-white p-5 text-slate-900 sm:p-8 lg:p-12" aria-labelledby="login-heading">
                    <div class="mx-auto w-full max-w-md">
                        <div class="mb-6 sm:mb-8">
                            <p class="text-xs font-black uppercase tracking-[0.16em] text-deped-blue">Welcome back</p>
                            <h2 id="login-heading" class="mt-2 text-2xl font-black tracking-tight text-slate-950 sm:text-3xl">Sign in to your portal</h2>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">Sign in using your registered email address or username.</p>
                        </div>

                        @if(session('error'))
                            <div role="alert" class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm text-rose-800">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        @if(session('success'))
                            <div role="status" class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm text-emerald-800">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        @if($errors->any())
                            <div role="alert" class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm text-rose-800">
                                <p class="font-bold">Please check the sign-in details and try again.</p>
                                <ul class="mt-1 list-inside list-disc">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST" class="space-y-5" id="loginForm">
                            @csrf
                            <div>
                                <label for="login" class="block text-sm font-bold text-slate-700">Email Address <span class="text-xs font-normal text-slate-500">(or username)</span></label>
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                                        </svg>
                                    </div>
                                    <input id="login" name="login" type="text" autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false" required value="{{ old('login') }}" placeholder="e.g. learner.blp@deped.gov.ph"
                                           class="min-h-12 w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-4 text-base text-slate-900 placeholder:text-slate-400 transition focus:border-deped-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-deped-blue/10 sm:text-sm">
                                </div>
                            </div>

                            <div x-data="{ showPassword: false }">
                                <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                                <div class="relative mt-2">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input id="password" name="password" type="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required placeholder="Enter your password"
                                           class="min-h-12 w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-20 text-base text-slate-900 placeholder:text-slate-400 transition focus:border-deped-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-deped-blue/10 sm:text-sm">
                                    <button type="button" @click="showPassword = !showPassword" :aria-pressed="showPassword.toString()" class="absolute inset-y-0 right-0 rounded-r-xl px-3 text-xs font-bold text-deped-blue hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-deped-blue" aria-label="Toggle password visibility">
                                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 pt-1 sm:flex-row sm:items-center sm:justify-between">
                                <label for="remember" class="inline-flex min-h-10 cursor-pointer items-center gap-2.5 text-sm text-slate-600">
                                    <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-deped-blue focus:ring-deped-blue">
                                    <span>Remember this session</span>
                                </label>
                                <a href="{{ route('register') }}" class="inline-flex min-h-10 items-center text-sm font-bold text-deped-blue hover:underline">Need an account?</a>
                            </div>

                            <button type="submit" class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-deped-blue px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-deped-blue/20 transition hover:bg-deped-dark focus:outline-none focus:ring-4 focus:ring-deped-blue/20 active:translate-y-px">
                                <span>Sign in to portal</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </form>

                        <details class="mt-6 rounded-2xl border border-slate-200 bg-slate-50/90 p-4 transition">
                            <summary class="cursor-pointer select-none text-xs font-black uppercase tracking-wider text-slate-700 marker:text-deped-blue">
                                Quick Demo Sign-In Credentials (By Email)
                            </summary>
                            <p class="mt-2 text-xs leading-relaxed text-slate-500">
                                Click any role or track below to auto-fill verified email sign-in credentials:
                            </p>

                            <!-- Track Learners -->
                            <!-- Track Learners -->
                            <div class="mt-3">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Enrolled ALS Learners by Program Track</p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <!-- BLP -->
                                    <button type="button" onclick="fillCredentials('learner.blp@deped.gov.ph', 'Password123!')"
                                            class="group flex items-center justify-between gap-2 rounded-xl border border-amber-200 bg-amber-50/70 p-2.5 text-left transition hover:border-amber-400 hover:bg-amber-100/80 focus:outline-none focus:ring-2 focus:ring-amber-500/40">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="inline-flex items-center rounded-md bg-amber-600 px-1.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">BLP</span>
                                                <span class="text-xs font-bold text-slate-800">Bayani Panaligan</span>
                                            </div>
                                            <span class="mt-0.5 block truncate text-[11px] font-medium text-slate-600 group-hover:text-amber-900">learner.blp@deped.gov.ph</span>
                                            <span class="text-[10px] text-amber-700 font-medium">Batis CLC &bull; Enrolled</span>
                                        </div>
                                    </button>

                                    <!-- Elementary -->
                                    <button type="button" onclick="fillCredentials('learner.elem@deped.gov.ph', 'Password123!')"
                                            class="group flex items-center justify-between gap-2 rounded-xl border border-blue-200 bg-blue-50/70 p-2.5 text-left transition hover:border-blue-400 hover:bg-blue-100/80 focus:outline-none focus:ring-2 focus:ring-blue-500/40">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="inline-flex items-center rounded-md bg-blue-600 px-1.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">Elem</span>
                                                <span class="text-xs font-bold text-slate-800">Elena Ramos</span>
                                            </div>
                                            <span class="mt-0.5 block truncate text-[11px] font-medium text-slate-600 group-hover:text-blue-900">learner.elem@deped.gov.ph</span>
                                            <span class="text-[10px] text-blue-700 font-medium">Addition Hills &bull; Enrolled</span>
                                        </div>
                                    </button>

                                    <!-- Junior High -->
                                    <button type="button" onclick="fillCredentials('learner.jhs@deped.gov.ph', 'Password123!')"
                                            class="group flex items-center justify-between gap-2 rounded-xl border border-indigo-200 bg-indigo-50/70 p-2.5 text-left transition hover:border-indigo-400 hover:bg-indigo-100/80 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="inline-flex items-center rounded-md bg-indigo-600 px-1.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">JHS</span>
                                                <span class="text-xs font-bold text-slate-800">Jun Santos</span>
                                            </div>
                                            <span class="mt-0.5 block truncate text-[11px] font-medium text-slate-600 group-hover:text-indigo-900">learner.jhs@deped.gov.ph</span>
                                            <span class="text-[10px] text-indigo-700 font-medium">Pedro Cruz &bull; Enrolled</span>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Staff & Administrators -->
                            <div class="mt-3 pt-3 border-t border-slate-200">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Instructional &amp; Administrative Staff</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <!-- Mobile Teacher -->
                                    <button type="button" onclick="fillCredentials('juan.delacruz@deped.gov.ph', 'Password123!')"
                                            class="group flex items-center justify-between gap-2 rounded-xl border border-emerald-200 bg-emerald-50/70 p-2.5 text-left transition hover:border-emerald-400 hover:bg-emerald-100/80 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="inline-flex items-center rounded-md bg-emerald-600 px-1.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">Teacher</span>
                                                <span class="text-xs font-bold text-slate-800">Teacher Juan Dela Cruz</span>
                                            </div>
                                            <span class="mt-0.5 block truncate text-[11px] font-medium text-slate-600 group-hover:text-emerald-900">juan.delacruz@deped.gov.ph</span>
                                            <span class="text-[10px] text-emerald-700 font-medium">West Crame CLC &bull; Active Teacher</span>
                                        </div>
                                    </button>

                                    <!-- Division Admin -->
                                    <button type="button" onclick="fillCredentials('admin.als@deped.gov.ph', 'Password123!')"
                                            class="group flex items-center justify-between gap-2 rounded-xl border border-deped-blue/20 bg-blue-50/70 p-2.5 text-left transition hover:border-deped-blue hover:bg-blue-100/80 focus:outline-none focus:ring-2 focus:ring-deped-blue/40">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5">
                                                <span class="inline-flex items-center rounded-md bg-deped-blue px-1.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">Admin</span>
                                                <span class="text-xs font-bold text-slate-800">Maria Elena Cruz Santos</span>
                                            </div>
                                            <span class="mt-0.5 block truncate text-[11px] font-medium text-slate-600 group-hover:text-deped-blue">admin.als@deped.gov.ph</span>
                                            <span class="text-[10px] text-deped-blue font-medium">Division Office &bull; Active Admin</span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </details>

                        <div class="mt-6 rounded-xl border border-emerald-100 bg-emerald-50/70 p-4">
                            <p class="text-sm font-bold text-slate-800">New to ALS in San Juan City?</p>
                            <p class="mt-1 text-xs leading-relaxed text-slate-600">Start your learner enrollment online using ALS Form 2.</p>
                            <a href="{{ route('register') }}" class="mt-3 inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm font-bold text-emerald-800 transition hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-600/30">
                                Begin online enrollment
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>

                        <div class="mt-3 rounded-xl border border-blue-100 bg-blue-50/70 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold text-slate-800">DepEd ALS Mobile Teacher?</p>
                                    <p class="text-[11px] text-slate-600">Register your teacher account online.</p>
                                </div>
                                <a href="{{ route('teacher.register') }}" class="inline-flex min-h-9 items-center justify-center rounded-lg border border-deped-blue/30 bg-white px-3 py-1.5 text-xs font-bold text-deped-blue hover:bg-deped-blue hover:text-white transition shadow-xs">
                                    Register as Teacher &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <p class="pb-2 text-center text-[11px] leading-relaxed text-slate-500">
            &copy; 2026 SDO San Juan City &bull; Alternative Learning System
        </p>
    </main>
</div>

<script>
function fillCredentials(user, pass) {
    document.getElementById('login').value = user;
    document.getElementById('password').value = pass;
    document.getElementById('password').focus();
}
</script>
@endsection
