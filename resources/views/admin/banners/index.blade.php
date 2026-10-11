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
            <p class="text-xs text-slate-500">Upload photos with Facebook-style drag &amp; zoom repositioning to choose the exact view shown on the homepage.</p>
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
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        
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
                            <span>Click to Reposition / Crop Photo</span>
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
                                title="Reposition photo view (Facebook Style)">
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

    <!-- ========================================================
         MODAL 1: UPLOAD NEW HERO BANNER MODAL
         ======================================================== -->
    <div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 sm:p-8 space-y-5 my-auto max-h-[92vh] overflow-y-auto" @click.away="uploadModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Upload New Hero Banner</h3>
                    <p class="text-xs text-slate-500">Pick an image and frame it with Facebook-style repositioning</p>
                </div>
                <button type="button" @click="uploadModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
            </div>

            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <!-- Cropped Base64 Payload Input -->
                <input type="hidden" name="cropped_image" :value="uploadCroppedImage">

                <!-- Image File Input with Auto-Cropper Trigger -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Banner Image (JPG, PNG, WebP) *</label>
                    
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/jpg"
                           @change="onUploadFileSelected($event)"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-deped-blue file:text-white hover:file:bg-deped-dark file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50">
                    
                    <!-- Cropped Preview Badge -->
                    <template x-if="uploadCroppedImage">
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-8 rounded-lg overflow-hidden border border-emerald-300 bg-black shrink-0">
                                    <img :src="uploadCroppedImage" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Custom Framing Applied (1920×800)</span>
                                    </div>
                                    <div class="text-[11px] text-emerald-600">Framed like Facebook cover photo</div>
                                </div>
                            </div>
                            <button type="button" @click="reopenUploadCrop()" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 text-white font-bold text-[11px] hover:bg-emerald-700 transition-colors">
                                Adjust Framing
                            </button>
                        </div>
                    </template>

                    <p class="text-[11px] text-slate-400">Selecting a photo will open the Facebook-style reposition tool so you can choose the exact framing.</p>
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
         MODAL 2: EDIT HERO BANNER MODAL (From User Screenshot)
         ======================================================== -->
    <div x-show="editModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 sm:p-8 space-y-5 my-auto max-h-[92vh] overflow-y-auto" @click.away="editModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Edit Hero Banner</h3>
                    <p class="text-xs text-slate-500">Update slide text, button link, or crop / replace photo</p>
                </div>
                <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
            </div>

            <form :action="'{{ url('/admin/banners') }}/' + activeBanner.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                
                <!-- Cropped Base64 Payload Input -->
                <input type="hidden" name="cropped_image" :value="editCroppedImage">

                <!-- Replace / Reposition Image Section -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase">Replace Image (Leave blank to keep existing)</label>
                    
                    <!-- Reposition Current Photo Trigger -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-16 h-9 rounded-lg overflow-hidden bg-slate-900 border border-slate-300 shrink-0">
                                <img :src="editCroppedImage || activeBanner.image_url" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-800 truncate" x-text="editCroppedImage ? 'Custom Framing Ready' : 'Current Active Photo'"></div>
                                <div class="text-[11px] text-slate-500" x-text="editCroppedImage ? 'New crop applied' : '1920×800 format'"></div>
                            </div>
                        </div>
                        <button type="button" 
                                @click="openCropperForEditExisting()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-deped-blue text-white font-bold text-xs hover:bg-deped-dark shadow-sm transition-all shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            <span>Crop / Reposition</span>
                        </button>
                    </div>

                    <!-- Replace with New File Input -->
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/jpg"
                           @change="onEditFileSelected($event)"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-deped-blue file:text-white hover:file:bg-deped-dark file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50">
                    <p class="text-[11px] text-slate-400">Selecting a new file will automatically launch the Facebook-style reposition tool.</p>
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
         MODAL 3: FACEBOOK COVER PHOTO REPOSITION & CROP MODAL
         Fixed layout: Pinned Header & Footer, zero overflow bugs
         ======================================================== -->
    <div x-show="cropper.isOpen" 
         class="fixed inset-0 z-[60] overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-5" 
         style="display: none;" 
         x-cloak>
        
        <div class="relative w-full max-w-4xl bg-white rounded-3xl border border-slate-200/90 shadow-2xl flex flex-col max-h-[92vh] overflow-hidden my-auto" 
             @click.away="cropper.cancel()">
            
            <!-- Sticky Header (Always 100% visible) -->
            <div class="shrink-0 px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white z-10">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-deped-blue/10 text-deped-blue flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>Reposition Hero Banner Photo</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-100 text-deped-blue font-bold uppercase tracking-wider">Facebook Style</span>
                        </h3>
                        <p class="text-xs text-slate-500">Drag to reposition photo • Scroll or use slider to zoom</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- Aspect Ratio Switcher -->
                    <div class="inline-flex items-center p-0.5 bg-slate-100 rounded-xl border border-slate-200 text-[11px] font-bold">
                        <button type="button" 
                                @click="cropper.setAspectRatio(1920/800, '2.4:1')"
                                :class="cropper.aspectRatio === 1920/800 ? 'bg-white text-deped-blue shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                class="px-2.5 py-1 rounded-lg transition-all">
                            2.4:1 (Hero Banner)
                        </button>
                        <button type="button" 
                                @click="cropper.setAspectRatio(16/9, '16:9')"
                                :class="cropper.aspectRatio === 16/9 ? 'bg-white text-deped-blue shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                class="px-2.5 py-1 rounded-lg transition-all">
                            16:9 (Landscape)
                        </button>
                    </div>

                    <button type="button" @click="cropper.cancel()" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none p-1.5 rounded-lg hover:bg-slate-100 transition-colors">&times;</button>
                </div>
            </div>

            <!-- Scrollable Body -->
            <div class="flex-1 overflow-y-auto px-5 sm:px-6 py-4 space-y-3.5">
                
                <!-- Viewport Container -->
                <div id="bannerCropperViewport"
                     class="relative w-full rounded-2xl overflow-hidden bg-slate-950 select-none touch-none border border-slate-800 shadow-inner group max-h-[360px]"
                     x-ref="viewportBox"
                     :style="'aspect-ratio: ' + cropper.aspectRatio + ';'"
                     @pointerdown="cropper.startDrag($event)"
                     @pointermove="cropper.onDrag($event)"
                     @pointerup="cropper.stopDrag()"
                     @pointercancel="cropper.stopDrag()"
                     @wheel.prevent="cropper.onWheel($event)"
                     :class="cropper.isDragging ? 'cursor-grabbing' : 'cursor-grab'">

                    <!-- Image Layer -->
                    <template x-if="cropper.imgElement && cropper.isLoaded">
                        <img :src="cropper.imgElement.src"
                             class="absolute left-1/2 top-1/2 pointer-events-none select-none max-w-none max-h-none will-change-transform"
                             :style="`transform: translate(-50%, -50%) translate3d(${cropper.posX}px, ${cropper.posY}px, 0) rotate(${cropper.rotation}deg) scale(${cropper.currentScale});`" />
                    </template>

                    <!-- Instruction Overlay Pill (Like Facebook) -->
                    <div class="absolute top-3 left-1/2 -translate-x-1/2 px-3.5 py-1.5 rounded-full bg-black/75 backdrop-blur-md text-white font-semibold text-xs flex items-center gap-2 pointer-events-none shadow-lg border border-white/15 transition-opacity duration-200"
                         :class="cropper.isDragging ? 'opacity-30' : 'opacity-95'">
                        <svg class="w-4 h-4 text-deped-gold shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/></svg>
                        <span>Drag photo to reposition</span>
                        <span class="text-white/40">•</span>
                        <span class="text-white/70">Scroll to zoom</span>
                    </div>

                    <!-- Rule of Thirds Grid Overlay -->
                    <div class="absolute inset-0 pointer-events-none grid grid-cols-3 grid-rows-3 transition-opacity duration-300"
                         :class="cropper.isDragging ? 'opacity-60' : 'opacity-15'">
                        <div class="border-r border-b border-white/40"></div>
                        <div class="border-r border-b border-white/40"></div>
                        <div class="border-b border-white/40"></div>
                        <div class="border-r border-b border-white/40"></div>
                        <div class="border-r border-b border-white/40"></div>
                        <div class="border-b border-white/40"></div>
                        <div class="border-r border-b border-white/40"></div>
                        <div class="border-r border-b border-white/40"></div>
                        <div></div>
                    </div>

                    <!-- Live Headline Simulation Overlay on Viewport -->
                    <div x-show="cropper.showOverlayText" 
                         class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-950/40 to-transparent flex items-center p-6 sm:p-8 pointer-events-none transition-opacity duration-300"
                         :class="cropper.isDragging ? 'opacity-35' : 'opacity-100'">
                        <div class="max-w-xs space-y-1">
                            <div class="text-[10px] font-bold text-deped-gold uppercase tracking-wider" x-text="(cropper.targetBanner && cropper.targetBanner.subtitle) ? cropper.targetBanner.subtitle : 'SDO San Juan City • Division ALS'"></div>
                            <div class="text-sm sm:text-base font-bold text-white font-serif leading-tight" x-text="(cropper.targetBanner && cropper.targetBanner.title) ? cropper.targetBanner.title : 'Alternative Learning System'"></div>
                            <div class="pt-1.5">
                                <span class="inline-block px-2.5 py-1 rounded bg-deped-blue text-[10px] font-bold text-white shadow-xs" x-text="(cropper.targetBanner && cropper.targetBanner.button_text) ? cropper.targetBanner.button_text + ' →' : 'Take A Tour →'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Resolution Tag -->
                    <div class="absolute bottom-3 right-3 px-2.5 py-1 rounded-lg bg-black/70 backdrop-blur-md text-emerald-400 font-mono text-[11px] font-bold border border-emerald-500/20 shadow-md">
                        <span x-text="cropper.aspectRatio === 1920/800 ? '1920 × 800 HD' : '1920 × 1080 HD'"></span>
                    </div>

                    <!-- Loading State with Spinner -->
                    <div x-show="!cropper.isLoaded && !cropper.hasError" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center text-white text-xs font-bold gap-2">
                        <svg class="animate-spin w-6 h-6 text-deped-blue" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Loading photo for repositioning...</span>
                    </div>

                    <!-- Error State with Retry -->
                    <div x-show="cropper.hasError" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center text-rose-300 text-xs font-bold gap-2 p-4 text-center">
                        <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span x-text="cropper.errorMessage || 'Unable to load photo.'"></span>
                    </div>
                </div>

                <!-- Compact Controls Bar -->
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 space-y-2.5">
                    
                    <!-- Zoom Slider Row -->
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-slate-700 uppercase shrink-0 w-12">Zoom:</span>
                        
                        <button type="button" 
                                @click="cropper.zoomStep(-0.1)"
                                class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 flex items-center justify-center font-bold text-base transition-colors shrink-0">
                            &minus;
                        </button>
                        
                        <input type="range" 
                               min="1.0" 
                               max="3.0" 
                               step="0.01" 
                               x-model.number="cropper.zoom"
                               @input="cropper.onZoomChange()"
                               class="w-full accent-deped-blue cursor-pointer h-2 bg-slate-200 rounded-lg">
                        
                        <button type="button" 
                                @click="cropper.zoomStep(0.1)"
                                class="w-7 h-7 rounded-lg bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 flex items-center justify-center font-bold text-base transition-colors shrink-0">
                            &plus;
                        </button>

                        <div class="w-14 px-1.5 py-1 rounded-lg bg-white border border-slate-300 text-slate-800 font-mono text-xs font-bold text-center shrink-0">
                            <span x-text="Math.round(cropper.zoom * 100) + '%'"></span>
                        </div>
                    </div>

                    <!-- Action Tools Row -->
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-200/60">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" 
                                    @click="cropper.centerAndClamp()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold text-xs transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Center Framing</span>
                            </button>

                            <button type="button" 
                                    @click="cropper.rotate(-90)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold text-xs transition-colors"
                                    title="Rotate counter-clockwise">
                                <span>↺ -90°</span>
                            </button>

                            <button type="button" 
                                    @click="cropper.rotate(90)"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold text-xs transition-colors"
                                    title="Rotate clockwise">
                                <span>↻ +90°</span>
                            </button>

                            <!-- Toggle Headline Overlay Preview -->
                            <button type="button" 
                                    @click="cropper.showOverlayText = !cropper.showOverlayText"
                                    :class="cropper.showOverlayText ? 'bg-blue-50 text-deped-blue border-blue-200' : 'bg-white text-slate-600 border-slate-300'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border font-bold text-xs transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span x-text="cropper.showOverlayText ? 'Hide Title Preview' : 'Show Title Preview'"></span>
                            </button>
                        </div>

                        <div class="text-xs text-slate-500 font-medium">
                            Position: <span class="font-mono text-slate-700 font-bold" x-text="`X: ${Math.round(cropper.posX)}px, Y: ${Math.round(cropper.posY)}px`"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Footer (Always 100% visible) -->
            <div class="shrink-0 px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between z-10">
                <button type="button" 
                        @click="cropper.cancel()" 
                        :disabled="cropper.isSaving"
                        class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                    Cancel
                </button>
                
                <button type="button" 
                        @click="cropper.applyCrop()" 
                        :disabled="cropper.isSaving || !cropper.isLoaded"
                        :class="(cropper.isSaving || !cropper.isLoaded) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-deped-dark'"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-deped-blue text-white font-bold text-xs shadow-md shadow-deped-blue/25 transition-all">
                    <template x-if="cropper.isSaving">
                        <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    </template>
                    <template x-if="!cropper.isSaving">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <span x-text="cropper.isSaving ? 'Saving & Updating Banner...' : (cropper.targetMode === 'direct' ? 'Save Framing & Reposition' : 'Apply Framing & Crop')"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Dedicated Global Reposition Form (Guaranteed static ID outside loop) -->
    <form id="globalRepositionForm" method="POST" action="" style="display: none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="cropped_image" id="globalRepositionCroppedImage">
    </form>

</div>
@endsection

@push('scripts')
<script>
function toCleanImageUrl(url) {
    if (!url) return '';
    if (url.startsWith('data:') || url.startsWith('blob:')) return url;
    try {
        const u = new URL(url, window.location.origin);
        return u.pathname + u.search;
    } catch(e) {
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            return '/' + url.replace(/^\/+/, '');
        }
        return url;
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('bannerManager', () => ({
        init() {
            this.cropper.parent = this;
        },

        uploadModal: false,
        editModal: false,
        activeBanner: {},
        
        // Staged cropped images
        uploadCroppedImage: null,
        editCroppedImage: null,
        
        // Active file references
        uploadOriginalFileUrl: null,
        editOriginalFileUrl: null,

        // Cropper state machine
        cropper: {
            parent: null,
            isOpen: false,
            isLoaded: false,
            isSaving: false,
            hasError: false,
            errorMessage: '',
            imgElement: null,
            aspectRatio: 1920 / 800,
            aspectRatioName: '2.4:1',
            zoom: 1.0,
            minZoom: 1.0,
            maxZoom: 3.0,
            rotation: 0,
            posX: 0,
            posY: 0,
            isDragging: false,
            dragStartX: 0,
            dragStartY: 0,
            initialPosX: 0,
            initialPosY: 0,
            containerWidth: 0,
            containerHeight: 0,
            naturalWidth: 0,
            naturalHeight: 0,
            showOverlayText: true,
            targetMode: null, // 'upload', 'edit', 'direct'
            targetBanner: null,
            targetBannerId: null,
            _previewTimer: null,

            open(imageUrl, mode = 'direct', banner = null) {
                this.isOpen = true;
                this.isLoaded = false;
                this.isSaving = false;
                this.hasError = false;
                this.errorMessage = '';
                this.targetMode = mode;
                this.targetBanner = banner;
                this.targetBannerId = banner ? banner.id : null;
                this.zoom = 1.0;
                this.rotation = 0;
                this.posX = 0;
                this.posY = 0;
                this.aspectRatio = 1920 / 800;
                this.showOverlayText = true;

                const cleanUrl = toCleanImageUrl(imageUrl);
                const img = new Image();

                // Only set crossOrigin if URL is truly a foreign domain
                if (cleanUrl.startsWith('http://') || cleanUrl.startsWith('https://')) {
                    try {
                        const parsed = new URL(cleanUrl);
                        if (parsed.origin !== window.location.origin) {
                            img.crossOrigin = 'anonymous';
                        }
                    } catch(e) {}
                }

                const onImageReady = () => {
                    this.imgElement = img;
                    this.naturalWidth = img.naturalWidth;
                    this.naturalHeight = img.naturalHeight;
                    this.isLoaded = true;
                    this.hasError = false;

                    setTimeout(() => {
                        this.updateDimensions();
                        this.centerAndClamp();
                    }, 40);
                };

                img.onload = onImageReady;
                img.onerror = (e) => {
                    console.error('Failed to load image for repositioning:', cleanUrl, e);
                    // Fallback attempt: if it had crossOrigin, retry without it
                    if (img.crossOrigin) {
                        const fallbackImg = new Image();
                        fallbackImg.onload = () => {
                            this.imgElement = fallbackImg;
                            this.naturalWidth = fallbackImg.naturalWidth;
                            this.naturalHeight = fallbackImg.naturalHeight;
                            this.isLoaded = true;
                            this.hasError = false;
                            setTimeout(() => {
                                this.updateDimensions();
                                this.centerAndClamp();
                            }, 40);
                        };
                        fallbackImg.onerror = () => {
                            this.isLoaded = false;
                            this.hasError = true;
                            this.errorMessage = 'Unable to load image file. Please verify the photo.';
                        };
                        fallbackImg.src = cleanUrl;
                    } else {
                        this.isLoaded = false;
                        this.hasError = true;
                        this.errorMessage = 'Unable to load image file. Please verify the photo.';
                    }
                };

                img.src = cleanUrl;

                // If already cached in browser memory
                if (img.complete && img.naturalWidth > 0) {
                    onImageReady();
                }
            },

            cancel() {
                this.isOpen = false;
            },

            updateDimensions() {
                const vp = document.getElementById('bannerCropperViewport') || document.querySelector('[x-ref="viewportBox"]');
                if (vp && vp.clientWidth && vp.clientHeight) {
                    this.containerWidth = vp.clientWidth;
                    this.containerHeight = vp.clientHeight;
                }
            },

            get effectiveDimensions() {
                if (this.rotation % 180 !== 0) {
                    return { w: this.naturalHeight, h: this.naturalWidth };
                }
                return { w: this.naturalWidth, h: this.naturalHeight };
            },

            get baseScale() {
                if (!this.containerWidth || !this.containerHeight || !this.naturalWidth || !this.naturalHeight) {
                    return 1.0;
                }
                const eff = this.effectiveDimensions;
                return Math.max(this.containerWidth / eff.w, this.containerHeight / eff.h);
            },

            get currentScale() {
                return this.baseScale * this.zoom;
            },

            get slack() {
                const eff = this.effectiveDimensions;
                const dispW = eff.w * this.currentScale;
                const dispH = eff.h * this.currentScale;
                return {
                    x: Math.max(0, (dispW - this.containerWidth) / 2),
                    y: Math.max(0, (dispH - this.containerHeight) / 2)
                };
            },

            clampPosition() {
                const s = this.slack;
                this.posX = Math.max(-s.x, Math.min(s.x, this.posX));
                this.posY = Math.max(-s.y, Math.min(s.y, this.posY));
            },

            centerAndClamp() {
                this.posX = 0;
                this.posY = 0;
                this.clampPosition();
            },

            startDrag(e) {
                if (!this.isLoaded) return;
                this.isDragging = true;
                this.dragStartX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
                this.dragStartY = e.clientY || (e.touches && e.touches[0].clientY) || 0;
                this.initialPosX = this.posX;
                this.initialPosY = this.posY;
            },

            onDrag(e) {
                if (!this.isDragging) return;
                const currentX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
                const currentY = e.clientY || (e.touches && e.touches[0].clientY) || 0;
                const deltaX = currentX - this.dragStartX;
                const deltaY = currentY - this.dragStartY;

                this.posX = this.initialPosX + deltaX;
                this.posY = this.initialPosY + deltaY;
                this.clampPosition();
            },

            stopDrag() {
                if (this.isDragging) {
                    this.isDragging = false;
                }
            },

            onWheel(e) {
                const delta = e.deltaY < 0 ? 0.08 : -0.08;
                this.zoom = Math.max(this.minZoom, Math.min(this.maxZoom, parseFloat((this.zoom + delta).toFixed(2))));
                this.clampPosition();
            },

            zoomStep(step) {
                this.zoom = Math.max(this.minZoom, Math.min(this.maxZoom, parseFloat((this.zoom + step).toFixed(2))));
                this.clampPosition();
            },

            onZoomChange() {
                this.clampPosition();
            },

            rotate(deg) {
                this.rotation = (this.rotation + deg + 360) % 360;
                this.clampPosition();
            },

            setAspectRatio(ratio, name) {
                this.aspectRatio = ratio;
                this.aspectRatioName = name;
                setTimeout(() => {
                    this.updateDimensions();
                    this.clampPosition();
                }, 30);
            },

            generateCanvas(targetWidth = 1920) {
                if (!this.imgElement) {
                    console.error('generateCanvas failed: imgElement is missing');
                    return null;
                }

                const vp = document.getElementById('bannerCropperViewport') || document.querySelector('[x-ref="viewportBox"]');
                const cWidth = this.containerWidth || (vp && vp.clientWidth ? vp.clientWidth : 800) || 800;
                const cHeight = this.containerHeight || (vp && vp.clientHeight ? vp.clientHeight : Math.round(cWidth / this.aspectRatio)) || 333;

                const targetHeight = Math.round(targetWidth / this.aspectRatio);
                const canvas = document.createElement('canvas');
                canvas.width = targetWidth;
                canvas.height = targetHeight;
                const ctx = canvas.getContext('2d');
                if (!ctx) {
                    console.error('generateCanvas failed: unable to get 2d context');
                    return null;
                }
                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';

                const k = targetWidth / cWidth;
                const canvasX = (targetWidth / 2) + (this.posX * k);
                const canvasY = (targetHeight / 2) + (this.posY * k);
                const canvasScale = this.currentScale * k;

                ctx.save();
                ctx.translate(canvasX, canvasY);
                ctx.rotate((this.rotation * Math.PI) / 180);
                ctx.scale(canvasScale, canvasScale);
                ctx.drawImage(this.imgElement, -this.naturalWidth / 2, -this.naturalHeight / 2);
                ctx.restore();

                return canvas;
            },

            applyCrop() {
                if (this.isSaving) return;

                try {
                    const canvas = this.generateCanvas(1920);
                    if (!canvas) {
                        alert('Unable to process photo crop. Please wait until the image finishes loading.');
                        return;
                    }

                    const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
                    const manager = (this.parent) ? this.parent : Alpine.$data(document.querySelector('[x-data="bannerManager()"]'));

                    if (this.targetMode === 'upload') {
                        if (manager) manager.uploadCroppedImage = dataUrl;
                        this.isOpen = false;
                    } else if (this.targetMode === 'edit') {
                        if (manager) manager.editCroppedImage = dataUrl;
                        this.isOpen = false;
                    } else if (this.targetMode === 'direct') {
                        if (!this.targetBannerId) {
                            alert('No banner ID found for direct repositioning.');
                            return;
                        }

                        this.isSaving = true;

                        const form = document.getElementById('globalRepositionForm');
                        const input = document.getElementById('globalRepositionCroppedImage');

                        if (!form || !input) {
                            this.isSaving = false;
                            alert('Reposition form could not be found.');
                            return;
                        }

                        form.action = '{{ url('/admin/banners') }}/' + this.targetBannerId + '/reposition';
                        input.value = dataUrl;
                        form.submit();
                    }
                } catch (err) {
                    this.isSaving = false;
                    console.error('Error during applyCrop:', err);
                    alert('Error saving banner: ' + err.message);
                }
            }
        },

        // Helper open methods
        openUploadModal() {
            this.uploadCroppedImage = null;
            this.uploadOriginalFileUrl = null;
            this.uploadModal = true;
        },

        openEditModal(banner) {
            this.activeBanner = banner;
            this.editCroppedImage = null;
            this.editOriginalFileUrl = null;
            this.editModal = true;
        },

        openCropperForDirectBanner(banner) {
            this.cropper.open(banner.image_url || ('/' + banner.image_path), 'direct', banner);
        },

        openCropperForEditExisting() {
            const url = this.editOriginalFileUrl || this.activeBanner.image_url || ('/' + this.activeBanner.image_path);
            this.cropper.open(url, 'edit', this.activeBanner);
        },

        reopenUploadCrop() {
            if (this.uploadOriginalFileUrl) {
                this.cropper.open(this.uploadOriginalFileUrl, 'upload', null);
            }
        },

        onUploadFileSelected(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.uploadOriginalFileUrl = e.target.result;
                this.cropper.open(e.target.result, 'upload', null);
            };
            reader.readAsDataURL(file);
        },

        onEditFileSelected(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.editOriginalFileUrl = e.target.result;
                this.cropper.open(e.target.result, 'edit', this.activeBanner);
            };
            reader.readAsDataURL(file);
        }
    }));
});
</script>
@endpush
