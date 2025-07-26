<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Symfony\Component\HttpFoundation\Response;

class MarkCommentAsSpam extends Component
{
    public Comment $comment;

    public $body;

    protected $listeners = [
        'setMarkAsSpamComment',
    ];

    public function setMarkAsSpamComment($commentId): void
    {
        $this->comment = Comment::query()->findOrFail($commentId);

        $this->emit('markAsSpamCommentWasSet');
    }

    public function markAsSpam(): void
    {
        $user = auth()->user();

        if ( ! $user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        if ($user->id === $this->comment->user_id) {
            $this->emit('spamActionFailed', 'You cannot mark your own comment as spam.');
            return;
        }

        $alreadyReported = session()->get('comment_spam_reports', []);

        if (in_array($this->comment->id, $alreadyReported)) {
            $this->emit('spamActionFailed', 'You have already marked this comment as spam.');
            return;
        }

        $this->comment->increment('spam_reports');

        session()->put('comment_spam_reports', array_merge($alreadyReported, [$this->comment->id]));

        $this->emit('commentWasMarkedAsSpam', 'Comment was marked as spam!');
    }


    public function render()
    {
        return view('livewire.mark-comment-as-spam');
    }
}
