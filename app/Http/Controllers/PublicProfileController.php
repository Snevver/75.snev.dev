<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use App\Models\User;
use App\Services\ChallengeService;
use Carbon\CarbonImmutable;
use Inertia\Inertia;
use Inertia\Response;

class PublicProfileController extends Controller
{
    public function show(User $user, ChallengeService $challengeService): Response
    {
        abort_unless($user->is_public, 404);

        $start = CarbonImmutable::parse($user->challenge_start_date ?? now($user->timezone)->toDateString(), $user->timezone)->startOfDay();
        $today = CarbonImmutable::now($user->timezone)->startOfDay();

        $logs = DailyLog::query()
            ->where('user_id', $user->id)
            ->with(['taskCompletions.task', 'progressPhoto'])
            ->get()
            ->keyBy(fn(DailyLog $log) => $log->day_number);

        $days = collect(range(1, 75))->map(function (int $dayNumber) use ($start, $today, $logs, $user) {
            $date = $start->addDays($dayNumber - 1);
            $log = $logs->get($dayNumber);
            $isPast = $date->lt($today);
            $isToday = $date->isSameDay($today);

            $status = 'upcoming';
            if ($log?->is_complete) {
                $status = 'perfect';
            } elseif ($log) {
                $status = 'partial';
            } elseif ($isPast) {
                $status = 'missed';
            } elseif ($isToday) {
                $status = 'today';
            }

            return [
                'day_number' => $dayNumber,
                'date' => $date->toDateString(),
                'status' => $status,
                'details' => $log ? [
                    'is_complete' => $log->is_complete,
                    'tasks' => $log->taskCompletions->map(fn($completion) => [
                        'id' => $completion->task_id,
                        'title' => $completion->task?->title,
                        'completed_at' => optional($completion->completed_at)->toDateTimeString(),
                    ])->values(),
                    'photo_url' => ($user->share_public_photos && $log->progressPhoto?->is_public)
                        ? route('photos.show', $log->progressPhoto)
                        : null,
                ] : null,
            ];
        })->values();

        return Inertia::render('PublicProfile/Show', [
            'profile' => [
                'name' => $user->name,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'challenge_start_date' => optional($user->challenge_start_date)->toDateString(),
                // Keep stats for older built bundles that still render stat cards.
                'stats' => $challengeService->challengeStats($user),
            ],
            'days' => $days,
            'today' => $today->toDateString(),
        ]);
    }
}
