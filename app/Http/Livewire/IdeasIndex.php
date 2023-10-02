<?php

namespace App\Http\Livewire;

use App\Models\Idea;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class IdeasIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $ideas = Idea::query()
            ->addSelect(['voted_by_user' => Vote::query()->select('ideas.id')
                ->where('user_id', Auth::id())
                ->whereColumn('idea_id', 'ideas.id')
            ])
            ->with(['category', 'user', 'status'])
            ->withCount('votes')
            ->latest()
            ->simplePaginate(Idea::PAGINATION_COUNT);


        return view('livewire.ideas-index', [
            'ideas' => $ideas
        ]);
    }
}
