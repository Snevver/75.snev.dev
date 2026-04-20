<?php

namespace App\Http\Controllers;

use App\Models\ChallengeArchive;
use App\Models\ChallengeArchiveDay;
use App\Models\Task;
use App\Models\TaskCompletion;
use App\Models\User;
use App\Services\ChallengeService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DailyLogController extends Controller
{
    public function updateTask(Request $request, ChallengeService $challengeService): JsonResponse
    {
        $data = $request->validate([
            'task_id' => ['required', Rule::exists('tasks', 'id')],
            'completed' => ['required', 'boolean'],
            'log_date' => ['nullable', 'date'],
        ]);

        $user = $request->user();
        $date = CarbonImmutable::parse($data['log_date'] ?? now(), $user->timezone)->startOfDay();
        $log = $challengeService->logForDate($user, $date);
        $task = Task::findOrFail($data['task_id']);

        if ($data['completed']) {
            TaskCompletion::updateOrCreate(
                ['daily_log_id' => $log->id, 'task_id' => $task->id],
                ['completed_at' => now()]
            );
        } else {
            TaskCompletion::query()
                ->where('daily_log_id', $log->id)
                ->where('task_id', $task->id)
                ->delete();
        }

        $challengeService->recalculateCompletion($log);

        return response()->json(['ok' => true, 'is_complete' => $log->fresh()->is_complete]);
    }

    public function updateNotes(Request $request, ChallengeService $challengeService): JsonResponse
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string'],
            'log_date' => ['nullable', 'date'],
        ]);

        $user = $request->user();
        $date = CarbonImmutable::parse($data['log_date'] ?? now(), $user->timezone)->startOfDay();
        $log = $challengeService->logForDate($user, $date);
        $log->notes = $data['notes'];
        $log->save();

        return response()->json(['ok' => true]);
    }

    public function restart(Request $request, ChallengeService $challengeService): JsonResponse
    {
        $user = $request->user();

        $this->archiveCurrentChallenge($user, $challengeService);

        $user->challenge_start_date = now($user->timezone)->toDateString();
        $user->challenge_failed_at = null;
        $user->save();
        $user->dailyLogs()->delete();

        return response()->json(['ok' => true]);
    }

    public function reset(Request $request, ChallengeService $challengeService): JsonResponse
    {
        $user = $request->user();

        $this->archiveCurrentChallenge($user, $challengeService);

        $user->challenge_start_date = now($user->timezone)->toDateString();
        $user->challenge_failed_at = null;
        $user->save();
        $user->dailyLogs()->delete();

        return response()->json(['ok' => true, 'message' => 'Challenge reset to day 1']);
    }

    public function archive(Request $request): Response
    {
        $user = $request->user();

        $completedChallenges = $user->challengeArchives()
            ->latest('created_at')
            ->get()
            ->map(fn(ChallengeArchive $archive) => [
                'id' => $archive->id,
                'start_date' => optional($archive->start_date)->toDateString(),
                'end_date' => optional($archive->end_date)->toDateString(),
                'completed_at' => optional($archive->completed_at)->toDateString(),
                'perfect_days' => $archive->perfect_days,
                'current_streak' => $archive->current_streak,
                'photos_uploaded' => $archive->photos_uploaded,
                'days_remaining' => $archive->days_remaining,
                'overview_url' => route('archive.show', $archive),
            ])
            ->values();

        return Inertia::render('Archive/Index', [
            'completedChallenges' => $completedChallenges,
        ]);
    }

    public function show(ChallengeArchive $archive, Request $request): Response
    {
        abort_unless($archive->user_id === $request->user()->id, 403);

        $days = $archive->days()
            ->orderBy('day_number')
            ->get()
            ->map(fn(ChallengeArchiveDay $day) => [
                'day_number' => $day->day_number,
                'date' => optional($day->log_date)->toDateString(),
                'status' => $day->status,
                'details' => [
                    'is_complete' => $day->is_complete,
                    'notes' => $day->notes,
                    'tasks' => collect($day->completed_tasks ?? [])->map(fn($title, $index) => [
                        'id' => $index + 1,
                        'title' => $title,
                        'completed_at' => null,
                    ])->values(),
                    'photo_url' => $day->photo_path ? route('archive.photo', $day) : null,
                ],
            ])
            ->values();

        return Inertia::render('Archive/Show', [
            'archive' => [
                'id' => $archive->id,
                'start_date' => optional($archive->start_date)->toDateString(),
                'end_date' => optional($archive->end_date)->toDateString(),
                'completed_at' => optional($archive->completed_at)->toDateString(),
            ],
            'stats' => [
                'perfect_days' => $archive->perfect_days,
                'current_streak' => $archive->current_streak,
                'photos_uploaded' => $archive->photos_uploaded,
                'days_remaining' => $archive->days_remaining,
            ],
            'days' => $days,
        ]);
    }

    public function showArchivedPhoto(ChallengeArchiveDay $day, Request $request): BinaryFileResponse
    {
        abort_unless($day->archive->user_id === $request->user()->id, 403);
        abort_unless($day->photo_path, 404);

        return response()->file(Storage::disk('local')->path($day->photo_path));
    }

    private function archiveCurrentChallenge(User $user, ChallengeService $challengeService): ?ChallengeArchive
    {
        $logs = $user->dailyLogs()
            ->with(['taskCompletions.task', 'progressPhoto'])
            ->orderBy('day_number')
            ->get()
            ->keyBy('day_number');

        if ($logs->isEmpty()) {
            return null;
        }

        $start = CarbonImmutable::parse($user->challenge_start_date ?? $logs->first()->log_date?->toDateString() ?? now()->toDateString(), $user->timezone)->startOfDay();
        $today = CarbonImmutable::now($user->timezone)->startOfDay();
        $stats = $challengeService->challengeStats($user);

        return DB::transaction(function () use ($user, $logs, $start, $today, $stats) {
            $archive = ChallengeArchive::create([
                'user_id' => $user->id,
                'start_date' => $start->toDateString(),
                'end_date' => $today->toDateString(),
                'completed_at' => now(),
                'days_logged' => min(75, $logs->count()),
                'perfect_days' => (int) ($stats['perfect_days'] ?? 0),
                'current_streak' => (int) ($stats['current_streak'] ?? 0),
                'photos_uploaded' => (int) ($stats['photos_uploaded'] ?? 0),
                'days_remaining' => (int) ($stats['days_remaining'] ?? 0),
            ]);

            foreach (range(1, 75) as $dayNumber) {
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

                ChallengeArchiveDay::create([
                    'challenge_archive_id' => $archive->id,
                    'day_number' => $dayNumber,
                    'log_date' => $date->toDateString(),
                    'status' => $status,
                    'is_complete' => (bool) ($log?->is_complete),
                    'notes' => $log?->notes,
                    'completed_tasks' => $log
                        ? $log->taskCompletions->map(fn($completion) => $completion->task?->title)->filter()->values()->all()
                        : [],
                    'photo_path' => $log?->progressPhoto?->file_path,
                ]);
            }

            return $archive;
        });
    }
}
