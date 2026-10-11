@extends('layouts.admin')

@section('title', 'Hero Banner Manager - SDO San Juan City ALS')
@section('page_title', 'Homepage Hero Banners')

@section('content')
<div class="space-y-6" x-data="bannerManager()">

    @if(session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('status') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold uppercase">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1 shadow-sm">
            <div class="font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please correct the following errors:</span>
            </div>
            <ul class="list-disc list-inside text-xs pl-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Action & Upload Trigger -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-[10px] font-bold text-deped-blue uppercase tracking-wider mb-1">
                <span>Kingster-Style Hero Banner Carousel</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900">Homepage Hero Photos &amp; Framing</h2>
            <p class="text-xs text-slate-500">Upload and reposition photos to choose the exact framing shown on the homepage.</p>
        </div>
        <button type="button" @click="openUploadModal()" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md shadow-deped-blue/20 transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Upload New Banner Photo</span>
        </button>
    </div>

    <!-- Banner List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($banners as $banner)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow group">
                <div>
                    <!-- Banner Photo Preview -->
                    <div class="relative h-48 bg-slate-900 overflow-hidden">
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" style="object-position: {{ $banner->position_x ?? 50 }}% {{ $banner->position_y ?? 50 }}%;" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold backdrop-blur-md {{ $banner->is_active ? 'bg-emerald-500/90 text-white' : 'bg-slate-800/80 text-slate-300' }}">
                                {{ $banner->is_active ? '● Active (In Rotation)' : '○ Inactive' }}
                            </span>
                        </div>

                        <!-- Sequence Badge -->
                        <div class="absolute bottom-3 left-3 px-2.5 py-1 rounded-lg bg-black/60 backdrop-blur-md text-white font-mono text-xs font-bold">
                            Slide #{{ $banner->order_seq }}
                        </div>

                        <!-- Quick Hover Reposition Overlay Button -->
                        <button type="button" 
                                @click="openCropperForDirectBanner({{ json_encode($banner) }})"
                                class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white gap-2 font-bold text-xs cursor-pointer">
                            <span class="p-2.5 rounded-full bg-white/20 border border-white/30 text-white shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            </span>
                            <span>Click to Reposition Photo</span>
                        </button>
                    </div>

                    <!-- Banner Content Details -->
                    <div class="p-5 space-y-2.5">
                        <div class="text-[10px] font-bold text-deped-blue uppercase tracking-wider">
                            {{ $banner->subtitle ?: 'SDO San Juan City' }}
                        </div>
                        <h3 class="text-base font-bold text-slate-900 leading-snug">
                            {{ $banner->title }}
                        </h3>
                        <div class="pt-2 flex items-center gap-2 text-xs text-slate-500">
                            <span class="font-semibold text-slate-700">Button:</span>
                            <span class="px-2 py-0.5 rounded bg-slate-100 font-mono text-[11px] text-slate-700 border border-slate-200">
                                {{ $banner->button_text }} &rarr; {{ $banner->button_url }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between gap-2">
                    <!-- Toggle Visibility -->
                    <form action="{{ route('admin.banners.toggle', $banner->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $banner->is_active ? 'bg-slate-200 text-slate-700 hover:bg-slate-300' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' }}">
                            {{ $banner->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <div class="flex items-center gap-1.5">
                        <!-- Dedicated Reposition Action Button -->
                        <button type="button" 
                                @click="openCropperForDirectBanner({{ json_encode($banner) }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 text-deped-blue hover:bg-blue-100 border border-blue-200/80 text-xs font-bold transition-colors"
                                title="Reposition photo view">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            <span>Reposition</span>
                        </button>

                        <!-- Edit Details Button -->
                        <button type="button" 
                                @click="openEditModal({{ json_encode($banner) }})"
                                class="p-2 rounded-lg text-slate-600 hover:bg-white hover:text-deped-blue hover:shadow-sm transition-all"
                                title="Edit Banner Details & Replace Photo">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this banner?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Delete Banner">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-slate-200">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <div class="text-base font-bold text-slate-800">No Hero Banners Found</div>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Upload a high-resolution photo to showcase on the top banner of the public homepage.</p>
                <button @click="openUploadModal()" class="mt-4 px-4 py-2 rounded-xl bg-deped-blue text-white text-xs font-bold">Upload First Banner</button>
            </div>
        @endforelse
    </div>

    <!-- =====================================================         MODAL 1: UPLOAD NEW HERO BANNER MODAL
         ======================================================== -->
    <div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 sm:p-8 space-y-5 my-auto max-h-[92vh] overflow-y-auto" @click.away="uploadModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Upload New Hero Banner</h3>
                    <p class="text-xs text-slate-500">Upload a high-resolution photo for the top banner carousel</p>
                </div>
                <button type="button" @click="uploadModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
            </div>

            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Image File Input -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Banner Image (JPG, PNG, WebP) *</label>
                    <input type="file" name="image" required accept="image/jpeg,image/png,image/webp,image/jpg"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-deped-blue file:text-white hover:file:bg-deped-dark file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50">
                    <p class="text-[11px] text-slate-400">Upload your original high-resolution photo. You can reposition its framing anytime using the Reposition tool.</p>
                </div>

                <!-- Headline / Title -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Headline / Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Alternative Learning System"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-deped-blue focus:bg-white font-medium">
                </div>

                <!-- Subtitle -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Top Subtitle</label>
                    <input type="text" name="subtitle" placeholder="e.g. SDO San Juan City • Division ALS Network"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-deped-blue focus:bg-white">
                </div>

                <!-- Button Settings -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Button Text</label>
                        <input type="text" name="button_text" value="Take A Tour" placeholder="e.g. Take A Tour"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Button Link URL</label>
                        <input type="text" name="button_url" value="/register" placeholder="e.g. /register"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    </div>
                </div>

                <!-- Sequence Order & Status -->
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Sequence Order</label>
                        <input type="number" name="order_seq" min="1" value="{{ count($banners) + 1 }}"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono">
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-deped-blue focus:ring-deped-blue">
                            <span>Active in Carousel</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" @click="uploadModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md shadow-deped-blue/20">Upload Banner</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================
         MODAL 2: EDIT HERO BANNER MODAL
         ======================================================== -->
    <div x-show="editModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 sm:p-8 space-y-5 my-auto max-h-[92vh] overflow-y-auto" @click.away="editModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Edit Hero Banner</h3>
                    <p class="text-xs text-slate-500">Update slide text, button link, or replace photo</p>
                </div>
                <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
            </div>

            <form :action="'{{ url('/admin/banners') }}/' + activeBanner.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Replace / Reposition Image Section -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Banner Photo</label>
                    
                    <!-- Current Photo & Reposition Trigger -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-16 h-9 rounded-lg overflow-hidden bg-slate-900 border border-slate-300 shrink-0">
                                <img :src="activeBanner.image_url" :style="'object-position: ' + (activeBanner.position_x || 50) + '% ' + (activeBanner.position_y || 50) + '%;'" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-800 truncate" x-text="activeBanner.title || 'Current Photo'"></div>
                                <div class="text-[11px] text-slate-500" x-text="`Framing: ${activeBanner.position_y || 50}% Vertical`"></div>
                            </div>
                        </div>
                        <button type="button" 
                                @click="editModal = false; openCropperForDirectBanner(activeBanner)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-deped-blue text-white font-bold text-xs hover:bg-deped-dark shadow-sm transition-all shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            <span>Reposition Photo</span>
                        </button>
                    </div>

                    <!-- Replace with New File Input -->
                    <label class="block text-xs font-semibold text-slate-600">Replace image file (optional):</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/jpg"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-deped-blue file:text-white hover:file:bg-deped-dark file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50">
                </div>

                <!-- Headline / Title -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Headline / Title *</label>
                    <input type="text" name="title" required x-model="activeBanner.title"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-deped-blue focus:bg-white font-medium">
                </div>

                <!-- Subtitle -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Top Subtitle</label>
                    <input type="text" name="subtitle" x-model="activeBanner.subtitle"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-deped-blue focus:bg-white">
                </div>

                <!-- Button Settings -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Button Text</label>
                        <input type="text" name="button_text" x-model="activeBanner.button_text"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Button Link URL</label>
                        <input type="text" name="button_url" x-model="activeBanner.button_url"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs">
                    </div>
                </div>

                <!-- Sequence Order & Status -->
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Sequence Order</label>
                        <input type="number" name="order_seq" min="1" x-model="activeBanner.order_seq"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono">
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                            <input type="checkbox" name="is_active" value="1" :checked="activeBanner.is_active" class="rounded border-slate-300 text-deped-blue focus:ring-deped-blue">
                            <span>Active in Carousel</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-deped-blue hover:bg-deped-dark text-white font-bold text-xs shadow-md shadow-deped-blue/20">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================
         MODAL 3: REPOSITION HERO BANNER PHOTO MODAL
         Non-destructive framing: Adjusts focal point with CSS object-position
         ======================================================== -->
    <div x-show="cropper.isOpen" 
         class="fixed inset-0 z-[60] overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-5" 
         style="display: none;" 
         x-cloak>
        
        <div class="relative w-full max-w-5xl bg-white rounded-3xl border border-slate-200/90 shadow-2xl flex flex-col max-h-[94vh] overflow-hidden my-auto" 
             @click.away="cropper.cancel()">
            
            <!-- Sticky Header -->
            <div class="shrink-0 px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white z-10">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-deped-blue/10 text-deped-blue flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">
                            Reposition Hero Banner Photo
                        </h3>
                        <p class="text-xs text-slate-500">Drag photo to adjust framing &bull; 1:1 identical to homepage viewing size</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- Aspect Ratio Badge -->
                    <div class="inline-flex items-center px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700">
                        <span>1920 &times; 800 HD (2.4:1)</span>
                    </div>

                    <button type="button" @click="cropper.cancel()" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none p-1.5 rounded-lg hover:bg-slate-100 transition-colors">&times;</button>
                </div>
            </div>

            <!-- Scrollable Body -->
            <div class="flex-1 overflow-y-auto px-5 sm:px-6 py-4 space-y-3.5">
                
                <!-- Viewport Container (Matching 1920x800 aspect ratio exactly) -->
                <div id="bannerCropperViewport"
                     class="relative w-full rounded-2xl overflow-hidden bg-slate-950 select-none touch-none border border-slate-800 shadow-inner group"
                     style="aspect-ratio: 1920 / 800;"
                     @pointerdown.prevent="cropper.startDrag($event)"
                     @pointermove.window="cropper.onDrag($event)"
                     @pointerup.window="cropper.stopDrag($event)"
                     @wheel.prevent="cropper.onWheel($event)"
                     :class="cropper.isDragging ? 'cursor-grabbing' : 'cursor-grab'">

                    <!-- Image Layer: Displayed via CSS object-position (No destructive crop, original preserved) -->
                    <template x-if="cropper.imageUrl">
                        <img :src="cropper.imageUrl"
                             class="w-full h-full object-cover pointer-events-none select-none transition-none will-change-[object-position]"
                             :style="'object-position: ' + cropper.posX + '% ' + cropper.posY + '%;'" />
                    </template>

                    <!-- Instruction Overlay Pill (as in Image 2) -->
                    <div class="absolute top-3.5 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full bg-black/75 backdrop-blur-md text-white font-semibold text-xs flex items-center gap-2 pointer-events-none shadow-lg border border-white/15 transition-opacity duration-200"
                         :class="cropper.isDragging ? 'opacity-30' : 'opacity-95'">
                        <span class="text-amber-400">✋</span>
                        <span>Drag photo to reposition &bull; Scroll wheel to adjust</span>
                    </div>

                    <!-- Live Headline Simulation Overlay on Viewport: Matching Homepage exactly -->
                    <div x-show="cropper.showOverlayText" 
                         class="absolute inset-0 bg-gradient-to-r from-[#040e24]/95 via-[#07193f]/80 via-40% to-transparent flex items-center p-6 sm:p-10 md:p-12 pointer-events-none transition-opacity duration-300"
                         :class="cropper.isDragging ? 'opacity-35' : 'opacity-100'">
                        <div class="max-w-md space-y-2 pointer-events-none">
                            <!-- Yellow Eyebrow Subtitle -->
                            <div class="text-[11px] sm:text-xs font-extrabold text-amber-400 uppercase tracking-widest drop-shadow-sm" 
                                 x-text="(cropper.targetBanner && cropper.targetBanner.subtitle) ? cropper.targetBanner.subtitle : 'THE BEST PARALLEL LEARNING SYSTEM'"></div>
                            
                            <!-- Bold White Headline -->
                            <div class="text-xl sm:text-2xl md:text-3xl font-serif font-bold text-white leading-tight drop-shadow-lg" 
                                 x-text="(cropper.targetBanner && cropper.targetBanner.title) ? cropper.targetBanner.title : 'Alternative Learning System San Juan'"></div>
                            
                            <!-- Royal Blue Button -->
                            <div class="pt-2">
                                <span class="inline-flex items-center gap-1 px-4 py-2 rounded-xl bg-deped-blue text-xs font-bold text-white shadow-md shadow-deped-blue/30" 
                                      x-text="(cropper.targetBanner && cropper.targetBanner.button_text) ? cropper.targetBanner.button_text + ' →' : 'Take A Tour →'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- 1920x800 Resolution & Coordinates Badge (like Image 2) -->
                    <div class="absolute bottom-3 right-3 flex items-center gap-2 pointer-events-none select-none">
                        <div class="px-2.5 py-1 rounded-lg bg-black/70 backdrop-blur-md text-slate-300 font-mono text-[11px] font-bold border border-white/10 shadow-md">
                            <span x-text="`Vertical: ${cropper.posY}% | Horizontal: ${cropper.posX}%`"></span>
                        </div>
                        <div class="px-2.5 py-1 rounded-lg bg-black/85 backdrop-blur-md text-emerald-400 font-mono text-[11px] font-bold border border-emerald-500/20 shadow-md">
                            1920 &times; 800 HD
                        </div>
                    </div>
                </div>

                <!-- Reposition Controls Bar -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
                    
                    <!-- Vertical Position Slider -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                            <span class="uppercase">Vertical Framing</span>
                            <span class="font-mono text-deped-blue font-bold" x-text="cropper.posY === 0 ? 'Top (0%)' : (cropper.posY === 50 ? 'Center (50%)' : (cropper.posY === 100 ? 'Bottom (100%)' : cropper.posY + '%'))"></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[11px] font-semibold text-slate-400 shrink-0">Top (0%)</span>
                            <input type="range" 
                                   min="0" 
                                   max="100" 
                                   step="1" 
                                   x-model.number="cropper.posY"
                                   class="w-full accent-deped-blue cursor-pointer h-2 bg-slate-200 rounded-lg">
                            <span class="text-[11px] font-semibold text-slate-400 shrink-0">Bottom (100%)</span>
                        </div>
                    </div>

                    <!-- Preset Positions & Tools -->
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-200/60">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-xs font-semibold text-slate-500 mr-1">Quick Alignment:</span>
                            
                            <button type="button" 
                                    @click="cropper.setPos(50, 0)"
                                    :class="cropper.posY === 0 ? 'bg-deped-blue text-white' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-300'"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors">
                                Top
                            </button>

                            <button type="button" 
                                    @click="cropper.setPos(50, 25)"
                                    :class="cropper.posY === 25 ? 'bg-deped-blue text-white' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-300'"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors"
                                    title="Upper framing (ideal for faces / stage)">
                                Upper (Faces)
                            </button>

                            <button type="button" 
                                    @click="cropper.setPos(50, 50)"
                                    :class="cropper.posY === 50 ? 'bg-deped-blue text-white' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-300'"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors">
                                Center
                            </button>

                            <button type="button" 
                                    @click="cropper.setPos(50, 75)"
                                    :class="cropper.posY === 75 ? 'bg-deped-blue text-white' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-300'"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors">
                                Lower
                            </button>

                            <button type="button" 
                                    @click="cropper.setPos(50, 100)"
                                    :class="cropper.posY === 100 ? 'bg-deped-blue text-white' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-300'"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-colors">
                                Bottom
                            </button>
                        </div>

                        <!-- Toggle Headline Overlay Preview -->
                        <button type="button" 
                                @click="cropper.showOverlayText = !cropper.showOverlayText"
                                :class="cropper.showOverlayText ? 'bg-blue-50 text-deped-blue border-blue-200' : 'bg-white text-slate-600 border-slate-300'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span x-text="cropper.showOverlayText ? 'Hide Title Preview' : 'Show Title Preview'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sticky Footer -->
            <div class="shrink-0 px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between z-10">
                <button type="button" 
                        @click="cropper.cancel()" 
                        :disabled="cropper.isSaving"
                        class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                    Cancel
                </button>
                
                <button type="button" 
                        @click="cropper.saveReposition()" 
                        :disabled="cropper.isSaving"
                        :class="cropper.isSaving ? 'opacity-50 cursor-not-allowed' : 'hover:bg-deped-dark'"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-deped-blue text-white font-bold text-xs shadow-md shadow-deped-blue/25 transition-all">
                    <template x-if="cropper.isSaving">
                        <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </template>
                    <template x-if="!cropper.isSaving">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <span x-text="cropper.isSaving ? 'Saving Position...' : 'Save Reposition'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Dedicated Global Reposition Form -->
    <form id="globalRepositionForm" method="POST" action="" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="position_x" id="globalRepositionPosX">
        <input type="hidden" name="position_y" id="globalRepositionPosY">
    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bannerManager', () => ({
        uploadModal: false,
        editModal: false,
        activeBanner: {},

        // Reposition state machine (Non-destructive, CSS object-position)
        cropper: {
            isOpen: false,
            isSaving: false,
            imageUrl: '',
            aspectRatio: 1920 / 800,
            aspectRatioName: '2.4:1',
            posX: 50,
            posY: 50,
            isDragging: false,
            dragStartX: 0,
            dragStartY: 0,
            initialPosX: 50,
            initialPosY: 50,
            showOverlayText: true,
            targetBanner: null,
            targetBannerId: null,

            open(imageUrl, posX = 50, posY = 50, banner = null) {
                this.isOpen = true;
                this.isSaving = false;
                this.imageUrl = imageUrl;
                this.targetBanner = banner;
                this.targetBannerId = banner ? banner.id : null;
                this.posX = typeof posX === 'number' ? posX : 50;
                this.posY = typeof posY === 'number' ? posY : 50;
                this.aspectRatio = 1920 / 800;
                this.showOverlayText = true;
            },

            cancel() {
                this.isOpen = false;
            },

            setAspectRatio(ratio, name) {
                this.aspectRatio = ratio;
                this.aspectRatioName = name;
            },

            setPos(x, y) {
                this.posX = Math.max(0, Math.min(100, x));
                this.posY = Math.max(0, Math.min(100, y));
            },

            startDrag(e) {
                if (e.button !== undefined && e.button !== 0) return; // Only primary mouse button
                if (e.preventDefault) e.preventDefault();
                this.isDragging = true;
                
                const clientX = (e.clientX !== undefined) ? e.clientX : (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
                const clientY = (e.clientY !== undefined) ? e.clientY : (e.touches && e.touches[0] ? e.touches[0].clientY : 0);

                this.dragStartX = clientX;
                this.dragStartY = clientY;
                this.initialPosX = this.posX;
                this.initialPosY = this.posY;

                if (e.pointerId && e.currentTarget && e.currentTarget.setPointerCapture) {
                    try {
                        e.currentTarget.setPointerCapture(e.pointerId);
                    } catch (_) {}
                }
            },

            onDrag(e) {
                if (!this.isDragging) return;
                if (e.preventDefault) e.preventDefault();

                const clientX = (e.clientX !== undefined) ? e.clientX : (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
                const clientY = (e.clientY !== undefined) ? e.clientY : (e.touches && e.touches[0] ? e.touches[0].clientY : 0);

                const deltaX = clientX - this.dragStartX;
                const deltaY = clientY - this.dragStartY;

                const vp = document.getElementById('bannerCropperViewport');
                const vpHeight = (vp && vp.clientHeight) ? vp.clientHeight : 350;
                const vpWidth = (vp && vp.clientWidth) ? vp.clientWidth : 840;

                // Dragging UP pulls bottom into view (increases posY)
                // Dragging DOWN pulls top into view (decreases posY)
                const factorY = (deltaY / vpHeight) * 100;
                const factorX = (deltaX / vpWidth) * 100;

                this.posY = Math.max(0, Math.min(100, Math.round(this.initialPosY - factorY)));
                this.posX = Math.max(0, Math.min(100, Math.round(this.initialPosX - factorX)));
            },

            stopDrag(e) {
                if (!this.isDragging) return;
                this.isDragging = false;
                if (e && e.pointerId && e.currentTarget && e.currentTarget.releasePointerCapture) {
                    try {
                        e.currentTarget.releasePointerCapture(e.pointerId);
                    } catch (_) {}
                }
            },

            onWheel(e) {
                if (e.preventDefault) e.preventDefault();
                // Scrolling down shifts lower, scrolling up shifts higher
                const delta = Math.sign(e.deltaY) * 3;
                this.posY = Math.max(0, Math.min(100, this.posY + delta));
            },

            saveReposition() {
                if (this.isSaving) return;

                if (!this.targetBannerId) {
                    alert('No banner selected for repositioning.');
                    return;
                }

                this.isSaving = true;

                const form = document.getElementById('globalRepositionForm');
                const inputX = document.getElementById('globalRepositionPosX');
                const inputY = document.getElementById('globalRepositionPosY');

                if (!form || !inputX || !inputY) {
                    this.isSaving = false;
                    alert('Reposition form not found.');
                    return;
                }

                form.action = '{{ url('/admin/banners') }}/' + this.targetBannerId + '/reposition';
                inputX.value = this.posX;
                inputY.value = this.posY;
                form.submit();
            }
        },

        openUploadModal() {
            this.uploadModal = true;
        },

        openEditModal(banner) {
            this.activeBanner = banner;
            this.editModal = true;
        },

        openCropperForDirectBanner(banner) {
            const url = banner.image_url || ('/' + banner.image_path);
            const posX = banner.position_x !== null && banner.position_x !== undefined ? banner.position_x : 50;
            const posY = banner.position_y !== null && banner.position_y !== undefined ? banner.position_y : 50;
            this.cropper.open(url, posX, posY, banner);
        }
    }));
});
</script>
@endpush
