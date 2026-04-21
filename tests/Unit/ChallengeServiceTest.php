<?php

namespace Tests\Unit;

use App\Models\DailyLog;
use App\Models\User;
use App\Services\ChallengeService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChallengeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_challenge_stats_use_existing_logs_when_start_date_is_missing(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-04-12 09:00:00', 'UTC'));

        $user = User::factory()->create([
            'timezone' => 'UTC',
            'challenge_start_date' => null,
        ]);

        DailyLog::create([
            'user_id' => $user->id,
            'log_date' => '2026-04-10',
            'day_number' => 1,
            'is_complete' => true,
        ]);

        DailyLog::create([
            'user_id' => $user->id,
            'log_date' => '2026-04-12',
            'day_number' => 2,
            'is_complete' => true,
        ]);

        $service = app(ChallengeService::class);
        $stats = $service->challengeStats($user);

        $this->assertSame(3, $service->currentDay($user));
        $this->assertSame(72, $stats['days_remaining']);
        $this->assertSame(2, $stats['perfect_days']);
        $this->assertSame(1, $stats['current_streak']);
    }

    public function test_log_for_date_continues_from_the_first_log_without_a_start_date(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-04-12 09:00:00', 'UTC'));

        $user = User::factory()->create([
            'timezone' => 'UTC',
            'challenge_start_date' => null,
        ]);

        DailyLog::create([
            'user_id' => $user->id,
            'log_date' => '2026-04-10',
            'day_number' => 1,
            'is_complete' => true,
        ]);

        $service = app(ChallengeService::class);
        $log = $service->logForDate($user, CarbonImmutable::parse('2026-04-12 00:00:00', 'UTC'));

        $this->assertSame(3, $log->day_number);
        $this->assertSame('2026-04-12', $log->log_date->toDateString());
    }
}