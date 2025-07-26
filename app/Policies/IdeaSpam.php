<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;

class IdeaSpam
{
    public function markAsSpam(User $user, Idea $idea): bool
    {
        if ($user->id === $idea->user_id) {
            return false;
        }
        return true;
    }
}
