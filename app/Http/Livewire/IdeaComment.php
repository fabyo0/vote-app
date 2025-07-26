<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;

class IdeaComment extends Component
{
    public $comment;

    public $ideaUserID;

    protected $listeners = [
        'commentWasUpdated',
        'commentWasMarkedAsSpam' => '$refresh',
        'commentWasMarkedAsNotSpam',
    ];

    public function commentWasUpdated(): void
    {
        $this->comment->refresh();
    }

    public function commentWasMarkedAsNotSpam(): void
    {
        $this->comment->refresh();
    }

    public function mount(Comment $comment, $ideaUserID): void
    {
        $this->comment = $comment;
        $this->ideaUserID = $ideaUserID;
    }

    public function render()
    {
        return view('livewire.idea-comment');
    }
}
