<?php

namespace Azuriom\Plugin\Polls\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOption extends Model
{
    protected $table = 'polls_poll_options';

    protected $fillable = [
        'poll_id',
        'label',
        'votes_count',
    ];

    protected $casts = [
        'votes_count' => 'integer',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function percentage(int $totalVotes): float
    {
        if ($totalVotes <= 0) {
            return 0;
        }

        return round(($this->votes_count / $totalVotes) * 100, 1);
    }
}
