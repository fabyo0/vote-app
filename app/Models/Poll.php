<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'idea_id',
        'question',
        'options',
        'is_active',
        'ends_at',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
        'ends_at' => 'datetime',
    ];

    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function voters(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'poll_votes')
            ->withPivot('option_index')
            ->withTimestamps();
    }

    public function hasVotedByUser(?User $user): bool
    {
        if (!$user instanceof \App\Models\User) {
            return false;
        }

        return $this->votes()->where('user_id', $user->id)->exists();
    }

    public function getVoteCounts(): array
    {
        $counts = [];
        $totalVotes = $this->votes()->count();

        foreach ($this->options as $index => $option) {
            $voteCount = $this->votes()->where('option_index', $index)->count();
            $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 1) : 0;
            
            $counts[$index] = [
                'option' => $option,
                'votes' => $voteCount,
                'percentage' => $percentage,
            ];
        }

        return $counts;
    }

    public function isExpired(): bool
    {
        if (!$this->ends_at) {
            return false;
        }

        return now()->isAfter($this->ends_at);
    }

    public function canVote(): bool
    {
        return $this->is_active && !$this->isExpired();
    }
}
