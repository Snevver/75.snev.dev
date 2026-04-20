<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CalendarController extends Controller
{
    public function index(Request $request): Response
    {
        $month = $request->string('month')->toString() ?: now($request->user()->timezone)->format('Y-m');
        [$year, $monthNumber] = explode('-', $month);

        $logs = DailyLog::query()
            ->where('user_id', $request->user()->id)
            ->whereYear('log_date', (int) $year)
            ->whereMonth('log_date', (int) $monthNumber)
            ->with(['progressPhoto', 'taskCompletions.task'])
            ->get();

        return Inertia::render('Calendar/Index', [
            'month' => $month,
            'logs' => $logs,
        ]);
    }
}
