<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningStrand;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * Display all self-learning modules repository.
     */
    public function index(Request $request): View
    {
        $query = Module::with(['strand', 'uploader.profile']);

        if ($request->filled('strand_id')) {
            $query->where('strand_id', $request->input('strand_id'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('sub_level')) {
            $query->where('sub_level', $request->input('sub_level'));
        }

        if ($request->filled('language')) {
            $query->where('language', $request->input('language'));
        }

        if ($request->filled('q')) {
            $search = '%' . $request->input('q') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('description', 'like', $search)
                  ->orWhere('module_number', 'like', $search);
            });
        }

        $modules = $query->orderBy('strand_id')->orderBy('level')->paginate(12)->withQueryString();
        $strands = LearningStrand::orderBy('order_seq')->get();

        return view('admin.modules.index', compact('modules', 'strands'));
    }

    /**
     * Upload and publish new ALS module.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'strand_id' => ['required', 'exists:learning_strands,id'],
            'level' => ['required', 'in:BLP,Elementary,Junior_High'],
            'sub_level' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'in:English,Filipino'],
            'module_number' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:25600'], // Enforce PDF-only & 25MB max
            'version' => ['nullable', 'string', 'max:20'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $file = $request->file('pdf_file');
        $strand = LearningStrand::find($validated['strand_id']);
        $fileName = Str::slug("{$strand->code}-{$validated['level']}-{$validated['module_number']}") . '_' . time() . '.pdf';
        $relativeFolder = 'modules';
        $destinationPath = storage_path("app/public/{$relativeFolder}");

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);
        $relativePath = "{$relativeFolder}/{$fileName}";
        $fileSize = File::size("{$destinationPath}/{$fileName}");

        Module::create([
            'strand_id' => $validated['strand_id'],
            'level' => $validated['level'],
            'sub_level' => $validated['sub_level'] ?? ($validated['level'] === 'Elementary' ? Module::SUB_LEVEL_LOWER_ELEMENTARY : null),
            'language' => $validated['language'] ?? Module::LANGUAGE_ENGLISH,
            'module_number' => $validated['module_number'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'pdf_file_path' => $relativePath,
            'pdf_file_size' => $fileSize,
            'version' => $validated['version'] ?: '1.0',
            'is_published' => $request->boolean('is_published', true),
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Self-Learning Module uploaded and published successfully.');
    }

    /**
     * Update module details or replace PDF file.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $module = Module::findOrFail($id);

        $validated = $request->validate([
            'strand_id' => ['required', 'exists:learning_strands,id'],
            'level' => ['required', 'in:BLP,Elementary,Junior_High'],
            'sub_level' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'in:English,Filipino'],
            'module_number' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'pdf_file' => ['nullable', 'file', 'mimes:pdf', 'max:25600'],
            'version' => ['nullable', 'string', 'max:20'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'strand_id' => $validated['strand_id'],
            'level' => $validated['level'],
            'sub_level' => $validated['sub_level'] ?? $module->sub_level,
            'language' => $validated['language'] ?? $module->language,
            'module_number' => $validated['module_number'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'version' => $validated['version'] ?: $module->version,
            'is_published' => $request->boolean('is_published', true),
        ];

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $strand = LearningStrand::find($validated['strand_id']);
            $fileName = Str::slug("{$strand->code}-{$validated['level']}-{$validated['module_number']}") . '_' . time() . '.pdf';
            $destinationPath = storage_path('app/public/modules');
            $file->move($destinationPath, $fileName);

            $updateData['pdf_file_path'] = "modules/{$fileName}";
            $updateData['pdf_file_size'] = File::size("{$destinationPath}/{$fileName}");
        }

        $module->update($updateData);

        return back()->with('success', 'Module updated successfully.');
    }

    /**
     * Toggle module publish status.
     */
    public function togglePublish($id): RedirectResponse
    {
        $module = Module::findOrFail($id);
        $module->update(['is_published' => !$module->is_published]);

        $statusLabel = $module->is_published ? 'published' : 'unpublished';
        return back()->with('success', "Module has been {$statusLabel}.");
    }

    /**
     * Delete module.
     */
    public function destroy($id): RedirectResponse
    {
        $module = Module::findOrFail($id);
        $module->delete();

        return back()->with('success', 'Module removed from repository.');
    }
}
