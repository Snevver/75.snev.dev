<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class ChallengeService
{
    public function todayFor(User $user): CarbonImmutable
    {
        return CarbonImmutable::now($user->timezone)->startOfDay();
    }

    public function currentDay(User $user): int
    {
        if (! $user->challenge_start_date) {
            return 1;
        }

        $start = CarbonImmutable::parse($user->challenge_start_date, $user->timezone)->startOfDay();
        $today = $this->todayFor($user);

        return max(1, min(75, $start->diffInDays($today) + 1));
    }

    public function logForDate(User $user, CarbonImmutable $date): DailyLog
    {
        $start = CarbonImmutable::parse($user->challenge_start_date ?? $date->toDateString(), $user->timezone)->startOfDay();
        $dayNumber = max(1, min(75, $start->diffInDays($date) + 1));
        $dateString = $date->toDateString();

        $existing = DailyLog::query()
            ->where('user_id', $user->id)
            ->whereDate('log_date', $dateString)
            ->first();

        if ($existing) {
            return $existing;
        }

        try {
            return DailyLog::create([
                'user_id' => $user->id,
                'log_date' => $dateString,
                'day_number' => $dayNumber,
            ]);
        } catch (QueryException $exception) {
            // Another request created the same row concurrently.
            if ((string) $exception->getCode() !== '23000') {
                throw $exception;
            }

            return DailyLog::query()
                ->where('user_id', $user->id)
                ->whereDate('log_date', $dateString)
                ->firstOrFail();
        }
    }

    public function recalculateCompletion(DailyLog $log): void
    {
        $dailyTaskIds = Task::active()->where('frequency', 'daily')->pluck('id');
        $completedCount = $log->taskCompletions()->whereIn('task_id', $dailyTaskIds)->count();
        $log->is_complete = $dailyTaskIds->count() > 0 && $completedCount === $dailyTaskIds->count();
        $log->save();
    }

    public function weeklyWeightliftingProgress(User $user, CarbonImmutable $date): array
    {
        $task = Task::active()->where('frequency', 'weekly')->orderBy('sort_order')->first();
        if (! $task) {
            return ['task' => null, 'completed' => 0, 'target' => 0, 'days' => []];
        }

        $startOfWeek = $date->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $date->endOfWeek(Carbon::SUNDAY);

        $logs = DailyLog::query()
            ->where('user_id', $user->id)
            ->whereBetween('log_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->with(['taskCompletions' => fn ($query) => $query->where('task_id', $task->id)])
            ->get()
            ->keyBy(fn (DailyLog $log) => $log->log_date->toDateString());

        $days = collect(range(0, 6))->map(function (int $offset) use ($startOfWeek, $logs) {
            $day = $startOfWeek->addDays($offset);
            $log = $logs->get($day->toDateString());

            return [
                'label' => $day->shortEnglishDayOfWeek,
                'date' => $day->toDateString(),
                'completed' => $log?->taskCompletions?->isNotEmpty() ?? false,
            ];
        });

        return [
            'task' => $task,
            'completed' => $days->where('completed', true)->count(),
            'target' => 3,
            'days' => $days->values(),
        ];
    }

    public function challengeStats(User $user): array
    {
        $logs = $user->dailyLogs()->get();
        $perfectDays = $logs->where('is_complete', true)->count();
        $photos = $user->progressPhotos()->count();
        $day = $this->currentDay($user);

        return [
            'perfect_days' => $perfectDays,
            'current_streak' => $this->currentStreak($logs),
            'photos_uploaded' => $photos,
            'days_remaining' => max(0, 75 - $day),
        ];
    }

    private function currentStreak(Collection $logs): int
    {
        $streak = 0;
        foreach ($logs->sortByDesc('log_date') as $log) {
            if (! $log->is_complete) {
                break;
            }
            $streak++;
        }

        return $streak;
    }
}
