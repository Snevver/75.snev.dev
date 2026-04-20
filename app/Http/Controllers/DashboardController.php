<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\ChallengeService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(ChallengeService $challengeService): Response
    {
        $user = auth()->user();
        $today = $challengeService->todayFor($user);
        $log = $challengeService->logForDate($user, $today)->load(['taskCompletions', 'progressPhoto']);
        $tasks = Task::active()->orderBy('sort_order')->get();
        $completedTaskIds = $log->taskCompletions->pluck('task_id')->all();

        return Inertia::render('Dashboard', [
            'currentDay' => $challengeService->currentDay($user),
            'today' => $today->toDateString(),
            'todayNotes' => $log->notes,
            'challengeFailed' => (bool) $user->challenge_failed_at,
            'tasks' => $tasks,
            'completedTaskIds' => $completedTaskIds,
            'weeklyProgress' => $challengeService->weeklyWeightliftingProgress($user, $today),
            'stats' => $challengeService->challengeStats($user),
            'todayPhoto' => $log->progressPhoto ? [
                'id' => $log->progressPhoto->id,
                'url' => route('photos.show', $log->progressPhoto),
                'taken_at' => optional($log->progressPhoto->taken_at)->toDateTimeString(),
            ] : null,
        ]);
    }
}
