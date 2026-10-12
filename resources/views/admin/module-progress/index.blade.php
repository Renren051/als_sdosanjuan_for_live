@extends('layouts.admin')

@section('title', 'Learner Module Progress - SDO San Juan City ALS')
@section('page_title', 'Learner Module Progress')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm text-slate-500">Reading activity and completed mini-games synced from the mobile app.</p>
            <p class="mt-1 text-xs font-medium text-slate-400">{{ $isTeacher ? 'Showing learners assigned to you or your CLCs.' : 'Showing division-wide learner activity.' }}</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-deped-blue hover:underline">Back to Dashboard</a>
    </div>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
        @foreach([
            ['Learners with activity', $summary['learners'], 'bg-blue-50 text-blue-700'],
            ['Tracked modules', $summary['modules'], 'bg-slate-100 text-slate-700'],
            ['Completed', $summary['completed'], 'bg-emerald-50 text-emerald-700'],
            ['In progress', $summary['in_progress'], 'bg-amber-50 text-amber-700'],
            ['Downloaded', $summary['downloaded'], 'bg-indigo-50 text-indigo-700'],
        ] as [$label, $value, $color])
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-extrabold {{ $color }}">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.module-progress.index') }}" class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row">
        <label class="flex-1">
            <span class="sr-only">Search learner or module</span>
            <input type="search" name="q" value="{{ $search }}" placeholder="Search learner or module" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-deped-blue focus:outline-none focus:ring-2 focus:ring-deped-blue/10">
        </label>
        <label>
            <span class="sr-only">Progress status</span>
            <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 sm:w-48">
                <option value="">All progress</option>
                <option value="downloaded" @selected($status === 'downloaded')>Downloaded</option>
                <option value="in_progress" @selected($status === 'in_progress')>In progress</option>
                <option value="completed" @selected($status === 'completed')>Completed</option>
            </select>
        </label>
        <button type="submit" class="rounded-xl bg-deped-blue px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800">Apply</button>
        @if($search !== '' || $status !== '')
            <a href="{{ route('admin.module-progress.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50">Clear</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="font-bold text-slate-900">Module activity</h2>
                <p class="text-xs text-slate-500">{{ $progressRows->total() }} records · refreshed when learner devices sync online</p>
            </div>
        </div>

        @forelse($progressRows as $progress)
            @php($attempt = $latestAttempts->get($progress->user_id . ':' . $progress->module_id))
            <article class="grid gap-3 border-b border-slate-100 p-5 last:border-b-0 md:grid-cols-[minmax(0,1.2fr)_minmax(0,1.5fr)_minmax(180px,1fr)] md:items-center">
                <div class="min-w-0">
                    <p class="truncate font-bold text-slate-900">{{ $progress->user?->full_name ?? 'Learner' }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $progress->user?->username }}</p>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-800">{{ $progress->module?->title ?? 'Module unavailable' }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-deped-blue" style="width: {{ $progress->progress_percent }}%"></div>
                        </div>
                        <span class="w-10 text-right text-xs font-bold text-slate-600">{{ $progress->progress_percent }}%</span>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Page {{ $progress->last_page_read ?? 0 }} of {{ $progress->total_pages ?: '?' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 md:justify-end">
                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $progress->completion_status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($progress->completion_status === 'in_progress' ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $progress->completion_status)) }}
                    </span>
                    @if($attempt)
                        <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-bold text-violet-700">Game {{ $attempt->score }}/{{ $attempt->max_score }}</span>
                    @endif
                    <span class="w-full text-right text-[11px] text-slate-400">Synced {{ $progress->synced_at?->diffForHumans() ?? '—' }}</span>
                </div>
            </article>
        @empty
            <div class="px-6 py-14 text-center">
                <p class="font-bold text-slate-700">No module activity found</p>
                <p class="mt-1 text-sm text-slate-500">Progress appears after learners open modules and their devices sync online.</p>
            </div>
        @endforelse

        @if($progressRows->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $progressRows->links() }}</div>
        @endif
    </div>
</div>
@endsection
