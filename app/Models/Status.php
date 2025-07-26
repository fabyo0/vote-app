<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Status
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Idea> $ideas
 * @property-read int|null $ideas_count
 * @method static \Database\Factories\StatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Status extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'classes'];

    public static function getCount(): array
    {
        return Idea::query()
            ->selectRaw('count(*) as all_statues')
            ->selectRaw('count(case when status_id = 1 then 1 end) as open')
            ->selectRaw('count(case when status_id = 2 then 1 end) as considering ')
            ->selectRaw('count(case when status_id = 3 then 1 end) as in_progress')
            ->selectRaw('count(case when status_id = 4 then 1 end) as implemented')
            ->selectRaw('count(case when status_id = 5 then 1 end) as closes')
            ->first()
            ->toArray();
    }

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }
}
