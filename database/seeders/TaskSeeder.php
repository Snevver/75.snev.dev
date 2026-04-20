<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            ['title' => 'Drink 2L of water', 'description' => null, 'frequency' => 'daily', 'icon' => '💧', 'sort_order' => 1],
            ['title' => 'Cardio for 45 minutes (minimum)', 'description' => null, 'frequency' => 'daily', 'icon' => '🏃', 'sort_order' => 2],
            ['title' => 'Eat clean (no cheat meals, no alcohol)', 'description' => null, 'frequency' => 'daily', 'icon' => '🥗', 'sort_order' => 3],
            ['title' => 'Get out of bed at your set wake time', 'description' => null, 'frequency' => 'daily', 'icon' => '⏰', 'sort_order' => 4],
            ['title' => 'Take a progress photo', 'description' => null, 'frequency' => 'daily', 'icon' => '📷', 'sort_order' => 5],
            ['title' => 'Take creatine', 'description' => null, 'frequency' => 'daily', 'icon' => '🧪', 'sort_order' => 6],
            ['title' => 'Read 10 pages of a non-fiction book', 'description' => null, 'frequency' => 'daily', 'icon' => '📚', 'sort_order' => 7],
            ['title' => 'Weightlifting session (3x/week)', 'description' => null, 'frequency' => 'weekly', 'icon' => '🏋️', 'sort_order' => 8],
        ];

        foreach ($tasks as $task) {
            Task::updateOrCreate(['title' => $task['title']], $task);
        }
    }
}
