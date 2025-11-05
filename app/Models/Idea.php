<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\IdeaObserver;
use App\Traits\IdeaScopes;
use App\Traits\Votable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @mixin \Eloquent
 */
class Idea extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use IdeaScopes;
    use Votable;

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
