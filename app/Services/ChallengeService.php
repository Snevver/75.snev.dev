<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;

class ChallengeService
{
    public function todayFor(User $user): CarbonImmutable
    {
        return CarbonImmutable::now($user->timezone)->startOfDay();
    }

    public function currentDay(User $user): int
    {
        $today = $this->todayFor($user);
        $start = $this->challengeStartDateFor($user, $today);
        $loggedDay = $this->loggedDayFor($user, $today);
        $elapsedDay = (int) max(1, min(75, $start->diffInDays($today) + 1));

        return max($elapsedDay, $loggedDay);
    }

    public function logForDate(User $user, CarbonImmutable $date): DailyLog
    {
        $start = $this->challengeStartDateFor($user, $date);
        $dayNumber = (int) max(1, min(75, $start->diffInDays($date) + 1));
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
        $perfectDays = $user->dailyLogs()->where('is_complete', true)->count();
        $photos = $user->progressPhotos()->count();
        $day = $this->currentDay($user);

        return [
            'perfect_days' => $perfectDays,
            'current_streak' => $this->currentStreak($user),
            'photos_uploaded' => $photos,
            'days_remaining' => (int) max(0, 75 - $day),
        ];
    }

    private function currentStreak(User $user): int
    {
        $today = $this->todayFor($user);
        $start = $this->challengeStartDateFor($user, $today);
        $challengeEnd = $start->addDays(74);
        $end = $today->lessThan($challengeEnd) ? $today : $challengeEnd;

        $logs = $user->dailyLogs()
            ->whereBetween('log_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('log_date')
            ->get()
            ->keyBy(fn (DailyLog $log) => $log->log_date->toDateString());

        $latestLog = $logs->first();

        if (! $latestLog) {
            return 0;
        }

        $streak = 0;
        for ($date = $latestLog->log_date->startOfDay(); $date->greaterThanOrEqualTo($start); $date = $date->subDay()) {
            $log = $logs->get($date->toDateString());

            if (! $log?->is_complete) {
                break;
            }
            $streak++;
        }

        return $streak;
    }

    private function loggedDayFor(User $user, CarbonImmutable $today): int
    {
        $loggedDays = $user->dailyLogs()
            ->whereDate('log_date', '<', $today->toDateString())
            ->count();

        return (int) max(1, min(75, $loggedDays + 1));
    }

    private function challengeStartDateFor(User $user, CarbonImmutable $fallbackDate): CarbonImmutable
    {
        if ($user->challenge_start_date) {
            return CarbonImmutable::parse($user->challenge_start_date, $user->timezone)->startOfDay();
        }

        $firstLogDate = $user->dailyLogs()->orderBy('log_date')->value('log_date');

        if ($firstLogDate) {
            return CarbonImmutable::parse($firstLogDate, $user->timezone)->startOfDay();
        }

        return $fallbackDate->startOfDay();
    }
}
