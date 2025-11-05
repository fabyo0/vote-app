<?php

declare(strict_types=1);

namespace App\Traits;

use App\Exceptions\VoteNotFoundException;
use App\Models\User;
use App\Models\Vote;

trait Votable
{
    /**
     * Check if idea is voted by user
     */
    public function isVotedByUser(?User $user): bool
    {
        if ( ! $user) {
            return false;
        }

        return Vote::where('user_id', $user->id)
            ->where('idea_id', $this->id)
            ->exists();
    }

    /**
     * Add a vote to the idea
     */
    public function vote(User $user): void
    {
        if ($this->isVotedByUser($user)) {
            return;
        }

        $this->votes()->attach($user->id);
    }

    /**
     * Remove a vote from the idea
     *
     * @throws VoteNotFoundException
     */
    public function removeVote(User $user): void
    {
        if ( ! $this->isVotedByUser($user)) {
            throw new VoteNotFoundException();
        }

        $this->votes()->detach($user->id);
    }
}
