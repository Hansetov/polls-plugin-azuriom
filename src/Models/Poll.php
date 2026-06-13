<?php

namespace Azuriom\Plugin\Polls\Models;

use Azuriom\Models\Traits\Loggable;
use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Poll extends Model
{
    use Loggable;

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    protected $table = 'polls_polls';

    protected $fillable = [
        'title',
        'description',
        'status',
        'multiple_choice',
    ];

    protected $casts = [
        'multiple_choice' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes(): HasManyThrough
    {
        return $this->hasManyThrough(PollVote::class, PollOption::class);
    }

    public function hasVoted(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return PollVote::query()
            ->where('poll_id', $this->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function totalVotes(): int
    {
        return $this->options->sum('votes_count');
    }
}
