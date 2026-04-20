<?php

namespace App\Models;

use App\Models\ChallengeArchive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeArchiveDay extends Model
{
    protected $fillable = [
        'challenge_archive_id',
        'day_number',
        'log_date',
        'status',
        'is_complete',
        'notes',
        'completed_tasks',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
            'is_complete' => 'boolean',
            'completed_tasks' => 'array',
        ];
    }

    public function archive(): BelongsTo
    {
        return $this->belongsTo(ChallengeArchive::class, 'challenge_archive_id');
    }
}
