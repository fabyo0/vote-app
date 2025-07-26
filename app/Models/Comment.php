<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'idea_id',
        'parent_id',      
        'status_id',
        'body',
        'is_status_update',
    ];

    protected $perPage = 7;

    protected $casts = [
        'is_status_update' => 'boolean',
    ];

    // ============================================
    // EXISTING RELATIONSHIPS
    // ============================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    // ============================================
    // NEW REPLY RELATIONSHIPS
    // ============================================

    /**
     * Parent comment (for replies)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Child comments (replies to this comment)
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
                    ->with(['user', 'status'])
                    ->latest();
    }

    // ============================================
    // QUERY SCOPES
    // ============================================

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
        return $query->with(['replies' => function ($query) {
            $query->with(['user', 'status'])->latest();
        }]);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Check if this comment is a reply
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if this comment has replies
     */
    public function hasReplies(): bool
    {
        return $this->replies()->count() > 0;
    }

    /**
     * Get reply count
     */
    public function getRepliesCountAttribute(): int
    {
        return $this->replies()->count();
    }
}
