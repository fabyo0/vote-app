<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasReplies
{
    /**
     * Child comments (replies to this comment)
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with(['user', 'status'])
            ->latest();
    }

    /**
     * Check if this comment is a reply
     */
    public function isReply(): bool
    {
        return null !== $this->parent_id;
    }

    /**
     * Check if this comment has replies
     */
    public function hasReplies(): bool
    {
        return $this->replies()->exists();
    }

    /**
     * Get reply count
     */
    public function getRepliesCountAttribute(): int
    {
        return $this->replies_count ?? $this->replies()->count();
    }
}
