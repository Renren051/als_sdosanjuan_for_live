<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModuleGameAttempt;
use App\Models\ModuleProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherProgressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = ModuleProgress::query()->with(['user.profile', 'module.strand']);

        if ($user->isMobileTeacher()) {
            $query->whereHas('user.enrollments', function (Builder $enrollments) use ($user) {
                $enrollments->where(function (Builder $assignment) use ($user) {
                    $assignment->where('assigned_teacher_id', $user->id)
                        ->orWhereHas('clc', fn (Builder $clcs) => $clcs->where('assigned_teacher_id', $user->id));
                });
            });
        }

        $search = trim($request->input('q', ''));
        if ($search !== '') {
            $query->where(function (Builder $matches) use ($search) {
                $matches->whereHas('module', fn (Builder $modules) => $modules->where('title', 'like', "%{$search}%"))
                    ->orWhereHas('user', function (Builder $users) use ($search) {
                        $users->where('username', 'like', "%{$search}%")
                            ->orWhereHas('profile', function (Builder $profiles) use ($search) {
                                $profiles->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $status = $request->input('status');
        if (in_array($status, [
            ModuleProgress::STATUS_DOWNLOADED,
            ModuleProgress::STATUS_IN_PROGRESS,
            ModuleProgress::STATUS_COMPLETED,
        ], true)) {
            $query->where('completion_status', $status);
        }

        $perPage = min(max((int) $request->input('per_page', 100), 1), 100);
        $rows = $query->orderByDesc('synced_at')->paginate($perPage);
        $pageRows = collect($rows->items());
        $userIds = $pageRows->pluck('user_id')->unique();
        $moduleIds = $pageRows->pluck('module_id')->unique();
        $attempts = ModuleGameAttempt::query()
            ->whereIn('user_id', $userIds)
            ->whereIn('module_id', $moduleIds)
            ->orderByDesc('attempted_at')
            ->get()
            ->unique(fn (ModuleGameAttempt $attempt) => $attempt->user_id . ':' . $attempt->module_id)
            ->keyBy(fn (ModuleGameAttempt $attempt) => $attempt->user_id . ':' . $attempt->module_id);

        $data = $pageRows->map(function (ModuleProgress $progress) use ($attempts) {
            $attempt = $attempts->get($progress->user_id . ':' . $progress->module_id);

            return [
                'learner_id' => $progress->user_id,
                'learner_name' => $progress->user?->full_name ?? 'Learner',
                'module_id' => $progress->module_id,
                'module_title' => $progress->module?->title ?? 'Module unavailable',
                'strand_title' => $progress->module?->strand?->title,
                'completion_status' => $progress->completion_status,
                'progress_percent' => $progress->progress_percent,
                'last_page_read' => $progress->last_page_read,
                'total_pages' => $progress->total_pages,
                'last_opened_at' => $progress->last_opened_at?->toIso8601String(),
                'synced_at' => $progress->synced_at?->toIso8601String(),
                'latest_game_score' => $attempt?->score,
                'latest_game_max_score' => $attempt?->max_score,
                'latest_game_attempted_at' => $attempt?->attempted_at?->toIso8601String(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
            ],
        ]);
    }
}
