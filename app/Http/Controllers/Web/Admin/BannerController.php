<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BannerController extends Controller
{
    /**
     * Display all hero banners with management options.
     */
    public function index(): View
    {
        $banners = HeroBanner::orderBy('order_seq')->orderBy('id', 'desc')->get();

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Store a newly created hero banner.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'order_seq' => ['nullable', 'integer', 'min:1'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:8192'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imageFile = $request->file('image');
        $destinationDir = public_path('images/banners');

        if (!File::isDirectory($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        $extension = $imageFile->getClientOriginalExtension();
        $fileName = 'hero-slide-' . time() . '-' . Str::random(6) . '.' . $extension;
        $imageFile->move($destinationDir, $fileName);

        $relativePath = 'images/banners/' . $fileName;

        $maxOrder = HeroBanner::max('order_seq') ?? 0;

        HeroBanner::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? 'SDO San Juan City • Division ALS Network',
            'image_path' => $relativePath,
            'button_text' => $validated['button_text'] ?: 'Take A Tour',
            'button_url' => $validated['button_url'] ?: '/register',
            'order_seq' => $validated['order_seq'] ?? ($maxOrder + 1),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('status', 'Hero banner uploaded and added successfully!');
    }

    /**
     * Update an existing hero banner.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $banner = HeroBanner::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'order_seq' => ['nullable', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:8192'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $destinationDir = public_path('images/banners');

            if (!File::isDirectory($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }

            $extension = $imageFile->getClientOriginalExtension();
            $fileName = 'hero-slide-' . time() . '-' . Str::random(6) . '.' . $extension;
            $imageFile->move($destinationDir, $fileName);

            // If the old image was custom uploaded, clean it up if safe
            if (!empty($banner->image_path) && !str_contains($banner->image_path, 'hero-slide-1.jpg') && !str_contains($banner->image_path, 'hero-slide-2.jpg') && !str_contains($banner->image_path, 'hero-slide-3.jpg')) {
                $oldPath = public_path($banner->image_path);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $banner->image_path = 'images/banners/' . $fileName;
        }

        $banner->title = $validated['title'];
        $banner->subtitle = $validated['subtitle'] ?? '';
        $banner->button_text = $validated['button_text'] ?: 'Take A Tour';
        $banner->button_url = $validated['button_url'] ?: '/register';
        if (isset($validated['order_seq'])) {
            $banner->order_seq = $validated['order_seq'];
        }
        $banner->is_active = $request->has('is_active');
        $banner->save();

        return redirect()->route('admin.banners.index')->with('status', 'Hero banner updated successfully!');
    }

    /**
     * Toggle the active visibility status of a hero banner.
     */
    public function toggleActive(int $id): RedirectResponse
    {
        $banner = HeroBanner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        $state = $banner->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.banners.index')->with('status', "Banner '{$banner->title}' has been {$state}.");
    }

    /**
     * Remove a hero banner from the database.
     */
    public function destroy(int $id): RedirectResponse
    {
        $banner = HeroBanner::findOrFail($id);

        // Delete uploaded file if not a seeded template asset
        if (!empty($banner->image_path) && !str_contains($banner->image_path, 'hero-slide-1.jpg') && !str_contains($banner->image_path, 'hero-slide-2.jpg') && !str_contains($banner->image_path, 'hero-slide-3.jpg')) {
            $filePath = public_path($banner->image_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('status', 'Hero banner deleted successfully.');
    }
}
