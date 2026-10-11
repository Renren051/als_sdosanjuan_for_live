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
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:12288'],
            'cropped_image' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!$request->hasFile('image') && empty($validated['cropped_image'])) {
            return back()->withErrors(['image' => 'Please select and crop an image for the hero banner.'])->withInput();
        }

        if (!empty($validated['cropped_image']) && str_starts_with($validated['cropped_image'], 'data:image')) {
            $relativePath = $this->saveCroppedImage($validated['cropped_image']);
        } else {
            $imageFile = $request->file('image');
            $destinationDir = public_path('images/banners');

            if (!File::isDirectory($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }

            $extension = $imageFile->getClientOriginalExtension();
            $fileName = 'hero-slide-' . time() . '-' . Str::random(6) . '.' . $extension;
            $imageFile->move($destinationDir, $fileName);
            $relativePath = 'images/banners/' . $fileName;
        }

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
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:12288'],
            'cropped_image' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['cropped_image']) && str_starts_with($validated['cropped_image'], 'data:image')) {
            $relativePath = $this->saveCroppedImage($validated['cropped_image']);
            $this->deleteOldBannerImage($banner->image_path);
            $banner->image_path = $relativePath;
        } elseif ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $destinationDir = public_path('images/banners');

            if (!File::isDirectory($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }

            $extension = $imageFile->getClientOriginalExtension();
            $fileName = 'hero-slide-' . time() . '-' . Str::random(6) . '.' . $extension;
            $imageFile->move($destinationDir, $fileName);

            $this->deleteOldBannerImage($banner->image_path);
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
     * Dedicated action to quickly update the crop / reposition framing of an existing hero banner.
     */
    public function reposition(Request $request, int $id): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $banner = HeroBanner::findOrFail($id);

        $validated = $request->validate([
            'cropped_image' => ['required', 'string'],
        ]);

        if (!str_starts_with($validated['cropped_image'], 'data:image')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Invalid cropped image payload format.'], 422);
            }
            return back()->withErrors(['cropped_image' => 'Invalid cropped image payload format.']);
        }

        try {
            $relativePath = $this->saveCroppedImage($validated['cropped_image']);
            $this->deleteOldBannerImage($banner->image_path);

            $banner->image_path = $relativePath;
            $banner->save();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Framing and crop updated successfully for '{$banner->title}'!",
                    'image_url' => asset($banner->image_path),
                ]);
            }

            return redirect()->route('admin.banners.index')->with('status', "Framing and crop updated successfully for '{$banner->title}'!");
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Failed to save cropped image: ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['cropped_image' => 'Failed to save cropped image: ' . $e->getMessage()]);
        }
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

        $this->deleteOldBannerImage($banner->image_path);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('status', 'Hero banner deleted successfully.');
    }

    /**
     * Save a base64 encoded cropped image to the banners directory.
     */
    protected function saveCroppedImage(string $base64Data): string
    {
        $destinationDir = public_path('images/banners');

        if (!File::isDirectory($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        $extension = 'jpg';
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
            $type = strtolower($matches[1]);
            if (in_array($type, ['png', 'webp', 'jpeg', 'jpg'])) {
                $extension = ($type === 'jpeg') ? 'jpg' : $type;
            }
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        }

        $decoded = base64_decode($base64Data);
        if ($decoded === false) {
            throw new \InvalidArgumentException('Invalid base64 image data.');
        }

        $imageInfo = @getimagesizefromstring($decoded);
        if ($imageInfo === false) {
            throw new \InvalidArgumentException('Uploaded cropped binary is not a valid image.');
        }

        $fileName = 'hero-slide-' . time() . '-' . Str::random(6) . '.' . $extension;
        $filePath = $destinationDir . DIRECTORY_SEPARATOR . $fileName;

        File::put($filePath, $decoded);

        return 'images/banners/' . $fileName;
    }

    /**
     * Clean up an older uploaded banner file while preserving default template slides.
     */
    protected function deleteOldBannerImage(?string $path): void
    {
        if (!empty($path) && 
            !str_contains($path, 'hero-slide-1.jpg') && 
            !str_contains($path, 'hero-slide-2.jpg') && 
            !str_contains($path, 'hero-slide-3.jpg')) {
            $fullPath = public_path($path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }
}
