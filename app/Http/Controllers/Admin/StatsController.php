<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StatsController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $today = now($request->user()->timezone)->toDateString();

        return Inertia::render('Admin/Stats', [
            'stats' => [
                'total_users' => User::count(),
                'active_challenges' => User::whereNotNull('challenge_start_date')->whereNull('challenge_failed_at')->count(),
                'tasks_completed_today' => DailyLog::whereDate('log_date', $today)->where('is_complete', true)->count(),
            ],
        ]);
    }
}
