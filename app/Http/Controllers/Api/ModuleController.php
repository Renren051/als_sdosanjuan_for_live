<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LearningStrand;
use App\Models\Module;
use App\Models\ModuleProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ModuleController extends Controller
{
    /**
     * List all published modules filtered by Strand and Academic Level.
     * GET /api/v1/modules
     */
    public function index(Request $request): JsonResponse
    {
        $query = Module::with(['strand', 'uploader.profile'])
            ->published();

        // Filter by strand_id or strand_code
        if ($request->filled('strand_id')) {
            $query->where('strand_id', $request->input('strand_id'));
        } elseif ($request->filled('strand_code')) {
            $query->whereHas('strand', function ($q) use ($request) {
                $q->where('code', $request->input('strand_code'));
            });
        }

        // Program track scoping for authenticated learners
        $user = $request->user('sanctum') ?? $request->user('web');
        if ($user && $user->isLearner()) {
            $enrollment = $user->latestEnrollment;
            $enrolledLevel = match ($enrollment?->program_track) {
                \App\Models\Enrollment::TRACK_BLP => Module::LEVEL_BLP,
                \App\Models\Enrollment::TRACK_AE_ELEMENTARY => Module::LEVEL_ELEMENTARY,
                \App\Models\Enrollment::TRACK_AE_JUNIOR_HIGH => Module::LEVEL_JUNIOR_HIGH,
                default => null,
            };
            if ($enrolledLevel) {
                $query->where('level', $enrolledLevel);
            }
        } elseif ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        // Filter by sub_level (Lower Elementary, Advanced Elementary)
        if ($request->filled('sub_level')) {
            $query->where('sub_level', $request->input('sub_level'));
        }

        // Filter by language (English, Filipino)
        if ($request->filled('language')) {
            $query->where('language', $request->input('language'));
        }

        // Search query
        if ($request->filled('q')) {
            $searchTerm = '%' . $request->input('q') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhere('module_number', 'like', $searchTerm);
            });
        }

        $modules = $query->orderBy('strand_id')
            ->orderBy('level')
            ->get();

        // If authenticated user is a learner, attach their progress
        $user = $request->user('sanctum');
        if ($user && $user->isLearner()) {
            $userProgress = ModuleProgress::where('user_id', $user->id)
                ->get()
                ->keyBy('module_id');

            $modules->transform(function ($mod) use ($userProgress) {
                $progress = $userProgress->get($mod->id);
                $mod->user_progress = $progress ? [
                    'completion_status' => $progress->completion_status,
                    'last_page_read' => $progress->last_page_read,
                    'total_pages' => $progress->total_pages,
                    'downloaded_at' => $progress->downloaded_at,
                    'last_opened_at' => $progress->last_opened_at,
                    'percent' => $progress->progress_percent,
                ] : null;
                return $mod;
            });
        }

        return response()->json([
            'success' => true,
            'count' => $modules->count(),
            'data' => $modules,
        ]);
    }

    /**
     * Get module details.
     * GET /api/v1/modules/{id}
     */
    public function show($id, Request $request): JsonResponse
    {
        $module = Module::with(['strand', 'uploader.profile'])
            ->published()
            ->findOrFail($id);

        $user = $request->user('sanctum') ?? $request->user('web');
        if ($user && $user->isLearner()) {
            $enrollment = $user->latestEnrollment;
            $enrolledLevel = match ($enrollment?->program_track) {
                \App\Models\Enrollment::TRACK_BLP => Module::LEVEL_BLP,
                \App\Models\Enrollment::TRACK_AE_ELEMENTARY => Module::LEVEL_ELEMENTARY,
                \App\Models\Enrollment::TRACK_AE_JUNIOR_HIGH => Module::LEVEL_JUNIOR_HIGH,
                default => null,
            };
            if ($enrolledLevel && $module->level !== $enrolledLevel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. This module belongs to the ' . $module->level . ' curriculum, but your enrolled program track is ' . ($enrollment->track_label ?? $enrollment->program_track) . '.',
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $module,
        ]);
    }

    /**
     * Stream or download the PDF binary.
     * GET /api/v1/modules/{id}/download
     */
    public function download($id, Request $request)
    {
        $user = $request->user('sanctum') ?? $request->user('web');
        $moduleQuery = Module::query();
        if ($user && $user->isLearner()) {
            $moduleQuery->published();
        }
        $module = $moduleQuery->findOrFail($id);
        if ($user && $user->isLearner()) {
            $enrollment = $user->latestEnrollment;
            $enrolledLevel = match ($enrollment?->program_track) {
                \App\Models\Enrollment::TRACK_BLP => Module::LEVEL_BLP,
                \App\Models\Enrollment::TRACK_AE_ELEMENTARY => Module::LEVEL_ELEMENTARY,
                \App\Models\Enrollment::TRACK_AE_JUNIOR_HIGH => Module::LEVEL_JUNIOR_HIGH,
                default => null,
            };
            if ($enrolledLevel && $module->level !== $enrolledLevel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. This module belongs to the ' . $module->level . ' curriculum, but your enrolled program track is ' . ($enrollment->track_label ?? $enrollment->program_track) . '.',
                ], 403);
            }
        }

        $fullPath = storage_path('app/public/' . $module->pdf_file_path);

        if (!File::exists($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Module PDF file not found on server.',
            ], 404);
        }

        // Track download for authenticated learner
        if ($user && $user->isLearner()) {
            $existing = ModuleProgress::where('user_id', $user->id)->where('module_id', $module->id)->first();
            if (!$existing) {
                ModuleProgress::create([
                    'user_id' => $user->id,
                    'module_id' => $module->id,
                    'downloaded_at' => now(),
                    'completion_status' => ModuleProgress::STATUS_DOWNLOADED,
                    'device_identifier' => $request->header('User-Agent') ?? 'Web/Mobile Client',
                    'synced_at' => now(),
                ]);
            } else {
                $existing->update([
                    'downloaded_at' => $existing->downloaded_at ?: now(),
                ]);
            }
        }

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($module->pdf_file_path) . '"',
            'Cache-Control' => 'public, max-age=86400',
        ];

        return response()->file($fullPath, $headers);
    }

    /**
     * List all Learning Strands.
     * GET /api/v1/strands
     */
    public function getStrands(Request $request): JsonResponse
    {
        $user = $request->user('sanctum') ?? $request->user('web');
        $enrolledLevel = null;
        if ($user && $user->isLearner()) {
            $enrollment = $user->latestEnrollment;
            $enrolledLevel = match ($enrollment?->program_track) {
                \App\Models\Enrollment::TRACK_BLP => Module::LEVEL_BLP,
                \App\Models\Enrollment::TRACK_AE_ELEMENTARY => Module::LEVEL_ELEMENTARY,
                \App\Models\Enrollment::TRACK_AE_JUNIOR_HIGH => Module::LEVEL_JUNIOR_HIGH,
                default => null,
            };
        }

        $strands = LearningStrand::withCount([
            'publishedModules' => function ($q) use ($enrolledLevel) {
                if ($enrolledLevel) {
                    $q->where('level', $enrolledLevel);
                }
            }
        ])
            ->orderBy('order_seq')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $strands,
        ]);
    }
}
