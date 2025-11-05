<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\IdeaStatus;
use App\Models\Category;
use App\Models\Status;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait IdeaScopes
{
    /**
     * Filter ideas by status
     */
    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if (!$status || $status === IdeaStatus::All->value) {
            return $query;
        }

        $statusId = Status::where('name', $status)->value('id');

        return $query->when($statusId, fn($q) => $q->where('status_id', $statusId));
    }

    /**
     * Filter ideas by category
     */
    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        if (!$category || 'All Categories' === $category) {
            return $query;
        }

        $categoryId = Category::where('name', $category)->value('id');

        return $query->when($categoryId, fn($q) => $q->where('category_id', $categoryId));
    }

    /**
     * Search ideas by title
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search || mb_strlen($search) < 3) {
            return $query;
        }

        return $query->where(function ($q) use ($search): void {
            $q->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    /**
     * Filter by top voted ideas
     */
    public function scopeTopVoted(Builder $query): Builder
    {
        return $query->orderByDesc('votes_count');
    }

    /**
     * Filter by user's own ideas
     */
    public function scopeByUser(Builder $query, ?int $userId = null): Builder
    {
        $userId ??= Auth::id();

        return $query->where('user_id', $userId)
            ->orderByDesc('created_at');
    }

    /**
     * Filter spam ideas
     */
    public function scopeSpamIdeas(Builder $query): Builder
    {
        return $query->where('spam_reports', '>', 0)
            ->orderByDesc('spam_reports');
    }

    /**
     * Filter ideas with spam comments
     */
    public function scopeSpamComments(Builder $query): Builder
    {
        return $query->whereHas('comments', function ($q): void {
            $q->where('spam_reports', '>', 0);
        })->orderByDesc('created_at');
    }

    /**
     * Add user vote information
     */
    public function scopeWithUserVote(Builder $query, ?int $userId = null): Builder
    {
        $userId ??= Auth::id();

        if (!$userId) {
            return $query;
        }

        return $query->addSelect([
            'voted_by_user' => Vote::select('idea_id')
                ->where('user_id', $userId)
                ->whereColumn('idea_id', 'ideas.id')
                ->limit(1),
        ]);
    }

    /**
     * Apply filter based on filter type
     */
    public function scopeApplyFilter(Builder $query, ?string $filter): Builder
    {
        return match ($filter) {
            'Top Voted' => $query->topVoted(),
            'My Ideas' => $query->byUser(),
            'Spam Ideas' => $query->spamIdeas(),
            'Spam Comments' => $query->spamComments(),
            default => $query->orderByDesc('created_at'),
        };
    }

    /**
     * Main scope for ideas index with all filters
     */
    public function scopeForIndex(Builder $query, array $filters = []): Builder
    {
        return $query
            ->byStatus($filters['status'] ?? null)
            ->byCategory($filters['category'] ?? null)
            ->search($filters['search'] ?? null)
            ->applyFilter($filters['filter'] ?? null)
            ->withUserVote()
            ->with(['category', 'user', 'status'])
            ->withCount(['votes', 'comments']);
    }
}
