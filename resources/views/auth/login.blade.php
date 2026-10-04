@extends('layouts.app')

@section('title', 'Sign In - SDO San Juan City ALS')

@section('body')
<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-900 via-deped-navy to-slate-950 text-slate-100 relative overflow-hidden">
    <!-- Background Ambient Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-deped-blue/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center relative z-10 px-4">
        <!-- Logo Badge -->
        <div class="mx-auto w-16 h-16 rounded-2xl bg-gradient-to-tr from-deped-blue to-blue-600 p-0.5 shadow-xl shadow-deped-blue/30 flex items-center justify-center">
            <div class="w-full h-full bg-slate-950/40 rounded-[14px] flex items-center justify-center backdrop-blur-sm">
                <svg class="w-9 h-9 text-deped-gold" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2zm0 8.27L4.74 7 12 3.73 19.26 7 12 10.27zM3 10.05v5.86L12 21l9-5.09v-5.86L12 15l-9-4.95z"/>
                </svg>
            </div>
        </div>

        <h2 class="mt-4 text-2xl font-black tracking-tight text-white sm:text-3xl">
            SDO San Juan City
        </h2>
        <p class="text-xs uppercase tracking-widest font-semibold text-deped-gold mt-1">
            Alternative Learning System (ALS)
        </p>
        <p class="text-sm text-slate-400 mt-1">
            Student Information & Modular LMS Portal
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg relative z-10 px-4">
        <div class="bg-white/95 backdrop-blur-xl py-8 px-6 sm:px-10 shadow-2xl rounded-3xl border border-white/20 text-slate-900">
            <!-- Flash Alert -->
            @if(session('error'))
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5" id="loginForm">
                @csrf
                <div>
                    <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Username or Email
                    </label>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input id="login" name="login" type="text" autocomplete="username" required 
                               value="{{ old('login') }}"
                               placeholder="e.g. admin_als or learner_mark" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-deped-blue focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Password
                        </label>
                    </div>
                    <div class="mt-1.5 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               placeholder="••••••••••••" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-deped-blue focus:bg-white transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-deped-blue rounded border-slate-300 focus:ring-deped-blue">
                        <span class="text-xs text-slate-600 font-medium">Remember session</span>
                    </label>
                    <a href="{{ route('register') }}" class="text-xs font-semibold text-deped-blue hover:underline">
                        Need an account?
                    </a>
                </div>

                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-sm tracking-wide shadow-lg shadow-deped-blue/30 hover:shadow-deped-blue/50 transition-all flex items-center justify-center gap-2">
                    <span>Sign In to Portal</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            <!-- Quick Demo Credentials Switcher -->
            <div class="mt-6 pt-5 border-t border-slate-200">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2.5 text-center">
                    Quick Demo Credentials (One-Click Fill)
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" 
                            onclick="fillCredentials('admin_als', 'Password123!')"
                            class="px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-deped-blue hover:text-white border border-slate-200 text-xs font-semibold text-slate-700 transition-all text-center">
                        Admin
                    </button>
                    <button type="button" 
                            onclick="fillCredentials('teacher_juan', 'Password123!')"
                            class="px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-600 hover:text-white border border-slate-200 text-xs font-semibold text-slate-700 transition-all text-center">
                        Teacher
                    </button>
                    <button type="button" 
                            onclick="fillCredentials('learner_mark', 'Password123!')"
                            class="px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-amber-600 hover:text-white border border-slate-200 text-xs font-semibold text-slate-700 transition-all text-center">
                        Learner
                    </button>
                </div>
            </div>

            <!-- Online Enrollment CTA -->
            <div class="mt-6 text-center">
                <p class="text-xs text-slate-500">
                    Are you an Out-of-School Youth or Adult (OSYA)?
                </p>
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center gap-1.5 mt-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-xl border border-emerald-200 transition-all">
                    <span>Complete Online ALS Form 2 Enrolment &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function fillCredentials(user, pass) {
    document.getElementById('login').value = user;
    document.getElementById('password').value = pass;
}
</script>
@endsection
