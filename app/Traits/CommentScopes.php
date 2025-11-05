<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CommentScopes
{
    /**
     * Get only parent comments (not replies)
     */
    public function scopeParentOnly(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get only replies (child comments)
     */
    public function scopeRepliesOnly(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Get comments with their replies
     */
    public function scopeWithReplies(Builder $query): Builder
    {
        return $query->with(['replies' => function ($query): void {
            $query->with(['user', 'status'])->latest();
        }]);
    }

    /**
     * Get comments with spam reports
     */
    public function scopeSpamReported(Builder $query): Builder
    {
        return $query->where('spam_reports', '>', 0)
            ->orderByDesc('spam_reports');
    }

    /**
     * Get comments for a specific idea with full relationships
     */
    public function scopeForIdea(Builder $query, int $ideaId): Builder
    {
        return $query->where('idea_id', $ideaId)
            ->parentOnly()
            ->with(['user', 'status'])
            ->withCount('replies')
            ->latest();
    }
}
