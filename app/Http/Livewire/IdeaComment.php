<?php

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
      //  $this->goToPage(1);
    }


    public function commentWasMarkedAsNotSpam(): void
    {
        $this->comment->refresh();
    }

    public function mount(Comment $comment, $ideaUserID)
    {
        $this->comment = $comment;
        $this->ideaUserID = $ideaUserID;
    }


    public function render()
    {
        return view('livewire.idea-comment');
    }
}
