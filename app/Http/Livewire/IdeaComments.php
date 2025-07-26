<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Idea;
use Livewire\Component;
use Livewire\WithPagination;

class IdeaComments extends Component
{
    use WithPagination;

    public $idea;

    protected $listeners = [
        'commentWasAdded' => '$refresh',
        'commentWasDeleted' => '$refresh',
        'statusWasUpdating' => '$refresh',
    ];

    public function mount(Idea $idea): void
    {
        $this->idea = $idea;
    }

    public function commentWasDeleted(): void
    {
        $this->idea->refresh();
        $this->goToPage(1);
    }

    public function statusWasUpdating(): void
    {
        $this->idea->refresh();
        $this->goToPage(1);
    }

    public function commentWasAdded(): void
    {
        $this->idea->refresh();
        $this->goToPage($this->idea->comments()->paginate()->lastPage());
    }

    public function render()
    {
        return view('livewire.idea-comments', [
            'comments' => Comment::with(['user', 'status'])
                ->where('idea_id', $this->idea->id)->paginate(),
        ]);
    }
}
