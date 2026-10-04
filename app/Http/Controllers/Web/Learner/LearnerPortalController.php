<?php

namespace App\Http\Controllers\Web\Learner;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\ModuleProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LearnerPortalController extends Controller
{
    /**
     * Learner Home Dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user()->load([
            'profile',
            'latestEnrollment.clc',
            'latestEnrollment.assignedTeacher.profile',
            'documents',
        ]);

        $enrollment = $user->latestEnrollment;

        // User module progress summary
        $progressRecords = ModuleProgress::with(['module.strand'])
            ->where('user_id', $user->id)
            ->latest('last_opened_at')
            ->take(4)
            ->get();

        $completedCount = ModuleProgress::where('user_id', $user->id)
            ->where('completion_status', ModuleProgress::STATUS_COMPLETED)
            ->count();

        $downloadedCount = ModuleProgress::where('user_id', $user->id)->count();

        return view('learner.dashboard', compact(
            'user',
            'enrollment',
            'progressRecords',
            'completedCount',
            'downloadedCount'
        ));
    }

    /**
     * Document Upload Queue and Credential Status.
     */
    public function documents(): View
    {
        $user = Auth::user();
        $enrollment = $user->latestEnrollment;

        $documents = LearnerDocument::where('user_id', $user->id)
            ->with('reviewer.profile')
            ->get()
            ->keyBy('document_type');

        $requiredDocs = [
            LearnerDocument::TYPE_PSA_BIRTH_CERT => [
                'title' => 'PSA Birth Certificate',
                'description' => 'Clear photocopy or digital scan of your Philippine Statistics Authority birth certificate.',
                'required' => true,
            ],
            LearnerDocument::TYPE_BARANGAY_CLEARANCE => [
                'title' => 'Barangay Clearance / Certificate of Residency',
                'description' => 'Issued by your San Juan City Barangay Hall validating your current residency.',
                'required' => true,
            ],
            LearnerDocument::TYPE_PHOTO_ID => [
                'title' => '2x2 Recent Identification Photo',
                'description' => 'Formal 2x2 photograph with white background for official ALS enrollment records.',
                'required' => true,
            ],
            LearnerDocument::TYPE_FORM_137_SF10 => [
                'title' => 'DepEd Form 137 / School Form 10 (SF10)',
                'description' => 'Permanent school record from the last formal elementary or high school attended (if applicable).',
                'required' => false,
            ],
            LearnerDocument::TYPE_PORTFOLIO_DOC => [
                'title' => 'Learner Portfolio / Prior Learning Document',
                'description' => 'Optional certificates of training, work portfolio, or prior assessment sheets.',
                'required' => false,
            ],
        ];

        return view('learner.documents', compact('user', 'enrollment', 'documents', 'requiredDocs'));
    }

    /**
     * Handle document upload from learner portal.
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => ['required', 'in:psa_birth_cert,barangay_clearance,form_137_sf10,photo_id,portfolio_document'],
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // 10MB limit
        ]);

        $user = Auth::user();
        $file = $request->file('document_file');
        $docType = $validated['document_type'];
        $enrollment = $user->latestEnrollment;

        $extension = $file->getClientOriginalExtension();
        $safeFileName = "{$docType}_" . time() . '_' . Str::random(6) . '.' . $extension;
        $relativeFolder = "documents/{$user->id}";
        $destinationPath = storage_path("app/public/{$relativeFolder}");

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $safeFileName);
        $relativePath = "{$relativeFolder}/{$safeFileName}";

        LearnerDocument::updateOrCreate(
            [
                'user_id' => $user->id,
                'document_type' => $docType,
            ],
            [
                'enrollment_id' => $enrollment?->id,
                'file_path' => $relativePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => File::size("{$destinationPath}/{$safeFileName}"),
                'mime_type' => $file->getClientMimeType() ?: 'application/octet-stream',
                'verification_status' => LearnerDocument::STATUS_PENDING,
                'verification_notes' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]
        );

        return back()->with('success', 'Document uploaded successfully! Your assigned Mobile Teacher will verify your file.');
    }

    /**
     * Web Module Catalog with direct download and in-browser reading.
     */
    public function modules(Request $request): View
    {
        $user = Auth::user();
        $query = Module::with(['strand', 'uploader.profile'])
            ->published();

        if ($request->filled('strand_id')) {
            $query->where('strand_id', $request->input('strand_id'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('q')) {
            $search = '%' . $request->input('q') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('description', 'like', $search)
                  ->orWhere('module_number', 'like', $search);
            });
        }

        $modules = $query->orderBy('strand_id')->orderBy('level')->paginate(9)->withQueryString();
        $strands = LearningStrand::orderBy('order_seq')->get();

        $userProgress = ModuleProgress::where('user_id', $user->id)
            ->get()
            ->keyBy('module_id');

        return view('learner.modules', compact('modules', 'strands', 'userProgress'));
    }
}
