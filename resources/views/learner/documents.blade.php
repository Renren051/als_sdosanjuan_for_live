@extends('layouts.learner')

@section('title', 'My Documents & Credentials - SDO San Juan City ALS')
@section('page_title', 'My Documents')

@section('content')
<div class="space-y-6 pt-4" x-data="{ uploadModal: false, activeDocType: '', activeDocTitle: '' }">

    <!-- Header -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-deped-blue/10 text-deped-blue text-[11px] font-bold uppercase tracking-wider mb-2">
                <span>DepEd ALS Credentials &bull; Staff Review Verification</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Credential Documents & Verification</h1>
            <p class="text-xs text-slate-500 mt-1">
                Upload clear scans or photos of your required official documents for Mobile Teacher verification.
            </p>
        </div>
        <div class="text-xs text-slate-400">
            Accepted: <strong class="text-slate-700">PDF, JPG, PNG</strong> (Max 10MB per file)
        </div>
    </div>

    <!-- Required Documents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($requiredDocs as $type => $info)
            @php
                $doc = $documents->get($type);
            @endphp
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-2xl {{ $doc ? ($doc->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-700' : ($doc->verification_status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700')) : 'bg-slate-100 text-slate-400' }} flex items-center justify-center font-bold shrink-0">
                                @if($doc && $doc->verification_status === 'verified')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @elseif($doc && $doc->verification_status === 'rejected')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">{{ $info['title'] }}</h3>
                                <div class="text-[10px] uppercase font-bold {{ $info['required'] ? 'text-amber-700' : 'text-slate-400' }}">
                                    {{ $info['required'] ? 'Mandatory Requirement' : 'Optional Credential' }}
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($doc)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $doc->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : ($doc->verification_status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ ucfirst($doc->verification_status) }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                    Not Uploaded
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 mt-3">
                        {{ $info['description'] }}
                    </p>

                    @if($doc)
                        <div class="mt-4 p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="font-medium truncate max-w-[200px]" title="{{ $doc->file_name }}">{{ $doc->file_name }}</span>
                                <span class="font-mono text-[10px] text-slate-400">{{ $doc->formatted_file_size }}</span>
                            </div>

                            @if($doc->verification_status === 'rejected' && $doc->verification_notes)
                                <div class="mt-2.5 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                                    <strong class="block text-[11px] uppercase font-bold">Correction Note from Reviewer:</strong>
                                    {{ $doc->verification_notes }}
                                </div>
                            @elseif($doc->verification_status === 'verified' && $doc->reviewer)
                                <div class="mt-2 text-[11px] text-emerald-700 font-medium">
                                    Verified by {{ $doc->reviewer->full_name }}
                                    @if($doc->reviewed_at)
                                        <span class="text-slate-400">on {{ $doc->reviewed_at->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            @elseif($doc->verification_status === 'pending')
                                <div class="mt-2 text-[11px] text-amber-700 font-medium">
                                    Queued for Mobile Teacher verification
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                    @if($doc)
                        <a href="{{ route('learner.documents.file', $doc->id) }}" target="_blank" 
                           class="text-xs font-bold text-deped-blue hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>View Document</span>
                        </a>
                    @else
                        <span></span>
                    @endif

                    <button type="button" 
                            @click="activeDocType = '{{ $type }}'; activeDocTitle = '{{ $info['title'] }}'; uploadModal = true"
                            class="px-4 py-2 rounded-xl {{ $doc ? ($doc->verification_status === 'rejected' ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700') : 'bg-deped-blue hover:bg-deped-dark text-white shadow-sm' }} font-bold text-xs transition-colors">
                        {{ $doc ? ($doc->verification_status === 'rejected' ? 'Re-upload Corrected File' : 'Replace File') : 'Upload Document' }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Upload Modal -->
    <div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="uploadModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900" x-text="'Upload: ' + activeDocTitle"></h3>
                    <button @click="uploadModal = false" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
                </div>

                <form action="{{ route('learner.documents.upload') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="document_type" :value="activeDocType">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Select File (PDF, JPG, PNG)</label>
                        <p class="text-[11px] text-slate-500 mb-2">Ensure text, dry seal, and signatures are legible.</p>
                        <input type="file" name="document_file" required accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-deped-blue/10 file:text-deped-blue hover:file:bg-deped-blue/20">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="uploadModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-deped-blue hover:bg-deped-dark rounded-xl shadow-md">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
