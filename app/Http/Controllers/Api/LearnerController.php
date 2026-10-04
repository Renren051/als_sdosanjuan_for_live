<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncProgressRequest;
use App\Http\Requests\UploadDocumentRequest;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\ModuleProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LearnerController extends Controller
{
    /**
     * Multipart form-data upload for learner credentials.
     * POST /api/v1/learner/documents/upload
     */
    public function uploadDocument(UploadDocumentRequest $request): JsonResponse
    {
        $user = $request->user();
        $file = $request->file('document_file');
        $docType = $request->input('document_type');
        $enrollmentId = $request->input('enrollment_id');

        // If enrollment_id not explicitly given, find user's latest enrollment
        if (!$enrollmentId) {
            $latestEnrollment = $user->latestEnrollment;
            $enrollmentId = $latestEnrollment?->id;
        }

        $extension = $file->getClientOriginalExtension();
        $safeFileName = "{$docType}_" . time() . '_' . Str::random(6) . '.' . $extension;
        $relativeFolder = "documents/{$user->id}";
        $destinationPath = storage_path("app/public/{$relativeFolder}");

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $safeFileName);
        $relativePath = "{$relativeFolder}/{$safeFileName}";

        // Upsert or create document
        $document = LearnerDocument::updateOrCreate(
            [
                'user_id' => $user->id,
                'document_type' => $docType,
                'enrollment_id' => $enrollmentId,
            ],
            [
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

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully and queued for teacher verification.',
            'data' => [
                'document' => $document,
                'type_label' => $document->document_type_label,
            ],
        ], 201);
    }

    /**
     * Retrieve profile and enrollment status notes.
     * GET /api/v1/learner/enrollment-status
     */
    public function getEnrollmentStatus(Request $request): JsonResponse
    {
        $user = $request->user();
        $enrollment = Enrollment::with([
            'clc',
            'assignedTeacher.profile',
            'documents',
        ])
        ->where('user_id', $user->id)
        ->latest()
        ->first();

        $profile = $user->profile;

        return response()->json([
            'success' => true,
            'data' => [
                'lrn' => $user->lrn,
                'status' => $enrollment ? $enrollment->enrollment_status : 'not_enrolled',
                'status_label' => $enrollment ? ucfirst(str_replace('_', ' ', $enrollment->enrollment_status)) : 'Not Enrolled',
                'program_track' => $enrollment?->program_track,
                'track_label' => $enrollment?->track_label,
                'calendar_year' => $enrollment?->calendar_year,
                'remarks' => $enrollment?->remarks,
                'enrolled_at' => $enrollment?->enrolled_at,
                'clc' => $enrollment?->clc,
                'assigned_teacher' => $enrollment?->assignedTeacher ? [
                    'id' => $enrollment->assignedTeacher->id,
                    'name' => $enrollment->assignedTeacher->full_name,
                    'contact_number' => $enrollment->assignedTeacher->profile?->contact_number,
                    'email' => $enrollment->assignedTeacher->email,
                ] : null,
                'documents' => $enrollment?->documents ?? [],
                'profile' => $profile,
            ],
        ]);
    }

    /**
     * Asynchronous telemetry sync from mobile app.
     * POST /api/v1/learner/sync-progress
     */
    public function syncProgress(SyncProgressRequest $request): JsonResponse
    {
        $user = $request->user();
        $deviceIdentifier = $request->input('device_identifier');
        $items = $request->input('items', []);

        $syncedCount = 0;
        $now = now();

        foreach ($items as $item) {
            ModuleProgress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'module_id' => $item['module_id'],
                ],
                [
                    'downloaded_at' => $item['downloaded_at'] ?? $now,
                    'last_opened_at' => $item['last_opened_at'] ?? $now,
                    'completion_status' => $item['completion_status'],
                    'device_identifier' => $deviceIdentifier,
                    'last_page_read' => $item['last_page_read'] ?? 1,
                    'total_pages' => $item['total_pages'] ?? null,
                    'synced_at' => $now,
                ]
            );
            $syncedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully synced {$syncedCount} telemetry records.",
            'data' => [
                'synced_count' => $syncedCount,
                'synced_at' => $now->toIso8601String(),
            ],
        ]);
    }
}
