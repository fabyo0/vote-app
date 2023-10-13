<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;

class IdeaComment extends Component
{
    public $comment;
    public $ideaUserID;


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
