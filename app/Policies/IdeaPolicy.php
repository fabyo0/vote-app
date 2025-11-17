<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IdeaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Idea $idea)
    {
        if ($user->id !== $idea->user_id) {
            return false;
        }

        $oneHourAgo = now()->subHour();

        return $idea->created_at->greaterThanOrEqualTo($oneHourAgo);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Idea $idea): bool
    {
        return $idea->user_id === $user->id || $user->isAdmin();
    }
}
