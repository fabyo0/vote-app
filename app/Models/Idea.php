<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\IdeaStatus;
use App\Exceptions\VoteNotFoundException;
use App\Observers\IdeaObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * App\Models\Idea
 *
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property int $status_id
 * @property int $spam_reports
 * @property string $title
 * @property string|null $slug
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read Status|null $status
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $votes
 * @property-read int|null $votes_count
 * @method static Builder|Idea applyFilter(?string $filter)
 * @method static Builder|Idea byCategory(?string $category)
 * @method static Builder|Idea byStatus(?string $status)
 * @method static Builder|Idea byUser(?int $userId = null)
 * @method static \Database\Factories\IdeaFactory factory($count = null, $state = [])
 * @method static Builder|Idea forIndex(array $filters = [])
 * @method static Builder|Idea newModelQuery()
 * @method static Builder|Idea newQuery()
 * @method static Builder|Idea query()
 * @method static Builder|Idea search(?string $search)
 * @method static Builder|Idea spamComments()
 * @method static Builder|Idea spamIdeas()
 * @method static Builder|Idea topVoted()
 * @method static Builder|Idea whereCategoryId($value)
 * @method static Builder|Idea whereCreatedAt($value)
 * @method static Builder|Idea whereDescription($value)
 * @method static Builder|Idea whereId($value)
 * @method static Builder|Idea whereSlug($value)
 * @method static Builder|Idea whereSpamReports($value)
 * @method static Builder|Idea whereStatusId($value)
 * @method static Builder|Idea whereTitle($value)
 * @method static Builder|Idea whereUpdatedAt($value)
 * @method static Builder|Idea whereUserId($value)
 * @method static Builder|Idea withUserVote(?int $userId = null)
 * @mixin \Eloquent
 */
class Idea extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

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
        if ( ! $this->isVotedByUser($user)) {
            throw new VoteNotFoundException();
        }

        $this->votes()->detach($user->id);
    }


    // ============================================
    // QUERY SCOPES
    // ============================================

    /**
     * Filter ideas by status
     */
    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if ( ! $status || $status === IdeaStatus::All->value) {
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
        if ( ! $category || 'All Categories' === $category) {
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
        if ( ! $search || mb_strlen($search) < 3) {
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

        if ( ! $userId) {
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

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(150)
            ->height(150)
            ->sharpen(10);
    }


    protected static function booted(): void
    {
        Idea::observe(IdeaObserver::class);
    }
}
