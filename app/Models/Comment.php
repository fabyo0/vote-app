<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'idea_id',
        'body'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }

}
