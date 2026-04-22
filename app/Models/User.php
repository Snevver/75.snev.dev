<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'is_admin',
        'is_public',
        'share_public_photos',
        'timezone',
        'challenge_start_date',
        'challenge_failed_at',
        'deactivated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_public' => 'boolean',
            'share_public_photos' => 'boolean',
            'deactivated_at' => 'datetime',
        ];
    }

    protected array $currentChallengeAttributes = [];

    public function currentChallenge(): HasOne
    {
        return $this->hasOne(CurrentChallenge::class);
    }

    public function getChallengeStartDateAttribute(): ?CarbonInterface
    {
        return $this->currentChallenge?->start_date;
    }

    public function getChallengeFailedAtAttribute(): ?CarbonInterface
    {
        return $this->currentChallenge?->failed_at;
    }

    public function setChallengeStartDateAttribute($value): void
    {
        $this->queueCurrentChallengeAttribute('start_date', $value);
    }

    public function setChallengeFailedAtAttribute($value): void
    {
        $this->queueCurrentChallengeAttribute('failed_at', $value);
    }

    protected static function booted(): void
    {
        static::saved(function (self $user): void {
            if ($user->currentChallengeAttributes === []) {
                return;
            }

            $user->persistCurrentChallenge($user->currentChallengeAttributes);
            $user->currentChallengeAttributes = [];
        });
    }

    protected function queueCurrentChallengeAttribute(string $key, mixed $value): void
    {
        $this->currentChallengeAttributes[$key] = match ($key) {
            'start_date' => $value ? CarbonImmutable::parse($value, $this->timezone)->startOfDay() : null,
            'failed_at' => $value ? CarbonImmutable::parse($value, $this->timezone) : null,
            default => $value,
        };

        if ($this->exists && $this->getKey()) {
            $this->persistCurrentChallenge($this->currentChallengeAttributes);
            $this->currentChallengeAttributes = [];
        }
    }

    protected function persistCurrentChallenge(array $attributes): void
    {
        $startDate = array_key_exists('start_date', $attributes)
            ? $attributes['start_date']
            : $this->currentChallenge?->start_date;
        $failedAt = array_key_exists('failed_at', $attributes)
            ? $attributes['failed_at']
            : $this->currentChallenge?->failed_at;

        if (! $startDate && ! $failedAt) {
            $this->currentChallenge()->delete();
            $this->unsetRelation('currentChallenge');

            return;
        }

        $currentChallenge = CurrentChallenge::updateOrCreate(
            ['user_id' => $this->getKey()],
            [
                'start_date' => $startDate,
                'failed_at' => $failedAt,
            ]
        );

        $this->setRelation('currentChallenge', $currentChallenge);
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function progressPhotos(): HasMany
    {
        return $this->hasMany(ProgressPhoto::class);
    }

    public function challengeArchives(): HasMany
    {
        return $this->hasMany(ChallengeArchive::class);
    }

    public function getRouteKeyName(): string
    {
        return 'username';
    }
}
