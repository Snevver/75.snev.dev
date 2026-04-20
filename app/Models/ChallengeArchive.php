<?php

namespace App\Models;

use App\Models\ChallengeArchiveDay;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChallengeArchive extends Model
{
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'completed_at',
        'days_logged',
        'perfect_days',
        'current_streak',
        'photos_uploaded',
        'days_remaining',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(ChallengeArchiveDay::class);
    }
}
