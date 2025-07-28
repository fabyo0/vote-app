<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;

class IdeaComment extends Component
{
    public $comment;
    public $ideaUserID;
    public $showReplyForm = false;

    protected $listeners = [
        'commentWasUpdated',
        'commentWasMarkedAsSpam' => '$refresh',
        'commentWasMarkedAsNotSpam',
        'replyWasAdded' => 'handleReplyAdded',
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

    public function toggleReplyForm()
    {
        if (auth()->guest()) {
            return redirect()->route('login');
        }

        $this->showReplyForm = ! $this->showReplyForm;
    }

    public function handleReplyAdded(): void
    {
        $this->showReplyForm = false;
        $this->comment->refresh();
        $this->comment->load('replies.user', 'replies.status');
    }

    public function render()
    {
        return view('livewire.idea-comment');
    }
}
