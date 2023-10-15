<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithPagination;

class IdeasIndex extends Component
{
    use WithPagination;

    public $status;

    public $category;

    public $filter;

    public $search;

    protected $queryString = [
        'status',
        'category',
        'filter',
        'search' => ['except' => ''],
    ];

    protected $listeners = [
        'queryStringUpdatedStatus',
    ];

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter()
    {
        if ($this->filter === 'My Ideas') {
            if (auth()->guest()) {
                return Redirect::route('login');
            }
        }
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
        $categories = Category::query()->select('id', 'name')->get();

        $ideas = Idea::query()
            ->when($this->status && $this->status !== 'All', function ($query) use ($statues) {
                $query->where('status_id', $statues->get($this->status));
            })
            ->when($this->category && $this->category !== 'All Categories', function ($query) use ($categories) {
                $query->where('category_id', $categories->pluck('id', 'name')->get($this->category));
            })
            ->when($this->filter && $this->filter === 'Top Voted', function ($query) {
                $query->orderByDesc('votes_count');
            })
            ->when($this->filter && $this->filter === 'My Ideas', function ($query) {
                $query->where('user_id', auth()->id())->orderByDesc('created_at');
            })
            ->when($this->filter && $this->filter === 'Spam Ideas', function ($query) {
                $query->where('spam_reports', '>', 0)->orderByDesc('spam_reports');
            })
            ->when($this->filter && $this->filter === 'Spam Comments', function ($query) {
                $query->whereHas('comments', function ($query) {
                    $query->where('spam_reports', '>', 0)->orderByDesc('spam_reports');
                });
            })
            ->when(strlen($this->search) >= 3, function ($query) {
                return $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->addSelect(['voted_by_user' => Vote::query()->select('ideas.id')
                ->where('user_id', Auth::id())
                ->whereColumn('idea_id', 'ideas.id'),
            ])
            ->with(['category', 'user', 'status'])
            ->withCount(['votes','comments'])
            ->simplePaginate()
            ->withQueryString();

        return view('livewire.ideas-index', [
            'ideas' => $ideas,
            'categories' => $categories,
        ]);
    }
}
