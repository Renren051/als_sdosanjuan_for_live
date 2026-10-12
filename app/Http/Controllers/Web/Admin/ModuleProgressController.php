<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ModuleGameAttempt;
use App\Models\ModuleProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ModuleProgressController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = $this->progressQuery($user, $request);

        $summary = [
            'learners' => (clone $query)->distinct('user_id')->count('user_id'),
            'modules' => (clone $query)->count(),
            'completed' => (clone $query)->where('completion_status', ModuleProgress::STATUS_COMPLETED)->count(),
            'in_progress' => (clone $query)->where('completion_status', ModuleProgress::STATUS_IN_PROGRESS)->count(),
            'downloaded' => (clone $query)->where('completion_status', ModuleProgress::STATUS_DOWNLOADED)->count(),
        ];

        $progressRows = $query
            ->orderByDesc('synced_at')
            ->paginate(20)
            ->withQueryString();

        $latestAttempts = $this->latestAttemptsFor($progressRows->getCollection());

        return view('admin.module-progress.index', [
            'progressRows' => $progressRows,
            'latestAttempts' => $latestAttempts,
            'summary' => $summary,
            'isTeacher' => $user->isMobileTeacher(),
            'search' => $request->string('q')->toString(),
            'status' => $request->string('status')->toString(),
        ]);
    }

    private function progressQuery(User $user, Request $request): Builder
    {
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

        return $query;
    }

    private function latestAttemptsFor(Collection $progressRows): Collection
    {
        if ($progressRows->isEmpty()) {
            return collect();
        }

        $userIds = $progressRows->pluck('user_id')->unique();
        $moduleIds = $progressRows->pluck('module_id')->unique();

        return ModuleGameAttempt::query()
            ->whereIn('user_id', $userIds)
            ->whereIn('module_id', $moduleIds)
            ->orderByDesc('attempted_at')
            ->get()
            ->unique(fn (ModuleGameAttempt $attempt) => $attempt->user_id . ':' . $attempt->module_id)
            ->keyBy(fn (ModuleGameAttempt $attempt) => $attempt->user_id . ':' . $attempt->module_id);
    }
}
