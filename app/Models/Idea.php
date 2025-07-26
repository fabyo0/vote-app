<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\VoteNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Enums\IdeaStatus;
use Illuminate\Support\Str;


class Idea extends Model
{
    use HasFactory;

    protected $perPage = 10;

    protected $fillable = [
        'user_id',
        'category_id',
        'status_id',
        'title',
        'slug',
        'description',
        'spam_reports',
    ];


    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'votes');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function isVotedByUser(?User $user): bool
    {
        if ( ! $user) {
            return false;
        }

        return Vote::where('user_id', $user->id)
            ->where('idea_id', $this->id)
            ->exists();
    }

    public function vote(User $user): void
    {
        if ($this->isVotedByUser($user)) {
            return;
        }
        $this->votes()->attach($user->id);
    }

    /**
     * @throws VoteNotFoundException
     */
    public function removeVote(User $user): void
    {
        if ($this->isVotedByUser($user)) {
            return;
        }

        $this->votes()->detach($user);
    }



    // ============================================
    // QUERY SCOPES
    // ============================================

    /**
     * Filter ideas by status
     */
    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if (!$status || $status === IdeaStatus::All->value) {
            return $query;
        }

        // If using enum, convert status name to ID
        $statusId = Status::where('name', $status)->value('id');

        return $query->when($statusId, fn($q) => $q->where('status_id', $statusId));
    }

    /**
     * Filter ideas by category
     */
    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        if (!$category || $category === 'All Categories') {
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
        if (!$search || strlen($search) < 3) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
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
        $userId = $userId ?? Auth::id();

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
        return $query->whereHas('comments', function ($q) {
            $q->where('spam_reports', '>', 0);
        })->orderByDesc('created_at');
    }

    /**
     * Add user vote information
     */
    public function scopeWithUserVote(Builder $query, ?int $userId = null): Builder
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return $query;
        }

        return $query->addSelect([
            'voted_by_user' => Vote::select('idea_id')
                ->where('user_id', $userId)
                ->whereColumn('idea_id', 'ideas.id')
                ->limit(1)
        ]);
    }

    /**
     * Apply filter based on filter type
     */
    public function scopeApplyFilter(Builder $query, ?string $filter): Builder
    {
        return match($filter) {
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

    protected static function booted(): void
    {
        static::creating(function (Idea $idea) {
            $idea->slug = static::generateUniqueSlug($idea->title);
        });

        static::updating(function (Idea $idea) {
            if ($idea->isDirty('title')) {
                $idea->slug = static::generateUniqueSlug($idea->title, $idea->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = \Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

}
