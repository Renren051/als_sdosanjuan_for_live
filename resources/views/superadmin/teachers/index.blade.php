@extends('layouts.admin')

@section('title', 'Mobile Teacher Management | Super Admin SDO San Juan City')

@section('content')
<div x-data="{
    createModalOpen: false,
    editModalOpen: false,
    resetModalOpen: false,
    currentTeacher: {
        id: '',
        first_name: '',
        middle_name: '',
        last_name: '',
        name_extension: '',
        username: '',
        email: '',
        contact_number: '',
        gender: 'male',
        civil_status: 'single',
        highest_education_attained: '',
        status: 'active',
        clc_ids: []
    },
    resetTeacher: {
        id: '',
        name: ''
    },
    openEdit(teacher, assignedClcIds) {
        this.currentTeacher = {
            id: teacher.id,
            first_name: teacher.profile ? teacher.profile.first_name : '',
            middle_name: teacher.profile ? (teacher.profile.middle_name || '') : '',
            last_name: teacher.profile ? teacher.profile.last_name : '',
            name_extension: teacher.profile ? (teacher.profile.name_extension || '') : '',
            username: teacher.username,
            email: teacher.email,
            contact_number: teacher.profile ? (teacher.profile.contact_number || '') : '',
            gender: teacher.profile ? (teacher.profile.gender || 'male') : 'male',
            civil_status: teacher.profile ? (teacher.profile.civil_status || 'single') : 'single',
            highest_education_attained: teacher.profile ? (teacher.profile.highest_education_attained || '') : '',
            status: teacher.status,
            clc_ids: assignedClcIds
        };
        this.editModalOpen = true;
    },
    openReset(id, name) {
        this.resetTeacher = { id: id, name: name };
        this.resetModalOpen = true;
    }
}" class="p-4 sm:p-6 lg:p-8 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Super Admin Restricted Access</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Mobile Teacher Management</h1>
            <p class="text-sm text-slate-500">Create, configure, assign, and manage official ALS Mobile Teacher accounts across all San Juan barangays.</p>
        </div>
        <div>
            <button @click="createModalOpen = true" 
                    type="button" 
                    class="inline-flex items-center gap-2 px-5 py-3 bg-deped-blue hover:bg-deped-navy text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition-all hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Add New Mobile Teacher</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
        <div class="font-bold">Please correct the following errors:</div>
        <ul class="list-disc list-inside text-xs space-y-0.5">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Operational Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-deped-blue border border-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Teachers</div>
                <div class="text-2xl font-black text-slate-900">{{ $totalTeachers }}</div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Teachers</div>
                <div class="text-2xl font-black text-emerald-600">{{ $activeTeachers }}</div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Inactive Accounts</div>
                <div class="text-2xl font-black text-slate-700">{{ $inactiveTeachers }}</div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Assigned CLCs</div>
                <div class="text-2xl font-black text-amber-600">{{ $assignedClcsCount }}</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('superadmin.teachers.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-5">
                <label for="search" class="block text-xs font-bold text-slate-700 uppercase mb-1">Search Teacher</label>
                <div class="relative">
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search by name, username, email..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue focus:border-deped-blue">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="sm:col-span-3">
                <label for="status" class="block text-xs font-bold text-slate-700 uppercase mb-1">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue focus:border-deped-blue">
                    <option value="">All Statuses</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <label for="clc_id" class="block text-xs font-bold text-slate-700 uppercase mb-1">Assigned Center</label>
                <select id="clc_id" name="clc_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue focus:border-deped-blue">
                    <option value="">All CLCs</option>
                    @foreach($allClcs as $c)
                    <option value="{{ $c->id }}" {{ (string)$clcFilter === (string)$c->id ? 'selected' : '' }}>
                        {{ $c->clc_name }} ({{ $c->barangay }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-1 flex gap-2">
                <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl text-sm transition-all shadow-sm">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Mobile Teachers Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-black uppercase tracking-wider text-slate-600">
                        <th class="py-4 px-6">Teacher Profile</th>
                        <th class="py-4 px-6">Username &amp; Email</th>
                        <th class="py-4 px-6">Assigned CLCs</th>
                        <th class="py-4 px-6 text-center">Active Learners</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($teachers as $teacher)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        
                        <!-- Teacher Profile -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-deped-blue/10 text-deped-blue border border-deped-blue/20 font-black text-sm flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($teacher->profile?->first_name ?? $teacher->username, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 leading-snug">
                                        {{ $teacher->full_name }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $teacher->profile?->contact_number ?: 'No phone recorded' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Username & Email -->
                        <td class="py-4 px-6">
                            <div class="font-mono text-xs font-semibold text-slate-800">{{ $teacher->username }}</div>
                            <div class="text-xs text-slate-500">{{ $teacher->email }}</div>
                        </td>

                        <!-- Assigned CLCs -->
                        <td class="py-4 px-6">
                            @if($teacher->assignedClcs->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5 max-w-xs">
                                    @foreach($teacher->assignedClcs as $clc)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-deped-blue border border-blue-200">
                                        {{ $clc->clc_name }}
                                    </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-slate-400 italic">No CLC assigned yet</span>
                            @endif
                        </td>

                        <!-- Active Learners -->
                        <td class="py-4 px-6 text-center">
                            @php
                                $learnerCount = $teacher->assignedLearners()->whereIn('enrollment_status', ['enrolled', 'under_review'])->count();
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $learnerCount > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                {{ $learnerCount }} learners
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-4 px-6 text-center">
                            @if($teacher->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Active</span>
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                <span>Inactive</span>
                            </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-6 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                
                                <!-- Edit Button -->
                                @php
                                    $clcIds = $teacher->assignedClcs->pluck('id')->values()->all();
                                @endphp
                                <button type="button" 
                                        @click="openEdit({{ json_encode($teacher) }}, {{ json_encode($clcIds) }})"
                                        class="p-2 text-slate-600 hover:text-deped-blue hover:bg-slate-100 rounded-lg transition-colors"
                                        title="Edit Information">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                <!-- Reset Password Button -->
                                <button type="button" 
                                        @click="openReset({{ $teacher->id }}, '{{ addslashes($teacher->full_name) }}')"
                                        class="p-2 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                        title="Reset Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </button>

                                <!-- Toggle Status Form -->
                                <form action="{{ route('superadmin.teachers.toggleStatus', $teacher->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="p-2 {{ $teacher->status === 'active' ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-lg transition-colors"
                                            title="{{ $teacher->status === 'active' ? 'Deactivate Teacher' : 'Activate Teacher' }}"
                                            onclick="return confirm('Are you sure you want to change this teacher account status?')">
                                        @if($teacher->status === 'active')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>

                                <!-- Delete Form -->
                                <form action="{{ route('superadmin.teachers.destroy', $teacher->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors"
                                            title="Delete Teacher"
                                            onclick="return confirm('Are you sure you want to permanently delete this Mobile Teacher account? This action cannot be undone.')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            <div class="max-w-sm mx-auto space-y-3">
                                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                </div>
                                <div class="font-bold text-slate-700">No Mobile Teachers Found</div>
                                <p class="text-xs text-slate-500">No teacher accounts matched your current search filters or none have been created yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $teachers->links() }}
        </div>
        @endif
    </div>

    <!-- ============================================================ -->
    <!-- MODAL 1: CREATE NEW MOBILE TEACHER -->
    <!-- ============================================================ -->
    <div x-show="createModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="createModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="createModalOpen = false"></div>

            <div x-show="createModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
                
                <form action="{{ route('superadmin.teachers.store') }}" method="POST">
                    @csrf
                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Create Mobile Teacher Account</h3>
                                <p class="text-xs text-slate-500">Provide official identity and system credentials for the new teacher.</p>
                            </div>
                            <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
                        </div>

                        <!-- Personal Details -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">First Name *</label>
                                <input type="text" name="first_name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Middle Name</label>
                                <input type="text" name="middle_name" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Last Name *</label>
                                <input type="text" name="last_name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                        </div>

                        <!-- Credentials -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username *</label>
                                <input type="text" name="username" required placeholder="e.g. teacher_santos" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-deped-blue">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Official Email *</label>
                                <input type="email" name="email" required placeholder="e.g. santos@deped.gov.ph" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                        </div>

                        <!-- Contact & Status -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mobile Contact</label>
                                <input type="text" name="contact_number" placeholder="0917XXXXXXX" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Initial Password *</label>
                                <input type="password" name="password" required minlength="8" placeholder="Min. 8 characters" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Initial Status</label>
                                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- CLC Assignment Multi-Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Assign to Learning Centers (CLCs)</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto p-3 bg-slate-50 rounded-xl border border-slate-200">
                                @foreach($allClcs as $c)
                                <label class="flex items-center gap-2 text-xs text-slate-700 hover:text-slate-900 cursor-pointer">
                                    <input type="checkbox" name="clc_ids[]" value="{{ $c->id }}" class="rounded text-deped-blue focus:ring-deped-blue">
                                    <span class="truncate">{{ $c->clc_name }} ({{ $c->barangay }})</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="createModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-deped-blue hover:bg-deped-navy text-white font-bold text-sm rounded-xl shadow-md transition-all">
                            Save Teacher Account
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL 2: EDIT MOBILE TEACHER -->
    <!-- ============================================================ -->
    <div x-show="editModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="editModalOpen = false"></div>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
                <form :action="'{{ url('superadmin/teachers') }}/' + currentTeacher.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Edit Mobile Teacher</h3>
                                <p class="text-xs text-slate-500">Update teacher identity, contact details, and assigned CLCs.</p>
                            </div>
                            <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
                        </div>

                        <!-- Personal Details -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">First Name *</label>
                                <input type="text" name="first_name" x-model="currentTeacher.first_name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Middle Name</label>
                                <input type="text" name="middle_name" x-model="currentTeacher.middle_name" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Last Name *</label>
                                <input type="text" name="last_name" x-model="currentTeacher.last_name" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                        </div>

                        <!-- Credentials -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username *</label>
                                <input type="text" name="username" x-model="currentTeacher.username" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-deped-blue">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email *</label>
                                <input type="email" name="email" x-model="currentTeacher.email" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                        </div>

                        <!-- Contact & Status -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mobile Contact</label>
                                <input type="text" name="contact_number" x-model="currentTeacher.contact_number" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Account Status</label>
                                <select name="status" x-model="currentTeacher.status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-deped-blue">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- CLC Assignment Multi-Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Assigned Learning Centers (CLCs)</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto p-3 bg-slate-50 rounded-xl border border-slate-200">
                                @foreach($allClcs as $c)
                                <label class="flex items-center gap-2 text-xs text-slate-700 hover:text-slate-900 cursor-pointer">
                                    <input type="checkbox" name="clc_ids[]" value="{{ $c->id }}" :checked="currentTeacher.clc_ids.includes({{ $c->id }})" class="rounded text-deped-blue focus:ring-deped-blue">
                                    <span class="truncate">{{ $c->clc_name }} ({{ $c->barangay }})</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="editModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-deped-blue hover:bg-deped-navy text-white font-bold text-sm rounded-xl shadow-md transition-all">
                            Update Teacher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL 3: RESET PASSWORD -->
    <!-- ============================================================ -->
    <div x-show="resetModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="resetModalOpen = false"></div>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <form :action="'{{ url('superadmin/teachers') }}/' + resetTeacher.id + '/reset-password'" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="text-lg font-bold text-slate-900">Reset Teacher Password</h3>
                            <button type="button" @click="resetModalOpen = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">&times;</button>
                        </div>

                        <p class="text-xs text-slate-600">
                            Resetting password for: <strong class="text-slate-900" x-text="resetTeacher.name"></strong>
                        </p>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">New Password *</label>
                            <input type="password" name="new_password" required minlength="8" placeholder="Enter new password (min. 8 characters)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="p-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="resetModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-md transition-all">
                            Confirm Password Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
