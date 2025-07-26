<?php

namespace App\Observers;

use App\Models\Idea;
use Illuminate\Support\Str;

final class IdeaObserver
{
    public function creating(Idea $idea): void
    {
        $idea->slug = $this->generateUniqueSlug($idea->title);
    }

    public function updating(Idea $idea): void
    {
        if ($idea->isDirty('title')) {
            $idea->slug = $this->generateUniqueSlug($idea->title, $idea->id);
        }
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Idea::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
