<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'idea_id'
    ];

/*    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function idea(): HasMany
    {
        return $this->hasMany(Idea::class);
    }*/
}
