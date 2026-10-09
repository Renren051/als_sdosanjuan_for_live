@extends('layouts.admin')

@section('title', 'Hero Banner Manager - SDO San Juan City ALS')
@section('page_title', 'Homepage Hero Banners')

@section('content')
<div class="space-y-6" x-data="{ uploadModal: false, editModal: false, activeBanner: {} }">

    @if(session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('status') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold uppercase">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
            <div class="font-bold">Please correct the following errors:</div>
            <ul class="list-disc list-inside text-xs">
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
            <h2 class="text-lg font-bold text-slate-900">Homepage Hero Photos &amp; Transitions</h2>
            <p class="text-xs text-slate-500">Upload and configure photos shown on the top of the homepage with smooth slow-fade transitions.</p>
        </div>
        <button type="button" @click="uploadModal = true" 
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

                    <div class="flex items-center gap-1">
                        <!-- Edit Button -->
                        <button type="button" 
                                @click="activeBanner = {{ json_encode($banner) }}; editModal = true"
                                class="p-2 rounded-lg text-slate-600 hover:bg-white hover:text-deped-blue hover:shadow-sm transition-all"
                                title="Edit Banner Details">
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
                <button @click="uploadModal = true" class="mt-4 px-4 py-2 rounded-xl bg-deped-blue text-white text-xs font-bold">Upload First Banner</button>
            </div>
        @endforelse
    </div>

    <!-- Upload Modal -->
    <div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-5" @click.away="uploadModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Upload New Hero Banner</h3>
                    <p class="text-xs text-slate-500">Will be added to the homepage carousel with slow fade transition</p>
                </div>
                <button type="button" @click="uploadModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <!-- Image File Input -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Banner Image (JPG, PNG, WebP) *</label>
                    <input type="file" name="image" required accept="image/jpeg,image/png,image/webp,image/jpg"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-deped-blue file:text-white hover:file:bg-deped-dark file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50">
                    <p class="text-[11px] text-slate-400 mt-1">Recommended size: 1920x800 or 1920x1080 (High definition landscape). Max 8MB.</p>
                </div>

                <!-- Title (Main Heading in Serif font) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Headline / University/Program Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Alternative Learning System"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-deped-blue focus:bg-white font-medium">
                </div>

                <!-- Subtitle (Top text like 'The Best University Of The State') -->
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

    <!-- Edit Modal -->
    <div x-show="editModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 sm:p-8 space-y-5" @click.away="editModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Edit Hero Banner</h3>
                    <p class="text-xs text-slate-500">Update slide text, button link, or replace photo</p>
                </div>
                <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form :action="'{{ url('/admin/banners') }}/' + activeBanner.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                
                <!-- Replace Image File (Optional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Replace Image (Leave blank to keep existing)</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/jpg"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-deped-blue file:text-white hover:file:bg-deped-dark file:cursor-pointer border border-slate-200 rounded-xl bg-slate-50">
                </div>

                <!-- Title -->
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

</div>
@endsection
