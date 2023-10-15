<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class MarkCommentAsSpam extends Component
{

    public Comment $comment;


    public $body;


    protected $listeners = [
        'setMarkAsSpamComment'
    ];

    public function setMarkAsSpamComment($commentId): void
    {
        $this->comment = Comment::query()->findOrFail($commentId);

        $this->emit('markAsSpamCommentWasSet');
    }


    public function markAsSpam(): void
    {
        if (auth()->guest()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        //Increment spam report
        $this->comment->spam_reports++;
        $this->comment->save();

        $this->emit('commentWasMarkedAsSpam', 'Comment was marked as spam!');
    }


    public function render()
    {
        return view('livewire.mark-comment-as-spam');
    }
}
