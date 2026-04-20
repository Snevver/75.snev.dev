<?php

use App\Http\Controllers\Admin\StatsController as AdminStatsController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OtherUsersController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/u/{user:username}', [PublicProfileController::class, 'show'])->name('public.profile');
Route::get('/photos/{photo}', [PhotoController::class, 'show'])->name('photos.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/overview', [OverviewController::class, 'index'])->name('overview.index');
    Route::get('/users', [OtherUsersController::class, 'index'])->name('users.index');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/photos', [PhotoController::class, 'index'])->name('photos.index');
    Route::post('/photos', [PhotoController::class, 'store'])->name('photos.store');
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
    Route::post('/logs/task', [DailyLogController::class, 'updateTask'])->name('logs.task.update');
    Route::post('/logs/notes', [DailyLogController::class, 'updateNotes'])->name('logs.notes.update');
    Route::post('/challenge/restart', [DailyLogController::class, 'restart'])->name('challenge.restart');
    Route::post('/challenge/reset', [DailyLogController::class, 'reset'])->name('challenge.reset');
    Route::get('/archive', [DailyLogController::class, 'archive'])->name('archive.index');
    Route::get('/archive/{archive}', [DailyLogController::class, 'show'])->name('archive.show');
    Route::get('/archive/photos/{day}', [DailyLogController::class, 'showArchivedPhoto'])->name('archive.photo');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/stats', AdminStatsController::class)->name('stats');
    Route::get('/tasks', [AdminTaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [AdminTaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [AdminTaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [AdminTaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::patch('/users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');
});

Route::get('/offline', fn() => Inertia::render('Offline'))->name('offline');

require __DIR__ . '/auth.php';
