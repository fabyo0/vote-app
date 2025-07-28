<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\IdeaStatus;
use App\Models\Category;
use App\Models\Idea;
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
        'refreshIdeas' => '$refresh',
        'ideaWasCreated' => 'handleIdeaWasCreated',
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
        if ('My Ideas' === $this->filter) {
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

    public function handleIdeaWasCreated($message): void
    {
        $this->resetPage();

        $this->render();
    }

    public function mount(): void
    {
        $this->status = request()->status ?? 'All';
    }

    public function render()
    {
        // Get categories for the filter dropdown
        $categories = Category::select('id', 'name')->get();

        // Build filters array
        $filters = $this->getFilters();

        // Get ideas using the model scope
        $ideas = Idea::forIndex($filters)
            ->simplePaginate()
            ->withQueryString();

        return view('livewire.ideas-index', [
            'ideas' => $ideas,
            'categories' => $categories,
        ]);
    }

    /**
     * Clear all filters
     */
    public function clearFilters(): void
    {
        $this->status = IdeaStatus::All->value;
        $this->category = 'All Categories';
        $this->filter = null;
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Set a specific filter
     */
    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    /**
     * Get current filters as array
     */
    private function getFilters(): array
    {
        return [
            'status' => $this->status,
            'category' => $this->category,
            'filter' => $this->filter,
            'search' => $this->search,
        ];
    }
}
