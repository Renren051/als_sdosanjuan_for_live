@extends('layouts.admin')

@section('title', 'CLC & Allocation Manager - SDO San Juan City ALS')
@section('page_title', 'Community Learning Centers & Allocation Manager')

@section('content')
<div class="space-y-6" x-data="{ addClcModal: false, editModal: false, activeClc: {} }">

    <!-- Top Action & Overview -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-slate-900">San Juan City Learning Center Network</h2>
            <p class="text-xs text-slate-500">Manned community centers mapping out-of-school youth across all 21 barangays</p>
        </div>
        <button type="button" @click="addClcModal = true" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md shadow-deped-blue/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Create New Learning Center</span>
        </button>
    </div>

    <!-- CLC Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($clcs as $clc)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex items-start justify-between gap-2">
                        <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-deped-blue font-mono font-bold text-[10px] uppercase">
                            {{ $clc->clc_code }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $clc->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($clc->status) }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mt-2">{{ $clc->clc_name }}</h3>
                    <div class="text-xs text-slate-500 flex items-center gap-1.5 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span>Brgy. {{ $clc->barangay }} &bull; {{ $clc->clc_type }}</span>
                    </div>

                    @if($clc->physical_location_notes)
                        <p class="text-xs text-slate-600 mt-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            {{ $clc->physical_location_notes }}
                        </p>
                    @endif

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400 font-medium">Assigned Mobile Teacher:</span>
                        <span class="font-bold text-slate-800">
                            {{ $clc->assignedTeacher ? $clc->assignedTeacher->profile?->first_name . ' ' . $clc->assignedTeacher->profile?->last_name : 'Unassigned' }}
                        </span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 font-black text-xs border border-emerald-200">
                            {{ $clc->active_learners }} Enrolled
                        </span>
                        @if($clc->pending_learners > 0)
                            <span class="px-2 py-1 rounded-xl bg-amber-50 text-amber-700 font-bold text-[10px] border border-amber-200">
                                {{ $clc->pending_learners }} Pending
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('admin.enrollments.index', ['clc_id' => $clc->id]) }}" 
                       class="text-xs font-bold text-deped-blue hover:underline">
                        View Learners &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Create CLC Modal -->
    <div x-show="addClcModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="addClcModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Add New Community Learning Center</h3>
                    <button @click="addClcModal = false" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
                </div>

                <form action="{{ route('admin.clcs.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">CLC Name *</label>
                        <input type="text" name="clc_name" required placeholder="e.g. Tibagan Covered Court CLC" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">CLC Code *</label>
                            <input type="text" name="clc_code" required placeholder="CLC-SJ-TIB-01" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono uppercase">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase">Facility Type *</label>
                            <select name="clc_type" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                                <option value="Barangay Hall">Barangay Hall</option>
                                <option value="Public School">Public School</option>
                                <option value="Community Center">Community Center</option>
                                <option value="Church / Faith-Based">Church / Faith-Based</option>
                                <option value="BJMP / Jail Facility">BJMP / Jail Facility</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">San Juan Barangay *</label>
                        <select name="barangay" required class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                            @foreach($barangays as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Assigned Mobile Teacher</label>
                        <select name="assigned_teacher_id" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                            <option value="">Unassigned Pool</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Location Notes / Landmarks</label>
                        <textarea name="physical_location_notes" rows="2" placeholder="e.g. 2nd Floor, Daycare Room, near Barangay Health Center" class="mt-1 w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="addClcModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-deped-blue hover:bg-deped-dark rounded-xl shadow-md">Create Learning Center</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
