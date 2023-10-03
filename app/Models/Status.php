<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'classes'];

    public static function getCount(): array
    {
        return Idea::query()
            ->selectRaw("count(*) as all_statues")
            ->selectRaw("count(case when status_id = 1 then 1 end) as Open")
            ->selectRaw("count(case when status_id = 2 then 2 end) as considering ")
            ->selectRaw("count(case when status_id = 3 then 3 end) as in_progress")
            ->selectRaw("count(case when status_id = 4 then 4 end) as implemented")
            ->selectRaw("count(case when status_id = 5 then 5 end) as closes")
            ->first()
            ->toArray();
    }

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }
}
