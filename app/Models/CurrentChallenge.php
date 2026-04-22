<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrentChallenge extends Model
{
    protected $fillable = [
        'user_id',
        'start_date',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'failed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}