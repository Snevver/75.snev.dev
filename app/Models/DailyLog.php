<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    protected $fillable = [
        'user_id',
        'log_date',
        'day_number',
        'notes',
        'is_complete',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
            'is_complete' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taskCompletions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class);
    }

    public function progressPhoto(): HasOne
    {
        return $this->hasOne(ProgressPhoto::class);
    }
}
