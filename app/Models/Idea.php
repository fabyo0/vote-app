<?php

namespace App\Models;

use App\Exceptions\VoteNotFoundException;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Idea extends Model
{
    use HasFactory;
    use Sluggable;

    const PAGINATION_COUNT = 15;

    protected $fillable = [
        'user_id',
        'category_id',
        'status_id',
        'title',
        'slug',
        'description',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

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

    public function isVotedByUser(?User $user): bool
    {
        if (! $user) {
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
    /*  public function removeVote(User $user): void
      {
          $vote = Vote::query()
              ->where('user_id', $user->id)
              ->where('idea_id', $this->id)
              ->first();

          if ($vote) {
              $vote->delete();
          }
          else{
              throw new VoteNotFoundException;
          }
      }*/

    public function removeVote(User $user): void
    {
        if ($this->isVotedByUser($user)) {
            return;
        }

        $this->votes()->detach($user);
    }
}
