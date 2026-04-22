<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OverviewControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_overview_keeps_previous_day_data_visible_by_date(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-04-22 09:00:00', 'UTC'));

        $user = User::factory()->create([
            'timezone' => 'UTC',
            'challenge_start_date' => '2026-04-20',
        ]);

        $task = Task::create([
            'title' => 'Morning run',
            'description' => null,
            'frequency' => 'daily',
            'icon' => '🏃',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $dayTwoLog = DailyLog::create([
            'user_id' => $user->id,
            'log_date' => '2026-04-21',
            'day_number' => 1,
            'is_complete' => true,
        ]);

        $dayTwoLog->taskCompletions()->create([
            'task_id' => $task->id,
            'completed_at' => '2026-04-21 09:15:00',
        ]);

        DailyLog::create([
            'user_id' => $user->id,
            'log_date' => '2026-04-22',
            'day_number' => 2,
            'is_complete' => false,
        ]);

        $response = $this->actingAs($user)
            ->withHeader('X-Inertia', 'true')
            ->get(route('overview.index'));

        $response->assertOk();
        $response->assertJsonPath('component', 'Overview/Index');
        $response->assertJsonPath('props.days.1.date', '2026-04-21');
        $response->assertJsonPath('props.days.1.details.is_complete', true);
        $response->assertJsonPath('props.days.1.details.tasks.0.title', $task->title);
        $response->assertJsonPath('props.days.2.date', '2026-04-22');
    }
}