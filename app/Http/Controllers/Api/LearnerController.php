<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncGameScoresRequest;
use App\Http\Requests\SyncProgressRequest;
use App\Http\Requests\UploadDocumentRequest;
use App\Models\Enrollment;
use App\Models\LearnerDocument;
use App\Models\ModuleProgress;
use App\Models\ModuleGameAttempt;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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

    /**
     * Check whether this learner has already played a module's one-time game.
     * GET /api/v1/learner/modules/{moduleId}/game-status
     */
    public function moduleGameStatus(int $moduleId, Request $request): JsonResponse
    {
        $module = Module::query()->published()->findOrFail($moduleId);
        $user = $request->user();
        $enrollment = $user->latestEnrollment;
        $enrolledLevel = match ($enrollment?->program_track) {
            Enrollment::TRACK_BLP => Module::LEVEL_BLP,
            Enrollment::TRACK_AE_ELEMENTARY => Module::LEVEL_ELEMENTARY,
            Enrollment::TRACK_AE_JUNIOR_HIGH => Module::LEVEL_JUNIOR_HIGH,
            default => null,
        };

        if ($enrolledLevel && $module->level !== $enrolledLevel) {
            return response()->json([
                'success' => false,
                'message' => 'This module does not match the learner\'s enrolled program.',
            ], 403);
        }

        $played = ModuleGameAttempt::query()
            ->where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => ['played' => $played],
        ]);
    }
    /**
     * Store completed offline mini-game attempts for teacher reporting.
     * POST /api/v1/learner/sync-game-scores
     */
    public function syncGameScores(SyncGameScoresRequest $request): JsonResponse
    {
        $user = $request->user();
        $attempts = $request->input('attempts', []);
        $moduleIds = collect($attempts)->pluck('module_id')->unique()->values();
        $completedModuleIds = ModuleProgress::query()
            ->where('user_id', $user->id)
            ->whereIn('module_id', $moduleIds)
            ->where('completion_status', ModuleProgress::STATUS_COMPLETED)
            ->pluck('module_id')
            ->all();

        $incompleteModuleIds = $moduleIds->diff($completedModuleIds)->values();
        if ($incompleteModuleIds->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Complete each module before syncing its game score.',
                'data' => [
                    'incomplete_module_ids' => $incompleteModuleIds,
                ],
            ], 422);
        }

        $deviceIdentifier = $request->input('device_identifier');
        $syncedCount = 0;
        $duplicateCount = 0;
        DB::transaction(function () use ($user, $attempts, $deviceIdentifier, &$syncedCount, &$duplicateCount) {
            // Serialize syncs for this learner so separate devices cannot create two
            // first attempts for the same module at the same time.
            DB::table('users')->where('id', $user->id)->lockForUpdate()->first();

            foreach ($attempts as $attempt) {
                $existingAttempt = ModuleGameAttempt::query()
                    ->where('user_id', $user->id)
                    ->where('attempt_uuid', $attempt['attempt_uuid'])
                    ->first();

                if ($existingAttempt) {
                    $syncedCount++;
                    continue;
                }

                $alreadyPlayed = ModuleGameAttempt::query()
                    ->where('user_id', $user->id)
                    ->where('module_id', $attempt['module_id'])
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyPlayed) {
                    $duplicateCount++;
                    continue;
                }

                ModuleGameAttempt::create([
                    'attempt_uuid' => $attempt['attempt_uuid'],
                    'user_id' => $user->id,
                    'module_id' => $attempt['module_id'],
                    'game_type' => $attempt['game_type'],
                    'score' => $attempt['score'],
                    'max_score' => $attempt['max_score'],
                    'moves' => $attempt['moves'],
                    'elapsed_seconds' => $attempt['elapsed_seconds'],
                    'device_identifier' => $deviceIdentifier,
                    'attempted_at' => $attempt['attempted_at'],
                ]);
                $syncedCount++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Mini-game scores synced successfully.',
            'data' => [
                'synced_count' => $syncedCount,
                'duplicate_count' => $duplicateCount,
            ],
        ]);
    }
}
