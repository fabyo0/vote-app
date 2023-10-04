<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class IdeasIndex extends Component
{
    use WithPagination;

    public $status;
    public $category;


    protected $queryString = [
        'status',
        'category'
    ];

    protected $listeners = ['queryStringUpdatedStatus'];


    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function queryStringUpdatedStatus($newStatus): void
    {
        $this->resetPage();
        $this->status = $newStatus;
    }

    public function mount(): void
    {
        $this->status = request()->status ?? 'All';
       // $this->category = request()->category ?? 'All Categories';
    }


    public function render()
    {
        $statues = Status::all()->pluck('id', 'name');
        $categories = Category::all();


        $ideas = Idea::query()
            ->when($this->status && $this->status !== 'All', function ($query) use ($statues) {
                $query->where('status_id', $statues->get($this->status));
            })->when($this->category && $this->category !== 'All Categories', function ($query) use ($categories) {
                $query->where('category_id', $categories->pluck('id', 'name')->get($this->category));
            })
            ->addSelect(['voted_by_user' => Vote::query()->select('ideas.id')
                ->where('user_id', Auth::id())
                ->whereColumn('idea_id', 'ideas.id')
            ])
            ->with(['category', 'user', 'status'])
            ->withCount('votes')
            ->latest()
            ->simplePaginate(Idea::PAGINATION_COUNT);


        return view('livewire.ideas-index', [
            'ideas' => $ideas,
            'categories' => $categories
        ]);
    }
}
