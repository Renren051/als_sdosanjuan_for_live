<?php

namespace App\Http\Controllers\Web\Learner;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\ModuleProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LearnerPortalController extends Controller
{
    /**
     * Define the standard DepEd ALS credential document definitions.
     */
    protected function getRequiredDocsDefinition(): array
    {
        return [
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
    }

    /**
     * Learner Home Dashboard.
     */
    public function dashboard(): View
    {
        $user = Auth::user()->load([
            'profile',
            'latestEnrollment.clc',
            'latestEnrollment.assignedTeacher.profile',
            'documents.reviewer.profile',
        ]);

        $enrollment = $user->latestEnrollment;
        $requiredDocs = $this->getRequiredDocsDefinition();
        $documents = $user->documents->keyBy('document_type');

        // Calculate document verification metrics
        $mandatoryTypes = array_keys(array_filter($requiredDocs, fn ($d) => $d['required']));
        $mandatoryCount = count($mandatoryTypes);
        $verifiedMandatoryCount = 0;
        $rejectedCount = 0;

        foreach ($mandatoryTypes as $type) {
            if (isset($documents[$type]) && $documents[$type]->verification_status === LearnerDocument::STATUS_VERIFIED) {
                $verifiedMandatoryCount++;
            }
        }

        foreach ($documents as $doc) {
            if ($doc->verification_status === LearnerDocument::STATUS_REJECTED) {
                $rejectedCount++;
            }
        }

        // User module progress summary
        $progressRecords = ModuleProgress::with(['module.strand'])
            ->where('user_id', $user->id)
            ->whereHas('module', function ($q) {
                $q->published();
            })
            ->latest('last_opened_at')
            ->take(4)
            ->get();

        $continueReading = ModuleProgress::with(['module.strand'])
            ->where('user_id', $user->id)
            ->where('completion_status', ModuleProgress::STATUS_IN_PROGRESS)
            ->whereHas('module', function ($q) {
                $q->published();
            })
            ->latest('last_opened_at')
            ->first();

        $completedCount = ModuleProgress::where('user_id', $user->id)
            ->where('completion_status', ModuleProgress::STATUS_COMPLETED)
            ->whereHas('module', function ($q) {
                $q->published();
            })
            ->count();

        $inProgressCount = ModuleProgress::where('user_id', $user->id)
            ->where('completion_status', ModuleProgress::STATUS_IN_PROGRESS)
            ->whereHas('module', function ($q) {
                $q->published();
            })
            ->count();

        $totalPublishedModules = Module::published()->count();
        $overallProgressPercent = $totalPublishedModules > 0
            ? min(100, (int) round(($completedCount / $totalPublishedModules) * 100))
            : 0;

        return view('learner.dashboard', compact(
            'user',
            'enrollment',
            'requiredDocs',
            'documents',
            'mandatoryCount',
            'verifiedMandatoryCount',
            'rejectedCount',
            'progressRecords',
            'continueReading',
            'completedCount',
            'inProgressCount',
            'totalPublishedModules',
            'overallProgressPercent'
        ));
    }

    /**
     * Dedicated place for learners to follow their enrollment journey.
     */
    public function enrollment(): View
    {
        $user = Auth::user()->load([
            'profile',
            'latestEnrollment.clc',
            'latestEnrollment.assignedTeacher.profile',
            'documents.reviewer.profile',
        ]);

        $enrollment = $user->latestEnrollment;
        $requiredDocs = $this->getRequiredDocsDefinition();
        $documents = $user->documents->keyBy('document_type');

        // Document count summaries
        $mandatoryTypes = array_keys(array_filter($requiredDocs, fn ($d) => $d['required']));
        $mandatoryCount = count($mandatoryTypes);
        $verifiedCount = 0;
        $rejectedCount = 0;
        $pendingCount = 0;

        foreach ($mandatoryTypes as $type) {
            if (isset($documents[$type])) {
                if ($documents[$type]->verification_status === LearnerDocument::STATUS_VERIFIED) {
                    $verifiedCount++;
                } elseif ($documents[$type]->verification_status === LearnerDocument::STATUS_REJECTED) {
                    $rejectedCount++;
                } elseif ($documents[$type]->verification_status === LearnerDocument::STATUS_PENDING) {
                    $pendingCount++;
                }
            }
        }

        return view('learner.enrollment', compact(
            'user',
            'enrollment',
            'requiredDocs',
            'documents',
            'mandatoryCount',
            'verifiedCount',
            'rejectedCount',
            'pendingCount'
        ));
    }

    /**
     * Dedicated place for learners to view their own profile.
     */
    public function profile(): View
    {
        $user = Auth::user()->load([
            'profile',
            'latestEnrollment.clc',
            'latestEnrollment.assignedTeacher.profile',
        ]);

        $enrollment = $user->latestEnrollment;

        return view('learner.profile', compact('user', 'enrollment'));
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

        $requiredDocs = $this->getRequiredDocsDefinition();

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
     * Securely view or stream an uploaded credential file.
     * Enforces that learners can ONLY view their own documents.
     */
    public function viewDocument($id): BinaryFileResponse
    {
        $document = LearnerDocument::findOrFail($id);
        $currentUser = Auth::user();

        // Strict isolation check: Learners can only view their own documents
        if ($currentUser->isLearner() && $document->user_id !== $currentUser->id) {
            abort(403, 'Unauthorized access. You may only view your own documents.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'The requested document file could not be found on the server.');
        }

        $fullPath = Storage::disk('public')->path($document->file_path);

        $headers = [
            'Content-Type' => $document->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . basename($document->file_name) . '"',
        ];

        return response()->file($fullPath, $headers);
    }

    /**
     * Web Module Catalog with direct download, in-browser reading, and resume points.
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

    /**
     * Open published module in browser reader and track or resume reading session.
     */
    public function readModule($id, Request $request): View
    {
        $user = Auth::user();
        // Ensure only published modules can be opened by learners
        $module = Module::published()->with('strand')->findOrFail($id);

        // Find or create progress record for this learner and module
        $progress = ModuleProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $module->id,
            ],
            [
                'downloaded_at' => now(),
                'last_opened_at' => now(),
                'completion_status' => ModuleProgress::STATUS_IN_PROGRESS,
                'last_page_read' => 1,
                'total_pages' => 2,
                'device_identifier' => $request->userAgent() ?: 'Web Browser',
                'synced_at' => now(),
            ]
        );

        // If previously only downloaded, transition to in_progress
        if ($progress->completion_status === ModuleProgress::STATUS_DOWNLOADED) {
            $progress->completion_status = ModuleProgress::STATUS_IN_PROGRESS;
        }

        $progress->last_opened_at = now();
        $progress->save();

        // Determine resume page
        $resumePage = (int) $request->input('page', $progress->last_page_read ?: 1);
        if ($resumePage < 1) {
            $resumePage = 1;
        }

        return view('learner.read-module', compact('module', 'progress', 'resumePage'));
    }

    /**
     * Save reading telemetry and progress from the browser reader.
     */
    public function updateProgress(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        $module = Module::published()->findOrFail($id);

        $validated = $request->validate([
            'last_page_read' => ['required', 'integer', 'min:1'],
            'total_pages' => ['nullable', 'integer', 'min:1'],
            'completion_status' => ['nullable', 'in:downloaded,in_progress,completed'],
        ]);

        $lastPage = (int) $validated['last_page_read'];
        $totalPages = isset($validated['total_pages']) ? (int) $validated['total_pages'] : null;

        $existing = ModuleProgress::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->first();

        if ($totalPages === null) {
            $totalPages = $existing?->total_pages ?: 2;
        }

        // Determine status
        $status = $validated['completion_status'] ?? null;
        if (!$status) {
            if ($totalPages && $lastPage >= $totalPages) {
                $status = ModuleProgress::STATUS_COMPLETED;
            } else {
                $status = ModuleProgress::STATUS_IN_PROGRESS;
            }
        }

        $progress = ModuleProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $module->id,
            ],
            [
                'last_opened_at' => now(),
                'last_page_read' => $lastPage,
                'total_pages' => $totalPages,
                'completion_status' => $status,
                'device_identifier' => $request->userAgent() ?: 'Web Browser',
                'synced_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Progress saved successfully.',
            'progress' => [
                'completion_status' => $progress->completion_status,
                'last_page_read' => $progress->last_page_read,
                'total_pages' => $progress->total_pages,
                'percent' => $progress->progress_percent,
                'is_completed' => $progress->isCompleted(),
            ],
        ]);
    }
}
