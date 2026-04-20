<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ProgressPhoto extends Model
{
    protected $fillable = [
        'user_id',
        'daily_log_id',
        'file_path',
        'taken_at',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'datetime',
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyLog(): BelongsTo
    {
        return $this->belongsTo(DailyLog::class);
    }
}
