<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OverviewController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $start = CarbonImmutable::parse($user->challenge_start_date ?? now($user->timezone)->toDateString(), $user->timezone)->startOfDay();
        $today = CarbonImmutable::now($user->timezone)->startOfDay();

        $logs = DailyLog::query()
            ->where('user_id', $user->id)
            ->with(['taskCompletions.task', 'progressPhoto'])
            ->get()
            ->keyBy(fn (DailyLog $log) => $log->log_date->toDateString());

        $days = collect(range(1, 75))->map(function (int $dayNumber) use ($start, $today, $logs) {
            $date = $start->addDays($dayNumber - 1);
            $log = $logs->get($date->toDateString());
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
                    'notes' => $log->notes,
                    'is_complete' => $log->is_complete,
                    'tasks' => $log->taskCompletions->map(fn ($completion) => [
                        'id' => $completion->task_id,
                        'title' => $completion->task?->title,
                        'completed_at' => optional($completion->completed_at)->toDateTimeString(),
                    ])->values(),
                    'photo_url' => $log->progressPhoto ? route('photos.show', $log->progressPhoto) : null,
                ] : null,
            ];
        })->values();

        return Inertia::render('Overview/Index', [
            'days' => $days,
            'today' => $today->toDateString(),
        ]);
    }
}
