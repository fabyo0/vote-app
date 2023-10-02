<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Idea extends Model
{
    use HasFactory;
    use Sluggable;

    const PAGINATION_COUNT = 10;

    protected $fillable = [
        'user_id',
        'category_id',
        'status_id',
        'title',
        'slug',
        'description'
    ];


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
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
        return Vote::query()->where('user_id', $user->id)
            ->where('idea_id', $this->id)
            ->exists();
    }

    /*   public function getStatusClasses(): string
       {
           $allowedStatus = [
               'Open' => 'bg-gray-200',
               'Considering' => 'bg-purple text-white',
               'In Progress' => 'bg-yellow text-white',
               'Implemented' => 'bg-green text-white',
               'Closed' => 'bg-red text-white'
           ];

           return $allowedStatus[$this->status->name];
       }*/

    /*  public function getRouteKeyName(): string
    {
    return 'slug';
    }*/

}
